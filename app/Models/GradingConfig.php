<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradingConfig extends Model
{
    protected $table = 'grading_config';

    protected $fillable = [
        'school_year_id',
        'control_weight',
        'exam_weight',
        'mention_excellent',
        'mention_very_good',
        'mention_good',
        'mention_fairly_good',
        'passing_grade',
    ];

    protected $casts = [
        'control_weight' => 'integer',
        'exam_weight' => 'integer',
        'mention_excellent' => 'decimal:2',
        'mention_very_good' => 'decimal:2',
        'mention_good' => 'decimal:2',
        'mention_fairly_good' => 'decimal:2',
        'passing_grade' => 'decimal:2',
    ];

    /**
     * Get the instance for active school year (or create default)
     */
    public static function instance(): self
    {
        $schoolYear = SchoolYear::where('is_active', true)->first();
        
        if (!$schoolYear) {
            // Return a default config object without saving
            return new self([
                'control_weight' => 40,
                'exam_weight' => 60,
                'mention_excellent' => 16,
                'mention_very_good' => 14,
                'mention_good' => 12,
                'mention_fairly_good' => 10,
                'passing_grade' => 10,
            ]);
        }

        $config = static::where('school_year_id', $schoolYear->id)->first();

        if (!$config) {
            $config = static::create([
                'school_year_id' => $schoolYear->id,
                'control_weight' => 40,
                'exam_weight' => 60,
                'mention_excellent' => 16,
                'mention_very_good' => 14,
                'mention_good' => 12,
                'mention_fairly_good' => 10,
                'passing_grade' => 10,
            ]);
        }

        return $config;
    }

    /**
     * Accessors for compatibility with code using threshold naming
     */
    public function getExcellentThresholdAttribute(): float
    {
        return (float) ($this->mention_excellent ?? 16);
    }

    public function getVeryGoodThresholdAttribute(): float
    {
        return (float) ($this->mention_very_good ?? 14);
    }

    public function getGoodThresholdAttribute(): float
    {
        return (float) ($this->mention_good ?? 12);
    }

    public function getFairlyGoodThresholdAttribute(): float
    {
        return (float) ($this->mention_fairly_good ?? 10);
    }

    public function getPassThresholdAttribute(): float
    {
        return (float) ($this->passing_grade ?? 10);
    }

    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }
}
