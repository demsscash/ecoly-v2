<?php

namespace App\Livewire\Admin;

use App\Models\Student;
use App\Models\Payment;
use App\Models\Grade;
use App\Models\Trimester;
use App\Models\SchoolSetting;
use App\Models\GradingConfig;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Barryvdh\DomPDF\Facade\Pdf;
use Mpdf\Mpdf;

#[Layout('layouts.app')]
class StudentShow extends Component
{
    public Student $student;
    public ?int $selectedTrimesterId = null;
    public string $activeTab = 'grades';

    public function mount(Student $student): void
    {
        $this->student = $student->load(['class', 'schoolYear']);
        
        $currentTrimester = Trimester::where('school_year_id', $student->school_year_id)
            ->where('status', 'open')
            ->first();
        
        $this->selectedTrimesterId = $currentTrimester?->id 
            ?? Trimester::where('school_year_id', $student->school_year_id)->first()?->id;
    }

    public function getTitle(): string
    {
        return $this->student->full_name . ' - Ecoly';
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    /**
     * Get student payments summary
     */
    public function getPaymentsSummary(): array
    {
        $payments = Payment::where('student_id', $this->student->id)
            ->where('school_year_id', $this->student->school_year_id)
            ->get();

        $totalDue = $payments->sum('amount');
        $totalPaid = $payments->sum('amount_paid');
        $balance = $totalDue - $totalPaid;

        return [
            'total_due' => $totalDue,
            'total_paid' => $totalPaid,
            'balance' => $balance,
            'status' => $balance <= 0 ? 'paid' : ($totalPaid > 0 ? 'partial' : 'pending'),
        ];
    }

    /**
     * Download financial statement PDF
     */
    public function downloadFinancialStatement()
    {
        $payments = Payment::where('student_id', $this->student->id)
            ->where('school_year_id', $this->student->school_year_id)
            ->orderBy('type')
            ->orderBy('month')
            ->get();

        $summary = $this->getPaymentsSummary();
        $school = SchoolSetting::first();

        $data = [
            'school' => [
                'name' => $school?->name_fr ?? 'École',
                'name_ar' => $school?->name_ar ?? '',
                'address' => $school?->address_fr ?? '',
                'phone' => $school?->phone ?? '',
                'logo' => $school?->logo_path ?? null,
            ],
            'student' => [
                'full_name' => $this->student->full_name,
                'matricule' => $this->student->matricule,
                'class' => $this->student->class?->name ?? '-',
                'school_year' => $this->student->schoolYear?->name ?? '-',
                'guardian_name' => $this->student->guardian_name,
                'guardian_phone' => $this->student->guardian_phone,
            ],
            'payments' => $payments,
            'summary' => $summary,
            'generated_at' => now()->format('d/m/Y H:i'),
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

        $html = view('pdf.financial-statement', $data)->render();
        $mpdf->WriteHTML($html);
        
        $pdfContent = $mpdf->Output('', 'S');
        $filename = 'situation_financiere_' . $this->student->matricule . '.pdf';

        return response()->streamDownload(function () use ($pdfContent) {
            echo $pdfContent;
        }, $filename, ['Content-Type' => 'application/pdf']);
    }

    /**
     * Calculate trimester average for a student
     */
    protected function calculateTrimesterAverage(int $trimesterId): ?float
    {
        $grades = Grade::with('subject')
            ->where('student_id', $this->student->id)
            ->where('trimester_id', $trimesterId)
            ->get();

        if ($grades->isEmpty()) {
            return null;
        }

        $totalWeighted = 0;
        $totalCoef = 0;

        foreach ($grades as $grade) {
            if ($grade->average !== null) {
                $coef = $grade->subject->classes()
                    ->where('classes.id', $this->student->class_id)
                    ->first()?->pivot?->coefficient ?? $grade->subject->coefficient;
                $totalWeighted += $grade->average * $coef;
                $totalCoef += $coef;
            }
        }

        return $totalCoef > 0 ? round($totalWeighted / $totalCoef, 2) : null;
    }

    /**
     * Get mention based on average
     */
    protected function getMention(?float $average): ?string
    {
        if ($average === null) return null;

        $config = GradingConfig::first();
        if (!$config) {
            if ($average >= 16) return 'Très Bien';
            if ($average >= 14) return 'Bien';
            if ($average >= 12) return 'Assez Bien';
            if ($average >= 10) return 'Passable';
            return 'Insuffisant';
        }

        if ($average >= $config->mention_excellent) return 'Excellent';
        if ($average >= $config->mention_tres_bien) return 'Très Bien';
        if ($average >= $config->mention_bien) return 'Bien';
        if ($average >= $config->mention_assez_bien) return 'Assez Bien';
        if ($average >= $config->mention_passable) return 'Passable';
        return 'Insuffisant';
    }

    /**
     * Get student rank in class for a trimester
     */
    public function getStudentRankForTrimester(?int $trimesterId): array
    {
        if (!$trimesterId || !$this->student->class_id) {
            return ['rank' => null, 'total' => 0];
        }

        $classmates = Student::where('class_id', $this->student->class_id)
            ->where('status', 'active')
            ->get();

        $averages = [];

        foreach ($classmates as $classmate) {
            $grades = Grade::with('subject')
                ->where('student_id', $classmate->id)
                ->where('trimester_id', $trimesterId)
                ->get();

            $totalWeighted = 0;
            $totalCoef = 0;

            foreach ($grades as $grade) {
                if ($grade->average !== null) {
                    $coef = $grade->subject->classes()
                        ->where('classes.id', $this->student->class_id)
                        ->first()?->pivot?->coefficient ?? $grade->subject->coefficient;
                    $totalWeighted += $grade->average * $coef;
                    $totalCoef += $coef;
                }
            }

            $averages[$classmate->id] = $totalCoef > 0 ? round($totalWeighted / $totalCoef, 2) : null;
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
            if ($studentId === $this->student->id) {
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
     * Get trimester averages for display
     */
    public function getTrimesterAverages(): array
    {
        $trimesters = Trimester::where('school_year_id', $this->student->school_year_id)
            ->orderBy('start_date')
            ->get();

        $averages = [];
        foreach ($trimesters as $trimester) {
            $averages[$trimester->id] = [
                'name' => $trimester->name,
                'average' => $this->calculateTrimesterAverage($trimester->id),
            ];
        }

        return $averages;
    }

    /**
     * Get annual average
     */
    public function getAnnualAverage(): ?float
    {
        $trimesterAverages = $this->getTrimesterAverages();
        $validAverages = array_filter(
            array_column($trimesterAverages, 'average'),
            fn($v) => $v !== null
        );

        return !empty($validAverages) ? round(array_sum($validAverages) / count($validAverages), 2) : null;
    }

    /**
     * Get student annual rank
     */
    public function getAnnualRank(): array
    {
        if (!$this->student->class_id) {
            return ['rank' => null, 'total' => 0];
        }

        $classmates = Student::where('class_id', $this->student->class_id)
            ->where('status', 'active')
            ->get();

        $trimesters = Trimester::where('school_year_id', $this->student->school_year_id)->get();

        $annualAverages = [];

        foreach ($classmates as $classmate) {
            $trimesterAvgs = [];

            foreach ($trimesters as $trimester) {
                $grades = Grade::with('subject')
                    ->where('student_id', $classmate->id)
                    ->where('trimester_id', $trimester->id)
                    ->get();

                $totalWeighted = 0;
                $totalCoef = 0;

                foreach ($grades as $grade) {
                    if ($grade->average !== null) {
                        $coef = $grade->subject->classes()
                            ->where('classes.id', $this->student->class_id)
                            ->first()?->pivot?->coefficient ?? $grade->subject->coefficient;
                        $totalWeighted += $grade->average * $coef;
                        $totalCoef += $coef;
                    }
                }

                if ($totalCoef > 0) {
                    $trimesterAvgs[] = round($totalWeighted / $totalCoef, 2);
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
            if ($studentId === $this->student->id) {
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
     * Download attestation PDF
     */
    public function downloadAttestation()
    {
        $school = SchoolSetting::first();
        
        $pdf = Pdf::loadView('pdf.attestation', [
            'student' => $this->student,
            'school' => $school,
            'date' => now(),
        ]);
        
        $pdf->setPaper('A4', 'portrait');
        
        return response()->streamDownload(
            fn() => print($pdf->output()),
            'attestation_' . $this->student->matricule . '.pdf'
        );
    }

    public function render()
    {
        $trimesters = Trimester::where('school_year_id', $this->student->school_year_id)
            ->orderBy('start_date')
            ->get();

        $grades = collect();
        $trimesterAverage = null;
        
        if ($this->selectedTrimesterId) {
            $grades = Grade::with('subject')
                ->where('student_id', $this->student->id)
                ->where('trimester_id', $this->selectedTrimesterId)
                ->get()
                ->keyBy('subject_id');

            $trimesterAverage = $this->calculateTrimesterAverage($this->selectedTrimesterId);
        }

        $subjects = $this->student->class?->subjects()
            ->orderBy('name_fr')
            ->get() ?? collect();

        $rankInfo = $this->getStudentRankForTrimester($this->selectedTrimesterId);
        $mention = $this->getMention($trimesterAverage);
        
        $trimesterAverages = $this->getTrimesterAverages();
        $annualAverage = $this->getAnnualAverage();
        $annualRank = $this->getAnnualRank();
        $annualMention = $this->getMention($annualAverage);

        // Payments data
        $payments = Payment::where('student_id', $this->student->id)
            ->where('school_year_id', $this->student->school_year_id)
            ->orderBy('type')
            ->orderBy('month')
            ->get();
        $paymentsSummary = $this->getPaymentsSummary();

        return view('livewire.admin.student-show', [
            'trimesters' => $trimesters,
            'grades' => $grades,
            'subjects' => $subjects,
            'trimesterAverage' => $trimesterAverage,
            'rankInfo' => $rankInfo,
            'mention' => $mention,
            'trimesterAverages' => $trimesterAverages,
            'annualAverage' => $annualAverage,
            'annualRank' => $annualRank,
            'annualMention' => $annualMention,
            'payments' => $payments,
            'paymentsSummary' => $paymentsSummary,
        ]);
    }
}
