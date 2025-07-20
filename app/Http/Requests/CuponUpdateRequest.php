<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CuponUpdateRequest extends FormRequest
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
        $cuponId = $this->route('cupon')->id;
        
        return [
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('cupons', 'code')->ignore($cuponId)
            ],
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,fixed',
            'discount' => 'required|numeric|min:0',
            'minimum_amount' => 'required|numeric|min:0',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after:valid_from',
            'usage_limit' => 'nullable|integer|min:1',
            'active' => 'boolean'
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
            'code.required' => 'O código do cupom é obrigatório.',
            'code.unique' => 'Este código de cupom já existe.',
            'code.max' => 'O código do cupom não pode ter mais de 50 caracteres.',
            'name.required' => 'O nome do cupom é obrigatório.',
            'name.max' => 'O nome do cupom não pode ter mais de 255 caracteres.',
            'type.required' => 'O tipo do cupom é obrigatório.',
            'type.in' => 'O tipo deve ser percentage ou fixed.',
            'discount.required' => 'O valor do desconto é obrigatório.',
            'discount.numeric' => 'O desconto deve ser um número.',
            'discount.min' => 'O desconto deve ser maior ou igual a zero.',
            'minimum_amount.required' => 'O valor mínimo é obrigatório.',
            'minimum_amount.numeric' => 'O valor mínimo deve ser um número.',
            'minimum_amount.min' => 'O valor mínimo deve ser maior ou igual a zero.',
            'valid_from.required' => 'A data de início é obrigatória.',
            'valid_from.date' => 'A data de início deve ser uma data válida.',
            'valid_until.required' => 'A data de fim é obrigatória.',
            'valid_until.date' => 'A data de fim deve ser uma data válida.',
            'valid_until.after' => 'A data de fim deve ser posterior à data de início.',
            'usage_limit.integer' => 'O limite de uso deve ser um número inteiro.',
            'usage_limit.min' => 'O limite de uso deve ser maior que zero.',
            'active.boolean' => 'O status deve ser verdadeiro ou falso.'
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
            'code' => 'código do cupom',
            'name' => 'nome do cupom',
            'type' => 'tipo',
            'discount' => 'desconto',
            'minimum_amount' => 'valor mínimo',
            'valid_from' => 'data de início',
            'valid_until' => 'data de fim',
            'usage_limit' => 'limite de uso',
            'active' => 'status'
        ];
    }
} 