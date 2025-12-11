<?php

namespace App\Livewire\Admin;

use App\Models\SchoolClass;
use App\Models\SchoolYear;
use App\Models\Subject;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Matières par classe - Ecoly')]
class ClassSubjects extends Component
{
    public ?int $selectedClassId = null;
    public ?SchoolClass $selectedClass = null;
    
    public bool $showAddModal = false;
    public ?int $newSubjectId = null;
    public ?int $newGradeBase = null;

    public function selectClass(int $classId): void
    {
        $this->selectedClassId = $classId;
        $this->selectedClass = SchoolClass::find($classId);
    }

    public function addSubject(): void
    {
        $this->validate([
            'newSubjectId' => 'required|exists:subjects,id',
        ]);

        $exists = $this->selectedClass->subjects()->where('subjects.id', $this->newSubjectId)->exists();
        
        if ($exists) {
            $this->dispatch('toast', message: __('Subject already assigned to this class.'), type: 'error');
            return;
        }

        $this->selectedClass->subjects()->attach($this->newSubjectId, [
            'grade_base' => $this->newGradeBase,
        ]);

        $this->showAddModal = false;
        $this->reset(['newSubjectId', 'newGradeBase']);
        $this->dispatch('toast', message: __('Subject assigned successfully.'), type: 'success');
    }

    public function updateGradeBase(int $subjectId, $value): void
    {
        $this->selectedClass->subjects()->updateExistingPivot($subjectId, [
            'grade_base' => $value ?: null,
        ]);
        $this->dispatch('toast', message: __('Grade base updated successfully.'), type: 'success');
    }

    public function updateTeacher(int $subjectId, $value): void
    {
        $this->selectedClass->subjects()->updateExistingPivot($subjectId, [
            'teacher_id' => $value ?: null,
        ]);
        $this->dispatch('toast', message: __('Teacher updated successfully.'), type: 'success');
    }

    public function removeSubject(int $subjectId): void
    {
        $this->selectedClass->subjects()->detach($subjectId);
        $this->dispatch('toast', message: __('Subject removed from class.'), type: 'success');
    }

    public function render()
    {
        $schoolYear = SchoolYear::where('is_active', true)->first();

        $classes = SchoolClass::when($schoolYear, fn($q) => $q->where('school_year_id', $schoolYear->id))
            ->where('is_active', true)
            ->with('subjects')
            ->orderBy('level')
            ->orderBy('name')
            ->get();

        $classSubjects = collect();
        $availableSubjects = collect();
        $teachers = collect();

        if ($this->selectedClassId) {
            $this->selectedClass = SchoolClass::find($this->selectedClassId);
            $classSubjects = $this->selectedClass->subjects()->orderBy('name_fr')->get();
            
            $assignedIds = $classSubjects->pluck('id');
            $availableSubjects = Subject::where('is_active', true)
                ->whereNotIn('id', $assignedIds)
                ->orderBy('name_fr')
                ->get();

            $teachers = User::where('role', 'teacher')
                ->where('is_active', true)
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get();
        }

        return view('livewire.admin.class-subjects', [
            'classes' => $classes,
            'classSubjects' => $classSubjects,
            'availableSubjects' => $availableSubjects,
            'teachers' => $teachers,
        ]);
    }
}
