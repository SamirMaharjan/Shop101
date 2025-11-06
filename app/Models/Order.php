<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'shopify_order_id',
        'order_number',
        'email',
        'total_price',
        'subtotal_price',
        'total_tax',
        'currency',
        'financial_status',
        'fulfillment_status',
        'line_items',
        'customer',
        'shipping_address',
        'shopify_created_at',
        'shopify_updated_at',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'subtotal_price' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'line_items' => 'array',
        'customer' => 'array',
        'shipping_address' => 'array',
        'shopify_created_at' => 'datetime',
        'shopify_updated_at' => 'datetime',
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }
}