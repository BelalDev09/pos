<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'sku'            => $this->sku,
            'barcode'        => $this->barcode,
            'description'    => $this->description,
            'image'          => $this->image
                ? asset('storage/' . $this->image)
                : null,
            'cost_price'     => (float) $this->cost_price,
            'selling_price'  => (float) $this->selling_price,
            'unit'           => $this->unit,
            'track_stock'    => $this->track_stock,
            'is_active'      => $this->is_active,
            'is_pos_visible' => $this->is_pos_visible,
            'product_type'   => $this->product_type,
            'profit_margin'  => $this->profit_margin,

            // Relationships
            'category'       => $this->whenLoaded('category', fn() => [
                'id'   => $this->category->id,
                'name' => $this->category->name,
            ]),
            'brand'          => $this->whenLoaded('brand', fn() => [
                'id'   => $this->brand->id,
                'name' => $this->brand->name,
            ]),
            'tax_rate'       => $this->whenLoaded('taxRate', fn() => [
                'id'           => $this->taxRate->id,
                'name'         => $this->taxRate->name,
                'rate'         => (float) $this->taxRate->rate,
                'is_inclusive' => $this->taxRate->is_inclusive,
            ]),
            'variants'       => $this->whenLoaded(
                'variants',
                fn() => VariantResource::collection($this->variants)
            ),
            'default_variant_id' => $this->whenLoaded(
                'defaultVariant',
                fn() => $this->defaultVariant?->id
            ),
            'stock_qty'      => $this->when(
                isset($this->stock_qty),
                fn() => (float) $this->stock_qty
            ),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
