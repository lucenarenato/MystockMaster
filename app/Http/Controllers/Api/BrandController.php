<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StoreBrandRequest;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BrandController extends BaseController
{
    /**
     * List all brands.
     */
    public function index(Request $request)
    {
        try {
            $query = Brand::with('products');

            if ($request->filled('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            $sort = $request->get('sort', 'name');
            $order = $request->get('order', 'asc');
            $query->orderBy($sort, $order);

            $brands = $query->paginate($request->get('per_page', 15));

            return $this->sendResponse($brands, 'Brands list retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Store a new brand.
     */
    public function store(StoreBrandRequest $request)
    {
        DB::beginTransaction();
        try {
            $brand = Brand::create($request->validated());

            DB::commit();

            return $this->sendResponse($brand, 'Brand created successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), [], 422);
        }
    }

    /**
     * Display a specific brand.
     */
    public function show($id)
    {
        try {
            $brand = Brand::with('products')->find($id);

            if (is_null($brand)) {
                return $this->sendError('Brand not found');
            }

            return $this->sendResponse($brand, 'Brand retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Update a brand.
     */
    public function update(Request $request, $id)
    {
        try {
            $brand = Brand::findOrFail($id);
            $brand->update($request->only(['name', 'description', 'image']));

            return $this->sendResponse($brand, 'Brand updated successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], 422);
        }
    }

    /**
     * Delete a brand.
     */
    public function destroy($id)
    {
        try {
            $brand = Brand::findOrFail($id);
            $brand->delete();

            return $this->sendResponse([], 'Brand deleted successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
