<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressStoreRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'cep' => 'required|string|max:9',
            'street' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'complement' => 'nullable|string|max:255',
            'neighborhood' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|size:2',
            'is_default' => 'boolean',
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
            'name.required' => 'O nome do endereço é obrigatório.',
            'name.max' => 'O nome do endereço não pode ter mais de 255 caracteres.',
            'cep.required' => 'O CEP é obrigatório.',
            'cep.max' => 'O CEP não pode ter mais de 9 caracteres.',
            'street.required' => 'A rua é obrigatória.',
            'street.max' => 'A rua não pode ter mais de 255 caracteres.',
            'number.required' => 'O número é obrigatório.',
            'number.max' => 'O número não pode ter mais de 20 caracteres.',
            'complement.max' => 'O complemento não pode ter mais de 255 caracteres.',
            'neighborhood.required' => 'O bairro é obrigatório.',
            'neighborhood.max' => 'O bairro não pode ter mais de 255 caracteres.',
            'city.required' => 'A cidade é obrigatória.',
            'city.max' => 'A cidade não pode ter mais de 255 caracteres.',
            'state.required' => 'O estado é obrigatório.',
            'state.size' => 'O estado deve ter exatamente 2 caracteres.',
            'is_default.boolean' => 'O status de padrão deve ser verdadeiro ou falso.'
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
            'name' => 'nome do endereço',
            'cep' => 'CEP',
            'street' => 'rua',
            'number' => 'número',
            'complement' => 'complemento',
            'neighborhood' => 'bairro',
            'city' => 'cidade',
            'state' => 'estado',
            'is_default' => 'endereço padrão'
        ];
    }
} 