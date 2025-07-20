<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductVariationRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'variation_name' => 'required|string|max:255',
            'variation_value' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'price_adjustment' => 'nullable|numeric'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'variation_name.required' => 'O nome da variação é obrigatório.',
            'variation_name.max' => 'O nome da variação não pode ter mais de 255 caracteres.',
            'variation_value.required' => 'O valor da variação é obrigatório.',
            'variation_value.max' => 'O valor da variação não pode ter mais de 255 caracteres.',
            'quantity.required' => 'A quantidade é obrigatória.',
            'quantity.integer' => 'A quantidade deve ser um número inteiro.',
            'quantity.min' => 'A quantidade deve ser maior ou igual a zero.',
            'price_adjustment.numeric' => 'O ajuste de preço deve ser um número.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'variation_name' => 'nome da variação',
            'variation_value' => 'valor da variação',
            'quantity' => 'quantidade',
            'price_adjustment' => 'ajuste de preço'
        ];
    }
} 