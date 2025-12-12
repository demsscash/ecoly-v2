<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class SchoolClass extends Model
{
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
        'main_teacher_id',
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
     * Get available levels
     */
    public static function levels(): array
    {
        return [
            '1' => '1ère Année',
            '2' => '2ème Année',
            '3' => '3ème Année',
            '4' => '4ème Année',
            '5' => '5ème Année',
            '6' => '6ème Année',
        ];
    }

    /**
     * Get available sections
     */
    public static function sections(): array
    {
        return ['A', 'B', 'C', 'D', 'E', 'F'];
    }

    /**
     * Get level name attribute
     */
    protected function levelName(): Attribute
    {
        return Attribute::make(
            get: fn() => self::levels()[$this->level] ?? $this->level,
        );
    }

    /**
     * Relationships
     */
    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function mainTeacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'main_teacher_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'class_subject', 'class_id', 'subject_id')
            ->withPivot(['teacher_id', 'grade_base'])
            ->withTimestamps();
    }
}
