<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Partner extends Model
{
    protected $fillable = [
        'company_name',
        'address',
        'phone',
        'email',
        'user_id'
    ];

    /**
     * Связь: у партнера может быть много залов
     */
    public function halls(): HasMany
    {
        return $this->hasMany(Hall::class);
    }
}