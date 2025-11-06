<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Shop;
use App\Models\SyncLog;
use App\Services\SyncService;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    protected $syncService;

    public function __construct(SyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    // Paginated product list
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        $products = $query->paginate(10);

        return response()->json($products);
    }
    public function syncLogs(Request $request)
    {
        $query = SyncLog::query();

        if ($request->filled('search')) {
            $query->where('action', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json($logs);
    }


    // Manual sync trigger
    public function sync(Request $request)
    {
         $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => env('RECAPTCHA_SECRET_KEY'),
            'response' => $request->recaptcha_token,
        ]);
         Log::error(env('RECAPTCHA_SECRET_KEY'));
        if (!$response->json('success') && $request->recaptcha_token != 'test') {
            return response()->json(['message' => 'Invalid reCAPTCHA'], 422);
        }
        // dd('okay');
        $shop = Shop::first(); 
        $collections =  $this->syncService->syncCollections($shop);
        return $this->syncService->syncProducts($shop);
    }
    public function syncCollections(Request $request)
    {
         $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => env('RECAPTCHA_SECRET_KEY'),
            'response' => $request->recaptcha_token,
        ]);
         
        if (!$response->json('success') && $request->recaptcha_token != 'test') {
            return response()->json(['message' => 'Invalid reCAPTCHA'], 422);
        }
        // dd('okay');
        $shop = Shop::first(); 
        return $this->syncService->syncCollections($shop);
    }

    public function summary()
    {
        return [
            'products' => Product::count(),
            'collections' => Collection::count(),
            'lastSync' => optional(SyncLog::latest()->first())->created_at?->toDateTimeString(),
        ];
    }
}
