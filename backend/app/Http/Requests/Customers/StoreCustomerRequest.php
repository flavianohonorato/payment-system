<?php

namespace App\Http\Requests\Customers;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'unique:customers,email'],
            'cpf_cnpj'      => ['required', 'string', 'unique:customers,cpf_cnpj'],
            'phone'         => ['nullable', 'string'],
            'address'       => ['nullable', 'string'],
            'external_id'   => ['nullable', 'string']
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'name.required'     => 'O campo nome é obrigatório.',
            'name.string'       => 'O campo nome deve ser uma string.',
            'name.max'          => 'O campo nome deve ter no máximo 255 caracteres.',
            'email.required'    => 'O campo e-mail é obrigatório.',
            'email.email'       => 'O campo e-mail deve ser um e-mail válido.',
            'email.unique'      => 'O e-mail informado já está em uso.',
            'cpf_cnpj.required' => 'O campo CPF/CNPJ é obrigatório.',
            'cpf_cnpj.string'   => 'O campo CPF/CNPJ deve ser uma string.',
            'cpf_cnpj.unique'   => 'O CPF/CNPJ informado já está em uso.',
            'phone.string'      => 'O campo telefone deve ser uma string.',
            'address.string'    => 'O campo endereço deve ser uma string.',
            'external_id.string' => 'O campo external_id deve ser uma string.'
        ];
    }
}
