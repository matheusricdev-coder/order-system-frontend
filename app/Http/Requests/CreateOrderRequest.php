<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CreateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'userId' => ['required', 'string', 'uuid'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.productId' => ['required', 'string', 'uuid'],
            'items.*.quantity' => ['required', 'integer', 'gt:0'],
        ];
    }
}
