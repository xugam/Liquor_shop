<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'total_amount',
        'status',
        'user_id',
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
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
