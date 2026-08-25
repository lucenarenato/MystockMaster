<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StoreAdjustmentRequest;
use App\Models\AdjustedProduct;
use App\Models\Adjustment;
use App\Models\ProductWarehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdjustmentController extends BaseController
{
    /**
     * List all adjustments with optional filters.
     */
    public function index(Request $request)
    {
        try {
            $query = Adjustment::with(['adjustedProducts.product', 'adjustedProducts.warehouse']);

            if ($request->filled('warehouse_id')) {
                $query->whereHas('adjustedProducts', function ($q) use ($request) {
                    $q->where('warehouse_id', $request->warehouse_id);
                });
            }

            if ($request->filled('date_from')) {
                $query->whereDate('date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('date', '<=', $request->date_to);
            }

            if ($request->filled('search')) {
                $query->where('reference', 'like', '%' . $request->search . '%');
            }

            $sort = $request->get('sort', 'id');
            $order = $request->get('order', 'desc');
            $query->orderBy($sort, $order);

            $adjustments = $query->paginate($request->get('per_page', 15));

            return $this->sendResponse($adjustments, 'Adjustments list retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Store a new adjustment (balanço/contagem de estoque).
     */
    public function store(StoreAdjustmentRequest $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();

            $adjustment = Adjustment::create([
                'date' => $validated['date'],
                'note' => $validated['note'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                AdjustedProduct::create([
                    'adjustment_id' => $adjustment->id,
                    'product_id'    => $item['product_id'],
                    'warehouse_id'  => $validated['warehouse_id'],
                    'quantity'      => $item['quantity'],
                    'type'          => $item['type'],
                ]);

                $productWarehouse = ProductWarehouse::where('product_id', $item['product_id'])
                    ->where('warehouse_id', $validated['warehouse_id'])
                    ->first();

                if ($productWarehouse) {
                    if ($item['type'] === 'add') {
                        $productWarehouse->update([
                            'qty' => $productWarehouse->qty + $item['quantity'],
                        ]);
                    } else {
                        $productWarehouse->update([
                            'qty' => max(0, $productWarehouse->qty - $item['quantity']),
                        ]);
                    }
                }
            }

            DB::commit();

            $adjustment->load(['adjustedProducts.product', 'adjustedProducts.warehouse']);

            return $this->sendResponse($adjustment, 'Adjustment created successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), [], 422);
        }
    }

    /**
     * Display a specific adjustment.
     */
    public function show($id)
    {
        try {
            $adjustment = Adjustment::with(['adjustedProducts.product', 'adjustedProducts.warehouse'])->find($id);

            if (is_null($adjustment)) {
                return $this->sendError('Adjustment not found');
            }

            return $this->sendResponse($adjustment, 'Adjustment retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Update an adjustment.
     */
    public function update(Request $request, $id)
    {
        try {
            $adjustment = Adjustment::findOrFail($id);
            $adjustment->update($request->only(['date', 'note']));

            $adjustment->load(['adjustedProducts.product', 'adjustedProducts.warehouse']);

            return $this->sendResponse($adjustment, 'Adjustment updated successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], 422);
        }
    }

    /**
     * Delete an adjustment.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $adjustment = Adjustment::findOrFail($id);

            foreach ($adjustment->adjustedProducts as $adjusted) {
                $productWarehouse = ProductWarehouse::where('product_id', $adjusted->product_id)
                    ->where('warehouse_id', $adjusted->warehouse_id)
                    ->first();

                if ($productWarehouse) {
                    if ($adjusted->type === 'add') {
                        $productWarehouse->update([
                            'qty' => max(0, $productWarehouse->qty - $adjusted->quantity),
                        ]);
                    } else {
                        $productWarehouse->update([
                            'qty' => $productWarehouse->qty + $adjusted->quantity,
                        ]);
                    }
                }
            }

            $adjustment->adjustedProducts()->delete();
            $adjustment->delete();

            DB::commit();

            return $this->sendResponse([], 'Adjustment deleted successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage());
        }
    }
}
