<?php

namespace App\Exports;

use App\Models\Grade;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Trimester;
use App\Services\GradeCalculationService;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClassGradesExport implements FromArray, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    protected int $classId;
    protected int $trimesterId;
    protected GradeCalculationService $gradeCalc;
    protected array $subjects = [];

    public function __construct(int $classId, int $trimesterId, GradeCalculationService $gradeCalc = null)
    {
        $this->classId = $classId;
        $this->trimesterId = $trimesterId;

        // Use injected service or instantiate
        $this->gradeCalc = $gradeCalc ?? app(GradeCalculationService::class);

        $this->subjects = Subject::whereHas('classes', function ($q) use ($classId) {
            $q->where('classes.id', $classId);
        })->orderBy('name_fr')->get()->toArray();
    }

    public function headings(): array
    {
        $headings = ['Rang', 'Matricule', 'Nom', 'Prénom'];

        foreach ($this->subjects as $subject) {
            $headings[] = $subject['code'] . ' (Ctrl)';
            $headings[] = $subject['code'] . ' (Exam)';
            $headings[] = $subject['code'] . ' (Moy)';
        }

        $headings[] = 'Moyenne Générale';

        return $headings;
    }

    public function array(): array
    {
        $students = Student::where('class_id', $this->classId)
            ->where('status', 'active')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $data = [];

        foreach ($students as $student) {
            $totalWeighted = 0;
            $totalCoef = 0;
            $row = [
                'rank' => '',
                'matricule' => $student->matricule,
                'last_name' => $student->last_name,
                'first_name' => $student->first_name,
            ];

            foreach ($this->subjects as $subject) {
                $grade = Grade::where('student_id', $student->id)
                    ->where('subject_id', $subject['id'])
                    ->where('trimester_id', $this->trimesterId)
                    ->first();

                $coef = $this->gradeCalc->getSubjectCoefficient(
                    Subject::find($subject['id']),
                    $this->classId
                );

                $row[] = $grade?->control_grade ?? '';
                $row[] = $grade?->exam_grade ?? '';
                $row[] = $grade?->average ?? '';

                if ($grade?->average !== null) {
                    $totalWeighted += $grade->average * $coef;
                    $totalCoef += $coef;
                }
            }

            $row['average'] = $totalCoef > 0 ? round($totalWeighted / $totalCoef, 2) : '';
            $data[] = ['row' => $row, 'average' => $row['average']];
        }

        // Sort by average
        usort($data, function ($a, $b) {
            if ($a['average'] === '' && $b['average'] === '') return 0;
            if ($a['average'] === '') return 1;
            if ($b['average'] === '') return -1;
            return $b['average'] <=> $a['average'];
        });

        // Add ranks
        $result = [];
        $rank = 0;
        $lastAvg = null;
        foreach ($data as $index => $item) {
            if ($item['average'] !== '' && $item['average'] !== $lastAvg) {
                $rank = $index + 1;
                $lastAvg = $item['average'];
            } elseif ($item['average'] === '') {
                $rank = '-';
            }
            $item['row']['rank'] = $rank;
            $result[] = array_values($item['row']);
        }

        return $result;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1a365d'],
                ],
            ],
        ];
    }

    public function title(): string
    {
        $class = SchoolClass::find($this->classId);
        $trimester = Trimester::find($this->trimesterId);
        return $class->name . ' - ' . $trimester->name_fr;
    }
}
