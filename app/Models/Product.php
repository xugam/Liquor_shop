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
        'price',
        'slug',
        'category_id',
        'brand_id',
        'status',
        'base_unit_id',
        'volume_ml',
        'stock',
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
}
