<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ShopifyController extends Controller
{
    public function redirect(Request $request)
    {
        $shop = $request->get('shop');
        if (!$shop) {
            return "Shop parameter missing";
        }
        Log::info('Shopify Redirect Request for shop: ' . $shop);

        $scopes = env('SHOPIFY_SCOPES');
        $redirect_uri = env('SHOPIFY_REDIRECT_URI');
        $api_key = env('SHOPIFY_API_KEY');
        // dd( $redirect_uri);
        $install_url = "https://{$shop}/admin/oauth/authorize?client_id={$api_key}&scope={$scopes}&redirect_uri={$redirect_uri}&state=" . csrf_token();
        // dd($install_url);
        return redirect($install_url);
    }

    public function callback(Request $request)
    {
        // dd('Callback hit');
        $shop = $request->get('shop');
        $code = $request->get('code');
        $hmac = $request->get('hmac');

        // Validate HMAC
        $query = $request->except('hmac');
        ksort($query);
        $query_string = http_build_query($query);
        $calculated_hmac = hash_hmac('sha256', $query_string, env('SHOPIFY_API_SECRET'));

        if (!hash_equals($hmac, $calculated_hmac)) {
            abort(403, 'HMAC validation failed');
        }

        // Exchange code for access token
        $response = Http::post("https://{$shop}/admin/oauth/access_token", [
            'client_id' => env('SHOPIFY_API_KEY'),
            'client_secret' => env('SHOPIFY_API_SECRET'),
            'code' => $code,
        ]);
        Log::info('Shopify Access Token Response for shop: ' . $shop, $response->json());
        $access_token = $response->json()['access_token'];

        // Optional: fetch shop info
        $shop_info = Http::withHeaders([
            'X-Shopify-Access-Token' => $access_token
        ])->get("https://{$shop}/admin/api/2025-10/shop.json")->json()['shop'];

        // Save or update shop in DB
        $shopRecord = Shop::updateOrCreate(
            ['shop_domain' => $shop],
            [
                'access_token' => $access_token,
                'shop_name'    => $shop_info['name'] ?? null,
                'shop_email'   => $shop_info['email'] ?? null,
                'shop_owner'   => $shop_info['shop_owner'] ?? null,
                'currency'     => $shop_info['currency'] ?? null,
                'timezone'     => $shop_info['iana_timezone'] ?? null,
            ]
        );

        // Save in session
        Session::put('shopify_shop', $shopRecord->shop_domain);
        Session::put('shopify_access_token', $shopRecord->access_token);

        // return redirect('/dashboard');
        return redirect()->to('http://localhost:8000');
    }
}
