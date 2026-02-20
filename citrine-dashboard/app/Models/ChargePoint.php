<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChargePoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'identifier',
        'status',
        'location_id',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}





