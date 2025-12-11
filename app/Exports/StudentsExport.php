<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected ?int $classId;
    protected ?string $status;
    protected int $schoolYearId;

    public function __construct(int $schoolYearId, ?int $classId = null, ?string $status = null)
    {
        $this->schoolYearId = $schoolYearId;
        $this->classId = $classId;
        $this->status = $status;
    }

    public function query()
    {
        $query = Student::query()
            ->with(['class', 'schoolYear'])
            ->where('school_year_id', $this->schoolYearId)
            ->orderBy('last_name')
            ->orderBy('first_name');

        if ($this->classId) {
            $query->where('class_id', $this->classId);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Matricule',
            'NNI',
            'Nom',
            'Prénom',
            'Nom (AR)',
            'Prénom (AR)',
            'Date naissance',
            'Lieu naissance',
            'Genre',
            'Nationalité',
            'Classe',
            'Tuteur',
            'Téléphone',
            'Email',
            'Statut',
            'Date inscription',
        ];
    }

    public function map($student): array
    {
        return [
            $student->matricule,
            $student->nni ?? '',
            $student->last_name,
            $student->first_name,
            $student->last_name_ar ?? '',
            $student->first_name_ar ?? '',
            $student->birth_date->format('d/m/Y'),
            $student->birth_place ?? '',
            $student->gender === 'male' ? 'Masculin' : 'Féminin',
            $student->nationality ?? '',
            $student->class?->name ?? 'Non assigné',
            $student->guardian_name,
            $student->guardian_phone,
            $student->guardian_email ?? '',
            $this->getStatusLabel($student->status),
            $student->enrollment_date->format('d/m/Y'),
        ];
    }

    protected function getStatusLabel(string $status): string
    {
        return match($status) {
            'active' => 'Actif',
            'inactive' => 'Inactif',
            'transferred' => 'Transféré',
            'graduated' => 'Diplômé',
            default => $status,
        };
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
}
