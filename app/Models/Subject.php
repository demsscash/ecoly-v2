<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subject extends Model
{
    protected $fillable = [
        'code',
        'name_fr',
        'name_ar',
        'coefficient',
        'is_active',
    ];

    protected $casts = [
        'coefficient' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'class_subject', 'subject_id', 'class_id')
            ->withPivot(['teacher_id', 'grade_base'])
            ->withTimestamps();
    }
}
