<?php

namespace App\Services;

use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\Trimester;

class BulletinService
{
    protected GradeCalculationService $gradeCalc;

    public function __construct(GradeCalculationService $gradeCalc)
    {
        $this->gradeCalc = $gradeCalc;
    }

    /**
     * Get bulletin data for a student and trimester
     */
    public function getBulletinData(Student $student, Trimester $trimester): array
    {
        $school = SchoolSetting::instance();

        $subjects = $student->class->subjects()
            ->orderBy('name_fr')
            ->get();

        $grades = $this->gradeCalc->getStudentGradesBySubject($student, $trimester->id);

        $subjectsData = [];

        foreach ($subjects as $subject) {
            $grade = $grades->get($subject->id);
            $gradeBase = $this->gradeCalc->getSubjectGradeBase($subject, $student->class_id, $student->class);
            $coefficient = $this->gradeCalc->getSubjectCoefficient($subject, $student->class_id);

            $subjectsData[] = [
                'name' => $subject->name_fr,
                'name_ar' => $subject->name_ar,
                'code' => $subject->code,
                'grade_base' => $gradeBase,
                'coefficient' => $coefficient,
                'control' => $grade?->control_grade,
                'exam' => $grade?->exam_grade,
                'average' => $grade?->average,
                'appreciation' => $grade?->appreciation,
            ];
        }

        // Use GradeCalculationService for proper average calculation (with coefficients for college/lycee)
        $trimesterAverage = $this->gradeCalc->calculateStudentAverage($student, $trimester->id);

        // Get rank
        $rankInfo = $this->gradeCalc->getStudentRank($student, $trimester);

        // Get mention
        $mention = $this->gradeCalc->getMention($trimesterAverage);

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
}
