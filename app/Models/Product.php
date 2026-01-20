<?php

namespace App\Models;

use App\Traits\SlugTrait;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use SlugTrait;
    use InteractsWithMedia;
    protected $fillable = [
        'name',
        'slug',
        'category_id',
        'brand_id',
        'status',
        'sku'
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('product_images')->useDisk('public');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }


    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }
    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function getStockAtLocation($locationId)
    {
        return $this->stockMovements()
            ->where('location_id', $locationId)
            ->sum('quantity'); // Always in base units
    }
}
