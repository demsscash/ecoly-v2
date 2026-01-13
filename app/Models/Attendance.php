<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'student_id',
        'date',
        'status',
        'justification_note',
        'justification_file',
        'marked_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Check if status requires parent notification
     */
    public function requiresNotification(): bool
    {
        return in_array($this->status, ['absent', 'late', 'left_early']);
    }

    /**
     * Get status label
     */
    public function getStatusLabel(): string
    {
        return match($this->status) {
            'present' => __('Present'),
            'absent' => __('Absent'),
            'late' => __('Late'),
            'justified' => __('Justified'),
            'left_early' => __('Left Early'),
            default => $this->status,
        };
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'present' => 'badge-success',
            'absent' => 'badge-error',
            'late' => 'badge-warning',
            'justified' => 'badge-info',
            'left_early' => 'badge-warning',
            default => 'badge-ghost',
        };
    }

    /**
     * Relationships
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function markedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class);
    }

    public function timetable(): BelongsTo
    {
        return $this->belongsTo(Timetable::class);
    }

}
