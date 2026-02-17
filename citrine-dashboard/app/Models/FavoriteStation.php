<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FavoriteStation extends Model
{
    protected $fillable = ['user_id', 'charge_point_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chargePoint()
    {
        return $this->belongsTo(ChargePoint::class);
    }
}
