<?php

namespace App\Livewire\Teacher;

use App\Models\SchoolClass;
use App\Models\SchoolYear;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
#[Title('Mes Classes - Ecoly')]
class MyClasses extends Component
{
    public ?int $selectedYearId = null;

    public function mount(): void
    {
        $activeYear = SchoolYear::active();
        $this->selectedYearId = $activeYear?->id ?? SchoolYear::latest()->first()?->id;
    }

    public function render()
    {
        $years = SchoolYear::orderByDesc('start_date')->get();
        $user = auth()->user();

        // Get classes where this teacher is assigned (via class_subject)
        $classIds = DB::table('class_subject')
            ->where('teacher_id', $user->id)
            ->pluck('class_id')
            ->unique();

        $classes = SchoolClass::with(['schoolYear', 'students' => function ($q) {
                $q->where('status', 'active');
            }])
            ->whereIn('id', $classIds)
            ->when($this->selectedYearId, fn($q) => $q->where('school_year_id', $this->selectedYearId))
            ->orderBy('level')
            ->orderBy('section')
            ->get();

        // Get subjects for each class that this teacher teaches
        $classSubjects = [];
        foreach ($classes as $class) {
            $subjects = DB::table('class_subject')
                ->join('subjects', 'subjects.id', '=', 'class_subject.subject_id')
                ->where('class_subject.class_id', $class->id)
                ->where('class_subject.teacher_id', $user->id)
                ->select('subjects.id', 'subjects.name_fr', 'subjects.code', 'class_subject.coefficient')
                ->get();
            
            $classSubjects[$class->id] = $subjects;
        }

        // Check if user is main teacher for any class
        $mainTeacherClasses = SchoolClass::where('main_teacher_id', $user->id)
            ->when($this->selectedYearId, fn($q) => $q->where('school_year_id', $this->selectedYearId))
            ->pluck('id')
            ->toArray();

        return view('livewire.teacher.my-classes', [
            'years' => $years,
            'classes' => $classes,
            'classSubjects' => $classSubjects,
            'mainTeacherClasses' => $mainTeacherClasses,
        ]);
    }
}
