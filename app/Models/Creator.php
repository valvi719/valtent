<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable; 

class Creator extends Authenticatable
{
    use HasFactory, Notifiable;
    
    protected $table = 'creators';

    // Fillable fields for mass assignment
    protected $fillable = [
        'name',
        'phone',
        'email',
        'otp',
        'otp_expires_at',
        'email_verified_at',
        'password',
        'address',
        'city',
        'profile_photo',
    ];

    // Hidden fields for serialization (e.g., password should not be exposed in responses)
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Attributes that should be cast to native types
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Optionally, add any relationships or custom methods here
}
