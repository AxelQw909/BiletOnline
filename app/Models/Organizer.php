<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organizer extends Model
{
    // Указываем, какие поля можно массово заполнять
    protected $fillable = [
        'company_name',
        'phone',
        'email',
        'user_id' // если модель привязана к аккаунту пользователя
    ];

    /**
     * Связь: у одного организатора может быть много концертов
     */
    public function concerts(): HasMany
    {
        return $this->hasMany(Concert::class);
    }
}