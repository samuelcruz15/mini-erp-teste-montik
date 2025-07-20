<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderStoreRequest extends FormRequest
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
        $rules = [
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'selected_address' => 'nullable|integer|exists:addresses,id',
        ];

        if (!$this->selected_address) {
            $rules = array_merge($rules, [
                'shipping_address' => 'required|string|max:500',
                'shipping_city' => 'required|string|max:100',
                'shipping_state' => 'required|string|max:2',
                'shipping_zipcode' => 'required|string|regex:/^\d{5}-?\d{3}$/',
                'number' => 'required|string|max:10',
                'complement' => 'nullable|string|max:100',
                'neighborhood' => 'required|string|max:100',
            ]);
        }

        return $rules;
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
            'customer_phone.max' => 'O telefone não pode ter mais de 20 caracteres.',
            'shipping_address.required' => 'O endereço de entrega é obrigatório.',
            'shipping_address.max' => 'O endereço não pode ter mais de 500 caracteres.',
            'shipping_city.required' => 'A cidade é obrigatória.',
            'shipping_city.max' => 'A cidade não pode ter mais de 100 caracteres.',
            'shipping_state.required' => 'O estado é obrigatório.',
            'shipping_state.max' => 'O estado deve ter 2 caracteres.',
            'shipping_zipcode.required' => 'O CEP é obrigatório.',
            'shipping_zipcode.regex' => 'O CEP deve ter o formato 00000-000 ou 00000000.',
            'number.required' => 'O número é obrigatório.',
            'number.max' => 'O número não pode ter mais de 10 caracteres.',
            'complement.max' => 'O complemento não pode ter mais de 100 caracteres.',
            'neighborhood.required' => 'O bairro é obrigatório.',
            'neighborhood.max' => 'O bairro não pode ter mais de 100 caracteres.',
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
            'number' => 'número',
            'complement' => 'complemento',
            'neighborhood' => 'bairro',
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

            if ($this->selected_address) {
                $user = auth()->user();
                $address = $user->addresses()->find($this->selected_address);
                if (!$address) {
                    $validator->errors()->add('selected_address', 'Endereço selecionado não é válido.');
                }
            }

            if ($this->shipping_state && !preg_match('/^[A-Z]{2}$/', strtoupper($this->shipping_state))) {
                $validator->errors()->add('shipping_state', 'O estado deve ser uma sigla válida (ex: SP, RJ).');
            }
        });
    }
}
