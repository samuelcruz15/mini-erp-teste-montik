<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WebhookOrderStatusRequest extends FormRequest
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
            'order_id' => 'required|integer|exists:orders,id',
            'status' => 'required|string'
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
            'order_id.required' => 'O ID do pedido é obrigatório.',
            'order_id.integer' => 'O ID do pedido deve ser um número inteiro.',
            'order_id.exists' => 'O pedido não foi encontrado.',
            'status.required' => 'O status é obrigatório.',
            'status.string' => 'O status deve ser uma string.'
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
            'order_id' => 'ID do pedido',
            'status' => 'status'
        ];
    }
} 