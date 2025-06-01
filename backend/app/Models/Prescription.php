<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{

    protected $fillable = ['user_id', 'note', 'delivery_address',  'delivery_date', 'delivery_time'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(PrescriptionImage::class, 'prescription_id');
    }

}
