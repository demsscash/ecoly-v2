<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trimester extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'school_year_id',
        'name_fr',
        'name_ar',
        'number',
        'start_date',
        'end_date',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    /**
     * Get the school year.
     */
    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }

    /**
     * Check if trimester is open.
     */
    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    /**
     * Check if trimester is closed.
     */
    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    /**
     * Open this trimester.
     */
    public function open(): void
    {
        $this->update(['status' => 'open']);
    }

    /**
     * Close this trimester.
     */
    public function close(): void
    {
        $this->update(['status' => 'closed']);
    }
}
