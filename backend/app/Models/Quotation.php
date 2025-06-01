<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
       protected $fillable = [
        'user_id',
        'prescription_id',
        'total_amount',
        'status',
    ];

    public function drugs()
    {
        return $this->hasMany(Drug::class, 'quotation_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
