<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocationProduct extends Model
{
    protected $fillable = [
        'product_id',
        'location_id',
        'quantity',
        'reorder_level'

    ];
}
