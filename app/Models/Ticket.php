<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'concert_id', 
        'ticket_code', 
        'row', 
        'seat', 
        'seat_id', // Добавили для связи с конкретным местом в базе
        'price', 
        'customer_name', 
        'customer_email', 
        'customer_phone', 
        'status'
    ];

    public function concert()
    {
        return $this->belongsTo(Concert::class);
    }

    /**
     * Связь с конкретным местом в зале.
     * Теперь билет четко знает, какое именно место в базе он занимает.
     */
    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }
}