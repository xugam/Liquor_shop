<?php

namespace App\Models;

use App\Traits\SlugTrait;
use Illuminate\Database\Eloquent\Model;

class ProductUnit extends Model
{
    use SlugTrait;
    protected $fillable = [
        'name',
        'product_id',
        'conversion_factor',
        'cost_price',
        'selling_price',
        'slug',
        'is_base_unit',
    ];
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // Helper: Convert unit to base
    public function convertToBaseUnits($quantity)
    {
        return $quantity * $this->conversion_factor;
    }
    public function BaseSellingPrice($quantity)
    {
        $baseUnit = $this->conversion_factor * $quantity;
        return $baseUnit * $this->selling_price;
    }
    public function BaseCostPrice($quantity)
    {
        $baseUnit = $this->conversion_factor * $quantity;
        return $baseUnit * $this->cost_price;
    }
}
