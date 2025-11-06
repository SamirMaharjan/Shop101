<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Collection extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'shopify_collection_id',
        'title',
        'description',
        'handle',
        'collection_type',
        'products_count',
        'image',
        'published_at',
        'shopify_created_at',
        'shopify_updated_at',
    ];

    protected $casts = [
        'image' => 'array',
        'published_at' => 'datetime',
        'shopify_created_at' => 'datetime',
        'shopify_updated_at' => 'datetime',
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }
}