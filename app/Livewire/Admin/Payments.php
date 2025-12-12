<?php

namespace App\Livewire\Admin;

use App\Models\Payment;
use App\Models\SchoolClass;
use App\Models\SchoolSettings;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Services\PdfService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use Mpdf\Mpdf;

#[Layout('layouts.app')]
#[Title('Paiements - Ecoly')]
class Payments extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterClass = '';
    public string $filterStatus = '';
    public string $filterType = '';

    public bool $showPaymentModal = false;
    public ?int $selectedPaymentId = null;
    public string $paymentAmount = '';
    public string $paymentMethod = 'cash';
    public string $paymentNotes = '';

    public bool $showInitModal = false;
    public string $initStartMonth = '10';
    public string $initEndMonth = '06';
    public bool $initIncludeRegistration = true;
    public string $initFilterClass = '';

    public array $selectedPayments = [];
    public bool $selectAll = false;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterClass(): void
    {
        $this->resetPage();
        $this->selectedPayments = [];
        $this->selectAll = false;
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedFilterType(): void
    {
        $this->resetPage();
    }

    public function updatedSelectAll(): void
    {
        if ($this->selectAll) {
            $schoolYear = SchoolYear::where('is_active', true)->first();
            $this->selectedPayments = Payment::when($schoolYear, fn($q) => $q->where('school_year_id', $schoolYear->id))
                ->when($this->filterClass, function ($q) {
                    $q->whereHas('student', fn($sq) => $sq->where('class_id', $this->filterClass));
                })
                ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
                ->when($this->filterType, fn($q) => $q->where('type', $this->filterType))
                ->where('status', 'pending')
                ->pluck('id')
                ->toArray();
        } else {
            $this->selectedPayments = [];
        }
    }

    public function openPaymentModal(int $paymentId): void
    {
        $payment = Payment::find($paymentId);
        if (!$payment) return;

        $this->selectedPaymentId = $paymentId;
        $this->paymentAmount = (string) $payment->balance;
        $this->paymentMethod = 'cash';
        $this->paymentNotes = '';
        $this->showPaymentModal = true;
    }

    public function recordPayment(): void
    {
        $this->validate([
            'paymentAmount' => 'required|numeric|min:1',
            'paymentMethod' => 'required|in:cash,transfer,mobile_money',
        ]);

        $payment = Payment::find($this->selectedPaymentId);
        if (!$payment) {
            $this->dispatch('toast', message: __('Payment not found.'), type: 'error');
            return;
        }

        $amount = (float) $this->paymentAmount;
        $maxAmount = $payment->balance;

        if ($amount > $maxAmount) {
            $this->dispatch('toast', message: __('Amount exceeds balance.'), type: 'error');
            return;
        }

        $newAmountPaid = $payment->amount_paid + $amount;
        $newStatus = $newAmountPaid >= $payment->amount ? 'paid' : 'partial';

        $payment->update([
            'amount_paid' => $newAmountPaid,
            'status' => $newStatus,
            'paid_date' => now(),
            'payment_method' => $this->paymentMethod,
            'reference' => $payment->reference ?? Payment::generateReference(),
            'notes' => $this->paymentNotes ?: $payment->notes,
            'received_by' => auth()->id(),
        ]);

        $this->showPaymentModal = false;
        $this->dispatch('toast', message: __('Payment recorded successfully.'), type: 'success');
    }

    /**
     * Download receipt PDF
     */
    public function downloadReceipt(int $paymentId)
    {
        $payment = Payment::with(['student.class', 'student.schoolYear', 'receivedBy'])->find($paymentId);
        
        if (!$payment || $payment->amount_paid <= 0) {
            $this->dispatch('toast', message: __('No payment to receipt.'), type: 'error');
            return;
        }

        $school = SchoolSettings::first();
        
        $data = [
            'school' => [
                'name' => $school?->name ?? 'École',
                'name_ar' => $school?->name_ar ?? '',
                'address' => $school?->address ?? '',
                'phone' => $school?->phone ?? '',
            ],
            'student' => [
                'full_name' => $payment->student->full_name,
                'matricule' => $payment->student->matricule,
                'class' => $payment->student->class?->name ?? '-',
                'school_year' => $payment->student->schoolYear?->name ?? '-',
            ],
            'payment' => [
                'reference' => $payment->reference ?? 'N/A',
                'type_label' => $payment->getTypeLabel(),
                'month' => $payment->month,
                'month_label' => $payment->getMonthLabel(),
                'amount' => $payment->amount,
                'amount_paid' => $payment->amount_paid,
                'method_label' => $this->getMethodLabel($payment->payment_method),
                'paid_date' => $payment->paid_date?->format('d/m/Y') ?? now()->format('d/m/Y'),
                'received_by' => $payment->receivedBy?->first_name . ' ' . $payment->receivedBy?->last_name ?? '-',
            ],
            'generated_at' => now()->format('d/m/Y H:i'),
        ];

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A5',
            'orientation' => 'P',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
        ]);

        $html = view('pdf.receipt', $data)->render();
        $mpdf->WriteHTML($html);
        
        $pdfContent = $mpdf->Output('', 'S');
        $filename = 'recu_' . $payment->reference . '.pdf';

        return response()->streamDownload(function () use ($pdfContent) {
            echo $pdfContent;
        }, $filename, ['Content-Type' => 'application/pdf']);
    }

    private function getMethodLabel(?string $method): string
    {
        return match($method) {
            'cash' => __('Cash'),
            'transfer' => __('Bank Transfer'),
            'mobile_money' => __('Mobile Money'),
            default => '-',
        };
    }

    public function openInitModal(): void
    {
        $this->initStartMonth = '10';
        $this->initEndMonth = '06';
        $this->initIncludeRegistration = true;
        $this->initFilterClass = '';
        $this->showInitModal = true;
    }

    public function initializePayments(): void
    {
        $schoolYear = SchoolYear::where('is_active', true)->first();
        if (!$schoolYear) {
            $this->dispatch('toast', message: __('No active school year.'), type: 'error');
            return;
        }

        $query = Student::where('school_year_id', $schoolYear->id)
            ->where('status', 'active')
            ->whereNotNull('class_id');

        if ($this->initFilterClass) {
            $query->where('class_id', $this->initFilterClass);
        }

        $students = $query->get();
        $count = 0;

        $months = $this->getMonthsRange($this->initStartMonth, $this->initEndMonth);

        foreach ($students as $student) {
            $created = $this->createStudentPayments(
                $student, 
                $schoolYear, 
                $months,
                $this->initIncludeRegistration
            );
            $count += $created;
        }

        $this->showInitModal = false;

        if ($count > 0) {
            $this->dispatch('toast', message: __(':count payment(s) created.', ['count' => $count]), type: 'success');
        } else {
            $this->dispatch('toast', message: __('No new payments to create.'), type: 'info');
        }
    }

    private function getMonthsRange(string $start, string $end): array
    {
        $schoolYearMonths = ['10', '11', '12', '01', '02', '03', '04', '05', '06'];
        
        $startIndex = array_search($start, $schoolYearMonths);
        $endIndex = array_search($end, $schoolYearMonths);
        
        if ($startIndex === false) $startIndex = 0;
        if ($endIndex === false) $endIndex = count($schoolYearMonths) - 1;
        
        return array_slice($schoolYearMonths, $startIndex, $endIndex - $startIndex + 1);
    }

    private function createStudentPayments(Student $student, SchoolYear $schoolYear, array $months, bool $includeRegistration = true): int
    {
        $class = $student->class;
        if (!$class) return 0;

        $created = 0;

        if ($includeRegistration && $class->registration_fee > 0) {
            $exists = Payment::where('student_id', $student->id)
                ->where('school_year_id', $schoolYear->id)
                ->where('type', 'registration')
                ->exists();

            if (!$exists) {
                Payment::create([
                    'student_id' => $student->id,
                    'school_year_id' => $schoolYear->id,
                    'type' => 'registration',
                    'amount' => $class->registration_fee,
                    'status' => 'pending',
                ]);
                $created++;
            }
        }

        if ($class->tuition_fee > 0) {
            foreach ($months as $monthStr) {
                $exists = Payment::where('student_id', $student->id)
                    ->where('school_year_id', $schoolYear->id)
                    ->where('type', 'tuition')
                    ->where('month', $monthStr)
                    ->exists();

                if (!$exists) {
                    Payment::create([
                        'student_id' => $student->id,
                        'school_year_id' => $schoolYear->id,
                        'type' => 'tuition',
                        'month' => $monthStr,
                        'amount' => $class->tuition_fee,
                        'status' => 'pending',
                    ]);
                    $created++;
                }
            }
        }

        return $created;
    }

    public function deleteSelected(): void
    {
        if (empty($this->selectedPayments)) {
            $this->dispatch('toast', message: __('No payments selected.'), type: 'error');
            return;
        }

        $count = Payment::whereIn('id', $this->selectedPayments)
            ->where('status', 'pending')
            ->delete();

        $this->selectedPayments = [];
        $this->selectAll = false;

        $this->dispatch('toast', message: __(':count payment(s) deleted.', ['count' => $count]), type: 'success');
    }

    public function getMonthOptions(): array
    {
        return [
            '10' => 'Octobre',
            '11' => 'Novembre',
            '12' => 'Décembre',
            '01' => 'Janvier',
            '02' => 'Février',
            '03' => 'Mars',
            '04' => 'Avril',
            '05' => 'Mai',
            '06' => 'Juin',
        ];
    }

    public function render()
    {
        $schoolYear = SchoolYear::where('is_active', true)->first();

        $payments = Payment::with(['student.class', 'receivedBy'])
            ->when($schoolYear, fn($q) => $q->where('school_year_id', $schoolYear->id))
            ->when($this->search, function ($q) {
                $q->whereHas('student', function ($sq) {
                    $sq->where('first_name', 'ilike', '%' . $this->search . '%')
                        ->orWhere('last_name', 'ilike', '%' . $this->search . '%')
                        ->orWhere('matricule', 'ilike', '%' . $this->search . '%');
                });
            })
            ->when($this->filterClass, function ($q) {
                $q->whereHas('student', fn($sq) => $sq->where('class_id', $this->filterClass));
            })
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterType, fn($q) => $q->where('type', $this->filterType))
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $classes = SchoolClass::when($schoolYear, fn($q) => $q->where('school_year_id', $schoolYear->id))
            ->where('is_active', true)
            ->orderBy('level')
            ->orderBy('name')
            ->get();

        $statsQuery = Payment::when($schoolYear, fn($q) => $q->where('school_year_id', $schoolYear->id));
        $stats = [
            'total_due' => (clone $statsQuery)->sum('amount'),
            'total_paid' => (clone $statsQuery)->sum('amount_paid'),
            'pending_count' => (clone $statsQuery)->where('status', 'pending')->count(),
            'paid_count' => (clone $statsQuery)->where('status', 'paid')->count(),
        ];
        $stats['balance'] = $stats['total_due'] - $stats['total_paid'];
        $stats['collection_rate'] = $stats['total_due'] > 0 ? round(($stats['total_paid'] / $stats['total_due']) * 100, 1) : 0;

        return view('livewire.admin.payments', [
            'payments' => $payments,
            'classes' => $classes,
            'stats' => $stats,
            'monthOptions' => $this->getMonthOptions(),
        ]);
    }
}
