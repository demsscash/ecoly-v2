<?php

namespace App\Services;

use App\Models\Grade;
use App\Models\GradingConfig;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\Trimester;

class BulletinService
{
    /**
     * Get bulletin data for a student and trimester
     */
    public function getBulletinData(Student $student, Trimester $trimester): array
    {
        $school = SchoolSetting::instance();
        $config = GradingConfig::instance();
        
        $subjects = $student->class->subjects()
            ->orderBy('name_fr')
            ->get();

        $grades = Grade::where('student_id', $student->id)
            ->where('trimester_id', $trimester->id)
            ->get()
            ->keyBy('subject_id');

        $subjectsData = [];
        $totalPoints = 0;
        $totalBase = 0;

        foreach ($subjects as $subject) {
            $grade = $grades->get($subject->id);
            $gradeBase = $subject->pivot->grade_base ?? $student->class->grade_base ?? 20;
            
            $subjectsData[] = [
                'name' => $subject->name_fr,
                'name_ar' => $subject->name_ar,
                'code' => $subject->code,
                'grade_base' => $gradeBase,
                'control' => $grade?->control_grade,
                'exam' => $grade?->exam_grade,
                'average' => $grade?->average,
                'appreciation' => $grade?->appreciation,
            ];

            if ($grade?->average !== null) {
                // Normalize to 20 for total calculation
                $normalized = ($grade->average / $gradeBase) * 20;
                $totalPoints += $normalized;
                $totalBase += 20;
            }
        }

        // Calculate trimester average (normalized to 20)
        $trimesterAverage = $totalBase > 0 ? round(($totalPoints / $totalBase) * 20, 2) : null;

        // Get rank
        $rankInfo = $this->getStudentRank($student, $trimester);

        // Get mention
        $mention = $this->getMention($trimesterAverage, $config);

        return [
            'school' => [
                'name' => $school->name ?? 'École Aboubacar Fall',
                'name_ar' => $school->name_ar ?? 'مدرسة أبو بكر فال',
                'address' => $school->address,
                'phone' => $school->phone,
                'email' => $school->email,
                'logo' => $school->logo_path,
            ],
            'student' => [
                'full_name' => $student->full_name,
                'full_name_ar' => $student->full_name_ar,
                'matricule' => $student->matricule,
                'birth_date' => $student->birth_date->format('d/m/Y'),
                'birth_place' => $student->birth_place,
                'photo' => $student->photo_path,
                'class' => $student->class->name,
                'school_year' => $student->schoolYear->name,
            ],
            'trimester' => [
                'name' => $trimester->name_fr,
                'name_ar' => $trimester->name_ar,
            ],
            'subjects' => $subjectsData,
            'summary' => [
                'average' => $trimesterAverage,
                'rank' => $rankInfo['rank'],
                'total_students' => $rankInfo['total'],
                'mention' => $mention,
            ],
            'generated_at' => now()->format('d/m/Y H:i'),
        ];
    }

    /**
     * Get student rank in class for trimester
     */
    protected function getStudentRank(Student $student, Trimester $trimester): array
    {
        $classmates = Student::where('class_id', $student->class_id)
            ->where('status', 'active')
            ->get();

        $averages = [];

        foreach ($classmates as $classmate) {
            $grades = Grade::with('subject')
                ->where('student_id', $classmate->id)
                ->where('trimester_id', $trimester->id)
                ->get();

            $totalPoints = 0;
            $totalBase = 0;

            foreach ($grades as $grade) {
                if ($grade->average !== null) {
                    $gradeBase = $grade->subject->classes()
                        ->where('classes.id', $student->class_id)
                        ->first()?->pivot?->grade_base ?? 20;
                    
                    $normalized = ($grade->average / $gradeBase) * 20;
                    $totalPoints += $normalized;
                    $totalBase += 20;
                }
            }

            $averages[$classmate->id] = $totalBase > 0 ? round(($totalPoints / $totalBase) * 20, 2) : null;
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
     * Get mention based on average
     */
    protected function getMention(?float $average, GradingConfig $config): ?string
    {
        if ($average === null) return null;

        if ($average >= $config->excellent_threshold) return 'Excellent';
        if ($average >= $config->very_good_threshold) return 'Très Bien';
        if ($average >= $config->good_threshold) return 'Bien';
        if ($average >= $config->fairly_good_threshold) return 'Assez Bien';
        if ($average >= $config->pass_threshold) return 'Passable';
        
        return 'Insuffisant';
    }
}
