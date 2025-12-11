<?php

namespace App\Livewire\Grades;

use App\Models\Grade;
use App\Models\SchoolClass;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Trimester;
use App\Models\GradingConfig;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Saisie des notes - Ecoly')]
class GradeEntry extends Component
{
    public ?int $selectedYearId = null;
    public ?int $selectedClassId = null;
    public ?int $selectedSubjectId = null;
    public ?int $selectedTrimesterId = null;
    
    public array $grades = [];
    public float $controlWeight = 40;
    public float $examWeight = 60;

    public function mount(): void
    {
        $activeYear = SchoolYear::active();
        $this->selectedYearId = $activeYear?->id ?? SchoolYear::latest()->first()?->id;
        $this->loadGradingConfig();
    }

    private function loadGradingConfig(): void
    {
        if (!$this->selectedYearId) {
            return;
        }

        $config = GradingConfig::where('school_year_id', $this->selectedYearId)->first();
        if ($config) {
            $this->controlWeight = $config->control_weight;
            $this->examWeight = $config->exam_weight;
        }
    }

    public function updatedSelectedYearId(): void
    {
        $this->reset(['selectedClassId', 'selectedSubjectId', 'selectedTrimesterId', 'grades']);
        $this->loadGradingConfig();
    }

    public function updatedSelectedClassId(): void
    {
        $this->reset(['selectedSubjectId', 'grades']);
    }

    public function updatedSelectedSubjectId(): void
    {
        $this->reset(['grades']);
        $this->loadGrades();
    }

    public function updatedSelectedTrimesterId(): void
    {
        $this->reset(['grades']);
        $this->loadGrades();
    }

    private function loadGrades(): void
    {
        if (!$this->selectedClassId || !$this->selectedSubjectId || !$this->selectedTrimesterId) {
            return;
        }

        $trimester = Trimester::find($this->selectedTrimesterId);
        if (!$trimester || $trimester->status === 'finalized') {
            $this->dispatch('toast', message: __('This trimester is finalized. Grades cannot be modified.'), type: 'warning');
        }

        $students = Student::where('class_id', $this->selectedClassId)
            ->where('status', 'active')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $existingGrades = Grade::where('class_id', $this->selectedClassId)
            ->where('subject_id', $this->selectedSubjectId)
            ->where('trimester_id', $this->selectedTrimesterId)
            ->get()
            ->keyBy('student_id');

        $this->grades = [];
        foreach ($students as $student) {
            $grade = $existingGrades->get($student->id);
            $this->grades[$student->id] = [
                'student_name' => $student->full_name,
                'control_grade' => $grade?->control_grade ?? '',
                'exam_grade' => $grade?->exam_grade ?? '',
                'average' => $grade?->average ?? '',
                'appreciation' => $grade?->appreciation ?? '',
            ];
        }
    }

    public function calculateAverage(int $studentId): void
    {
        $control = $this->grades[$studentId]['control_grade'];
        $exam = $this->grades[$studentId]['exam_grade'];

        $control = $control !== '' ? (float) $control : null;
        $exam = $exam !== '' ? (float) $exam : null;

        if ($control === null && $exam === null) {
            $this->grades[$studentId]['average'] = '';
            return;
        }

        $totalWeight = 0;
        $weightedSum = 0;

        if ($control !== null) {
            $weightedSum += $control * $this->controlWeight;
            $totalWeight += $this->controlWeight;
        }

        if ($exam !== null) {
            $weightedSum += $exam * $this->examWeight;
            $totalWeight += $this->examWeight;
        }

        $average = $totalWeight > 0 ? round($weightedSum / $totalWeight, 2) : '';
        $this->grades[$studentId]['average'] = $average;
    }

    public function save(): void
    {
        if (!$this->selectedClassId || !$this->selectedSubjectId || !$this->selectedTrimesterId) {
            return;
        }

        $trimester = Trimester::find($this->selectedTrimesterId);
        if (!$trimester || $trimester->status === 'finalized') {
            $this->dispatch('toast', message: __('This trimester is finalized. Grades cannot be modified.'), type: 'error');
            return;
        }

        $class = SchoolClass::find($this->selectedClassId);
        $maxGrade = $class?->grade_base ?? 20;

        foreach ($this->grades as $studentId => $gradeData) {
            $control = $gradeData['control_grade'] !== '' ? (float) $gradeData['control_grade'] : null;
            $exam = $gradeData['exam_grade'] !== '' ? (float) $gradeData['exam_grade'] : null;
            $average = $gradeData['average'] !== '' ? (float) $gradeData['average'] : null;
            $appreciation = $gradeData['appreciation'] ?: null;

            if ($control !== null && ($control < 0 || $control > $maxGrade)) {
                $this->dispatch('toast', message: __('Invalid grade for :name. Must be between 0 and :max.', ['name' => $gradeData['student_name'], 'max' => $maxGrade]), type: 'error');
                return;
            }
            if ($exam !== null && ($exam < 0 || $exam > $maxGrade)) {
                $this->dispatch('toast', message: __('Invalid grade for :name. Must be between 0 and :max.', ['name' => $gradeData['student_name'], 'max' => $maxGrade]), type: 'error');
                return;
            }

            Grade::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'subject_id' => $this->selectedSubjectId,
                    'trimester_id' => $this->selectedTrimesterId,
                ],
                [
                    'class_id' => $this->selectedClassId,
                    'control_grade' => $control,
                    'exam_grade' => $exam,
                    'average' => $average,
                    'appreciation' => $appreciation,
                    'entered_by' => auth()->id(),
                    'entered_at' => now(),
                ]
            );
        }

        $this->dispatch('toast', message: __('Grades saved successfully.'), type: 'success');
    }

    public function render()
    {
        $years = SchoolYear::orderByDesc('start_date')->get();

        $classes = collect();
        $subjects = collect();
        $trimesters = collect();

        if ($this->selectedYearId) {
            $user = auth()->user();
            $classQuery = SchoolClass::forYear($this->selectedYearId)->active();
            
            if ($user->isTeacher()) {
                $classIds = \DB::table('class_subject')
                    ->where('teacher_id', $user->id)
                    ->pluck('class_id')
                    ->unique();
                $classQuery->whereIn('id', $classIds);
            }
            
            $classes = $classQuery->orderBy('level')->orderBy('section')->get();

            $trimesters = Trimester::where('school_year_id', $this->selectedYearId)
                ->orderBy('start_date')
                ->get();
        }

        if ($this->selectedClassId) {
            $user = auth()->user();
            $subjectQuery = Subject::whereHas('classes', function ($q) {
                $q->where('class_id', $this->selectedClassId);
            });

            if ($user->isTeacher()) {
                $subjectIds = \DB::table('class_subject')
                    ->where('class_id', $this->selectedClassId)
                    ->where('teacher_id', $user->id)
                    ->pluck('subject_id');
                $subjectQuery->whereIn('id', $subjectIds);
            }

            $subjects = $subjectQuery->orderBy('name_fr')->get();
        }

        $selectedClass = $this->selectedClassId ? SchoolClass::find($this->selectedClassId) : null;
        $selectedTrimester = $this->selectedTrimesterId ? Trimester::find($this->selectedTrimesterId) : null;

        return view('livewire.grades.grade-entry', [
            'years' => $years,
            'classes' => $classes,
            'subjects' => $subjects,
            'trimesters' => $trimesters,
            'selectedClass' => $selectedClass,
            'selectedTrimester' => $selectedTrimester,
            'canEdit' => $selectedTrimester && $selectedTrimester->status !== 'finalized',
        ]);
    }
}
