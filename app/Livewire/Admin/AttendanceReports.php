<?php

namespace App\Livewire\Admin;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\SchoolYear;
use App\Models\SchoolSetting;
use App\Exports\AttendanceReportExport;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;

#[Layout('layouts.app')]
#[Title('Rapports d\'assiduité - Ecoly')]
class AttendanceReports extends Component
{
    public string $startDate;
    public string $endDate;
    public string $filterClass = '';
    public ?int $filterStudent = null;

    public function mount(): void
    {
        // Default: current month
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function exportPdf(): mixed
    {
        $schoolYear = SchoolYear::where('is_active', true)->first();
        $school = SchoolSetting::first();
        $stats = $this->getStatistics($schoolYear);
        
        // Get detailed records (only absences, late, left_early)
        $attendances = Attendance::with(['student.class'])
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->whereHas('student', function($q) use ($schoolYear) {
                $q->where('school_year_id', $schoolYear->id)
                  ->where('status', 'active');
            })
            ->whereIn('status', ['absent', 'late', 'left_early', 'justified'])
            ->when($this->filterClass, function($q) {
                $q->whereHas('student', fn($sq) => $sq->where('class_id', $this->filterClass));
            })
            ->orderBy('date', 'desc')
            ->limit(100)
            ->get();

        $className = $this->filterClass ? SchoolClass::find($this->filterClass)?->name : null;

        $data = [
            'school' => [
                'name' => $school?->name_fr ?? 'École',
            ],
            'period' => [
                'start' => Carbon::parse($this->startDate)->format('d/m/Y'),
                'end' => Carbon::parse($this->endDate)->format('d/m/Y'),
                'class' => $className,
            ],
            'stats' => $stats,
            'attendances' => $attendances,
        ];

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 15,
            'margin_bottom' => 15,
        ]);

        $html = view('pdf.attendance-report', $data)->render();
        $mpdf->WriteHTML($html);
        
        $filename = 'rapport_assiduite_' . $this->startDate . '_' . $this->endDate . '.pdf';
        
        return response()->streamDownload(function () use ($mpdf) {
            echo $mpdf->Output('', 'S');
        }, $filename, ['Content-Type' => 'application/pdf']);
    }

    public function exportExcel(): mixed
    {
        $schoolYear = SchoolYear::where('is_active', true)->first();
        $filename = 'rapport_assiduite_' . $this->startDate . '_' . $this->endDate . '.xlsx';
        
        return Excel::download(
            new AttendanceReportExport(
                $this->startDate,
                $this->endDate,
                $this->filterClass,
                $schoolYear->id
            ),
            $filename
        );
    }

    public function render()
    {
        $schoolYear = SchoolYear::where('is_active', true)->first();
        
        // Get statistics
        $stats = $this->getStatistics($schoolYear);
        
        $classes = SchoolClass::orderBy('level')->orderBy('section')->get();

        return view('livewire.admin.attendance-reports', [
            'stats' => $stats,
            'classes' => $classes,
        ]);
    }

    private function getStatistics(?SchoolYear $schoolYear): array
    {
        if (!$schoolYear) {
            return [
                'total_days' => 0,
                'total_attendances' => 0,
                'present_count' => 0,
                'absent_count' => 0,
                'late_count' => 0,
                'justified_count' => 0,
                'left_early_count' => 0,
                'attendance_rate' => 0,
                'by_class' => [],
            ];
        }

        $query = Attendance::whereBetween('date', [$this->startDate, $this->endDate])
            ->whereHas('student', function($q) use ($schoolYear) {
                $q->where('school_year_id', $schoolYear->id)
                  ->where('status', 'active');
            });

        if ($this->filterClass) {
            $query->whereHas('student', fn($q) => $q->where('class_id', $this->filterClass));
        }

        if ($this->filterStudent) {
            $query->where('student_id', $this->filterStudent);
        }

        $attendances = $query->get();

        $totalAttendances = $attendances->count();
        $presentCount = $attendances->where('status', 'present')->count();
        $attendanceRate = $totalAttendances > 0 ? ($presentCount / $totalAttendances) * 100 : 0;

        // Stats by class
        $byClass = [];
        if (!$this->filterClass) {
            $classes = SchoolClass::all();
            foreach ($classes as $class) {
                $classAttendances = $attendances->filter(fn($a) => $a->student->class_id === $class->id);
                $classTotal = $classAttendances->count();
                $classPresent = $classAttendances->where('status', 'present')->count();
                
                if ($classTotal > 0) {
                    $byClass[] = [
                        'class' => $class,
                        'total' => $classTotal,
                        'present' => $classPresent,
                        'rate' => ($classPresent / $classTotal) * 100,
                    ];
                }
            }
        }

        return [
            'total_days' => Carbon::parse($this->startDate)->diffInDays($this->endDate) + 1,
            'total_attendances' => $totalAttendances,
            'present_count' => $presentCount,
            'absent_count' => $attendances->where('status', 'absent')->count(),
            'late_count' => $attendances->where('status', 'late')->count(),
            'justified_count' => $attendances->where('status', 'justified')->count(),
            'left_early_count' => $attendances->where('status', 'left_early')->count(),
            'attendance_rate' => round($attendanceRate, 1),
            'by_class' => $byClass,
        ];
    }
}
