<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

use function Illuminate\Log\log;

class VerifyShopifyAuth
{
    public function handle(Request $request, Closure $next)
    {
        // dd('Middleware hit');
        $shop = $request->get('shop')??'v6wcpk-9u.myshopify.com';
        // Log::info('Verifying Shopify Auth for shop: ' . $shop);

        // Check session first
        if (Session::has('shopify_access_token') && Session::has('shopify_shop')) {
            return $next($request);
        }

        // Check DB if session not present
        if ($shop) {
            $shopRecord = Shop::where('shop_domain', $shop)->first();
            if ($shopRecord) {
                Session::put('shopify_shop', $shopRecord->shop_domain);
                Session::put('shopify_access_token', $shopRecord->access_token);
                return $next($request);
            }
        }

        // Redirect to OAuth if nothing found
        if ($shop) {
            $embeddedAppUrl = env('SHOPIFY_EMBEDDED_APP_URL');
             return redirect()->to($embeddedAppUrl.'/auth/redirect?shop=' . $shop);
            // return redirect($embeddedAppUrl)->route('shopify.redirect', ['shop' => $shop]);
        }

        return abort(400, 'Shop parameter missing');
    }
}
