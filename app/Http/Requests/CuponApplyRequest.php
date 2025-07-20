<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CuponApplyRequest extends FormRequest
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
            'cupon_code' => 'required|string|max:50'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'cupon_code.required' => 'O código do cupom é obrigatório.',
            'cupon_code.string' => 'O código do cupom deve ser uma string.',
            'cupon_code.max' => 'O código do cupom não pode ter mais de 50 caracteres.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'cupon_code' => 'código do cupom'
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $cupon = \App\Models\Cupon::where('code', strtoupper($this->cupon_code))->first();
            if (!$cupon) {
                $validator->errors()->add('cupon_code', 'Cupom não encontrado.');
                return;
            }

            if (!$cupon->active) {
                $validator->errors()->add('cupon_code', 'Este cupom está inativo.');
                return;
            }

            if (now() < $cupon->valid_from || now() > $cupon->valid_until) {
                $validator->errors()->add('cupon_code', 'Este cupom está fora do período de validade.');
                return;
            }

            if ($cupon->usage_limit > 0) {
                $usageCount = \App\Models\Order::where('cupon_id', $cupon->id)->count();
                if ($usageCount >= $cupon->usage_limit) {
                    $validator->errors()->add('cupon_code', 'Este cupom atingiu o limite de uso.');
                }
            }
        });
    }
}
