<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->string('shop_domain')->unique();
            $table->text('access_token');
            $table->string('shop_name')->nullable();
            $table->string('shop_email')->nullable();
            $table->string('shop_owner')->nullable();
            $table->string('currency')->nullable();
            $table->string('timezone')->nullable();
            $table->timestamp('last_sync_at')->nullable();
            $table->timestamps();
            
            $table->index('shop_domain');
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->onDelete('cascade');
            $table->string('shopify_product_id')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('vendor')->nullable();
            $table->string('product_type')->nullable();
            $table->enum('status', ['active', 'draft', 'archived'])->default('active');
            $table->json('tags')->nullable();
            $table->json('images')->nullable();
            $table->json('variants')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('inventory_quantity')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('shopify_created_at')->nullable();
            $table->timestamp('shopify_updated_at')->nullable();
            $table->timestamps();
            
            $table->index('shop_id');
            $table->index('status');
            $table->index('title');
        });

        Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->onDelete('cascade');
            $table->string('shopify_collection_id')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('handle')->nullable();
            $table->enum('collection_type', ['smart', 'custom'])->default('custom');
            $table->integer('products_count')->default(0);
            $table->json('image')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('shopify_created_at')->nullable();
            $table->timestamp('shopify_updated_at')->nullable();
            $table->timestamps();
            
            $table->index('shop_id');
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->onDelete('cascade');
            $table->string('shopify_order_id')->unique();
            $table->string('order_number');
            $table->string('email')->nullable();
            $table->decimal('total_price', 10, 2);
            $table->decimal('subtotal_price', 10, 2);
            $table->decimal('total_tax', 10, 2)->nullable();
            $table->string('currency');
            $table->string('financial_status')->nullable();
            $table->string('fulfillment_status')->nullable();
            $table->json('line_items')->nullable();
            $table->json('customer')->nullable();
            $table->json('shipping_address')->nullable();
            $table->timestamp('shopify_created_at')->nullable();
            $table->timestamp('shopify_updated_at')->nullable();
            $table->timestamps();
            
            $table->index('shop_id');
            $table->index('order_number');
        });

        Schema::create('sync_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->onDelete('cascade');
            $table->enum('sync_type', ['products', 'collections', 'orders']);
            $table->enum('status', ['started', 'completed', 'failed']);
            $table->integer('records_synced')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->index(['shop_id', 'sync_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_logs');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('collections');
        Schema::dropIfExists('products');
        Schema::dropIfExists('shops');
    }
};