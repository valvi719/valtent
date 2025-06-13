<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Moment extends Model
{
    protected $fillable = [
        'cre_id', 'triggered_by', 'type', 'message', 'link', 'is_read'
    ];

    public function user()
    {
        return $this->belongsTo(Creator::class, 'cre_id');
    }

    public function actor()
    {
        return $this->belongsTo(Creator::class, 'triggered_by');
    }
}
