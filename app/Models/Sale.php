<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'location_id',
        'payment_type',
        'total_amount',
        'date',

    ];
}
