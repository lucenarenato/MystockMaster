<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StoreSaleReturnRequest;
use App\Models\ProductWarehouse;
use App\Models\SaleReturn;
use App\Models\SaleReturnDetail;
use App\Models\SaleReturnPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleReturnController extends BaseController
{
    /**
     * List all sale returns.
     */
    public function index(Request $request)
    {
        try {
            $query = SaleReturn::with(['customer']);

            if ($request->filled('customer_id')) {
                $query->where('customer_id', $request->customer_id);
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

            return $this->sendResponse($returns, 'Sale returns list retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Store a new sale return.
     */
    public function store(StoreSaleReturnRequest $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();

            $total_amount = $validated['total_amount'];
            $paid_amount = $validated['paid_amount'] ?? 0;
            $due_amount = $total_amount - $paid_amount;

            $saleReturn = SaleReturn::create([
                'date'                => $validated['date'],
                'customer_id'         => $validated['customer_id'],
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
                SaleReturnDetail::create([
                    'sale_return_id' => $saleReturn->id,
                    'product_id'     => $item['product_id'],
                    'warehouse_id'   => $validated['warehouse_id'],
                    'quantity'       => $item['quantity'],
                    'price'          => $item['price'] * 100,
                    'sub_total'      => ($item['price'] * $item['quantity']) * 100,
                ]);

                $productWarehouse = ProductWarehouse::where('product_id', $item['product_id'])
                    ->where('warehouse_id', $validated['warehouse_id'])
                    ->first();

                if ($productWarehouse) {
                    $productWarehouse->update([
                        'qty' => $productWarehouse->qty + $item['quantity'],
                    ]);
                }
            }

            if ($paid_amount > 0) {
                SaleReturnPayment::create([
                    'date'           => $validated['date'],
                    'amount'         => $paid_amount * 100,
                    'sale_return_id' => $saleReturn->id,
                    'payment_method' => $validated['payment_method'] ?? 'cash',
                    'user_id'        => Auth::id(),
                ]);
            }

            DB::commit();

            $saleReturn->load(['customer', 'saleReturnDetails.product']);

            return $this->sendResponse($saleReturn, 'Sale return created successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), [], 422);
        }
    }

    /**
     * Display a specific sale return.
     */
    public function show($id)
    {
        try {
            $saleReturn = SaleReturn::with(['customer', 'saleReturnDetails.product', 'saleReturnPayments'])->find($id);

            if (is_null($saleReturn)) {
                return $this->sendError('Sale return not found');
            }

            return $this->sendResponse($saleReturn, 'Sale return retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Update a sale return.
     */
    public function update(Request $request, $id)
    {
        try {
            $saleReturn = SaleReturn::findOrFail($id);
            $saleReturn->update($request->only(['note', 'status', 'payment_status']));

            return $this->sendResponse($saleReturn, 'Sale return updated successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], 422);
        }
    }

    /**
     * Delete a sale return.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $saleReturn = SaleReturn::findOrFail($id);

            foreach ($saleReturn->saleReturnDetails as $detail) {
                $productWarehouse = ProductWarehouse::where('product_id', $detail->product_id)
                    ->where('warehouse_id', $detail->warehouse_id)
                    ->first();

                if ($productWarehouse) {
                    $productWarehouse->update([
                        'qty' => max(0, $productWarehouse->qty - $detail->quantity),
                    ]);
                }
            }

            $saleReturn->saleReturnDetails()->delete();
            $saleReturn->saleReturnPayments()->delete();
            $saleReturn->delete();

            DB::commit();

            return $this->sendResponse([], 'Sale return deleted successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage());
        }
    }
}
