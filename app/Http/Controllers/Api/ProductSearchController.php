<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductSearchController extends BaseController
{
    /**
     * Search products by name, code, or barcode.
     * Useful for barcode scanning and quick product lookup.
     */
    public function search(Request $request)
    {
        try {
            $query = Product::with(['category', 'brand']);

            if ($request->filled('barcode')) {
                // Search by barcode/code exactly
                $product = Product::where('code', $request->barcode)
                    ->with(['category', 'brand', 'warehouses'])
                    ->first();

                if (! $product) {
                    return $this->sendError('Product not found with this barcode');
                }

                return $this->sendResponse($product, 'Product found');
            }

            if ($request->filled('q')) {
                $search = $request->q;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('code', 'like', '%' . $search . '%')
                      ->orWhere('barcode_symbology', 'like', '%' . $search . '%');
                });
            }

            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->filled('brand_id')) {
                $query->where('brand_id', $request->brand_id);
            }

            // Only show active products
            $query->where('status', 1);

            $products = $query->paginate($request->get('per_page', 20));

            return $this->sendResponse($products, 'Products found');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Get product details with stock info per warehouse.
     */
    public function show($id)
    {
        try {
            $product = Product::with(['category', 'brand', 'warehouses'])->find($id);

            if (is_null($product)) {
                return $this->sendError('Product not found');
            }

            return $this->sendResponse($product, 'Product details retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Quick barcode lookup - returns minimal data for fast scanning.
     */
    public function barcode(Request $request)
    {
        try {
            $request->validate([
                'code' => 'required|string',
            ]);

            $product = Product::where('code', $request->code)
                ->select('id', 'name', 'code', 'price', 'cost', 'quantity', 'unit', 'stock_alert')
                ->with(['warehouses' => function ($q) {
                    $q->select('warehouses.id', 'warehouses.name')
                      ->pivot('qty', 'price', 'cost');
                }])
                ->first();

            if (! $product) {
                return $this->sendError('Product not found', [], 404);
            }

            return $this->sendResponse([
                'product'      => $product,
                'total_stock'  => $product->total_quantity,
                'avg_price'    => $product->average_price,
                'avg_cost'     => $product->average_cost,
            ], 'Product found');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
