<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\SchoolClass;
use App\Models\SchoolYear;
use App\Models\Subject;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Affectations - Ecoly')]
class TeacherAssignments extends Component
{
    public ?int $selectedYearId = null;
    public ?int $selectedTeacherId = null;
    
    public array $selectedClasses = [];
    public array $classSubjectAssignments = [];

    public function mount(): void
    {
        $activeYear = SchoolYear::active();
        $this->selectedYearId = $activeYear?->id ?? SchoolYear::latest()->first()?->id;
    }

    public function updatedSelectedYearId(): void
    {
        $this->selectedTeacherId = null;
        $this->selectedClasses = [];
        $this->classSubjectAssignments = [];
    }

    public function updatedSelectedTeacherId(): void
    {
        $this->loadTeacherAssignments();
    }

    private function loadTeacherAssignments(): void
    {
        if (!$this->selectedTeacherId) {
            $this->classSubjectAssignments = [];
            return;
        }

        // Get all class-subject assignments for this teacher
        $assignments = \DB::table('class_subject')
            ->where('teacher_id', $this->selectedTeacherId)
            ->get();

        $this->classSubjectAssignments = [];
        foreach ($assignments as $assignment) {
            $key = "{$assignment->class_id}_{$assignment->subject_id}";
            $this->classSubjectAssignments[$key] = true;
        }
    }

    public function toggleAssignment(int $classId, int $subjectId): void
    {
        $key = "{$classId}_{$subjectId}";
        
        if (isset($this->classSubjectAssignments[$key])) {
            // Remove assignment
            \DB::table('class_subject')
                ->where('class_id', $classId)
                ->where('subject_id', $subjectId)
                ->update(['teacher_id' => null]);
            
            unset($this->classSubjectAssignments[$key]);
            $this->dispatch('toast', message: __('Assignment removed.'), type: 'success');
        } else {
            // Add assignment
            $exists = \DB::table('class_subject')
                ->where('class_id', $classId)
                ->where('subject_id', $subjectId)
                ->exists();

            if ($exists) {
                \DB::table('class_subject')
                    ->where('class_id', $classId)
                    ->where('subject_id', $subjectId)
                    ->update(['teacher_id' => $this->selectedTeacherId]);
            } else {
                // Subject not assigned to class yet - inform user
                $this->dispatch('toast', message: __('Subject not assigned to this class. Add it first in Class Subjects.'), type: 'error');
                return;
            }
            
            $this->classSubjectAssignments[$key] = true;
            $this->dispatch('toast', message: __('Assignment added.'), type: 'success');
        }
    }

    public function setMainTeacher(int $classId): void
    {
        $class = SchoolClass::findOrFail($classId);
        
        if ($class->main_teacher_id === $this->selectedTeacherId) {
            // Remove as main teacher
            $class->update(['main_teacher_id' => null]);
            $this->dispatch('toast', message: __('Main teacher removed.'), type: 'success');
        } else {
            // Set as main teacher
            $class->update(['main_teacher_id' => $this->selectedTeacherId]);
            $this->dispatch('toast', message: __('Main teacher assigned.'), type: 'success');
        }
    }

    public function render()
    {
        $years = SchoolYear::orderByDesc('start_date')->get();
        
        $teachers = User::where('role', 'teacher')
            ->where('is_active', true)
            ->orderByRaw("CONCAT(first_name, ' ', last_name)")
            ->get();

        $classes = $this->selectedYearId 
            ? SchoolClass::with(['subjects', 'mainTeacher'])
                ->forYear($this->selectedYearId)
                ->active()
                ->orderBy('level')
                ->orderBy('section')
                ->get()
            : collect();

        return view('livewire.admin.teacher-assignments', [
            'years' => $years,
            'teachers' => $teachers,
            'classes' => $classes,
        ]);
    }
}
