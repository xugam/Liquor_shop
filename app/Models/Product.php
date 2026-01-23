<?php

namespace App\Models;

use App\Http\Resources\ProductUnitListResource;
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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            // Only generate SKU if not provided
            if (empty($product->sku)) {
                $product->sku = static::generateSKU($product);
            }
        });
    }

    /**
     * Generate a unique SKU for the product
     * Format: {CATEGORY_CODE}-{BRAND_CODE}-{NUMBER}
     * Example: WHI-JAC-001
     */
    protected static function generateSKU($product)
    {
        // Get category and brand codes (first 3 letters, uppercase)
        $categoryCode = strtoupper(substr($product->category->name ?? 'PRD', 0, 3));
        $brandCode = strtoupper(substr($product->brand->name ?? 'GEN', 0, 3));

        // Find the next sequence number for this category-brand combination
        $prefix = "{$categoryCode}-{$brandCode}-";
        $lastProduct = static::where('sku', 'like', $prefix . '%')
            ->orderBy('sku', 'desc')
            ->first();

        if ($lastProduct) {
            // Extract the number from the last SKU and increment
            $lastNumber = (int) substr($lastProduct->sku, -3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        // Format: {CATEGORY}-{BRAND}-{001}
        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

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

    public function units()
    {
        return $this->hasMany(ProductUnit::class);
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
