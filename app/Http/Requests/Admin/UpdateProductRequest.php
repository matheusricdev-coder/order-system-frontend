<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name'          => ['sometimes', 'string', 'max:255'],
            'description'   => ['sometimes', 'nullable', 'string', 'max:2000'],
            'categoryId'    => ['sometimes', 'string', 'uuid', 'exists:categories,id'],
            'priceAmount'   => ['sometimes', 'integer', 'min:1'],
            'priceCurrency' => ['sometimes', 'string', 'size:3'],
        ];
    }
}
