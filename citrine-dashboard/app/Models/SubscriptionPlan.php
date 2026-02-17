<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = ['name', 'price', 'free_kwh', 'discount_percentage', 'duration_days', 'description'];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
    ];
}
