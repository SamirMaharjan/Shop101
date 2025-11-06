<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShopifyWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Verify Shopify webhook (optional, but recommended)
        Log::info($request->all());
        $hmacHeader = $request->header('X-Shopify-Hmac-Sha256');
        $calculatedHmac = base64_encode(hash_hmac('sha256', $request->getContent(), env('SHOPIFY_WEBHOOK_SECRET'), true));

        if (!hash_equals($hmacHeader, $calculatedHmac)) {
            Log::warning('Invalid Shopify webhook signature.');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        Log::info('Shopify webhook received:', $request->all());

        return response()->json(['status' => 'ok']);
    }
}

