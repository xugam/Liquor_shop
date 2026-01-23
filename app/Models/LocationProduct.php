<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocationProduct extends Model
{
    protected $fillable = [
        'unit_id',
        'location_id',
        'quantity',
        'reorder_level'
    ];


    public function unit()
    {
        return $this->belongsTo(ProductUnit::class, 'unit_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
