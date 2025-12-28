<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassSubject extends Model
{
    protected $table = 'class_subject';

    protected $fillable = [
        'class_id',
        'subject_id',
        'teacher_id',
        'max_grade',
        'coefficient',    // NEW: for college/lycee weighted averages
    ];

    protected $casts = [
        'max_grade' => 'integer',
        'coefficient' => 'integer',
    ];

    /**
     * Get the class
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Get the subject
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the teacher
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // ========== NEW HELPER METHODS (NO REGRESSION) ==========

    /**
     * Get effective coefficient (1 for fondamental, actual value for college/lycee)
     */
    public function getEffectiveCoefficient(): int
    {
        // If class is fondamental, coefficient is always 1 (ignored in calculations)
        if ($this->class && $this->class->isFondamental()) {
            return 1;
        }
        
        return $this->coefficient ?? 1;
    }

    /**
     * Check if this assignment uses fixed grades (/20 for college/lycee)
     */
    public function usesFixedGrades(): bool
    {
        return $this->class && ($this->class->isCollege() || $this->class->isLycee());
    }

    /**
     * Get max grade (20 for college/lycee, custom for fondamental)
     */
    public function getEffectiveMaxGrade(): int
    {
        if ($this->usesFixedGrades()) {
            return 20;
        }
        
        return $this->max_grade ?? 20;
    }
}
