<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResultDescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
    ];

    /**
     * Get all results with this description
     */
    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }

    /**
     * Constants for result types
     */
    const BEAT = 1;
    const DREW = 2;
}
