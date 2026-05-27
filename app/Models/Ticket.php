<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'concert_id', 'ticket_code', 'row', 'seat', 'price', 'customer_name', 'customer_email', 'customer_phone', 'status'
    ];

    public function concert()
    {
        return $this->belongsTo(Concert::class);
    }
}