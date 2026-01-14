<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model
{
    protected $fillable = [
        'student_id',
        'subject_id',
        'class_id',
        'trimester_id',
        'control_grade',
        'exam_grade',
        'average',
        'appreciation',
        'entered_by',
        'entered_at',
        'is_validated',
        'validated_by',
        'validated_at',
    ];

    protected $casts = [
        'control_grade' => 'decimal:2',
        'exam_grade' => 'decimal:2',
        'average' => 'decimal:2',
        'entered_at' => 'datetime',
        'is_validated' => 'boolean',
        'validated_at' => 'datetime',
    ];

    /**
     * Get the student
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the subject
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the class
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Get the trimester
     */
    public function trimester(): BelongsTo
    {
        return $this->belongsTo(Trimester::class);
    }

    /**
     * Get the user who entered the grade
     */
    public function enteredBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'entered_by');
    }

    /**
     * Get the user who validated the grade
     */
    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'validated_by');
    }

    // ========== HELPER METHODS ==========

    /**
     * Check if this grade has a control grade
     */
    public function hasControlGrade(): bool
    {
        return $this->control_grade !== null;
    }

    /**
     * Check if this grade has an exam grade
     */
    public function hasExamGrade(): bool
    {
        return $this->exam_grade !== null;
    }

    /**
     * Check if the grade is complete (has average)
     */
    public function isComplete(): bool
    {
        return $this->average !== null;
    }

    /**
     * Get normalized grade (/20 for comparison)
     * Uses the average if available, otherwise prioritizes control over exam
     */
    public function getNormalizedGrade(): float
    {
        if ($this->average !== null) {
            return (float) $this->average;
        }

        $grade = $this->control_grade ?? $this->exam_grade;
        if ($grade === null) {
            return 0;
        }

        // Assume grades are already on /20 scale
        return (float) $grade;
    }
}
