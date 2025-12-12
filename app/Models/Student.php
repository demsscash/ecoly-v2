<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $fillable = [
        'matricule',
        'nni',
        'first_name',
        'last_name',
        'first_name_ar',
        'last_name_ar',
        'birth_date',
        'birth_place',
        'gender',
        'nationality',
        'photo',
        'guardian_name',
        'guardian_phone',
        'guardian_phone2',
        'guardian_email',
        'guardian_profession',
        'address',
        'class_id',
        'school_year_id',
        'enrollment_date',
        'previous_school',
        'notes',
        'status',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'enrollment_date' => 'date',
    ];

    public function class(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getFullNameArAttribute(): ?string
    {
        if (!$this->first_name_ar && !$this->last_name_ar) {
            return null;
        }
        return $this->first_name_ar . ' ' . $this->last_name_ar;
    }

    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo && file_exists(public_path('storage/' . $this->photo))) {
            return asset('storage/' . $this->photo);
        }
        
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->full_name) . '&background=6366f1&color=fff';
    }

    public static function generateMatricule(): string
    {
        $year = date('Y');
        $lastStudent = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastStudent && preg_match('/MAT-' . $year . '-(\d+)/', $lastStudent->matricule, $matches)) {
            $number = intval($matches[1]) + 1;
        } else {
            $number = 1;
        }
        
        return 'MAT-' . $year . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Initialize payments for new student
     */
    public function initializePayments(): void
    {
        if (!$this->class_id || !$this->school_year_id) return;

        $class = $this->class;
        $schoolYear = $this->schoolYear;

        // Registration fee
        if ($class->registration_fee > 0) {
            Payment::create([
                'student_id' => $this->id,
                'school_year_id' => $this->school_year_id,
                'type' => 'registration',
                'amount' => $class->registration_fee,
                'status' => 'pending',
            ]);
        }

        // Monthly tuition fees
        if ($class->tuition_fee > 0) {
            $months = $schoolYear->payment_months ?? 9;
            $startMonth = 10; // October
            
            for ($i = 0; $i < $months; $i++) {
                $month = (($startMonth + $i - 1) % 12) + 1;
                $monthStr = str_pad($month, 2, '0', STR_PAD_LEFT);
                
                Payment::create([
                    'student_id' => $this->id,
                    'school_year_id' => $this->school_year_id,
                    'type' => 'tuition',
                    'month' => $monthStr,
                    'amount' => $class->tuition_fee,
                    'status' => 'pending',
                ]);
            }
        }
    }

    /**
     * Get total payments summary
     */
    public function getPaymentsSummary(): array
    {
        $payments = $this->payments()->where('school_year_id', $this->school_year_id)->get();
        
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
}
