<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Serie extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    /**
     * Get classes in this serie
     */
    public function classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class, 'serie_id');
    }
}
