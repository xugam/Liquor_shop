<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'cost_price' => $this->cost_price,
            'selling_price' => $this->selling_price,
            'image' => $this->getFirstMediaUrl('product_images'),
            'sku' => $this->sku,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'brand' => $this->brand->name,
            'category' => $this->category->name,
        ];
    }
}
