<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Payment extends Model
{
    protected $fillable = [
        'student_id',
        'school_year_id',
        'type',
        'month',
        'amount',
        'amount_paid',
        'paid_date',
        'payment_method',
        'reference',
        'notes',
        'received_by',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'paid_date' => 'datetime',
    ];

    protected $appends = ['balance'];

    /**
     * Get balance attribute
     */
    protected function balance(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->amount - $this->amount_paid,
        );
    }

    /**
     * Get type label
     */
    public function getTypeLabel(): string
    {
        return match($this->type) {
            'registration' => __('Registration'),
            'tuition' => __('Tuition'),
            'other' => __('Other'),
            default => $this->type,
        };
    }

    /**
     * Get month label
     */
    public function getMonthLabel(): string
    {
        if (!$this->month) {
            return '-';
        }

        $months = [
            1 => __('January'),
            2 => __('February'),
            3 => __('March'),
            4 => __('April'),
            5 => __('May'),
            6 => __('June'),
            7 => __('July'),
            8 => __('August'),
            9 => __('September'),
            10 => __('October'),
            11 => __('November'),
            12 => __('December'),
        ];

        return $months[$this->month] ?? $this->month;
    }

    /**
     * Get payment method label
     */
    public function getMethodLabel(): string
    {
        return match($this->payment_method) {
            'cash' => __('Cash'),
            'check' => __('Check'),
            'transfer' => __('Transfer'),
            'mobile_money' => __('Mobile Money'),
            default => $this->payment_method ?? '-',
        };
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'paid' => 'badge-success',
            'partial' => 'badge-warning',
            'pending' => 'badge-error',
            default => 'badge-ghost',
        };
    }

    /**
     * Generate unique receipt reference
     */
    public function generateReference(): string
    {
        $year = now()->year;
        
        // Get the last receipt number for this year
        $lastPayment = self::whereYear('created_at', $year)
            ->whereNotNull('reference')
            ->orderByRaw("CAST(REPLACE(reference, 'REC-{$year}-', '') AS INTEGER) DESC")
            ->first();

        if ($lastPayment && $lastPayment->reference) {
            // Extract the number part
            $lastNumber = (int) str_replace("REC-{$year}-", '', $lastPayment->reference);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('REC-%d-%05d', $year, $newNumber);
    }

    /**
     * Relationships
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
