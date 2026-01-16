<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'location_id',
        'payment_type',
        'total_amount',
        'status'

    ];
    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
    public function cheque()
    {
        return $this->hasOne(Cheque::class);
    }
}
