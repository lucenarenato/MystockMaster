<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supplier_id'    => 'sometimes|required|exists:suppliers,id',
            'date'           => 'sometimes|required|date',
            'status'         => 'sometimes|required|integer|in:0,1,2,3,4',
            'payment_status' => 'sometimes|required|integer|in:0,1,2,3',
            'note'           => 'nullable|string|max:1000',
        ];
    }
}
