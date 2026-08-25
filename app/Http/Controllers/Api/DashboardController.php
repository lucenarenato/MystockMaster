<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Models\Adjustment;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends BaseController
{
    /**
     * Get dashboard summary with key metrics.
     */
    public function index(Request $request)
    {
        try {
            $warehouse_id = $request->get('warehouse_id');

            // Total products
            $totalProducts = Product::count();

            // Total stock value
            $stockQuery = DB::table('product_warehouse')
                ->selectRaw('SUM(qty * cost) as total_value, SUM(qty) as total_quantity');

            if ($warehouse_id) {
                $stockQuery->where('warehouse_id', $warehouse_id);
            }

            $stock = $stockQuery->first();

            // Sales summary (this month)
            $salesThisMonth = Sale::whereMonth('date', now()->month)
                ->whereYear('date', now()->year);
            if ($warehouse_id) {
                $salesThisMonth->where('warehouse_id', $warehouse_id);
            }
            $salesThisMonth = $salesThisMonth->sum('total_amount') / 100;

            // Purchases summary (this month)
            $purchasesThisMonth = Purchase::whereMonth('date', now()->month)
                ->whereYear('date', now()->year);
            if ($warehouse_id) {
                $purchasesThisMonth->where('warehouse_id', $warehouse_id);
            }
            $purchasesThisMonth = $purchasesThisMonth->sum('total_amount') / 100;

            // Pending payments
            $pendingSales = Sale::where('payment_status', '!=', 1)->sum('due_amount') / 100;
            $pendingPurchases = Purchase::where('payment_status', '!=', 1)->sum('due_amount') / 100;

            // Low stock products
            $lowStockProducts = Product::whereHas('warehouses', function ($q) use ($warehouse_id) {
                $q->whereColumn('product_warehouse.qty', '<', 'products.stock_alert');
                if ($warehouse_id) {
                    $q->where('product_warehouse.warehouse_id', $warehouse_id);
                }
            })->with(['category', 'brand'])->get();

            // Recent sales
            $recentSales = Sale::with(['customer'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            // Recent purchases
            $recentPurchases = Purchase::with(['supplier'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            return $this->sendResponse([
                'summary' => [
                    'total_products'       => $totalProducts,
                    'total_stock_quantity'  => $stock->total_quantity ?? 0,
                    'total_stock_value'     => ($stock->total_value ?? 0) / 100,
                    'sales_this_month'      => $salesThisMonth,
                    'purchases_this_month'  => $purchasesThisMonth,
                    'pending_sales_payment' => $pendingSales,
                    'pending_purchases_payment' => $pendingPurchases,
                    'total_customers'       => Customer::count(),
                    'total_suppliers'       => Supplier::count(),
                    'total_warehouses'      => Warehouse::count(),
                ],
                'low_stock_products'  => $lowStockProducts,
                'recent_sales'        => $recentSales,
                'recent_purchases'    => $recentPurchases,
            ], 'Dashboard retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Get stock levels per warehouse.
     */
    public function stock(Request $request)
    {
        try {
            $query = DB::table('product_warehouse')
                ->join('products', 'products.id', '=', 'product_warehouse.product_id')
                ->join('warehouses', 'warehouses.id', '=', 'product_warehouse.warehouse_id')
                ->select(
                    'product_warehouse.product_id',
                    'product_warehouse.warehouse_id',
                    'products.name as product_name',
                    'products.code as product_code',
                    'products.quantity as min_quantity',
                    'products.stock_alert',
                    'warehouses.name as warehouse_name',
                    'product_warehouse.qty',
                    'product_warehouse.cost',
                    'product_warehouse.price'
                );

            if ($request->filled('warehouse_id')) {
                $query->where('product_warehouse.warehouse_id', $request->warehouse_id);
            }

            if ($request->filled('product_id')) {
                $query->where('product_warehouse.product_id', $request->product_id);
            }

            if ($request->filled('low_stock')) {
                $query->whereColumn('product_warehouse.qty', '<', 'products.stock_alert');
            }

            if ($request->filled('search')) {
                $query->where(function ($q) use ($request) {
                    $q->where('products.name', 'like', '%' . $request->search . '%')
                      ->orWhere('products.code', 'like', '%' . $request->search . '%');
                });
            }

            $stock = $query->orderBy('products.name')->paginate($request->get('per_page', 50));

            return $this->sendResponse($stock, 'Stock levels retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
