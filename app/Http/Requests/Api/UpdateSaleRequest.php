<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id'         => 'sometimes|required|exists:customers,id',
            'date'                => 'sometimes|required|date',
            'status'              => 'sometimes|required|integer|in:0,1,2,3,4,5',
            'payment_status'      => 'sometimes|required|integer|in:0,1,2,3',
            'note'                => 'nullable|string|max:1000',
        ];
    }
}
