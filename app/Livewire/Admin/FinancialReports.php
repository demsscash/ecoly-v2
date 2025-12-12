<?php

namespace App\Livewire\Admin;

use App\Models\Payment;
use App\Models\SchoolYear;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\SchoolSetting;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;

#[Layout('layouts.app')]
#[Title('Rapports Financiers - Ecoly')]
class FinancialReports extends Component
{
    public ?int $selectedYearId = null;
    public ?int $selectedClassId = null;
    public string $selectedMonth = '';
    public string $reportType = 'unpaid'; // unpaid, monthly, by_class

    public function mount(): void
    {
        $activeYear = SchoolYear::where('is_active', true)->first();
        $this->selectedYearId = $activeYear?->id ?? SchoolYear::latest()->first()?->id;
    }

    /**
     * Get unpaid payments report
     */
    public function getUnpaidReport(): array
    {
        if (!$this->selectedYearId) {
            return [];
        }

        $students = Student::where('school_year_id', $this->selectedYearId)
            ->where('status', 'active')
            ->when($this->selectedClassId, fn($q) => $q->where('class_id', $this->selectedClassId))
            ->with(['class', 'payments' => function($q) {
                $q->where('school_year_id', $this->selectedYearId);
            }])
            ->get();

        $report = [];
        foreach ($students as $student) {
            $totalDue = $student->payments->sum('amount');
            $totalPaid = $student->payments->sum('amount_paid');
            $balance = $totalDue - $totalPaid;

            if ($balance > 0) {
                $report[] = [
                    'student' => $student,
                    'total_due' => $totalDue,
                    'total_paid' => $totalPaid,
                    'balance' => $balance,
                    'status' => $totalPaid > 0 ? 'partial' : 'pending',
                ];
            }
        }

        // Sort by balance descending
        usort($report, fn($a, $b) => $b['balance'] <=> $a['balance']);

        return $report;
    }

    /**
     * Get monthly collection report
     */
    public function getMonthlyReport(): array
    {
        if (!$this->selectedYearId) {
            return [];
        }

        $query = Payment::where('school_year_id', $this->selectedYearId)
            ->when($this->selectedClassId, function($q) {
                $q->whereHas('student', fn($sq) => $sq->where('class_id', $this->selectedClassId));
            });

        if ($this->selectedMonth) {
            $query->where('month', $this->selectedMonth);
        }

        $payments = $query->with(['student.class'])
            ->where('amount_paid', '>', 0)
            ->orderBy('paid_date', 'desc')
            ->get();

        // Group by month
        $monthlyData = $payments->groupBy(function($payment) {
            return $payment->paid_date ? $payment->paid_date->format('Y-m') : 'unknown';
        })->map(function($monthPayments) {
            return [
                'count' => $monthPayments->count(),
                'total_collected' => $monthPayments->sum('amount_paid'),
                'payments' => $monthPayments,
            ];
        });

        return $monthlyData->toArray();
    }

    /**
     * Get report by class
     */
    public function getByClassReport(): array
    {
        if (!$this->selectedYearId) {
            return [];
        }

        $classes = SchoolClass::where('school_year_id', $this->selectedYearId)
            ->where('is_active', true)
            ->with(['students' => function($q) {
                $q->where('status', 'active')
                    ->with(['payments' => function($pq) {
                        $pq->where('school_year_id', $this->selectedYearId);
                    }]);
            }])
            ->orderBy('level')
            ->orderBy('section')
            ->get();

        $report = [];
        foreach ($classes as $class) {
            $totalDue = 0;
            $totalPaid = 0;
            $studentsCount = $class->students->count();
            $paidCount = 0;
            $partialCount = 0;
            $pendingCount = 0;

            foreach ($class->students as $student) {
                $due = $student->payments->sum('amount');
                $paid = $student->payments->sum('amount_paid');
                $balance = $due - $paid;

                $totalDue += $due;
                $totalPaid += $paid;

                if ($balance <= 0) {
                    $paidCount++;
                } elseif ($paid > 0) {
                    $partialCount++;
                } else {
                    $pendingCount++;
                }
            }

            $report[] = [
                'class' => $class,
                'students_count' => $studentsCount,
                'total_due' => $totalDue,
                'total_paid' => $totalPaid,
                'balance' => $totalDue - $totalPaid,
                'paid_count' => $paidCount,
                'partial_count' => $partialCount,
                'pending_count' => $pendingCount,
                'collection_rate' => $totalDue > 0 ? ($totalPaid / $totalDue) * 100 : 0,
            ];
        }

        return $report;
    }

    /**
     * Download unpaid report as PDF
     */
    public function downloadUnpaidPdf()
    {
        $report = $this->getUnpaidReport();
        $school = SchoolSetting::first();
        $year = SchoolYear::find($this->selectedYearId);
        $class = $this->selectedClassId ? SchoolClass::find($this->selectedClassId) : null;

        $totalDue = collect($report)->sum('total_due');
        $totalPaid = collect($report)->sum('total_paid');
        $totalBalance = collect($report)->sum('balance');

        $html = view('pdf.unpaid-report', [
            'school' => $school,
            'year' => $year,
            'class' => $class,
            'report' => $report,
            'totalDue' => $totalDue,
            'totalPaid' => $totalPaid,
            'totalBalance' => $totalBalance,
        ])->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
        ]);

        $mpdf->WriteHTML($html);
        
        $filename = 'rapport-impayes-' . date('Y-m-d') . '.pdf';
        return response()->streamDownload(function() use ($mpdf) {
            echo $mpdf->Output('', 'S');
        }, $filename);
    }

    /**
     * Download class report as PDF
     */
    public function downloadClassReportPdf()
    {
        $report = $this->getByClassReport();
        $school = SchoolSetting::first();
        $year = SchoolYear::find($this->selectedYearId);

        $totalDue = collect($report)->sum('total_due');
        $totalPaid = collect($report)->sum('total_paid');
        $totalBalance = collect($report)->sum('balance');

        $html = view('pdf.class-financial-report', [
            'school' => $school,
            'year' => $year,
            'report' => $report,
            'totalDue' => $totalDue,
            'totalPaid' => $totalPaid,
            'totalBalance' => $totalBalance,
        ])->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L', // Landscape
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
        ]);

        $mpdf->WriteHTML($html);
        
        $filename = 'rapport-classes-' . date('Y-m-d') . '.pdf';
        return response()->streamDownload(function() use ($mpdf) {
            echo $mpdf->Output('', 'S');
        }, $filename);
    }

    public function render()
    {
        $years = SchoolYear::orderByDesc('start_date')->get();
        
        $classes = $this->selectedYearId
            ? SchoolClass::where('school_year_id', $this->selectedYearId)
                ->where('is_active', true)
                ->orderBy('level')
                ->orderBy('section')
                ->get()
            : collect();

        $data = match($this->reportType) {
            'unpaid' => ['report' => $this->getUnpaidReport()],
            'monthly' => ['monthlyData' => $this->getMonthlyReport()],
            'by_class' => ['classReport' => $this->getByClassReport()],
            default => [],
        };

        return view('livewire.admin.financial-reports', array_merge([
            'years' => $years,
            'classes' => $classes,
        ], $data));
    }
}
