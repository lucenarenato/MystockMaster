<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\MovementType;
use App\Enums\PaymentStatus;
use App\Enums\PurchaseStatus;
use App\Http\Requests\Api\StorePurchaseRequest;
use App\Http\Requests\Api\UpdatePurchaseRequest;
use App\Models\Movement;
use App\Models\Product;
use App\Models\ProductWarehouse;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\PurchasePayment;
use App\Services\TenantLimits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseController extends BaseController
{
    /**
     * List all purchases with optional filters and pagination.
     */
    public function index(Request $request)
    {
        try {
            $query = Purchase::with(['supplier', 'user']);

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('payment_status')) {
                $query->where('payment_status', $request->payment_status);
            }

            if ($request->filled('supplier_id')) {
                $query->where('supplier_id', $request->supplier_id);
            }

            if ($request->filled('warehouse_id')) {
                $query->where('warehouse_id', $request->warehouse_id);
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

            $purchases = $query->paginate($request->get('per_page', 15));

            return $this->sendResponse($purchases, 'Purchases list retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Store a new purchase.
     */
    public function store(StorePurchaseRequest $request)
    {
        DB::beginTransaction();
        try {
            app(TenantLimits::class)->check('purchases');

            $validated = $request->validated();

            $total_amount = 0;
            $total_tax = 0;
            $total_discount = 0;

            foreach ($validated['items'] as $item) {
                $item_total = $item['price'] * $item['quantity'];
                $item_discount = $item['discount'] ?? 0;
                $item_tax = $item['tax'] ?? 0;
                $total_discount += $item_discount;
                $total_tax += $item_tax;
                $total_amount += ($item_total - $item_discount + $item_tax);
            }

            $total_amount += ($validated['shipping_amount'] ?? 0);
            $paid_amount = $validated['paid_amount'] ?? 0;
            $due_amount = $total_amount - $paid_amount;

            if ($due_amount <= 0) {
                $payment_status = PaymentStatus::PAID;
                $purchase_status = PurchaseStatus::COMPLETED;
            } elseif ($paid_amount > 0) {
                $payment_status = PaymentStatus::PARTIAL;
                $purchase_status = PurchaseStatus::PENDING;
            } else {
                $payment_status = PaymentStatus::PENDING;
                $purchase_status = PurchaseStatus::PENDING;
            }

            $purchase = Purchase::create([
                'date'                => $validated['date'],
                'supplier_id'         => $validated['supplier_id'],
                'warehouse_id'        => $validated['warehouse_id'],
                'user_id'             => Auth::id(),
                'tax_percentage'      => $validated['tax_percentage'] ?? 0,
                'tax_amount'          => $total_tax * 100,
                'discount_percentage' => $validated['discount_percentage'] ?? 0,
                'discount_amount'     => $total_discount * 100,
                'shipping_amount'     => ($validated['shipping_amount'] ?? 0) * 100,
                'total_amount'        => $total_amount * 100,
                'paid_amount'         => $paid_amount * 100,
                'due_amount'          => $due_amount * 100,
                'status'              => $purchase_status,
                'payment_status'      => $payment_status,
                'payment_method'      => $validated['payment_method'] ?? 'cash',
                'note'                => $validated['note'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $sub_total = ($item['price'] * $item['quantity']) - ($item['discount'] ?? 0) + ($item['tax'] ?? 0);

                PurchaseDetail::create([
                    'purchase_id'             => $purchase->id,
                    'product_id'              => $item['product_id'],
                    'warehouse_id'            => $validated['warehouse_id'],
                    'name'                    => $product->name,
                    'code'                    => $product->code,
                    'quantity'                => $item['quantity'],
                    'price'                   => $item['price'] * 100,
                    'unit_price'              => $item['cost'] * 100,
                    'sub_total'               => $sub_total * 100,
                    'product_discount_amount' => ($item['discount'] ?? 0) * 100,
                    'product_discount_type'   => 'fixed',
                    'product_tax_amount'      => ($item['tax'] ?? 0) * 100,
                ]);

                $productWarehouse = ProductWarehouse::where('product_id', $product->id)
                    ->where('warehouse_id', $validated['warehouse_id'])
                    ->first();

                if (! $productWarehouse) {
                    $productWarehouse = new ProductWarehouse([
                        'product_id'   => $product->id,
                        'warehouse_id' => $validated['warehouse_id'],
                        'price'        => $item['price'] * 100,
                        'cost'         => $item['cost'] * 100,
                        'qty'          => 0,
                    ]);
                }

                $productWarehouse->update([
                    'qty'  => $productWarehouse->qty + $item['quantity'],
                    'cost' => $item['cost'] * 100,
                ]);

                Movement::create([
                    'type'         => MovementType::PURCHASE,
                    'quantity'     => $item['quantity'],
                    'price'        => $item['price'] * 100,
                    'date'         => $validated['date'],
                    'movable_type' => get_class($product),
                    'movable_id'   => $product->id,
                    'user_id'      => Auth::id(),
                ]);
            }

            if ($paid_amount > 0) {
                PurchasePayment::create([
                    'date'           => $validated['date'],
                    'user_id'        => Auth::id(),
                    'amount'         => $paid_amount * 100,
                    'purchase_id'    => $purchase->id,
                    'payment_method' => $validated['payment_method'] ?? 'cash',
                ]);
            }

            DB::commit();

            $purchase->load(['supplier', 'user', 'purchaseDetails.product']);

            return $this->sendResponse($purchase, 'Purchase created successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), [], 422);
        }
    }

    /**
     * Display a specific purchase.
     */
    public function show($id)
    {
        try {
            $purchase = Purchase::with(['supplier', 'user', 'purchaseDetails.product', 'purchasePayments'])->find($id);

            if (is_null($purchase)) {
                return $this->sendError('Purchase not found');
            }

            return $this->sendResponse($purchase, 'Purchase retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Update a purchase.
     */
    public function update(UpdatePurchaseRequest $request, $id)
    {
        try {
            $purchase = Purchase::findOrFail($id);
            $purchase->update($request->validated());

            $purchase->load(['supplier', 'user', 'purchaseDetails.product']);

            return $this->sendResponse($purchase, 'Purchase updated successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], 422);
        }
    }

    /**
     * Delete a purchase.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $purchase = Purchase::findOrFail($id);

            foreach ($purchase->purchaseDetails as $detail) {
                $productWarehouse = ProductWarehouse::where('product_id', $detail->product_id)
                    ->where('warehouse_id', $detail->warehouse_id)
                    ->first();

                if ($productWarehouse) {
                    $productWarehouse->update([
                        'qty' => max(0, $productWarehouse->qty - $detail->quantity),
                    ]);
                }
            }

            $purchase->purchaseDetails()->delete();
            $purchase->purchasePayments()->delete();
            $purchase->delete();

            DB::commit();

            return $this->sendResponse([], 'Purchase deleted successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage());
        }
    }
}
