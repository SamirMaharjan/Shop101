<?php

namespace App\Services;

use App\Models\Collection;
use App\Models\Product;
use App\Models\SyncLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Stmt\TryCatch;

class SyncService
{
    protected $shopifyService;

    public function __construct(ShopifyService $shopifyService)
    {
        $this->shopifyService = $shopifyService;
    }

    public function syncProducts($shop)
    {
        $log = SyncLog::create([
            'shop_id' => $shop->id,
            'type' => 'products',
            'synced_at' => now(),
            'status' => 'started',
        ]);
        $response = $this->shopifyService->fetchAllProducts($shop->shop_domain, $shop->access_token);
        // dd($response);
        if (!isset($response['data']['products']['edges'])  && count($response) === 0 ) {
        // if (!isset($response['data']['products']['edges']) ) {
            Log::error("Failed to sync products for {$shop->shop_domain}");
            $log->update([
                'status' => 'failed',
                'error_message' => 'All product syncs failed.'
            ]);
            return response()->json(['message' => 'Products synced Failed']);
        }

        // $log->update(['status' => 'in_progress']);
        // foreach ($response['data']['products']['edges'] as $edge) {
        //     $node = $edge['node'];

        //     Product::updateOrCreate(
        //         [
        //             'shop_id' => $shop->id,
        //             'shopify_product_id' => $node['id'],
        //             'title' => $node['title'],
        //             'status' => $node['status'],
        //         ]
        //     );
        // }
        $success_counter = 0;
        $fail_counter = 0;
        foreach ($response as $product) {
            DB::beginTransaction();
            try {
                Log::info($product['images']);
                Product::updateOrCreate(
                    [
                        'shop_id' => $shop->id,
                        'shopify_product_id' => $product['id'],
                    ],
                    [
                        'title' => $product['title'] ?? null,
                        'status' => $product['status'] ?? null,
                        'tags' => $product['tags'] ?? [],
                        'price' => $product['variants'][0]['price'] ?? null,
                        'images' => array_values($product['images'] ?? []),
                    ]
                );
                DB::commit();
                $success_counter++;
            } catch (\Exception $e) {
                $success_counter--;
                DB::rollBack();
                $fail_counter++;
                $log->update([
                    'status' => 'failed',
                    'error_message' => 'Error syncing product ID ' . $product['id'] . ': ' . $e->getMessage()
                ]);
                Log::error('Error logging product info: ' . $e->getMessage());
            }
        }
        // dd($success_counter, $fail_counter);
        if ($fail_counter > 0 && $success_counter == 0) {
            $log->update([
                'status' => 'failed',
                'error_message' => 'All product syncs failed.'
            ]);
            return response()->json(['message' => 'Products synced Failed']);
        } else {

            $log->update([
                'status' => 'completed',
                'completed_at' => now(),
                'records_synced' => $success_counter,
            ]);
        }
        return response()->json(['message' => 'Products synced successfully']);
    }
    public function syncCollections($shop)
    {
        $log = SyncLog::create([
            'shop_id' => $shop->id,
            'sync_type' => 'collections',
            'synced_at' => now(),
            'status' => 'started',
        ]);
        $response = $this->shopifyService->fetchAllCollections($shop->shop_domain, $shop->access_token);
        // dd($response);
        if (!isset($response['data']['collections']['edges'])  && count($response) === 0 ) {
        // if (!isset($response['data']['products']['edges']) ) {
            Log::error("Failed to sync products for {$shop->shop_domain}");
            $log->update([
                'status' => 'failed',
                'error_message' => 'All product syncs failed.'
            ]);
            return response()->json(['message' => 'Products synced Failed']);
        }

        // $log->update(['status' => 'in_progress']);
        // foreach ($response['data']['products']['edges'] as $edge) {
        //     $node = $edge['node'];

        //     Product::updateOrCreate(
        //         [
        //             'shop_id' => $shop->id,
        //             'shopify_product_id' => $node['id'],
        //             'title' => $node['title'],
        //             'status' => $node['status'],
        //         ]
        //     );
        // }
        $success_counter = 0;
        $fail_counter = 0;
        foreach ($response as $collection) {
            DB::beginTransaction();
            try {
                Collection::updateOrCreate(
                    [
                        'shop_id' => $shop->id,
                        'shopify_collection_id' => $collection['id'],
                    ],
                    [
                        'title' => $collection['title'] ?? null
                    ]
                );
                DB::commit();
                $success_counter++;
            } catch (\Exception $e) {
                $success_counter--;
                DB::rollBack();
                $fail_counter++;
                $log->update([
                    'status' => 'failed',
                    'error_message' => 'Error syncing collection ID ' . $collection['id'] . ': ' . $e->getMessage()
                ]);
                Log::error('Error logging collection info: ' . $e->getMessage());
            }
        }
        // dd($success_counter, $fail_counter);
        if ($fail_counter > 0 && $success_counter == 0) {
            $log->update([
                'status' => 'failed',
                'error_message' => 'All product syncs failed.'
            ]);
            return response()->json(['message' => 'Products synced Failed']);
        } else {

            $log->update([
                'status' => 'completed',
                'completed_at' => now(),
                'records_synced' => $success_counter,
            ]);
        }
        return response()->json(['message' => 'Products synced successfully']);
    }

    public function updateLocalProduct($shopDomain, $payload)
    {
        if (isset($payload['id'])) {
            Product::updateOrCreate(
                ['shopify_id' => $payload['id']],
                [
                    'title' => $payload['title'] ?? '',
                    'status' => $payload['status'] ?? 'active',
                ]
            );
        }
    }
}
