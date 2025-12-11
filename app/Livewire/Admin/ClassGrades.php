<?php

namespace App\Livewire\Admin;

use App\Models\Grade;
use App\Models\SchoolClass;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Trimester;
use App\Models\GradingConfig;
use App\Exports\ClassGradesExport;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.app')]
#[Title('Notes par classe - Ecoly')]
class ClassGrades extends Component
{
    public ?int $selectedClassId = null;
    public ?int $selectedTrimesterId = null;
    public bool $showHistory = false;
    public ?int $historyGradeId = null;

    public function mount(): void
    {
        $schoolYear = SchoolYear::where('is_active', true)->first();
        
        if ($schoolYear) {
            $firstClass = SchoolClass::where('school_year_id', $schoolYear->id)
                ->where('is_active', true)
                ->orderBy('level')
                ->first();
            $this->selectedClassId = $firstClass?->id;

            $trimester = Trimester::where('school_year_id', $schoolYear->id)
                ->where('status', 'open')
                ->first() 
                ?? Trimester::where('school_year_id', $schoolYear->id)->first();
            $this->selectedTrimesterId = $trimester?->id;
        }
    }

    /**
     * Calculate student rankings for a class/trimester
     */
    public function getStudentRankings(): array
    {
        if (!$this->selectedClassId || !$this->selectedTrimesterId) {
            return [];
        }

        $students = Student::where('class_id', $this->selectedClassId)
            ->where('status', 'active')
            ->get();

        $subjects = Subject::whereHas('classes', function ($q) {
            $q->where('classes.id', $this->selectedClassId);
        })->get();

        $rankings = [];

        foreach ($students as $student) {
            $totalWeighted = 0;
            $totalCoef = 0;
            $gradesData = [];
            $allValidated = true;
            $hasGrades = false;

            foreach ($subjects as $subject) {
                $grade = Grade::where('student_id', $student->id)
                    ->where('subject_id', $subject->id)
                    ->where('trimester_id', $this->selectedTrimesterId)
                    ->first();

                $coef = $subject->classes()
                    ->where('classes.id', $this->selectedClassId)
                    ->first()?->pivot?->coefficient ?? $subject->coefficient;

                $gradesData[$subject->id] = [
                    'grade_id' => $grade?->id,
                    'control' => $grade?->control_grade,
                    'exam' => $grade?->exam_grade,
                    'average' => $grade?->average,
                    'appreciation' => $grade?->appreciation,
                    'coefficient' => $coef,
                    'is_validated' => $grade?->is_validated ?? false,
                ];

                if ($grade?->average !== null) {
                    $hasGrades = true;
                    $totalWeighted += $grade->average * $coef;
                    $totalCoef += $coef;
                    if (!$grade->is_validated) {
                        $allValidated = false;
                    }
                }
            }

            $average = $totalCoef > 0 ? round($totalWeighted / $totalCoef, 2) : null;

            $rankings[] = [
                'student' => $student,
                'grades' => $gradesData,
                'average' => $average,
                'total_coef' => $totalCoef,
                'all_validated' => $hasGrades ? $allValidated : null,
            ];
        }

        usort($rankings, function ($a, $b) {
            if ($a['average'] === null && $b['average'] === null) return 0;
            if ($a['average'] === null) return 1;
            if ($b['average'] === null) return -1;
            return $b['average'] <=> $a['average'];
        });

        $rank = 0;
        $lastAverage = null;

        foreach ($rankings as $index => &$item) {
            if ($item['average'] === null) {
                $item['rank'] = '-';
            } elseif ($item['average'] === $lastAverage) {
                $item['rank'] = $rank;
            } else {
                $rank = $index + 1;
                $item['rank'] = $rank;
                $lastAverage = $item['average'];
            }
        }

        return $rankings;
    }

    /**
     * Calculate class statistics
     */
    public function getClassStatistics(array $rankings): array
    {
        $averages = array_filter(array_column($rankings, 'average'), fn($v) => $v !== null);

        if (empty($averages)) {
            return [
                'count' => count($rankings),
                'graded' => 0,
                'min' => null,
                'max' => null,
                'average' => null,
                'passed' => 0,
                'failed' => 0,
                'pass_rate' => 0,
            ];
        }

        $config = GradingConfig::instance();
        $passThreshold = $config->pass_threshold ?? 10;

        $passed = count(array_filter($averages, fn($v) => $v >= $passThreshold));
        $failed = count($averages) - $passed;

        return [
            'count' => count($rankings),
            'graded' => count($averages),
            'min' => round(min($averages), 2),
            'max' => round(max($averages), 2),
            'average' => round(array_sum($averages) / count($averages), 2),
            'passed' => $passed,
            'failed' => $failed,
            'pass_rate' => round(($passed / count($averages)) * 100, 1),
        ];
    }

    /**
     * Get subject statistics
     */
    public function getSubjectStatistics(array $rankings, int $subjectId): array
    {
        $grades = [];
        foreach ($rankings as $item) {
            if (isset($item['grades'][$subjectId]['average']) && $item['grades'][$subjectId]['average'] !== null) {
                $grades[] = $item['grades'][$subjectId]['average'];
            }
        }

        if (empty($grades)) {
            return ['min' => null, 'max' => null, 'average' => null];
        }

        return [
            'min' => round(min($grades), 2),
            'max' => round(max($grades), 2),
            'average' => round(array_sum($grades) / count($grades), 2),
        ];
    }

    /**
     * Validate all grades for a student in this trimester
     */
    public function validateStudentGrades(int $studentId): void
    {
        if (!auth()->user()->isAdmin()) {
            $this->dispatch('toast', message: __('Only admins can validate grades.'), type: 'error');
            return;
        }

        Grade::where('student_id', $studentId)
            ->where('trimester_id', $this->selectedTrimesterId)
            ->where('is_validated', false)
            ->update([
                'is_validated' => true,
                'validated_by' => auth()->id(),
                'validated_at' => now(),
            ]);

        $this->dispatch('toast', message: __('Grades validated successfully.'), type: 'success');
    }

    /**
     * Validate all grades for the class
     */
    public function validateAllGrades(): void
    {
        if (!auth()->user()->isAdmin()) {
            $this->dispatch('toast', message: __('Only admins can validate grades.'), type: 'error');
            return;
        }

        Grade::where('class_id', $this->selectedClassId)
            ->where('trimester_id', $this->selectedTrimesterId)
            ->where('is_validated', false)
            ->update([
                'is_validated' => true,
                'validated_by' => auth()->id(),
                'validated_at' => now(),
            ]);

        $this->dispatch('toast', message: __('All grades validated successfully.'), type: 'success');
    }

    /**
     * Show grade history modal
     */
    public function showGradeHistory(int $gradeId): void
    {
        $this->historyGradeId = $gradeId;
        $this->showHistory = true;
    }

    /**
     * Export grades to Excel
     */
    public function exportExcel()
    {
        if (!$this->selectedClassId || !$this->selectedTrimesterId) {
            $this->dispatch('toast', message: __('Please select a class and trimester.'), type: 'error');
            return;
        }

        $class = SchoolClass::find($this->selectedClassId);
        $trimester = Trimester::find($this->selectedTrimesterId);

        $filename = 'notes_' . str_replace(' ', '_', $class->name) . '_' . $trimester->name_fr . '_' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(
            new ClassGradesExport($this->selectedClassId, $this->selectedTrimesterId),
            $filename
        );
    }

    public function render()
    {
        $schoolYear = SchoolYear::where('is_active', true)->first();

        $classes = SchoolClass::when($schoolYear, fn($q) => $q->where('school_year_id', $schoolYear->id))
            ->where('is_active', true)
            ->orderBy('level')
            ->orderBy('name')
            ->get();

        $trimesters = Trimester::when($schoolYear, fn($q) => $q->where('school_year_id', $schoolYear->id))
            ->orderBy('start_date')
            ->get();

        $subjects = collect();
        if ($this->selectedClassId) {
            $subjects = Subject::whereHas('classes', function ($q) {
                $q->where('classes.id', $this->selectedClassId);
            })->orderBy('name_fr')->get();
        }

        $rankings = $this->getStudentRankings();
        $statistics = $this->getClassStatistics($rankings);

        $subjectStats = [];
        foreach ($subjects as $subject) {
            $subjectStats[$subject->id] = $this->getSubjectStatistics($rankings, $subject->id);
        }

        $selectedClass = $this->selectedClassId ? SchoolClass::find($this->selectedClassId) : null;

        $gradeHistory = null;
        if ($this->historyGradeId) {
            $gradeHistory = Grade::with(['histories.user', 'student', 'subject'])
                ->find($this->historyGradeId);
        }

        return view('livewire.admin.class-grades', [
            'classes' => $classes,
            'trimesters' => $trimesters,
            'subjects' => $subjects,
            'rankings' => $rankings,
            'statistics' => $statistics,
            'subjectStats' => $subjectStats,
            'selectedClass' => $selectedClass,
            'gradeHistory' => $gradeHistory,
        ]);
    }
}
