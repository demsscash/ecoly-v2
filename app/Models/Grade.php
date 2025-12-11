<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model
{
    use HasFactory;

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
    ];

    protected $casts = [
        'control_grade' => 'decimal:2',
        'exam_grade' => 'decimal:2',
        'average' => 'decimal:2',
        'entered_at' => 'datetime',
    ];

    /**
     * Get the student.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the subject.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the class.
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Get the trimester.
     */
    public function trimester(): BelongsTo
    {
        return $this->belongsTo(Trimester::class);
    }

    /**
     * Get the user who entered the grade.
     */
    public function enteredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entered_by');
    }
}
