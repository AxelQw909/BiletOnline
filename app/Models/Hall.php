<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hall extends Model
{
    // Убрали 'schema' из $fillable, так как колонки больше нет
    protected $fillable = ['user_id', 'name', 'address', 'capacity'];

    // Убрали protected $casts для 'schema', так как этого поля больше нет

    public function partner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Связь с рядами (теперь это основной способ получения данных о месте)
    public function rows() 
    { 
        return $this->hasMany(Row::class); 
    }
}