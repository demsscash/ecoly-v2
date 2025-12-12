<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'student_id',
        'school_year_id',
        'type',
        'month',
        'amount',
        'amount_paid',
        'status',
        'due_date',
        'paid_date',
        'payment_method',
        'reference',
        'notes',
        'received_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
    ];

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

    public function getBalanceAttribute(): float
    {
        return $this->amount - $this->amount_paid;
    }

    public function getTypeLabel(): string
    {
        return match($this->type) {
            'registration' => __('Registration Fee'),
            'tuition' => __('Tuition Fee'),
            'other' => __('Other'),
            default => $this->type,
        };
    }

    public function getMonthLabel(): string
    {
        if (!$this->month) return '-';
        
        $months = [
            '01' => 'Janvier', '02' => 'Février', '03' => 'Mars',
            '04' => 'Avril', '05' => 'Mai', '06' => 'Juin',
            '07' => 'Juillet', '08' => 'Août', '09' => 'Septembre',
            '10' => 'Octobre', '11' => 'Novembre', '12' => 'Décembre',
        ];
        
        return $months[$this->month] ?? $this->month;
    }

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
    public static function generateReference(): string
    {
        $year = date('Y');
        $prefix = 'REC-' . $year . '-';
        
        // Get the last reference for this year
        $lastPayment = self::whereNotNull('reference')
            ->where('reference', 'like', $prefix . '%')
            ->orderByRaw("CAST(REPLACE(reference, '{$prefix}', '') AS INTEGER) DESC")
            ->first();
        
        if ($lastPayment) {
            $lastNumber = (int) str_replace($prefix, '', $lastPayment->reference);
            $number = $lastNumber + 1;
        } else {
            $number = 1;
        }
        
        return $prefix . str_pad($number, 5, '0', STR_PAD_LEFT);
    }
}
