<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'company_name', 'address', 'phone'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    // Залы, принадлежащие партнеру
    public function halls()
    {
        return $this->hasMany(Hall::class);
    }

    // Концерты, организованные организатором
    public function concerts()
    {
        return $this->hasMany(Concert::class);
    }
}