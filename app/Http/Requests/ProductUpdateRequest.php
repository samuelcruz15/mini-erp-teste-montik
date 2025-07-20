<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
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
            'image' => 'nullable|url',
            'active' => 'boolean',
            'has_variations' => 'boolean',
            'default_quantity' => 'nullable|integer|min:0'
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
            'active' => 'status ativo',
            'has_variations' => 'tem variações',
            'default_quantity' => 'quantidade padrão'
        ];
    }
}
