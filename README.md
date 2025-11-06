# Shopify Embedded App (Laravel + React + Polaris)

This is a Shopify Embedded App built using **Laravel** for the backend and **React + Shopify Polaris** for the frontend. It allows merchants to view and manage products from your app, with proper Shopify OAuth 2.0 authentication.

---

## Features

* Shopify OAuth 2.0 authentication
* Embedded app support with App Bridge
* Product listing dashboard
* Collections, images, and variants handled via Laravel API
* Protected API endpoints with HMAC verification
* Recaptcha integration for product sync actions

---

## Prerequisites

* PHP >= 8.1
* Composer
* Node.js >= 18, npm >= 9
* MySQL or MariaDB
* Shopify Partner account and development store
* Ngrok (for local HTTPS tunnel)

---

## Setup Instructions

///There is issue regarding cors in ngrok url for react uis so need to access via localhost or 127.. once we install the shopify app from ngrok_url/auth/redirect this is hard-coded the redirection to localhost.

### 1️⃣ Clone the Repository

```bash
git clone https://github.com/yourusername/shopify-laravel-react-app.git
cd shopify-laravel-react-app
```

### 2️⃣ Install Laravel Dependencies

```bash
composer install
```

### 3️⃣ Install Node & React Dependencies

```bash
npm install
```

### 4️⃣ Create `.env` File

Copy `.env.example` to `.env`:

```bash
cp .env.example .env
```

Set the following variables:

```env
APP_NAME=ShopifyApp
APP_URL=https://your-ngrok-url.ngrok-free.dev

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=shopify_app
DB_USERNAME=root
DB_PASSWORD=

SHOPIFY_API_KEY=dumy-aa98e2bee58aa-dumy-2e470650c6fe17f3878-dumy
SHOPIFY_API_SECRET=dumy-shpss_f3e0cb6516-dumy-e0ec3620397990fc64ca40-dumy
SHOPIFY_SCOPES=read_orders,write_orders,read_product_listings,write_product_listings,read_products,write_products
SHOPIFY_REDIRECT_URI=http://unpatronisable-micki-plyingly.ngrok-free.dev/auth/callback
SHOPIFY_EMBEDDED_APP_URL=https://unpatronisable-micki-plyingly.ngrok-free.dev

VITE_RECAPTCHA_SITE_KEY=dumy-6Ld92QIsAAAAAGvcmOC_5vv-dumy-bAIlhpbQIVu6GqcVd-dumy
RECAPTCHA_SECRET_KEY=dumy-6Ld92QIsAAAAAF8X4qxcE-dumy-hu8fiHKPL9nBfzxgjf6-dumy
```

> ⚠️ Use **ngrok** for local HTTPS (`https://xyz.ngrok-free.dev`) because Shopify requires HTTPS.

### 5️⃣ Configure `config/services.php`

```php
'shopify' => [
    'client_id' => env('SHOPIFY_API_KEY'),
    'client_secret' => env('SHOPIFY_API_SECRET'),
    'scopes' => env('SHOPIFY_SCOPES', 'read_products,write_products'),
    'redirect_uri' => env('SHOPIFY_REDIRECT_URI'),
    'embedded_app_url' => env('SHOPIFY_EMBEDDED_APP_URL'),
],
```

### 6️⃣ Database Migration

Create the database (`shopify_app` in MySQL) and migrate:

```bash
php artisan migrate
```
```bash
php artisan db:seed --class=ShopifyShopsSeeder
```
### 7️⃣ Serve Laravel Locally with HTTPS

Use **ngrok** to expose your local server:

```bash
php artisan serve --host=127.0.0.1 --port=8000
ngrok http 8000
```

Copy the **ngrok HTTPS URL** and update `.env`:

```env
APP_URL=https://<ngrok-url>
SHOPIFY_REDIRECT_URI=https://<ngrok-url>/auth/callback
SHOPIFY_EMBEDDED_APP_URL=https://<ngrok-url>
```

### 8️⃣ Shopify Partner Dashboard Setup

1. Go to [Shopify Partner Dashboard → Apps → Your App → App setup](https://partners.shopify.com/).
2. Set **App URL**: `https://<ngrok-url>`
3. Add **Allowed redirect URL(s)**: `https://<ngrok-url>/auth/callback`
4. Make sure **API Key** and **API Secret** match your `.env`.

### 9️⃣ Install React Frontend

```bash
npm run dev
```

* By default, Vite serves React on `http://127.0.0.1:5173`.
* Ensure your Laravel Vite plugin points to your React entry (`resources/js/app.jsx`).

### 🔒 10️⃣ Middleware for Protected Routes

All API routes should use the `VerifyShopifyAuth` middleware:

```php
Route::middleware([VerifyShopifyAuth::class])->group(function () {
    Route::get('/api/products', [ProductController::class, 'index']);
});
```

* Checks `shop` parameter
* Validates HMAC (from Shopify)
* Redirects to install if shop not authorized

### 11️⃣ OAuth Flow

* Merchant opens:

```
https://<ngrok-url>/auth/install?shop=<shop>.myshopify.com
```

* Shopify prompts for app installation
* Callback hits `/auth/callback` → access token stored
* Redirects to your embedded app frontend

### 12️⃣ Recaptcha Integration

* Frontend: Add Recaptcha site key in React component for actions like product sync.
* Backend: Validate Recaptcha token via Google API before performing critical actions.

### 13️⃣ Example Routes

```php
Route::get('/auth/redirect', [ShopifyController::class, 'redirect'])->name('shopify.install');
Route::get('/auth/callback', [ShopifyController::class, 'callback'])->name('shopify.callback');

Route::middleware([VerifyShopifyAuth::class])->group(function () {
    Route::get('/api/products', [ProductController::class, 'index']);
});
```

### 14️⃣ Testing

1. Install app on development store.
2. Open the embedded app inside Shopify Admin.
3. Check `/api/products?shop=<shop>` returns products.
4. Test syncing products via frontend button → Recaptcha → backend API.

### 15️⃣ Useful Artisan Commands

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan serve
npm run dev
```


### 16️⃣ Notes

* Make sure **ngrok** URL is always HTTPS for Shopify.
* Once a shop is installed, you **don’t need to reinstall** to access dashboard; the access token is stored.
* HMAC verification ensures requests are genuinely from Shopify.
* use the env provided above.

## webhook subscription
```bash
curl -X POST "https://shop-domain.myshopify.com/admin/api/2025-10/webhooks.json" \
-H "X-Shopify-Access-Token: test-shpat_492df5562d40610-dumy-c83202b70ab1ee49c" \
-H "Content-Type: application/json" \
-d '{
  "webhook": {
    "topic": "products/update",
    "address": "https://ngrok-url.io/api/shopify/webhook",
    "format": "json"
  }
}
```

## useful curl
```bash
curl -X GET "https://samirmaharjan.myshopify.com/admin/api/2025-10/webhooks.json" \
  -H "X-Shopify-Access-Token: test-shpat_492df5562d4061-dumy-0c83202b70ab1ee49c" \
  -H "Content-Type: application/json"
  ```