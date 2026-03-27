<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CobrarSeguroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fecha_pago' => ['required', 'date', 'before_or_equal:today'],
            'metodo' => ['required', Rule::in(['efectivo', 'bizum', 'tarjeta', 'transferencia', 'otro'])],
            'notas' => ['nullable', 'string', 'max:2000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'notas' => ($notas = trim((string) $this->input('notas'))) === '' ? null : $notas,
        ]);
    }
}