<?php

namespace App\Models;

use App\Traits\SlugTrait;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use SlugTrait;
    protected $fillable = [
        'name',
        'status'
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
