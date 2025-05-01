<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Follower extends Model
{
    protected $table = 'followers';
    protected $fillable = ['cre_id', 'follower'];

    public function followerUser()
    {
    return $this->belongsTo(Creator::class, 'follower');
    }

}
