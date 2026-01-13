<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\Timetable;
use App\Models\SchoolClass;
use App\Models\TimeSlot;
use App\Jobs\SendAttendanceNotification;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Carbon\Carbon;

#[Layout('layouts.app')]
#[Title('Présence par Créneau - Ecoly')]
class AttendanceBySlot extends Component
{
    public string $selectedDate;
    public ?int $selectedClassId = null;
    public ?int $selectedSlotId = null;
    
    public array $attendances = [];
    public ?Timetable $currentTimetable = null;
    
    public function mount(): void
    {
        $this->selectedDate = today()->format('Y-m-d');
    }

    public function updatedSelectedDate(): void
    {
        $this->reset(['selectedSlotId', 'attendances', 'currentTimetable']);
    }

    public function updatedSelectedClassId(): void
    {
        $this->reset(['selectedSlotId', 'attendances', 'currentTimetable']);
    }

    public function updatedSelectedSlotId(): void
    {
        $this->loadAttendances();
    }

    public function loadAttendances(): void
    {
        if (!$this->selectedClassId || !$this->selectedSlotId) {
            $this->attendances = [];
            $this->currentTimetable = null;
            return;
        }

        $date = Carbon::parse($this->selectedDate);
        $dayOfWeek = strtolower($date->englishDayOfWeek);

        // Get timetable entry
        $activeYear = \App\Models\SchoolYear::where('is_active', true)->first();
        $this->currentTimetable = Timetable::with(['subject', 'teacher'])
            ->where('class_id', $this->selectedClassId)
            ->where('time_slot_id', $this->selectedSlotId)
            ->where('day_of_week', $dayOfWeek)
            ->where('school_year_id', $activeYear->id)
            ->first();

        if (!$this->currentTimetable) {
            $this->attendances = [];
            return;
        }

        // Load students
        $students = \App\Models\Student::where('class_id', $this->selectedClassId)
            ->where('status', 'active')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        // Load existing attendances
        $existingAttendances = Attendance::where('date', $this->selectedDate)
            ->where('time_slot_id', $this->selectedSlotId)
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->keyBy('student_id');

        // Build attendances array
        $this->attendances = [];
        foreach ($students as $student) {
            $existing = $existingAttendances->get($student->id);
            $this->attendances[$student->id] = [
                'student' => $student,
                'status' => $existing?->status ?? 'present',
                'note' => $existing?->justification_note ?? '',
            ];
        }
    }

    public function setStatus(int $studentId, string $status): void
    {
        if (isset($this->attendances[$studentId])) {
            $this->attendances[$studentId]['status'] = $status;
        }
    }

    public function markAllPresent(): void
    {
        foreach ($this->attendances as $studentId => $data) {
            $this->attendances[$studentId]['status'] = 'present';
        }
    }

    public function save(): void
    {
        if (empty($this->attendances)) {
            $this->dispatch('toast', message: __('No students to mark.'), type: 'error');
            return;
        }

        $saved = 0;
        foreach ($this->attendances as $studentId => $data) {
            $attendance = Attendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'date' => $this->selectedDate,
                    'time_slot_id' => $this->selectedSlotId,
                ],
                [
                    'status' => $data['status'],
                    'justification_note' => $data['note'] ?? null,
                    'marked_by' => auth()->id(),
                    'timetable_id' => $this->currentTimetable->id,
                ]
            );

            // Dispatch notification if absent
            if ($attendance->wasRecentlyCreated || $attendance->wasChanged('status')) {
                if ($attendance->status === 'absent') {
                    SendAttendanceNotification::dispatch($attendance);
                }
            }

            $saved++;
        }

        $this->dispatch('toast', message: __(':count attendance(s) saved.', ['count' => $saved]), type: 'success');
    }

    private function getTeacherClasses()
    {
        if (!auth()->user()->isTeacher()) {
            return collect();
        }

        $classIds = \DB::table('class_subject')
            ->where('teacher_id', auth()->id())
            ->pluck('class_id')
            ->unique();

        return SchoolClass::whereIn('id', $classIds)
            ->orderBy('level')->orderBy('section')
            ->get();
    }

    public function render()
    {
        $user = auth()->user();
        
        // Get available classes
        if ($user->isTeacher()) {
            $classes = $this->getTeacherClasses();
        } else {
            $classes = SchoolClass::orderBy('level')->orderBy('section')->get();
        }

        // Get time slots for selected date and class
        $availableSlots = collect();
        if ($this->selectedClassId && $this->selectedDate) {
            $date = Carbon::parse($this->selectedDate);
            $dayOfWeek = strtolower($date->englishDayOfWeek);
            
            $activeYear = \App\Models\SchoolYear::where('is_active', true)->first();
            
            $timetableSlots = Timetable::where('class_id', $this->selectedClassId)
                ->where('day_of_week', $dayOfWeek)
                ->where('school_year_id', $activeYear->id)
                ->pluck('time_slot_id');

            $availableSlots = TimeSlot::whereIn('id', $timetableSlots)
                ->where('is_active', true)
                ->where('is_break', false)
                ->orderBy('order')
                ->get();
        }

        return view('livewire.attendance-by-slot', [
            'classes' => $classes,
            'availableSlots' => $availableSlots,
        ]);
    }
}
