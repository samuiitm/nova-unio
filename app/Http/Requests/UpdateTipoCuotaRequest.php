<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTipoCuotaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:120'],
            'importe' => ['required', 'numeric', 'min:0'],
            'duracion_meses' => ['required', 'integer', 'min:1', 'max:24'],
            'activo' => ['nullable', 'boolean'],
        ];
    }
}