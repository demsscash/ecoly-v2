<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolYear extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'payment_months',
        'is_active',
        'is_archived',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
            'is_archived' => 'boolean',
        ];
    }

    /**
     * Get the active school year.
     */
    public static function active(): ?self
    {
        return self::where('is_active', true)->first();
    }

    /**
     * Activate this school year and deactivate others.
     */
    public function activate(): void
    {
        self::where('is_active', true)->update(['is_active' => false]);
        $this->update(['is_active' => true, 'is_archived' => false]);
    }

    /**
     * Archive this school year.
     */
    public function archive(): void
    {
        $this->update(['is_active' => false, 'is_archived' => true]);
    }

    /**
     * Get the trimesters for this school year.
     */
    public function trimesters(): HasMany
    {
        return $this->hasMany(Trimester::class);
    }

    /**
     * Scope for non-archived years.
     */
    public function scopeNotArchived($query)
    {
        return $query->where('is_archived', false);
    }

    /**
     * Scope for archived years.
     */
    public function scopeArchived($query)
    {
        return $query->where('is_archived', true);
    }
}
