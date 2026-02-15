<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'lat',
        'lng',
        'margin_target',
    ];

    public function chargePoints()
    {
        return $this->hasMany(ChargePoint::class);
    }
}
