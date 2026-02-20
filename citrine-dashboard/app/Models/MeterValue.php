<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeterValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'charge_point_id',
        'value',
    ];

    public function session()
    {
        return $this->belongsTo(ChargingSession::class, 'session_id');
    }

    public function chargePoint()
    {
        return $this->belongsTo(ChargePoint::class);
    }
}
