<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'category_id',
        'brand_id',
        'title',
        'description',
        'price',
        'discount_percentage',
        'stock',
        'weight',
        'sku',
        'warranty_information',
        'shipping_information',
        'availability_status',
        'return_policy',
        'minimum_order_quantity',
        'barcode',
        'qr_code',
        'thumbnail',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function dimensions(): HasOne
    {
        return $this->hasOne(ProductDimension::class, 'product_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->BelongsToMany(Tag::class, 'product_tags',
            'product_id', 'tag_id');
    }

    public function reviews(): BelongsToMany
    {
        return $this->BelongsToMany(Product::class, 'product_reviews',
            'product_id', 'user_id');
    }

    public function getCreateDate(): string
    {
        return (new Carbon($this->created_at))->format('Y-m-d');
    }
}
