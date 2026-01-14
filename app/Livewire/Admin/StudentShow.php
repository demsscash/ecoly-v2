<?php

namespace App\Livewire\Admin;

use App\Models\Student;
use App\Models\Payment;
use App\Models\Grade;
use App\Models\Trimester;
use App\Models\SchoolSetting;
use App\Services\GradeCalculationService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Mpdf\Mpdf;

#[Layout('layouts.app')]
class StudentShow extends Component
{
    public Student $student;
    public ?int $selectedTrimesterId = null;
    public string $activeTab = 'grades';

    protected GradeCalculationService $gradeCalc;

    public function boot(GradeCalculationService $gradeCalc): void
    {
        $this->gradeCalc = $gradeCalc;
    }

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
                'address_fr' => $school?->address_fr ?? '',
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
     * Get student rank in class for a trimester
     */
    public function getStudentRankForTrimester(?int $trimesterId): array
    {
        if (!$trimesterId) {
            return ['rank' => null, 'total' => 0];
        }

        $trimester = Trimester::find($trimesterId);
        return $this->gradeCalc->getStudentRank($this->student, $trimester);
    }

    /**
     * Get trimester averages for display
     */
    public function getTrimesterAverages(): array
    {
        return $this->gradeCalc->getTrimesterAverages($this->student);
    }

    /**
     * Get annual average
     */
    public function getAnnualAverage(): ?float
    {
        return $this->gradeCalc->calculateAnnualAverage($this->student);
    }

    /**
     * Get student annual rank
     */
    public function getAnnualRank(): array
    {
        return $this->gradeCalc->getAnnualRank($this->student);
    }

    /**
     * Download attestation PDF
     */
    public function downloadAttestation($lang = 'fr')
    {
        $school = SchoolSetting::first();

        $data = [
            'school' => [
                'name_fr' => $school?->name_fr ?? 'École',
                'name_ar' => $school?->name_ar ?? 'مدرسة',
                'address_fr' => $school?->address_fr ?? '',
                'address_ar' => $school?->address_ar ?? '',
                'phone' => $school?->phone ?? '',
                'email' => $school?->email ?? '',
                'director_name_fr' => $school?->director_name_fr ?? '',
                'director_name_ar' => $school?->director_name_ar ?? '',
                'logo_path' => $school?->logo_path ?? null,
                'signature_path' => $school?->signature_path ?? null,
                'stamp_path' => $school?->stamp_path ?? null,
            ],
            'student' => [
                'id' => $this->student->id,
                'full_name' => $this->student->full_name,
                'full_name_ar' => $this->student->full_name_ar,
                'matricule' => $this->student->matricule,
                'nni' => $this->student->nni,
                'birth_date' => $this->student->birth_date->format('d/m/Y'),
                'birth_place' => $this->student->birth_place,
                'birth_place_ar' => $this->student->birth_place_ar,
                'class' => $this->student->class?->name ?? '-',
                'school_year' => $this->student->schoolYear?->name ?? '-',
                'guardian_name' => $this->student->guardian_name,
                'guardian_name_ar' => $this->student->guardian_name_ar,
                'photo' => $this->student->photo_path,
            ],
        ];

        $template = $lang === 'ar' ? 'pdf.attestation-ar' : 'pdf.attestation-fr';

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 20,
            'margin_right' => 20,
            'margin_top' => 20,
            'margin_bottom' => 20,
        ]);

        $html = view($template, $data)->render();
        $mpdf->WriteHTML($html);

        $pdfContent = $mpdf->Output('', 'S');
        $langSuffix = $lang === 'ar' ? '_ar' : '_fr';
        $filename = 'attestation_' . $this->student->matricule . $langSuffix . '_' . now()->format('Ymd') . '.pdf';

        return response()->streamDownload(function () use ($pdfContent) {
            echo $pdfContent;
        }, $filename, ['Content-Type' => 'application/pdf']);
    }

    public function render()
    {
        $trimesters = Trimester::where('school_year_id', $this->student->school_year_id)
            ->orderBy('start_date')
            ->get();

        $grades = collect();
        $trimesterAverage = null;

        if ($this->selectedTrimesterId) {
            $grades = $this->gradeCalc->getStudentGradesBySubject($this->student, $this->selectedTrimesterId);
            $trimesterAverage = $this->gradeCalc->calculateStudentAverage($this->student, $this->selectedTrimesterId);
        }

        $subjects = $this->student->class?->subjects()
            ->orderBy('name_fr')
            ->get() ?? collect();

        $rankInfo = $this->getStudentRankForTrimester($this->selectedTrimesterId);
        $mention = $this->gradeCalc->getAppreciation($trimesterAverage);

        $trimesterAverages = $this->getTrimesterAverages();
        $annualAverage = $this->getAnnualAverage();
        $annualRank = $this->getAnnualRank();
        $annualMention = $this->gradeCalc->getAppreciation($annualAverage);

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
