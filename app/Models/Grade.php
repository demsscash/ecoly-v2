<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\GradeType;

class Grade extends Model
{
    protected $fillable = [
        'student_id',
        'class_subject_id',
        'trimester_id',
        'type',
        'control_number',    // NEW: for multiple controls (default 1)
        'grade',
        'max_grade',
    ];

    protected $casts = [
        'type' => GradeType::class,
        'grade' => 'float',
        'max_grade' => 'integer',
        'control_number' => 'integer',
    ];

    /**
     * Get the student
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the class subject
     */
    public function classSubject(): BelongsTo
    {
        return $this->belongsTo(ClassSubject::class);
    }

    /**
     * Get the trimester
     */
    public function trimester(): BelongsTo
    {
        return $this->belongsTo(Trimester::class);
    }

    // ========== NEW HELPER METHODS (NO REGRESSION) ==========

    /**
     * Check if this is a control grade
     */
    public function isControl(): bool
    {
        return $this->type === GradeType::CONTROL;
    }

    /**
     * Check if this is an exam grade
     */
    public function isExam(): bool
    {
        return $this->type === GradeType::EXAM;
    }

    /**
     * Get normalized grade (/20 for comparison)
     */
    public function getNormalizedGrade(): float
    {
        if ($this->max_grade == 0) {
            return 0;
        }
        
        return ($this->grade / $this->max_grade) * 20;
    }
}
