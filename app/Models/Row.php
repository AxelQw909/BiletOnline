<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Row extends Model {
    protected $fillable = ['hall_id', 'number'];
    public function seats() { return $this->hasMany(Seat::class); }
}