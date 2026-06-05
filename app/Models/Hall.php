<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hall extends Model
{
    // Добавили 'status' в $fillable, чтобы можно было сохранять его через контроллер
    protected $fillable = ['user_id', 'name', 'address', 'capacity', 'status'];

    public function partner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Связь с рядами
    public function rows() 
    { 
        return $this->hasMany(Row::class); 
    }

    public function user()
    {
        // Предполагается, что в таблице halls есть поле user_id
        return $this->belongsTo(User::class, 'user_id');
    }
}