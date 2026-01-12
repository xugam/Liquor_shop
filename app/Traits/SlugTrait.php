<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait SlugTrait
{
    /**
     * Boot the trait and automatically generate slug before saving.
     */
    public static function bootSlugTrait()
    {
        static::creating(function ($model) {
            if (empty($model->slug) && isset($model->name)) {
                $model->slug = Str::slug($model->name);
            }
        });

        static::updating(function ($model) {
            if (isset($model->name) && $model->isDirty('name')) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    /**
     * Optionally, generate slug manually
     */
    public function generateSlug($string)
    {
        return Str::slug($string);
    }
}
