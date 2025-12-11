<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradingConfig extends Model
{
    use HasFactory;

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
     * Get the school year.
     */
    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }

    /**
     * Get config for active year or create default.
     */
    public static function getForActiveYear(): ?self
    {
        $activeYear = SchoolYear::active();
        if (!$activeYear) {
            return null;
        }

        return self::firstOrCreate(
            ['school_year_id' => $activeYear->id],
            [
                'control_weight' => 40,
                'exam_weight' => 60,
                'mention_excellent' => 16.00,
                'mention_very_good' => 14.00,
                'mention_good' => 12.00,
                'mention_fairly_good' => 10.00,
                'passing_grade' => 10.00,
            ]
        );
    }

    /**
     * Get mention for a given average.
     */
    public function getMention(float $average, int $gradeBase = 20): string
    {
        // Convert to base 20 for comparison
        $normalizedAverage = $gradeBase === 10 ? $average * 2 : $average;

        if ($normalizedAverage >= $this->mention_excellent) {
            return 'Excellent';
        } elseif ($normalizedAverage >= $this->mention_very_good) {
            return 'Très Bien';
        } elseif ($normalizedAverage >= $this->mention_good) {
            return 'Bien';
        } elseif ($normalizedAverage >= $this->mention_fairly_good) {
            return 'Assez Bien';
        } else {
            return 'Passable';
        }
    }

    /**
     * Check if student passes.
     */
    public function isPassing(float $average, int $gradeBase = 20): bool
    {
        $normalizedAverage = $gradeBase === 10 ? $average * 2 : $average;
        return $normalizedAverage >= $this->passing_grade;
    }
}
