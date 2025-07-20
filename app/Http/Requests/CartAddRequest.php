<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartAddRequest extends FormRequest
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
            'product_id' => 'required|exists:products,id',
            'stock_id' => 'nullable|exists:stocks,id',
            'quantity' => 'required|integer|min:1|max:100'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'O produto é obrigatório.',
            'product_id.exists' => 'O produto selecionado não existe.',
            'quantity.required' => 'A quantidade é obrigatória.',
            'quantity.integer' => 'A quantidade deve ser um número inteiro.',
            'quantity.min' => 'A quantidade deve ser pelo menos 1.',
            'quantity.max' => 'A quantidade não pode ser maior que 100.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'product_id' => 'produto',
            'variation_value' => 'variação',
            'quantity' => 'quantidade'
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $product = \App\Models\Product::find($this->product_id);
            if ($product && !$product->active) {
                $validator->errors()->add('product_id', 'Este produto não está disponível.');
            }
            
            // Validar se o stock_id pertence ao produto
            if ($this->stock_id) {
                $stock = \App\Models\Stock::find($this->stock_id);
                if (!$stock || $stock->product_id != $this->product_id) {
                    $validator->errors()->add('stock_id', 'Variação inválida para este produto.');
                }
            }
        });
    }
}
