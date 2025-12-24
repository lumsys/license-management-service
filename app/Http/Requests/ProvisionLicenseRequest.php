<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProvisionLicenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_email' => 'required|email',
            'licenses' => 'required|array|min:1',
            'licenses.*.product_code' => 'required|string',
            'licenses.*.expires_at' => 'required|date',
            'licenses.*.seat_limit' => 'nullable|integer|min:1',
        ];
    }
}
