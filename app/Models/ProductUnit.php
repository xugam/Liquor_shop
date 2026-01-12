<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductUnit extends Model
{
    protected $fillable = [
        'name',
        'conversion_to_base_unit',
        'is_base_unit'
    ];
    public function products()
    {
        return $this->hasMany(Product::class, 'base_unit_id');
    }
}
