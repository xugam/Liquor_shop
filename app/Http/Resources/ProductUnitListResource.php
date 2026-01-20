<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductUnitListResource extends JsonResource
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
            'conversion_factor' => $this->conversion_factor,
            'product_id' => $this->product_id,
            'cost_price' => $this->cost_price,
            'selling_price' => $this->selling_price,
            'is_base_unit' => $this->is_base_unit,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
