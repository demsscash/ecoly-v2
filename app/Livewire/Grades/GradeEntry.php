<?php

namespace App\Livewire\Grades;

use App\Models\Grade;
use App\Models\GradingConfig;
use App\Models\SchoolClass;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Trimester;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Saisie des notes - Ecoly')]
class GradeEntry extends Component
{
    public ?int $selectedClassId = null;
    public ?int $selectedSubjectId = null;
    public ?int $selectedTrimesterId = null;
    
    public array $grades = [];
    public bool $isFinalized = false;
    public ?int $subjectGradeBase = null;

    public function updatedSelectedClassId(): void
    {
        $this->selectedSubjectId = null;
        $this->grades = [];
        $this->subjectGradeBase = null;
        $this->loadGrades();
    }

    public function updatedSelectedSubjectId(): void
    {
        $this->loadSubjectGradeBase();
        $this->loadGrades();
    }

    public function updatedSelectedTrimesterId(): void
    {
        $this->checkTrimesterStatus();
        $this->loadGrades();
    }

    protected function checkTrimesterStatus(): void
    {
        if ($this->selectedTrimesterId) {
            $trimester = Trimester::find($this->selectedTrimesterId);
            $this->isFinalized = $trimester?->status === 'finalized';
        }
    }

    protected function loadSubjectGradeBase(): void
    {
        if (!$this->selectedClassId || !$this->selectedSubjectId) {
            $this->subjectGradeBase = null;
            return;
        }

        $class = SchoolClass::find($this->selectedClassId);
        $pivot = $class?->subjects()->where('subjects.id', $this->selectedSubjectId)->first()?->pivot;
        
        // Use pivot grade_base if exists, otherwise class grade_base, default 20
        $this->subjectGradeBase = $pivot?->grade_base ?? $class?->grade_base ?? 20;
    }

    protected function loadGrades(): void
    {
        if (!$this->selectedClassId || !$this->selectedSubjectId || !$this->selectedTrimesterId) {
            $this->grades = [];
            return;
        }

        $students = Student::where('class_id', $this->selectedClassId)
            ->where('status', 'active')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $this->grades = [];

        foreach ($students as $student) {
            $grade = Grade::where('student_id', $student->id)
                ->where('subject_id', $this->selectedSubjectId)
                ->where('trimester_id', $this->selectedTrimesterId)
                ->first();

            $this->grades[$student->id] = [
                'student_name' => $student->full_name,
                'control_grade' => $grade?->control_grade ?? '',
                'exam_grade' => $grade?->exam_grade ?? '',
                'average' => $grade?->average,
                'appreciation' => $grade?->appreciation ?? '',
            ];
        }
    }

    /**
     * Generate automatic appreciation based on grade and grade base
     */
    protected function getAutoAppreciation(?float $average, int $gradeBase = 20): string
    {
        if ($average === null) return '';

        // Normalize to 20 scale for threshold comparison
        $normalized = ($average / $gradeBase) * 20;

        $config = GradingConfig::instance();

        if ($normalized >= $config->excellent_threshold) return 'Excellent';
        if ($normalized >= $config->very_good_threshold) return 'Très Bien';
        if ($normalized >= $config->good_threshold) return 'Bien';
        if ($normalized >= $config->fairly_good_threshold) return 'Assez Bien';
        if ($normalized >= $config->pass_threshold) return 'Passable';
        
        return 'Insuffisant';
    }

    /**
     * Calculate average and appreciation when grade changes
     */
    public function calculateAverage(int $studentId): void
    {
        if ($this->isFinalized) return;

        $control = $this->grades[$studentId]['control_grade'];
        $exam = $this->grades[$studentId]['exam_grade'];
        $gradeBase = $this->subjectGradeBase ?? 20;

        if ($control === '' && $exam === '') {
            $this->grades[$studentId]['average'] = null;
            $this->grades[$studentId]['appreciation'] = '';
            return;
        }

        $config = GradingConfig::instance();
        $controlWeight = $config->control_weight / 100;
        $examWeight = $config->exam_weight / 100;

        $controlVal = $control !== '' ? (float) $control : null;
        $examVal = $exam !== '' ? (float) $exam : null;

        if ($controlVal !== null && $examVal !== null) {
            $average = ($controlVal * $controlWeight) + ($examVal * $examWeight);
        } elseif ($controlVal !== null) {
            $average = $controlVal;
        } elseif ($examVal !== null) {
            $average = $examVal;
        } else {
            $average = null;
        }

        $this->grades[$studentId]['average'] = $average !== null ? round($average, 2) : null;
        
        // Always update appreciation dynamically
        $this->grades[$studentId]['appreciation'] = $this->getAutoAppreciation(
            $this->grades[$studentId]['average'],
            $gradeBase
        );
    }

    public function save(): void
    {
        if ($this->isFinalized) {
            $this->dispatch('toast', message: __('This trimester is finalized. Grades cannot be modified.'), type: 'error');
            return;
        }

        $gradeBase = $this->subjectGradeBase ?? 20;

        foreach ($this->grades as $studentId => $data) {
            // Validate grades against subject grade base
            if ($data['control_grade'] !== '' && ($data['control_grade'] < 0 || $data['control_grade'] > $gradeBase)) {
                $this->dispatch('toast', message: __('Invalid grade for :name. Must be between 0 and :max.', [
                    'name' => $data['student_name'],
                    'max' => $gradeBase
                ]), type: 'error');
                return;
            }
            if ($data['exam_grade'] !== '' && ($data['exam_grade'] < 0 || $data['exam_grade'] > $gradeBase)) {
                $this->dispatch('toast', message: __('Invalid grade for :name. Must be between 0 and :max.', [
                    'name' => $data['student_name'],
                    'max' => $gradeBase
                ]), type: 'error');
                return;
            }
        }

        foreach ($this->grades as $studentId => $data) {
            $controlGrade = $data['control_grade'] !== '' ? (float) $data['control_grade'] : null;
            $examGrade = $data['exam_grade'] !== '' ? (float) $data['exam_grade'] : null;

            if ($controlGrade === null && $examGrade === null) {
                Grade::where('student_id', $studentId)
                    ->where('subject_id', $this->selectedSubjectId)
                    ->where('trimester_id', $this->selectedTrimesterId)
                    ->delete();
                continue;
            }

            Grade::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'subject_id' => $this->selectedSubjectId,
                    'trimester_id' => $this->selectedTrimesterId,
                ],
                [
                    'class_id' => $this->selectedClassId,
                    'control_grade' => $controlGrade,
                    'exam_grade' => $examGrade,
                    'average' => $data['average'],
                    'appreciation' => $data['appreciation'] ?: $this->getAutoAppreciation($data['average'], $gradeBase),
                    'entered_by' => auth()->id(),
                    'entered_at' => now(),
                ]
            );
        }

        $this->dispatch('toast', message: __('Grades saved successfully.'), type: 'success');
    }

    public function render()
    {
        $user = auth()->user();
        $schoolYear = SchoolYear::where('is_active', true)->first();

        // Get classes based on role
        if ($user->isAdmin() || $user->isSecretary()) {
            $classes = SchoolClass::when($schoolYear, fn($q) => $q->where('school_year_id', $schoolYear->id))
                ->where('is_active', true)
                ->orderBy('level')
                ->orderBy('name')
                ->get();
        } else {
            // Teacher: only assigned classes
            $classes = SchoolClass::when($schoolYear, fn($q) => $q->where('school_year_id', $schoolYear->id))
                ->where('is_active', true)
                ->whereHas('subjects', function ($q) use ($user) {
                    $q->where('class_subject.teacher_id', $user->id);
                })
                ->orderBy('level')
                ->orderBy('name')
                ->get();
        }

        // Get subjects for selected class with grade_base
        $subjects = collect();
        if ($this->selectedClassId) {
            $subjectsQuery = Subject::whereHas('classes', function ($q) {
                $q->where('classes.id', $this->selectedClassId);
            });

            // Filter by teacher if not admin
            if (!$user->isAdmin() && !$user->isSecretary()) {
                $subjectsQuery->whereHas('classes', function ($q) use ($user) {
                    $q->where('classes.id', $this->selectedClassId)
                        ->where('class_subject.teacher_id', $user->id);
                });
            }

            $subjects = $subjectsQuery->orderBy('name_fr')->get();
            
            // Attach grade_base from pivot
            $class = SchoolClass::find($this->selectedClassId);
            foreach ($subjects as $subject) {
                $pivot = $class->subjects()->where('subjects.id', $subject->id)->first()?->pivot;
                $subject->grade_base_display = $pivot?->grade_base ?? $class->grade_base ?? 20;
            }
        }

        // Get trimesters
        $trimesters = Trimester::when($schoolYear, fn($q) => $q->where('school_year_id', $schoolYear->id))
            ->orderBy('start_date')
            ->get();

        // Select first open trimester by default
        if (!$this->selectedTrimesterId && $trimesters->isNotEmpty()) {
            $openTrimester = $trimesters->firstWhere('status', 'open');
            $this->selectedTrimesterId = $openTrimester?->id ?? $trimesters->first()->id;
            $this->checkTrimesterStatus();
        }

        $selectedClass = $this->selectedClassId ? SchoolClass::find($this->selectedClassId) : null;

        return view('livewire.grades.grade-entry', [
            'classes' => $classes,
            'subjects' => $subjects,
            'trimesters' => $trimesters,
            'selectedClass' => $selectedClass,
        ]);
    }
}
