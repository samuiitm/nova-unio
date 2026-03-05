<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CobrarCuotaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'fecha_pago' => ['required', 'date', 'before_or_equal:today'],
            'importe' => ['required', 'numeric', 'min:0'],
            'metodo' => ['required', 'in:efectivo,bizum,tarjeta,transferencia,otro'],
            'notas' => ['nullable', 'string', 'max:255'],
        ];
    }
}