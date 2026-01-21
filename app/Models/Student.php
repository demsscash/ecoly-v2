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
        'parent_id',
        'first_name',
        'last_name',
        'first_name_ar',
        'last_name_ar',
        'birth_date',
        'birth_place',
        'birth_place_ar',
        'gender',
        'nationality',
        'photo_path', // Changed from 'photo' to 'photo_path'
        'guardian_name',
        'guardian_name_ar',
        'guardian_phone',
        'guardian_phone_2',
        'guardian_email',
        'guardian_profession',
        'address',
        'address_ar',
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

    /**
     * Get full name attribute
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get full name in Arabic
     */
    public function getFullNameArAttribute(): ?string
    {
        if (!$this->first_name_ar && !$this->last_name_ar) {
            return null;
        }
        return ($this->first_name_ar ?? '') . ' ' . ($this->last_name_ar ?? '');
    }

    /**
     * Get photo URL - returns actual photo or default silhouette
     */
    public function getPhotoUrlAttribute(): string
    {
        // If photo exists, return it
        if ($this->photo_path && file_exists(public_path('storage/' . $this->photo_path))) {
            return asset('storage/' . $this->photo_path);
        }

        // Return gender-appropriate silhouette
        if ($this->gender === 'female') {
            return asset('images/default-female.svg');
        }

        return asset('images/default-male.svg');
    }

    /**
     * Generate unique matricule for student
     */
    public static function generateMatricule(?int $schoolYearId = null): string
    {
        $year = date('Y');

        $query = self::whereYear('created_at', $year);
        if ($schoolYearId) {
            $query->where('school_year_id', $schoolYearId);
        }

        $lastStudent = $query->orderBy('id', 'desc')->first();

        if ($lastStudent && preg_match('/MAT-' . $year . '-(\d+)/', $lastStudent->matricule, $matches)) {
            $number = intval($matches[1]) + 1;
        } else {
            $number = 1;
        }

        return 'MAT-' . $year . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Validate NNI format
     */
    public static function isValidNni(?string $nni): bool
    {
        if (!$nni) return true; // NNI is optional
        return strlen($nni) === 10 && ctype_digit($nni);
    }

    /**
     * Relationships
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

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
