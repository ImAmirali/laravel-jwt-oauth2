<?php

namespace App\Http\Controllers;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Display the product in a structured way
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category?->name,
            'price' => $this->price,
            'discountPercentage' => $this->discount_percentage,
            'rating' => $this->reviews->avg('rating'),
            'stock' => $this->stock,
            'tags' => $this->tags->pluck('name'),
            'brand' => $this->brand?->name,
            'sku' => $this->sku,
            'weight' => $this->weight,
            'dimensions' => $this->when($this->dimensions, [
                'width' => $this->dimensions?->width,
                'height' => $this->dimensions?->height,
                'depth' => $this->dimensions?->depth,
            ]),
            'warrantyInformation' => $this->warranty_information,
            'shippingInformation' => $this->shipping_information,
            'availabilityStatus' => $this->availability_status,
            'reviews' => $this->reviews->map(fn($review) => [
                'rating' => $review->rating,
                'comment' => $review->comment,
                'date' => $review->created_at->toISOString(),
                'reviewerName' => $review->user?->name ?? 'Anonymous',
                'reviewerEmail' => $review->user?->email,
            ]),
            'returnPolicy' => $this->return_policy,
            'minimumOrderQuantity' => $this->minimum_order_quantity,
            'meta' => [
                'createdAt' => $this->created_at->toISOString(),
                'updatedAt' => $this->updated_at->toISOString(),
                'barcode' => $this->barcode,
                'qrCode' => $this->qr_code,
            ],
            'thumbnail' => $this->thumbnail,
            'images' => $this->images->pluck('image_path'),
        ];
    }
}
