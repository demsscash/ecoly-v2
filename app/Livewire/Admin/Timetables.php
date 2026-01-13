<?php

namespace App\Livewire\Admin;

use App\Models\Timetable;
use App\Models\SchoolYear;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use App\Models\TimeSlot;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Emplois du Temps - Ecoly')]
class Timetables extends Component
{
    use WithPagination;

    public int $school_year_id;
    public string $filterClass = '';
    public string $filterDay = '';
    
    public bool $showModal = false;
    public ?int $editingId = null;
    
    public int $class_id;
    public int $subject_id;
    public ?int $teacher_id = null;
    public int $time_slot_id;
    public string $day_of_week = 'monday';
    public string $room = '';
    public string $notes = '';

    public function mount(): void
    {
        $activeYear = SchoolYear::where('is_active', true)->first();
        $this->school_year_id = $activeYear?->id ?? SchoolYear::latest()->first()?->id;
    }

    public function openCreateModal(): void
    {
        $this->reset(['class_id', 'subject_id', 'teacher_id', 'time_slot_id', 'day_of_week', 'room', 'notes', 'editingId']);
        $this->day_of_week = 'monday';
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $timetable = Timetable::findOrFail($id);
        
        $this->editingId = $timetable->id;
        $this->class_id = $timetable->class_id;
        $this->subject_id = $timetable->subject_id;
        $this->teacher_id = $timetable->teacher_id;
        $this->time_slot_id = $timetable->time_slot_id;
        $this->day_of_week = $timetable->day_of_week;
        $this->room = $timetable->room ?? '';
        $this->notes = $timetable->notes ?? '';
        
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'nullable|exists:users,id',
            'time_slot_id' => 'required|exists:time_slots,id',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'room' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:500',
        ]);

        // Check for conflicts
        $conflict = Timetable::where('class_id', $this->class_id)
            ->where('time_slot_id', $this->time_slot_id)
            ->where('day_of_week', $this->day_of_week)
            ->when($this->editingId, fn($q) => $q->where('id', '!=', $this->editingId))
            ->exists();

        if ($conflict) {
            $this->dispatch('toast', message: __('This time slot is already occupied for this class.'), type: 'error');
            return;
        }

        $data = [
            'school_year_id' => $this->school_year_id,
            'class_id' => $this->class_id,
            'subject_id' => $this->subject_id,
            'teacher_id' => $this->teacher_id,
            'time_slot_id' => $this->time_slot_id,
            'day_of_week' => $this->day_of_week,
            'room' => $this->room,
            'notes' => $this->notes,
        ];

        if ($this->editingId) {
            Timetable::findOrFail($this->editingId)->update($data);
            $message = __('Timetable updated successfully.');
        } else {
            Timetable::create($data);
            $message = __('Timetable created successfully.');
        }

        $this->showModal = false;
        $this->reset(['class_id', 'subject_id', 'teacher_id', 'time_slot_id', 'day_of_week', 'room', 'notes', 'editingId']);
        $this->dispatch('toast', message: $message, type: 'success');
    }

    public function delete(int $id): void
    {
        $timetable = Timetable::findOrFail($id);
        
        // Check if there are attendances
        if ($timetable->attendances()->count() > 0) {
            $this->dispatch('toast', message: __('Cannot delete timetable with attendance records.'), type: 'error');
            return;
        }

        $timetable->delete();
        $this->dispatch('toast', message: __('Timetable deleted successfully.'), type: 'success');
    }

    public function render()
    {
        $query = Timetable::with(['class', 'subject', 'teacher', 'timeSlot'])
            ->where('school_year_id', $this->school_year_id);

        if ($this->filterClass) {
            $query->where('class_id', $this->filterClass);
        }

        if ($this->filterDay) {
            $query->where('day_of_week', $this->filterDay);
        }

        $timetables = $query->orderBy('day_of_week')
            ->orderBy('time_slot_id')
            ->paginate(20);

        return view('livewire.admin.timetables', [
            'timetables' => $timetables,
            'classes' => SchoolClass::where('school_year_id', $this->school_year_id)
                ->orderBy('level')->orderBy('section')->get(),
            'subjects' => Subject::orderBy('name_fr')->get(),
            'teachers' => User::where('role', 'teacher')->where('is_active', true)
                ->orderBy('first_name')->get(),
            'timeSlots' => TimeSlot::where('is_active', true)->orderBy('order')->get(),
            'days' => Timetable::days(),
        ]);
    }
}
