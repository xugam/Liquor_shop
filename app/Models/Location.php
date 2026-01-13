<?php

namespace App\Models;

use App\Traits\SlugTrait;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use SlugTrait;
    protected $fillable = [
        'id',
        'name',
        'type',
        'slug',
        'address',
        'additional_info',
        'created_at',
        'updated_at',
    ];
}
