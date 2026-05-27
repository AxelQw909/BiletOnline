<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Concert extends Model
{
    // Добавил недостающие поля: organizer_id и payment_method (исправил на payment_info)
    protected $fillable = [
        'user_id', 
        'hall_id', 
        'title', 
        'date_time', 
        'payment_info', 
        'base_price', 
        'custom_prices', 
        'status'
    ];

    protected $casts = [
        'custom_prices' => 'array',
        'date_time' => 'datetime',
    ];
    

    // Убрал метод profile() из модели, так как это логика контроллера, а не модели данных
    
    public function organizer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function hall()
    {
        return $this->belongsTo(Hall::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
    
}