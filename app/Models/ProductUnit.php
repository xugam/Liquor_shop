<?php

namespace App\Models;

use App\Traits\SlugTrait;
use Illuminate\Database\Eloquent\Model;

class ProductUnit extends Model
{
    use SlugTrait;
    protected $fillable = [
        'name',
        'conversion_factor',
        'slug',
        'is_base_unit',
    ];
    public function products()
    {
        return $this->hasMany(Product::class, 'base_unit_id');
    }
}
