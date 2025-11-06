<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'shopify_product_id',
        'title',
        'description',
        'vendor',
        'product_type',
        'status',
        'tags',
        'images',
        'variants',
        'price',
        'inventory_quantity',
        'published_at',
        'shopify_created_at',
        'shopify_updated_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'images' => 'array',
        'variants' => 'array',
        'price' => 'decimal:2',
        'published_at' => 'datetime',
        'shopify_created_at' => 'datetime',
        'shopify_updated_at' => 'datetime',
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('title', 'LIKE', "%{$search}%");
    }
}