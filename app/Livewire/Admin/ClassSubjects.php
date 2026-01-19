<?php

namespace App\Livewire\Admin;

use App\Models\ClassSubject;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Affectation Matières - Ecoly')]
class ClassSubjects extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public ?int $editingAssignmentId = null;

    public ?int $class_id = null;
    public ?int $subject_id = null;
    public ?int $teacher_id = null;
    public int $max_grade = 20;
    public int $coefficient = 1;

    public ?int $filterClass = null;

    public function openCreateModal(): void
    {
        $this->reset(['class_id', 'subject_id', 'teacher_id', 'max_grade', 'coefficient', 'editingAssignmentId']);
        $this->max_grade = 20;
        $this->coefficient = 1;
        $this->showModal = true;
    }

    public function openEditModal(int $assignmentId): void
    {
        $assignment = ClassSubject::findOrFail($assignmentId);

        $this->editingAssignmentId = $assignment->id;
        $this->class_id = $assignment->class_id;
        $this->subject_id = $assignment->subject_id;
        $this->teacher_id = $assignment->teacher_id;
        $this->max_grade = $assignment->max_grade ?? 20;
        $this->coefficient = $assignment->coefficient ?? 1;

        $this->showModal = true;
    }

    public function updatedClassId(): void
    {
        if ($this->class_id) {
            $class = SchoolClass::find($this->class_id);
            
            if ($class && ($class->isCollege() || $class->isLycee())) {
                // College/Lycee: fixed /20
                $this->max_grade = 20;
            }
        }
    }

    public function save(): void
    {
        $rules = [
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'nullable|exists:users,id',
        ];

        $class = SchoolClass::find($this->class_id);

        // Max grade validation based on class type
        if ($class && ($class->isCollege() || $class->isLycee())) {
            // College/Lycee: always 20
            $this->max_grade = 20;
            $rules['coefficient'] = 'required|integer|min:1|max:10';
        } else {
            // Fondamental: custom max_grade, coefficient always 1
            $rules['max_grade'] = 'required|integer|min:1|max:100';
            $this->coefficient = 1;
        }

        $this->validate($rules);

        // Check if assignment already exists (exclude current record in edit mode)
        $query = ClassSubject::where('class_id', $this->class_id)
            ->where('subject_id', $this->subject_id);
        
        if ($this->editingAssignmentId) {
            $query->where('id', '!=', $this->editingAssignmentId);
        }
        
        if ($query->exists()) {
            $this->dispatch('toast',
                message: __('This subject is already assigned to this class.'),
                type: 'error'
            );
            return;
        }

        $data = [
            'class_id' => $this->class_id,
            'subject_id' => $this->subject_id,
            'teacher_id' => $this->teacher_id,
            'max_grade' => $this->max_grade,
            'coefficient' => $this->coefficient,
        ];

        if ($this->editingAssignmentId) {
            // Update
            $assignment = ClassSubject::findOrFail($this->editingAssignmentId);
            $assignment->update($data);

            $message = __('Assignment updated successfully.');
        } else {
            // Create
            ClassSubject::create($data);

            $message = __('Subject assigned successfully.');
        }

        $this->showModal = false;
        $this->reset(['class_id', 'subject_id', 'teacher_id', 'max_grade', 'coefficient', 'editingAssignmentId']);

        $this->dispatch('toast', message: $message, type: 'success');
    }

    public function delete(int $assignmentId): void
    {
        $assignment = ClassSubject::findOrFail($assignmentId);

        // Check if there are grades for this assignment
        if ($assignment->grades()->count() > 0) {
            $this->dispatch('toast',
                message: __('Cannot delete assignment that has grades.'),
                type: 'error'
            );
            return;
        }

        $assignment->delete();

        $this->dispatch('toast', message: __('Assignment deleted successfully.'), type: 'success');
    }

    public function render()
    {
        $query = ClassSubject::with(['class.serie', 'subject', 'teacher']);

        if ($this->filterClass) {
            $query->where('class_id', $this->filterClass);
        }

        $assignments = $query->paginate(15);

        $classes = SchoolClass::with('serie')
            ->orderBy('level')
            ->orderBy('section')
            ->get();

        $subjects = Subject::orderBy('name_fr')->get();

        $teachers = User::where('role', 'teacher')
            ->orderBy('first_name')
            ->get();

        return view('livewire.admin.class-subjects', [
            'assignments' => $assignments,
            'classes' => $classes,
            'subjects' => $subjects,
            'teachers' => $teachers,
        ]);
    }
}
