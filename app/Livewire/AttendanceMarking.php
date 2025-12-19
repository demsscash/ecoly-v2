<?php

namespace App\Livewire;

use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\Attendance;
use App\Models\SchoolYear;
use App\Jobs\SendAttendanceNotification;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Carbon\Carbon;

#[Layout('layouts.app')]
#[Title('Prise de présence - Ecoly')]
class AttendanceMarking extends Component
{
    public string $selectedDate;
    public ?int $selectedClassId = null;
    public array $attendances = [];
    public int $refreshKey = 0; // For forcing re-render
    
    public function mount(): void
    {
        $this->selectedDate = now()->format('Y-m-d');
        
        // If teacher, pre-select first assigned class
        if (auth()->user()->isTeacher()) {
            $firstClass = $this->getTeacherClasses()->first();
            $this->selectedClassId = $firstClass?->id;
        }
    }

    public function updatedSelectedDate(): void
    {
        $this->loadAttendances();
    }

    public function updatedSelectedClassId(): void
    {
        $this->loadAttendances();
    }

    public function loadAttendances(): void
    {
        if (!$this->selectedClassId) {
            $this->attendances = [];
            return;
        }

        $students = Student::where('class_id', $this->selectedClassId)
            ->where('status', 'active')
            ->where('school_year_id', SchoolYear::where('is_active', true)->first()?->id)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $existingAttendances = Attendance::where('date', $this->selectedDate)
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->keyBy('student_id');

        $this->attendances = [];
        foreach ($students as $student) {
            $this->attendances[$student->id] = [
                'student' => $student,
                'status' => $existingAttendances->get($student->id)?->status ?? 'present',
                'note' => $existingAttendances->get($student->id)?->justification_note ?? '',
            ];
        }
        
        $this->refreshKey++; // Force re-render
    }

    public function setStatus(int $studentId, string $status): void
    {
        if (!isset($this->attendances[$studentId])) {
            return;
        }

        $this->attendances[$studentId]['status'] = $status;
        $this->refreshKey++; // Force re-render
    }

    public function markAllPresent(): void
    {
        foreach ($this->attendances as $studentId => $data) {
            $this->attendances[$studentId]['status'] = 'present';
        }
        
        $this->refreshKey++; // Force re-render
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
                ],
                [
                    'status' => $data['status'],
                    'justification_note' => $data['note'] ?? null,
                    'marked_by' => auth()->id(),
                ]
            );

            // Dispatch notification job if status requires it
            if ($attendance->wasRecentlyCreated || $attendance->wasChanged('status')) {
                if ($attendance->requiresNotification()) {
                    SendAttendanceNotification::dispatch($attendance);
                }
            }

            $saved++;
        }

        $this->dispatch('toast', message: __(':count attendance(s) saved.', ['count' => $saved]), type: 'success');
        $this->loadAttendances();
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
            ->orderBy('level')
            ->orderBy('section')
            ->get();
    }

    public function render()
    {
        $user = auth()->user();
        
        // Get available classes based on role
        if ($user->isTeacher()) {
            $classes = $this->getTeacherClasses();
        } else {
            $classes = SchoolClass::orderBy('level')->orderBy('section')->get();
        }

        // Load attendances if class selected
        if ($this->selectedClassId && empty($this->attendances)) {
            $this->loadAttendances();
        }

        return view('livewire.attendance-marking', [
            'classes' => $classes,
        ]);
    }
}
