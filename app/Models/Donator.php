<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donator extends Model
{
    use HasFactory;

    protected $fillable = [
        'donator_id',
        'recipient_id',
        'content_id',
        'amount',
    ];

    // Relationships
    public function donor()
    {
        return $this->belongsTo(Creator::class, 'donator_id');
    }

    public function recipient()
    {
        return $this->belongsTo(Creator::class, 'recipient_id');
    }
    
    public function content()
    {
        return $this->belongsTo(Content::class);
    }
}
