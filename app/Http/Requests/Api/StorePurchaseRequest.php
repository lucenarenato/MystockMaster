<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supplier_id'         => 'required|exists:suppliers,id',
            'warehouse_id'        => 'required|exists:warehouses,id',
            'date'                => 'required|date',
            'tax_percentage'      => 'nullable|numeric|min:0|max:100',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'shipping_amount'     => 'nullable|numeric|min:0',
            'paid_amount'         => 'nullable|numeric|min:0',
            'status'              => 'required|integer|in:0,1,2,3,4',
            'payment_method'      => 'nullable|string|in:cash,card,bank_transfer,check,other',
            'note'                => 'nullable|string|max:1000',
            'items'               => 'required|array|min:1',
            'items.*.product_id'  => 'required|exists:products,id',
            'items.*.quantity'    => 'required|integer|min:1',
            'items.*.price'       => 'required|numeric|min:0',
            'items.*.cost'        => 'required|numeric|min:0',
            'items.*.discount'    => 'nullable|numeric|min:0',
            'items.*.tax'         => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_id.required'     => 'O fornecedor é obrigatório.',
            'supplier_id.exists'       => 'O fornecedor selecionado não existe.',
            'warehouse_id.required'    => 'O armazém é obrigatório.',
            'warehouse_id.exists'      => 'O armazém selecionado não existe.',
            'date.required'           => 'A data é obrigatória.',
            'items.required'          => 'A compra deve ter pelo menos um item.',
            'items.min'               => 'A compra deve ter pelo menos um item.',
            'items.*.product_id.required' => 'O produto é obrigatório em cada item.',
            'items.*.quantity.required'   => 'A quantidade é obrigatória em cada item.',
            'items.*.price.required'      => 'O preço é obrigatório em cada item.',
            'items.*.cost.required'       => 'O custo é obrigatório em cada item.',
        ];
    }
}
