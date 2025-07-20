<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string|max:500',
            'shipping_city' => 'required|string|max:100',
            'shipping_state' => 'required|string|max:2',
            'shipping_zipcode' => 'required|string|size:8',
            'notes' => 'nullable|string|max:1000'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'customer_name.required' => 'O nome do cliente é obrigatório.',
            'customer_name.max' => 'O nome do cliente não pode ter mais de 255 caracteres.',
            'customer_email.required' => 'O email do cliente é obrigatório.',
            'customer_email.email' => 'O email deve ser válido.',
            'customer_email.max' => 'O email não pode ter mais de 255 caracteres.',
            'customer_phone.required' => 'O telefone do cliente é obrigatório.',
            'customer_phone.max' => 'O telefone não pode ter mais de 20 caracteres.',
            'shipping_address.required' => 'O endereço de entrega é obrigatório.',
            'shipping_address.max' => 'O endereço não pode ter mais de 500 caracteres.',
            'shipping_city.required' => 'A cidade é obrigatória.',
            'shipping_city.max' => 'A cidade não pode ter mais de 100 caracteres.',
            'shipping_state.required' => 'O estado é obrigatório.',
            'shipping_state.max' => 'O estado deve ter 2 caracteres.',
            'shipping_zipcode.required' => 'O CEP é obrigatório.',
            'shipping_zipcode.size' => 'O CEP deve ter 8 dígitos.',
            'notes.max' => 'As observações não podem ter mais de 1000 caracteres.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'customer_name' => 'nome do cliente',
            'customer_email' => 'email do cliente',
            'customer_phone' => 'telefone do cliente',
            'shipping_address' => 'endereço de entrega',
            'shipping_city' => 'cidade',
            'shipping_state' => 'estado',
            'shipping_zipcode' => 'CEP',
            'notes' => 'observações'
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $cart = session()->get('cart', []);
            if (empty($cart)) {
                $validator->errors()->add('cart', 'O carrinho está vazio.');
            }

            if ($this->shipping_zipcode && !preg_match('/^\d{8}$/', $this->shipping_zipcode)) {
                $validator->errors()->add('shipping_zipcode', 'O CEP deve conter apenas 8 dígitos.');
            }

            if ($this->shipping_state && !preg_match('/^[A-Z]{2}$/', strtoupper($this->shipping_state))) {
                $validator->errors()->add('shipping_state', 'O estado deve ser uma sigla válida (ex: SP, RJ).');
            }
        });
    }
}
