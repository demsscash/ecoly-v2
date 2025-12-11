<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_fr',
        'name_ar',
        'code',
        'coefficient',
        'is_active',
    ];

    protected $casts = [
        'coefficient' => 'decimal:1',
        'is_active' => 'boolean',
    ];

    /**
     * Get the classes that have this subject.
     */
    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'class_subject', 'subject_id', 'class_id')
            ->withPivot(['teacher_id', 'coefficient'])
            ->withTimestamps();
    }

    /**
     * Get localized name.
     */
    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name_fr;
    }

    /**
     * Scope for active subjects.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
