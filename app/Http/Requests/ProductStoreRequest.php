<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->is_admin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'has_variations' => 'nullable|boolean',
            'image' => 'nullable|url',
            'variations' => 'nullable|array',
            'variations.*.name' => 'required_with:variations|string',
            'variations.*.value' => 'required_with:variations|string',
            'variations.*.quantity' => 'required_with:variations|integer|min:0',
            'variations.*.price_adjustment' => 'nullable|numeric',
            'default_quantity' => 'required_without:variations|integer|min:0'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome do produto é obrigatório.',
            'name.max' => 'O nome do produto não pode ter mais de 255 caracteres.',
            'price.required' => 'O preço é obrigatório.',
            'price.numeric' => 'O preço deve ser um número.',
            'price.min' => 'O preço deve ser maior que zero.',
            'image.url' => 'A URL da imagem deve ser válida.',
            'variations.*.name.required_with' => 'O nome da variação é obrigatório.',
            'variations.*.value.required_with' => 'O valor da variação é obrigatório.',
            'variations.*.quantity.required_with' => 'A quantidade da variação é obrigatória.',
            'variations.*.quantity.integer' => 'A quantidade deve ser um número inteiro.',
            'variations.*.quantity.min' => 'A quantidade deve ser maior ou igual a zero.',
            'default_quantity.required_without' => 'A quantidade padrão é obrigatória para produtos sem variações.',
            'default_quantity.integer' => 'A quantidade padrão deve ser um número inteiro.',
            'default_quantity.min' => 'A quantidade padrão deve ser maior ou igual a zero.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'nome do produto',
            'price' => 'preço',
            'description' => 'descrição',
            'image' => 'imagem',
            'variations' => 'variações',
            'default_quantity' => 'quantidade padrão'
        ];
    }
}
