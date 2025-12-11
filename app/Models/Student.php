<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'matricule',
        'first_name',
        'last_name',
        'first_name_ar',
        'last_name_ar',
        'birth_date',
        'birth_place',
        'birth_place_ar',
        'gender',
        'nationality',
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
        'status',
        'notes',
        'photo_path',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'enrollment_date' => 'date',
    ];

    /**
     * Get full name.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get full name in Arabic.
     */
    public function getFullNameArAttribute(): string
    {
        return "{$this->first_name_ar} {$this->last_name_ar}";
    }

    /**
     * Get localized full name.
     */
    public function getNameAttribute(): string
    {
        if (app()->getLocale() === 'ar' && $this->first_name_ar) {
            return $this->full_name_ar;
        }
        return $this->full_name;
    }

    /**
     * Get the class.
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Get the school year.
     */
    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }

    /**
     * Get photo URL.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo_path 
            ? asset('storage/' . $this->photo_path)
            : null;
    }

    /**
     * Generate unique matricule.
     */
    public static function generateMatricule(int $schoolYearId): string
    {
        $year = SchoolYear::find($schoolYearId);
        $yearPrefix = $year ? substr($year->name, 0, 4) : date('Y');
        
        $lastStudent = self::where('school_year_id', $schoolYearId)
            ->orderByDesc('id')
            ->first();
        
        $sequence = $lastStudent 
            ? (int) substr($lastStudent->matricule, -4) + 1 
            : 1;
        
        return $yearPrefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Scope for active students.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for specific school year.
     */
    public function scopeForYear($query, int $yearId)
    {
        return $query->where('school_year_id', $yearId);
    }

    /**
     * Scope for specific class.
     */
    public function scopeInClass($query, int $classId)
    {
        return $query->where('class_id', $classId);
    }
}
