<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Creator;
use App\Models\Content;

class ContentLike extends Model
{
    
    protected $table = 'content_like';
    protected $fillable = ['name', 'con_id','liked_by'];

    // Relationship with Creator (ContentLike belongs to a creator)
    public function creator()
    {
        return $this->belongsTo(Creator::class, 'liked_by');
    }

    // Relationship with Content (ContentLike belongs to content)
    public function content()
    {
        return $this->belongsTo(Content::class, 'con_id');
    }

    
}
