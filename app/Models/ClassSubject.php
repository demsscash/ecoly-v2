<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

use App\Models\Grade;

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

    /**
     * Get grades query for this class-subject assignment
     * Note: Grade uses class_id and subject_id separately, not class_subject_id
     *
     * @return Builder
     */
    public function gradesQuery(): Builder
    {
        return Grade::where('class_id', $this->class_id)
            ->where('subject_id', $this->subject_id);
    }

    /**
     * Get grades collection for this class-subject assignment
     *
     * @return Collection
     */
    public function getGrades(): Collection
    {
        return $this->gradesQuery()->get();
    }

    /**
     * Get grades for this class-subject assignment (legacy method)
     * Note: Deprecated in favor of gradesQuery() for clarity
     *
     * @return Builder
     * @deprecated Use gradesQuery() instead for clarity, or getGrades() for a Collection
     */
    public function grades(): Builder
    {
        return $this->gradesQuery();
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
