<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChargingSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'charge_point_id',
        'vehicle_id',
        'start_time',
        'end_time',
        'kwh_consumed',
        'total_cost',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'kwh_consumed' => 'float',
        'total_cost' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chargePoint()
    {
        return $this->belongsTo(ChargePoint::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(EvVehicle::class, 'vehicle_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'session_id');
    }

    public function meterValues()
    {
        return $this->hasMany(MeterValue::class, 'session_id');
    }
}
