<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'ratings_feedback';
    protected $fillable = ['user_id', 'charge_point_id', 'rating', 'comment'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chargePoint()
    {
        return $this->belongsTo(ChargePoint::class);
    }
}
