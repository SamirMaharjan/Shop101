<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_domain',
        'access_token',
        'shop_name',
        'shop_email',
        'shop_owner',
        'currency',
        'timezone',
        'last_sync_at',
    ];

    protected $hidden = [
        'access_token',
    ];

    protected $casts = [
        'last_sync_at' => 'datetime',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function collections(): HasMany
    {
        return $this->hasMany(Collection::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function syncLogs(): HasMany
    {
        return $this->hasMany(SyncLog::class);
    }

    public function getApiUrl(string $endpoint): string
    {
        return "https://{$this->shop_domain}/admin/api/" . config('services.shopify.api_version') . "/{$endpoint}";
    }
}