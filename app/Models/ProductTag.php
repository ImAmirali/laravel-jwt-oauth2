<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductTag extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'tag_id'
    ];

    public function tag():BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }

    public function product():BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
