<?php

namespace App\Http\Requests\Payments;

use App\Services\Asaas\Enums\BillingTypeEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProcessPaymentRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => 'sometimes|required|exists:customers,id',
            'billing_type' => [
                'required',
                'string',
                Rule::in(array_column(BillingTypeEnum::cases(), 'value'))
            ],
            'value'             => 'required|numeric|min:0.01',
            'description'       => 'required|string|max:255',
            'due_date'          => 'required|date|after_or_equal:today',
            'customer'          => 'required|array',
            'customer.name'     => 'required|string|max:255',
            'customer.email'    => 'required|email|max:255',
            'customer.cpf_cnpj' => 'required|string|min:11|max:14',
        ];
    }
}
