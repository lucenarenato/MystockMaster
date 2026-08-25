<?php

declare(strict_types=1);

use App\Http\Controllers\Api\ProductController as ProductApi;
use App\Http\Controllers\Api\CustomerController as CustomerApi;
use App\Http\Controllers\Api\CategoryController as CategoryApi;
use App\Http\Controllers\Api\SupplierController as SupplierApi;
use App\Http\Controllers\Api\ExpenseController as ExpenseApi;
use App\Http\Controllers\Api\RoleController as RoleApi;
use App\Http\Controllers\Api\WarehouseController as WarehouseApi;
use App\Http\Controllers\Api\AuthController as AuthApi;
use App\Http\Controllers\Api\SaleController as SaleApi;
use App\Http\Controllers\Api\PurchaseController as PurchaseApi;
use App\Http\Controllers\Api\AdjustmentController as AdjustmentApi;
use App\Http\Controllers\Api\TransferController as TransferApi;
use App\Http\Controllers\Api\SaleReturnController as SaleReturnApi;
use App\Http\Controllers\Api\PurchaseReturnController as PurchaseReturnApi;
use App\Http\Controllers\Api\BrandController as BrandApi;
use App\Http\Controllers\Api\DashboardController as DashboardApi;
use App\Http\Controllers\Api\ProductSearchController as ProductSearchApi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// ─── Auth (public) ───────────────────────────────────────────────────────────

// Register a new user and create token access
Route::post('/register', [AuthApi::class, 'register']);

// Create user access token
Route::post('/sanctum/token', function (Request $request) {
    $request->validate([
        'email'       => 'required|email',
        'password'    => 'required',
        'device_name' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    return $user->createToken($request->device_name)->plainTextToken;
});

// Login
Route::post('/login', [AuthApi::class, 'login']);

// ─── Protected Routes (auth + tenant) ────────────────────────────────────────

Route::middleware(['auth:sanctum', 'setTenant', 'ensure.same.tenant'])->group(function () {

    // ── Dashboard ──────────────────────────────────────────────────────────
    Route::get('/dashboard', [DashboardApi::class, 'index']);
    Route::get('/dashboard/stock', [DashboardApi::class, 'stock']);

    // ── Product Search / Barcode ───────────────────────────────────────────
    Route::get('/products/search', [ProductSearchApi::class, 'search']);
    Route::get('/products/barcode', [ProductSearchApi::class, 'barcode']);
    Route::get('/products/{id}/details', [ProductSearchApi::class, 'show']);

    // ── CRUD Resources ─────────────────────────────────────────────────────
    Route::apiResource('products', ProductApi::class);
    Route::apiResource('categories', CategoryApi::class);
    Route::apiResource('customers', CustomerApi::class);
    Route::apiResource('suppliers', SupplierApi::class);
    Route::apiResource('expenses', ExpenseApi::class);
    Route::apiResource('roles', RoleApi::class);
    Route::apiResource('warehouses', WarehouseApi::class);
    Route::apiResource('brands', BrandApi::class);

    // ── Sales ──────────────────────────────────────────────────────────────
    Route::apiResource('sales', SaleApi::class);

    // ── Purchases ──────────────────────────────────────────────────────────
    Route::apiResource('purchases', PurchaseApi::class);

    // ── Stock Adjustments (Balanço) ────────────────────────────────────────
    Route::apiResource('adjustments', AdjustmentApi::class);

    // ── Stock Transfers ────────────────────────────────────────────────────
    Route::apiResource('transfers', TransferApi::class);

    // ── Returns ────────────────────────────────────────────────────────────
    Route::apiResource('sale-returns', SaleReturnApi::class);
    Route::apiResource('purchase-returns', PurchaseReturnApi::class);
});
