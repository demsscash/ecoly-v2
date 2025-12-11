<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SchoolSetting extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name_fr',
        'name_ar',
        'address_fr',
        'address_ar',
        'phone',
        'email',
        'academic_inspection',
        'school_code',
        'logo_path',
        'stamp_path',
        'signature_path',
        'director_name_fr',
        'director_name_ar',
    ];

    /**
     * Get the singleton instance of school settings.
     */
    public static function instance(): self
    {
        return self::firstOrCreate(['id' => 1], [
            'name_fr' => 'École',
            'name_ar' => 'مدرسة',
        ]);
    }

    /**
     * Get logo URL.
     */
    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path ? Storage::url($this->logo_path) : null;
    }

    /**
     * Get stamp URL.
     */
    public function getStampUrlAttribute(): ?string
    {
        return $this->stamp_path ? Storage::url($this->stamp_path) : null;
    }

    /**
     * Get signature URL.
     */
    public function getSignatureUrlAttribute(): ?string
    {
        return $this->signature_path ? Storage::url($this->signature_path) : null;
    }
}
