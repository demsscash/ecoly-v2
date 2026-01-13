<?php

namespace App\Livewire;

use App\Models\Timetable;
use App\Models\SchoolClass;
use App\Models\TimeSlot;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Emploi du Temps - Ecoly')]
class TimetableView extends Component
{
    public ?int $selectedClassId = null;
    public array $schedule = [];

    public function mount(): void
    {
        // Auto-select first class for teachers
        if (auth()->user()->isTeacher()) {
            $firstClass = $this->getTeacherClasses()->first();
            if ($firstClass) {
                $this->selectedClassId = $firstClass->id;
                $this->loadSchedule();
            }
        }
    }

    public function updatedSelectedClassId(): void
    {
        $this->loadSchedule();
    }

    public function loadSchedule(): void
    {
        if (!$this->selectedClassId) {
            $this->schedule = [];
            return;
        }

        $activeYear = \App\Models\SchoolYear::where('is_active', true)->first();
        
        $timetables = Timetable::with(['subject', 'teacher', 'timeSlot'])
            ->where('class_id', $this->selectedClassId)
            ->where('school_year_id', $activeYear->id)
            ->get();

        // Build schedule grid: [time_slot_id][day] = timetable
        $this->schedule = [];
        foreach ($timetables as $timetable) {
            $this->schedule[$timetable->time_slot_id][$timetable->day_of_week] = $timetable;
        }
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
        
        // Get available classes based on role
        if ($user->isTeacher()) {
            $classes = $this->getTeacherClasses();
        } else {
            $classes = SchoolClass::orderBy('level')->orderBy('section')->get();
        }

        $timeSlots = TimeSlot::where('is_active', true)->orderBy('order')->get();
        $days = Timetable::days();

        return view('livewire.timetable-view', [
            'classes' => $classes,
            'timeSlots' => $timeSlots,
            'days' => $days,
        ]);
    }
}
