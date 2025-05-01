<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Following extends Model
{
    protected $table = 'following';
    protected $fillable = ['cre_id', 'whom'];

    public function followingUser()
    {
        return $this->belongsTo(Creator::class, 'whom');
    }

}
