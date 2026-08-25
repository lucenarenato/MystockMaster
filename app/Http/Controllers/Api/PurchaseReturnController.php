<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StorePurchaseReturnRequest;
use App\Models\ProductWarehouse;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnDetail;
use App\Models\PurchaseReturnPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseReturnController extends BaseController
{
    /**
     * List all purchase returns.
     */
    public function index(Request $request)
    {
        try {
            $query = PurchaseReturn::with(['supplier']);

            if ($request->filled('supplier_id')) {
                $query->where('supplier_id', $request->supplier_id);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
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

            $returns = $query->paginate($request->get('per_page', 15));

            return $this->sendResponse($returns, 'Purchase returns list retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Store a new purchase return.
     */
    public function store(StorePurchaseReturnRequest $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();

            $total_amount = $validated['total_amount'];
            $paid_amount = $validated['paid_amount'] ?? 0;
            $due_amount = $total_amount - $paid_amount;

            $purchaseReturn = PurchaseReturn::create([
                'date'                => $validated['date'],
                'supplier_id'         => $validated['supplier_id'],
                'user_id'             => Auth::id(),
                'warehouse_id'        => $validated['warehouse_id'],
                'tax_percentage'      => $validated['tax_percentage'] ?? 0,
                'discount_percentage' => $validated['discount_percentage'] ?? 0,
                'shipping_amount'     => ($validated['shipping_amount'] ?? 0) * 100,
                'total_amount'        => $total_amount * 100,
                'paid_amount'         => $paid_amount * 100,
                'due_amount'          => $due_amount * 100,
                'status'              => $due_amount <= 0 ? 2 : 0,
                'payment_status'      => $due_amount <= 0 ? 1 : ($paid_amount > 0 ? 2 : 0),
                'payment_method'      => $validated['payment_method'] ?? 'cash',
                'note'                => $validated['note'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                PurchaseReturnDetail::create([
                    'purchase_return_id' => $purchaseReturn->id,
                    'product_id'         => $item['product_id'],
                    'warehouse_id'       => $validated['warehouse_id'],
                    'quantity'           => $item['quantity'],
                    'price'              => $item['price'] * 100,
                    'sub_total'          => ($item['price'] * $item['quantity']) * 100,
                ]);

                $productWarehouse = ProductWarehouse::where('product_id', $item['product_id'])
                    ->where('warehouse_id', $validated['warehouse_id'])
                    ->first();

                if ($productWarehouse) {
                    $productWarehouse->update([
                        'qty' => max(0, $productWarehouse->qty - $item['quantity']),
                    ]);
                }
            }

            if ($paid_amount > 0) {
                PurchaseReturnPayment::create([
                    'date'                => $validated['date'],
                    'amount'              => $paid_amount * 100,
                    'purchase_return_id'  => $purchaseReturn->id,
                    'payment_method'      => $validated['payment_method'] ?? 'cash',
                    'user_id'             => Auth::id(),
                ]);
            }

            DB::commit();

            $purchaseReturn->load(['supplier', 'purchaseReturnDetails.product']);

            return $this->sendResponse($purchaseReturn, 'Purchase return created successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), [], 422);
        }
    }

    /**
     * Display a specific purchase return.
     */
    public function show($id)
    {
        try {
            $purchaseReturn = PurchaseReturn::with(['supplier', 'purchaseReturnDetails.product', 'purchaseReturnPayments'])->find($id);

            if (is_null($purchaseReturn)) {
                return $this->sendError('Purchase return not found');
            }

            return $this->sendResponse($purchaseReturn, 'Purchase return retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Update a purchase return.
     */
    public function update(Request $request, $id)
    {
        try {
            $purchaseReturn = PurchaseReturn::findOrFail($id);
            $purchaseReturn->update($request->only(['note', 'status', 'payment_status']));

            return $this->sendResponse($purchaseReturn, 'Purchase return updated successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], 422);
        }
    }

    /**
     * Delete a purchase return.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $purchaseReturn = PurchaseReturn::findOrFail($id);

            foreach ($purchaseReturn->purchaseReturnDetails as $detail) {
                $productWarehouse = ProductWarehouse::where('product_id', $detail->product_id)
                    ->where('warehouse_id', $detail->warehouse_id)
                    ->first();

                if ($productWarehouse) {
                    $productWarehouse->update([
                        'qty' => $productWarehouse->qty + $detail->quantity,
                    ]);
                }
            }

            $purchaseReturn->purchaseReturnDetails()->delete();
            $purchaseReturn->purchaseReturnPayments()->delete();
            $purchaseReturn->delete();

            DB::commit();

            return $this->sendResponse([], 'Purchase return deleted successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage());
        }
    }
}
