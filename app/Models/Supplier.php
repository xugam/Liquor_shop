<?php

namespace App\Models;

use App\Traits\SlugTrait;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use SlugTrait;
    protected $fillable = [
        'name',
        'slug',
        'phone',
        'address',
        'email',
        'status',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
