<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\StoreTransferRequest;
use App\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferController extends BaseController
{
    /**
     * List all transfers with optional filters.
     */
    public function index(Request $request)
    {
        try {
            $query = Transfer::with(['fromWarehouse', 'toWarehouse', 'user']);

            if ($request->filled('from_warehouse_id')) {
                $query->where('from_warehouse_id', $request->from_warehouse_id);
            }

            if ($request->filled('to_warehouse_id')) {
                $query->where('to_warehouse_id', $request->to_warehouse_id);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            if ($request->filled('search')) {
                $query->where('reference', 'like', '%' . $request->search . '%');
            }

            $sort = $request->get('sort', 'id');
            $order = $request->get('order', 'desc');
            $query->orderBy($sort, $order);

            $transfers = $query->paginate($request->get('per_page', 15));

            return $this->sendResponse($transfers, 'Transfers list retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Store a new transfer.
     */
    public function store(StoreTransferRequest $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();

            $transfer = Transfer::create([
                'from_warehouse_id' => $validated['from_warehouse_id'],
                'to_warehouse_id'   => $validated['to_warehouse_id'],
                'item'              => $validated['item'] ?? null,
                'total_qty'         => $validated['total_qty'],
                'total_tax'         => ($validated['total_tax'] ?? 0) * 100,
                'total_cost'        => $validated['total_cost'] * 100,
                'total_amount'      => $validated['total_amount'] * 100,
                'shipping'          => ($validated['shipping'] ?? 0) * 100,
                'status'            => $validated['status'] ?? 'pending',
                'note'              => $validated['note'] ?? null,
            ]);

            DB::commit();

            $transfer->load(['fromWarehouse', 'toWarehouse', 'user']);

            return $this->sendResponse($transfer, 'Transfer created successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), [], 422);
        }
    }

    /**
     * Display a specific transfer.
     */
    public function show($id)
    {
        try {
            $transfer = Transfer::with(['fromWarehouse', 'toWarehouse', 'user'])->find($id);

            if (is_null($transfer)) {
                return $this->sendError('Transfer not found');
            }

            return $this->sendResponse($transfer, 'Transfer retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Update a transfer.
     */
    public function update(Request $request, $id)
    {
        try {
            $transfer = Transfer::findOrFail($id);
            $transfer->update($request->only([
                'status', 'note', 'shipping',
            ]));

            $transfer->load(['fromWarehouse', 'toWarehouse', 'user']);

            return $this->sendResponse($transfer, 'Transfer updated successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], 422);
        }
    }

    /**
     * Delete a transfer.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $transfer = Transfer::findOrFail($id);
            $transfer->delete();

            DB::commit();

            return $this->sendResponse([], 'Transfer deleted successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage());
        }
    }
}
