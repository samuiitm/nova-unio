<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CobrarCuotaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fecha_pago' => ['required', 'date', 'before_or_equal:today'],
            'metodo' => ['required', 'in:efectivo,bizum,tarjeta,transferencia,otro'],
            'notas' => ['required', 'string', 'max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $notas = trim((string) $this->input('notas'));

        $this->merge([
            'notas' => $notas === '' ? null : mb_strtoupper($notas),
        ]);
    }

   public function attributes(): array
    {
        return [
            'notas' => 'mes pagado',
        ];
    }

    public function messages(): array
    {
        return [
            'notas.required' => 'El mes pagado es obligatorio al registrar el cobro.',
        ];
    }
}