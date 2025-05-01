<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable; 
use App\Models\ContentLike;

class Creator extends Authenticatable
{
    use HasFactory, Notifiable;
    
    protected $table = 'creators';

    // Fillable fields for mass assignment
    protected $fillable = [
        'name',
        'username',
        'phone',
        'email',
        'account_number',
        'ifsc_code',
        'otp',
        'otp_expires_at',
        'email_verified_at',
        'password',
        'address',
        'city',
        'profile_photo',
        'relationship_status',
        'relationship_status_since',
        'relationship_with',
        'bio',
    ];

    // Hidden fields for serialization (e.g., password should not be exposed in responses)
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Attributes that should be cast to native types
    protected $casts = [
        'email_verified_at' => 'datetime',
        'relationship_status_since' => 'date',
    ];

    // Optionally, add any relationships or custom methods here

    public function contents()
    {
        return $this->hasMany(Content::class, 'cre_id');
    }

    public function likes()
    {
        return $this->belongsToMany(Content::class, 'content_like', 'liked_by', 'con_id')->withTimestamps();
    }

    public function followers()
    {
        return $this->hasMany(Follower::class, 'cre_id');
    }

    public function following()
    {
        return $this->hasMany(Following::class, 'cre_id');
    }

    public function relationshipWithUser()
    {
        return $this->belongsTo(Creator::class, 'relationship_with', 'username');
    }
}
