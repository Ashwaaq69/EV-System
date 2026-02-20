<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvVehicle extends Model
{
    protected $table = 'ev_vehicles';

    protected $fillable = [
        'user_id',
        'brand',
        'model',
        'plate_number',
        'battery_capacity_kwh',
        'connector_type',
        'is_default',
    ];

    protected $casts = [
        'battery_capacity_kwh' => 'decimal:2',
        'is_default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
