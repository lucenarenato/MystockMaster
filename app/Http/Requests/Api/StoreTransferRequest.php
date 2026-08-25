<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id'   => 'required|exists:warehouses,id|different:from_warehouse_id',
            'item'              => 'nullable|string|max:255',
            'total_qty'         => 'required|integer|min:1',
            'total_tax'         => 'nullable|numeric|min:0',
            'total_cost'        => 'required|numeric|min:0',
            'total_amount'      => 'required|numeric|min:0',
            'shipping'          => 'nullable|numeric|min:0',
            'status'            => 'nullable|string|in:pending,completed,cancelled',
            'note'              => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'from_warehouse_id.required' => 'O armazém de origem é obrigatório.',
            'from_warehouse_id.exists'   => 'O armazém de origem não existe.',
            'to_warehouse_id.required'   => 'O armazém de destino é obrigatório.',
            'to_warehouse_id.exists'     => 'O armazém de destino não existe.',
            'to_warehouse_id.different'  => 'Os armazéns de origem e destino devem ser diferentes.',
            'total_qty.required'         => 'A quantidade total é obrigatória.',
            'total_qty.min'              => 'A quantidade total deve ser pelo menos 1.',
            'total_cost.required'        => 'O custo total é obrigatório.',
            'total_amount.required'      => 'O valor total é obrigatório.',
        ];
    }
}
