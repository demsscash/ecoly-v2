<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolClass extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'school_year_id',
        'name',
        'level',
        'section',
        'grade_base',
        'capacity',
        'tuition_fee',
        'registration_fee',
        'is_active',
    ];

    protected $casts = [
        'grade_base' => 'integer',
        'capacity' => 'integer',
        'tuition_fee' => 'decimal:2',
        'registration_fee' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Available grade levels.
     */
    public static function levels(): array
    {
        return [
            '1' => '1ère année',
            '2' => '2ème année',
            '3' => '3ème année',
            '4' => '4ème année',
            '5' => '5ème année',
            '6' => '6ème année',
        ];
    }

    /**
     * Available sections.
     */
    public static function sections(): array
    {
        return ['A', 'B', 'C', 'D', 'E'];
    }

    /**
     * Get the school year.
     */
    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }

    /**
     * Get the students in this class.
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    /**
     * Get subjects for this class.
     */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'class_subject', 'class_id', 'subject_id')
            ->withPivot(['teacher_id', 'coefficient'])
            ->withTimestamps();
    }

    /**
     * Get level name attribute.
     */
    public function getLevelNameAttribute(): string
    {
        return self::levels()[$this->level] ?? $this->level;
    }

    /**
     * Get full name (level + section).
     */
    public function getFullNameAttribute(): string
    {
        return $this->section 
            ? "{$this->level_name} {$this->section}"
            : $this->level_name;
    }

    /**
     * Scope for active classes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific school year.
     */
    public function scopeForYear($query, int $yearId)
    {
        return $query->where('school_year_id', $yearId);
    }
}
