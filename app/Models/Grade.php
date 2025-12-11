<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    protected static function booted(): void
    {
        // Log history on update
        static::updating(function (Grade $grade) {
            if ($grade->isDirty(['control_grade', 'exam_grade', 'average', 'appreciation'])) {
                GradeHistory::create([
                    'grade_id' => $grade->id,
                    'user_id' => auth()->id(),
                    'old_control_grade' => $grade->getOriginal('control_grade'),
                    'new_control_grade' => $grade->control_grade,
                    'old_exam_grade' => $grade->getOriginal('exam_grade'),
                    'new_exam_grade' => $grade->exam_grade,
                    'old_average' => $grade->getOriginal('average'),
                    'new_average' => $grade->average,
                    'old_appreciation' => $grade->getOriginal('appreciation'),
                    'new_appreciation' => $grade->appreciation,
                    'action' => 'update',
                ]);
            }
        });

        // Log history on create
        static::created(function (Grade $grade) {
            GradeHistory::create([
                'grade_id' => $grade->id,
                'user_id' => auth()->id(),
                'new_control_grade' => $grade->control_grade,
                'new_exam_grade' => $grade->exam_grade,
                'new_average' => $grade->average,
                'new_appreciation' => $grade->appreciation,
                'action' => 'create',
            ]);
        });
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function trimester(): BelongsTo
    {
        return $this->belongsTo(Trimester::class);
    }

    public function enteredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(GradeHistory::class)->orderBy('created_at', 'desc');
    }
}
