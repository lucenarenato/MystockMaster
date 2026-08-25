<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdjustmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date'                    => 'required|date',
            'note'                    => 'nullable|string|max:1000',
            'warehouse_id'            => 'required|exists:warehouses,id',
            'items'                   => 'required|array|min:1',
            'items.*.product_id'      => 'required|exists:products,id',
            'items.*.quantity'        => 'required|integer|min:0',
            'items.*.type'            => 'required|string|in:add,sub',
        ];
    }

    public function messages(): array
    {
        return [
            'date.required'              => 'A data é obrigatória.',
            'warehouse_id.required'      => 'O armazém é obrigatório.',
            'warehouse_id.exists'        => 'O armazém selecionado não existe.',
            'items.required'             => 'O ajuste deve ter pelo menos um item.',
            'items.min'                  => 'O ajuste deve ter pelo menos um item.',
            'items.*.product_id.required' => 'O produto é obrigatório em cada item.',
            'items.*.quantity.required'   => 'A quantidade é obrigatória em cada item.',
            'items.*.type.required'       => 'O tipo (add/sub) é obrigatório em cada item.',
            'items.*.type.in'            => 'O tipo deve ser "add" (adicionar) ou "sub" (subtrair).',
        ];
    }
}
