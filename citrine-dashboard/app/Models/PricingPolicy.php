<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingPolicy extends Model
{
    protected $fillable = ['name', 'base_rate_per_kwh', 'peak_rate_per_kwh', 'peak_start', 'peak_end', 'is_active'];

    protected $casts = [
        'base_rate_per_kwh' => 'decimal:4',
        'peak_rate_per_kwh' => 'decimal:4',
        'is_active' => 'boolean',
    ];
}
