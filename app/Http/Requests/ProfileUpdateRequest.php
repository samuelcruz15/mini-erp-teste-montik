<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
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
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'cpf' => 'nullable|string|max:14',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|in:M,F,O',
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
            'full_name.required' => 'O nome completo é obrigatório.',
            'full_name.max' => 'O nome completo não pode ter mais de 255 caracteres.',
            'phone.required' => 'O telefone é obrigatório.',
            'phone.max' => 'O telefone não pode ter mais de 20 caracteres.',
            'cpf.max' => 'O CPF não pode ter mais de 14 caracteres.',
            'birth_date.date' => 'A data de nascimento deve ser uma data válida.',
            'birth_date.before' => 'A data de nascimento deve ser anterior a hoje.',
            'gender.in' => 'O gênero deve ser M, F ou O.'
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
            'full_name' => 'nome completo',
            'phone' => 'telefone',
            'cpf' => 'CPF',
            'birth_date' => 'data de nascimento',
            'gender' => 'gênero'
        ];
    }
} 