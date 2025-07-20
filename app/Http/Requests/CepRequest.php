<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CepRequest extends FormRequest
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
            'cep' => 'required|string|regex:/^\d{5}-?\d{3}$/'
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
            'cep.required' => 'O CEP é obrigatório.',
            'cep.string' => 'O CEP deve ser uma string.',
            'cep.regex' => 'O CEP deve ter o formato 00000-000 ou 00000000.'
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->cep) {
                $cleanCep = preg_replace('/\D/', '', $this->cep);
                $this->merge(['cep' => $cleanCep]);
            }
        });
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'cep' => 'CEP'
        ];
    }
} 