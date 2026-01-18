<?php

namespace App\Services;

use App\Models\Grade;
use App\Models\GradingConfig;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Trimester;
use Illuminate\Support\Collection;

class GradeCalculationService
{
    protected ?GradingConfig $config = null;

    public function __construct()
    {
        $this->config = GradingConfig::first();
    }

    /**
     * Calculate the average for a student in a trimester
     */
    public function calculateStudentAverage(Student $student, int $trimesterId): ?float
    {
        $grades = Grade::with('subject')
            ->where('student_id', $student->id)
            ->where('trimester_id', $trimesterId)
            ->get();

        if ($grades->isEmpty()) {
            return null;
        }

        return $this->calculateAverageFromGrades($grades, $student->class_id);
    }

    /**
     * Calculate average from a collection of grades
     */
    public function calculateAverageFromGrades(Collection $grades, int $classId): ?float
    {
        if ($grades->isEmpty()) {
            return null;
        }

        $totalWeighted = 0;
        $totalCoef = 0;

        foreach ($grades as $grade) {
            if ($grade->average !== null) {
                $coef = $this->getSubjectCoefficient($grade->subject, $classId);
                $totalWeighted += $grade->average * $coef;
                $totalCoef += $coef;
            }
        }

        return $totalCoef > 0 ? round($totalWeighted / $totalCoef, 2) : null;
    }

    /**
     * Get coefficient for a subject in a class
     */
    public function getSubjectCoefficient(?Subject $subject, int $classId): int
    {
        if (!$subject) {
            return 1;
        }

        return $subject->classes()
            ->where('classes.id', $classId)
            ->first()?->pivot?->coefficient ?? $subject->coefficient ?? 1;
    }

    /**
     * Get grade base for a subject in a class
     */
    public function getSubjectGradeBase(Subject $subject, int $classId, SchoolClass $class): int
    {
        return $subject->classes()
            ->where('classes.id', $classId)
            ->first()?->pivot?->grade_base
            ?? $subject->classes()
                ->where('classes.id', $classId)
                ->first()?->pivot?->max_grade
            ?? $class->grade_base
            ?? 20;
    }

    /**
     * Calculate rankings for all students in a class for a trimester
     * Returns array with student data, grades, averages, and ranks
     */
    public function calculateClassRankings(SchoolClass $class, Trimester $trimester): array
    {
        $students = Student::where('class_id', $class->id)
            ->where('status', 'active')
            ->get();

        $subjects = Subject::whereHas('classes', function ($q) use ($class) {
            $q->where('classes.id', $class->id);
        })->orderBy('name_fr')->get();

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
                    ->where('trimester_id', $trimester->id)
                    ->first();

                $coef = $this->getSubjectCoefficient($subject, $class->id);

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

        // Sort by average (descending)
        usort($rankings, function ($a, $b) {
            if ($a['average'] === null && $b['average'] === null) return 0;
            if ($a['average'] === null) return 1;
            if ($b['average'] === null) return -1;
            return $b['average'] <=> $a['average'];
        });

        // Assign ranks
        $rank = 0;
        $lastAverage = null;

        foreach ($rankings as &$item) {
            if ($item['average'] === null) {
                $item['rank'] = '-';
            } elseif ($item['average'] === $lastAverage) {
                $item['rank'] = $rank;
            } else {
                $rank = count(array_filter($rankings, fn($r) =>
                    $r['average'] !== null && $r['average'] > $item['average']
                )) + 1;
                $item['rank'] = $rank;
                $lastAverage = $item['average'];
            }
        }

        return [
            'rankings' => $rankings,
            'subjects' => $subjects,
        ];
    }

    /**
     * Get student rank in class for a trimester
     */
    public function getStudentRank(Student $student, Trimester $trimester): array
    {
        if (!$student->class_id) {
            return ['rank' => null, 'total' => 0];
        }

        $classmates = Student::where('class_id', $student->class_id)
            ->where('status', 'active')
            ->get();

        $averages = [];

        foreach ($classmates as $classmate) {
            $averages[$classmate->id] = $this->calculateStudentAverage($classmate, $trimester->id);
        }

        $validAverages = array_filter($averages, fn($v) => $v !== null);
        arsort($validAverages);

        $rank = 1;
        $lastAvg = null;
        $studentRank = null;

        foreach ($validAverages as $studentId => $avg) {
            if ($avg !== $lastAvg) {
                $rank = array_search($studentId, array_keys($validAverages)) + 1;
                $lastAvg = $avg;
            }
            if ($studentId === $student->id) {
                $studentRank = $rank;
                break;
            }
        }

        return [
            'rank' => $studentRank,
            'total' => count($validAverages),
        ];
    }

    /**
     * Get annual average for a student
     */
    public function calculateAnnualAverage(Student $student): ?float
    {
        $trimesters = Trimester::where('school_year_id', $student->school_year_id)->get();

        $trimesterAverages = [];

        foreach ($trimesters as $trimester) {
            $avg = $this->calculateStudentAverage($student, $trimester->id);
            if ($avg !== null) {
                $trimesterAverages[] = $avg;
            }
        }

        return !empty($trimesterAverages)
            ? round(array_sum($trimesterAverages) / count($trimesterAverages), 2)
            : null;
    }

    /**
     * Get student annual rank
     */
    public function getAnnualRank(Student $student): array
    {
        if (!$student->class_id) {
            return ['rank' => null, 'total' => 0];
        }

        $classmates = Student::where('class_id', $student->class_id)
            ->where('status', 'active')
            ->get();

        $trimesters = Trimester::where('school_year_id', $student->school_year_id)->get();

        $annualAverages = [];

        foreach ($classmates as $classmate) {
            $trimesterAvgs = [];

            foreach ($trimesters as $trimester) {
                $avg = $this->calculateStudentAverage($classmate, $trimester->id);
                if ($avg !== null) {
                    $trimesterAvgs[] = $avg;
                }
            }

            $annualAverages[$classmate->id] = !empty($trimesterAvgs)
                ? round(array_sum($trimesterAvgs) / count($trimesterAvgs), 2)
                : null;
        }

        $validAverages = array_filter($annualAverages, fn($v) => $v !== null);
        arsort($validAverages);

        $rank = 1;
        $lastAvg = null;
        $studentRank = null;

        foreach ($validAverages as $studentId => $avg) {
            if ($avg !== $lastAvg) {
                $rank = array_search($studentId, array_keys($validAverages)) + 1;
                $lastAvg = $avg;
            }
            if ($studentId === $student->id) {
                $studentRank = $rank;
                break;
            }
        }

        return [
            'rank' => $studentRank,
            'total' => count($validAverages),
        ];
    }

    /**
     * Get appreciation based on average
     */
    public function getAppreciation(?float $average): ?string
    {
        if ($average === null) {
            return null;
        }

        // Fallback thresholds if config is not set
        $excellent = $this->config?->mention_excellent ?? 16;
        $veryGood = $this->config?->mention_very_good ?? 14;
        $good = $this->config?->mention_good ?? 12;
        $fairlyGood = $this->config?->mention_fairly_good ?? 10;
        $pass = $this->config?->passing_grade ?? 10;

        if ($average >= $excellent) return 'Excellent';
        if ($average >= $veryGood) return 'Très Bien';
        if ($average >= $good) return 'Bien';
        if ($average >= $fairlyGood) return 'Assez Bien';
        if ($average >= $pass) return 'Passable';

        return 'Insuffisant';
    }

    /**
     * Get mention (same as appreciation, kept for backward compatibility)
     */
    public function getMention(?float $average): ?string
    {
        return $this->getAppreciation($average);
    }

    /**
     * Get grades for a student in a trimester, keyed by subject_id
     */
    public function getStudentGradesBySubject(Student $student, int $trimesterId): Collection
    {
        return Grade::where('student_id', $student->id)
            ->where('trimester_id', $trimesterId)
            ->get()
            ->keyBy('subject_id');
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

        $passThreshold = $this->config?->passing_grade ?? 10;
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
            'pass_rate' => count($averages) > 0 ? round(($passed / count($averages)) * 100, 1) : 0,
        ];
    }

    /**
     * Get subject statistics for a class
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
     * Get trimester averages for a student
     */
    public function getTrimesterAverages(Student $student): array
    {
        $trimesters = Trimester::where('school_year_id', $student->school_year_id)
            ->orderBy('start_date')
            ->get();

        $averages = [];
        foreach ($trimesters as $trimester) {
            $averages[$trimester->id] = [
                'name' => $trimester->name,
                'average' => $this->calculateStudentAverage($student, $trimester->id),
            ];
        }

        return $averages;
    }

    /**
     * Get appreciation based on average with grade base normalization
     * Useful for grade entry where grades may not be on /20 scale
     */
    public function getAppreciationWithGradeBase(?float $average, int $gradeBase = 20): ?string
    {
        if ($average === null) {
            return null;
        }

        // Normalize to /20 scale for appreciation calculation
        $normalized = ($average / $gradeBase) * 20;

        return $this->getAppreciation($normalized);
    }
}
