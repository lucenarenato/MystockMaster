<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\MovementType;
use App\Enums\PaymentStatus;
use App\Enums\SaleStatus;
use App\Http\Requests\Api\StoreSaleRequest;
use App\Http\Requests\Api\UpdateSaleRequest;
use App\Models\Movement;
use App\Models\Product;
use App\Models\ProductWarehouse;
use App\Models\Sale;
use App\Models\SaleDetails;
use App\Models\SalePayment;
use App\Services\TenantLimits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleController extends BaseController
{
    /**
     * List all sales with optional filters and pagination.
     */
    public function index(Request $request)
    {
        try {
            $query = Sale::with(['customer', 'user']);

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('payment_status')) {
                $query->where('payment_status', $request->payment_status);
            }

            if ($request->filled('customer_id')) {
                $query->where('customer_id', $request->customer_id);
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

            $sales = $query->paginate($request->get('per_page', 15));

            return $this->sendResponse($sales, 'Sales list retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Store a new sale.
     */
    public function store(StoreSaleRequest $request)
    {
        DB::beginTransaction();
        try {
            app(TenantLimits::class)->check('sales');

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
                $sale_status = SaleStatus::COMPLETED;
            } elseif ($paid_amount > 0) {
                $payment_status = PaymentStatus::PARTIAL;
                $sale_status = SaleStatus::PENDING;
            } else {
                $payment_status = PaymentStatus::PENDING;
                $sale_status = SaleStatus::PENDING;
            }

            $sale = Sale::create([
                'date'                => $validated['date'],
                'customer_id'         => $validated['customer_id'],
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
                'status'              => $sale_status,
                'payment_status'      => $payment_status,
                'payment_method'      => $validated['payment_method'] ?? 'cash',
                'note'                => $validated['note'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $sub_total = ($item['price'] * $item['quantity']) - ($item['discount'] ?? 0) + ($item['tax'] ?? 0);

                SaleDetails::create([
                    'sale_id'                 => $sale->id,
                    'warehouse_id'            => $validated['warehouse_id'],
                    'product_id'              => $item['product_id'],
                    'name'                    => $product->name,
                    'code'                    => $product->code,
                    'quantity'                => $item['quantity'],
                    'price'                   => $item['price'] * 100,
                    'unit_price'              => $item['price'] * 100,
                    'sub_total'               => $sub_total * 100,
                    'product_discount_amount' => ($item['discount'] ?? 0) * 100,
                    'product_discount_type'   => 'fixed',
                    'product_tax_amount'      => ($item['tax'] ?? 0) * 100,
                ]);

                $productWarehouse = ProductWarehouse::where('product_id', $product->id)
                    ->where('warehouse_id', $validated['warehouse_id'])
                    ->first();

                if ($productWarehouse) {
                    $productWarehouse->update([
                        'qty' => $productWarehouse->qty - $item['quantity'],
                    ]);
                }

                Movement::create([
                    'type'         => MovementType::SALE,
                    'quantity'     => $item['quantity'],
                    'price'        => $item['price'] * 100,
                    'date'         => $validated['date'],
                    'movable_type' => get_class($product),
                    'movable_id'   => $product->id,
                    'user_id'      => Auth::id(),
                ]);
            }

            if ($paid_amount > 0) {
                SalePayment::create([
                    'date'           => $validated['date'],
                    'amount'         => $paid_amount * 100,
                    'sale_id'        => $sale->id,
                    'payment_method' => $validated['payment_method'] ?? 'cash',
                    'user_id'        => Auth::id(),
                ]);
            }

            DB::commit();

            $sale->load(['customer', 'user', 'saleDetails.product']);

            return $this->sendResponse($sale, 'Sale created successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), [], 422);
        }
    }

    /**
     * Display a specific sale.
     */
    public function show($id)
    {
        try {
            $sale = Sale::with(['customer', 'user', 'saleDetails.product', 'salePayments'])->find($id);

            if (is_null($sale)) {
                return $this->sendError('Sale not found');
            }

            return $this->sendResponse($sale, 'Sale retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Update a sale.
     */
    public function update(UpdateSaleRequest $request, $id)
    {
        try {
            $sale = Sale::findOrFail($id);
            $sale->update($request->validated());

            $sale->load(['customer', 'user', 'saleDetails.product']);

            return $this->sendResponse($sale, 'Sale updated successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], 422);
        }
    }

    /**
     * Delete a sale.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $sale = Sale::findOrFail($id);

            foreach ($sale->saleDetails as $detail) {
                $productWarehouse = ProductWarehouse::where('product_id', $detail->product_id)
                    ->where('warehouse_id', $detail->warehouse_id)
                    ->first();

                if ($productWarehouse) {
                    $productWarehouse->update([
                        'qty' => $productWarehouse->qty + $detail->quantity,
                    ]);
                }
            }

            $sale->saleDetails()->delete();
            $sale->salePayments()->delete();
            $sale->delete();

            DB::commit();

            return $this->sendResponse([], 'Sale deleted successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage());
        }
    }
}
