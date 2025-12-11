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
    public ?int $selectedYearId = null;
    public ?int $selectedClassId = null;
    
    // For adding subject
    public ?int $subjectId = null;
    public ?int $teacherId = null;
    public string $coefficient = '';
    
    public bool $showModal = false;

    public function mount(): void
    {
        $activeYear = SchoolYear::active();
        $this->selectedYearId = $activeYear?->id ?? SchoolYear::latest()->first()?->id;
    }

    public function updatedSelectedYearId(): void
    {
        $this->selectedClassId = null;
    }

    public function openAddModal(): void
    {
        $this->reset(['subjectId', 'teacherId', 'coefficient']);
        $this->showModal = true;
    }

    public function addSubject(): void
    {
        $this->validate([
            'subjectId' => 'required|exists:subjects,id',
            'teacherId' => 'nullable|exists:users,id',
            'coefficient' => 'nullable|numeric|min:0.5|max:10',
        ]);

        $class = SchoolClass::findOrFail($this->selectedClassId);
        
        // Check if already assigned
        if ($class->subjects()->where('subject_id', $this->subjectId)->exists()) {
            $this->dispatch('toast', message: __('Subject already assigned to this class.'), type: 'error');
            return;
        }

        $class->subjects()->attach($this->subjectId, [
            'teacher_id' => $this->teacherId ?: null,
            'coefficient' => $this->coefficient ?: null,
        ]);

        $this->dispatch('toast', message: __('Subject assigned successfully.'), type: 'success');
        $this->showModal = false;
        $this->reset(['subjectId', 'teacherId', 'coefficient']);
    }

    public function updateCoefficient(int $subjectId, string $value): void
    {
        $class = SchoolClass::findOrFail($this->selectedClassId);
        
        $class->subjects()->updateExistingPivot($subjectId, [
            'coefficient' => $value ?: null,
        ]);

        $this->dispatch('toast', message: __('Coefficient updated successfully.'), type: 'success');
    }

    public function updateTeacher(int $subjectId, ?int $teacherId): void
    {
        $class = SchoolClass::findOrFail($this->selectedClassId);
        
        $class->subjects()->updateExistingPivot($subjectId, [
            'teacher_id' => $teacherId ?: null,
        ]);

        $this->dispatch('toast', message: __('Teacher updated successfully.'), type: 'success');
    }

    public function removeSubject(int $subjectId): void
    {
        $class = SchoolClass::findOrFail($this->selectedClassId);
        $class->subjects()->detach($subjectId);

        $this->dispatch('toast', message: __('Subject removed from class.'), type: 'success');
    }

    public function render()
    {
        $years = SchoolYear::orderByDesc('start_date')->get();
        
        $classes = $this->selectedYearId 
            ? SchoolClass::forYear($this->selectedYearId)
                ->active()
                ->orderBy('level')
                ->orderBy('section')
                ->get()
            : collect();

        $classSubjects = collect();
        $availableSubjects = collect();
        
        if ($this->selectedClassId) {
            $class = SchoolClass::with(['subjects' => function($query) {
                $query->orderBy('name_fr');
            }])->find($this->selectedClassId);
            
            $classSubjects = $class?->subjects ?? collect();
            
            // Subjects not yet assigned to this class
            $assignedIds = $classSubjects->pluck('id')->toArray();
            $availableSubjects = Subject::active()
                ->whereNotIn('id', $assignedIds)
                ->orderBy('name_fr')
                ->get();
        }

        // Get teachers - for now get all active users (we'll filter by role later when we have teachers)
        $teachers = User::where('is_active', true)
            ->orderByRaw("CONCAT(first_name, ' ', last_name)")
            ->get();

        return view('livewire.admin.class-subjects', [
            'years' => $years,
            'classes' => $classes,
            'classSubjects' => $classSubjects,
            'availableSubjects' => $availableSubjects,
            'teachers' => $teachers,
        ]);
    }
}
