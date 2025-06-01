<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrescriptionImage extends Model
{
    protected $fillable = ['prescription_id', 'image_path'];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class, 'prescription_id');
    }

}
