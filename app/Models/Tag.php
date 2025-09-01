<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = ['name', 'description'];

    public function taggedProducts(): BelongsToMany
    {
        return $this->BelongsToMany(Product::class, 'product_tags', 'tag_id', 'product_id');
    }
}
