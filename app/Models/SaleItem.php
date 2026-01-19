<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $fillable = [
        'product_id',
        'sale_id',
        'unit_type',
        'quantity_selected',
        'quantitiy_base',
        'unit_price',
        'total_price',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    protected static function booted()
    {
        parent::boot();
        static::creating(function (SaleItem $saleItem) {
            if (!$saleItem->total_price) {
                $saleItem->total_price = $saleItem->quantity_selected * $saleItem->unit_price;
            }
        });
    }
}
