<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conbank extends Model
{
    use HasFactory;

    // Define the table associated with the model
    protected $table = 'conbank';

    // Define the primary key if it's not 'id' (optional)
    // protected $primaryKey = 'id'; 

    // Disable timestamps if you don't need created_at and updated_at columns
    public $timestamps = true;

    // Define the fillable attributes to protect against mass assignment vulnerabilities
    protected $fillable = [
        'cre_id',
        'balance',
        'deposits',
        'withdrawals',
        'loans',
        'interests',
        'created_at',
        'updated_at',
        'last_interest_applied',
    ];

    // Define any relationships (if needed)
    // For example, if you have a User model that corresponds to the 'cre_id'
    public function creator()
    {
        return $this->belongsTo(Creator::class, 'cre_id');
    }
}
