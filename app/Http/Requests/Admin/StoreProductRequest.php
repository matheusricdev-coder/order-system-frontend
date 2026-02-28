<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

final class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // protected by admin middleware at route level
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:255'],
            'description'   => ['sometimes', 'nullable', 'string', 'max:2000'],
            'categoryId'    => ['required', 'string', 'uuid', 'exists:categories,id'],
            'companyId'     => ['required', 'string', 'uuid', 'exists:companies,id'],
            'priceAmount'   => ['required', 'integer', 'min:1'],
            'priceCurrency' => ['sometimes', 'string', 'size:3'],
        ];
    }
}
