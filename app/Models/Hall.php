<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hall extends Model
{
    protected $fillable = ['user_id', 'name', 'address', 'capacity', 'schema'];

    protected $casts = [
        'schema' => 'array', // Автоматическое преобразование JSON в массив
    ];

    public function partner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}