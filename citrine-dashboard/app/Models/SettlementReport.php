<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettlementReport extends Model
{
    protected $fillable = ['report_date', 'total_revenue', 'total_tax', 'total_discounts', 'net_settlement', 'total_sessions', 'status'];

    protected $casts = [
        'report_date' => 'date',
        'total_revenue' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'total_discounts' => 'decimal:2',
        'net_settlement' => 'decimal:2',
    ];
}
