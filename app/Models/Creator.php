<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Creator extends Model
{
    use HasFactory;
    
    protected $table = 'creators';

    // Fillable fields for mass assignment
    protected $fillable = [
        'name',
        'phone',
        'email',
        'password',
        'address',
        'city',
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
