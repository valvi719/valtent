<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;
    protected $table = 'contents';
    protected $fillable = ['name', 'type', 'value','cre_id'];

    // Validation rules
    public static function validate($data)
    {
        return \Validator::make($data, [
            'name' => 'required|string|max:255',
            'type' => 'required|in:NFT,Media',
            'value' => 'required_if:type,Media|file|mimes:mp4,jpg,jpeg,png,mov,avi', // file validation
            'cre_id' => 'nullable|integer',
        ]);
    }
}
