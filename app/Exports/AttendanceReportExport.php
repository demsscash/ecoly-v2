<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $startDate;
    protected $endDate;
    protected $filterClass;
    protected $schoolYearId;

    public function __construct($startDate, $endDate, $filterClass, $schoolYearId)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->filterClass = $filterClass;
        $this->schoolYearId = $schoolYearId;
    }

    /**
     * Get collection of attendances
     */
    public function collection()
    {
        $query = Attendance::with(['student.class', 'markedBy'])
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->whereHas('student', function($q) {
                $q->where('school_year_id', $this->schoolYearId)
                  ->where('status', 'active');
            });

        if ($this->filterClass) {
            $query->whereHas('student', fn($q) => $q->where('class_id', $this->filterClass));
        }

        return $query->orderBy('date', 'desc')->get();
    }

    /**
     * Map rows
     */
    public function map($attendance): array
    {
        return [
            $attendance->date->format('d/m/Y'),
            $attendance->student->matricule,
            $attendance->student->full_name,
            $attendance->student->class?->name ?? '-',
            $this->getStatusLabel($attendance->status),
            $attendance->justification_note ?? '-',
            $attendance->markedBy?->first_name . ' ' . $attendance->markedBy?->last_name,
        ];
    }

    /**
     * Headings
     */
    public function headings(): array
    {
        return [
            'Date',
            'Matricule',
            'Élève',
            'Classe',
            'Statut',
            'Justification',
            'Marqué par',
        ];
    }

    /**
     * Styles
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    /**
     * Title
     */
    public function title(): string
    {
        return 'Rapport Assiduité';
    }

    /**
     * Get status label
     */
    private function getStatusLabel(string $status): string
    {
        return match($status) {
            'present' => 'Présent',
            'absent' => 'Absent',
            'late' => 'Retard',
            'justified' => 'Justifié',
            'left_early' => 'Parti tôt',
            default => $status,
        };
    }
}
