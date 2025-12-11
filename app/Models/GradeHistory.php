<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradeHistory extends Model
{
    protected $fillable = [
        'grade_id',
        'user_id',
        'old_control_grade',
        'new_control_grade',
        'old_exam_grade',
        'new_exam_grade',
        'old_average',
        'new_average',
        'old_appreciation',
        'new_appreciation',
        'action',
    ];

    protected $casts = [
        'old_control_grade' => 'decimal:2',
        'new_control_grade' => 'decimal:2',
        'old_exam_grade' => 'decimal:2',
        'new_exam_grade' => 'decimal:2',
        'old_average' => 'decimal:2',
        'new_average' => 'decimal:2',
    ];

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
