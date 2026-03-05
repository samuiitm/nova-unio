<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCuotaAlumnoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'tipo_cuota_id' => ['nullable', 'exists:tipos_cuota,id'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['nullable', 'date'],
            'importe' => ['nullable', 'numeric', 'min:0'],

            'estado' => ['required', 'in:pendiente,pagada'],

            'metodo' => ['required_if:estado,pagada', 'in:efectivo,bizum,tarjeta,transferencia,otro'],
            'fecha_pago' => ['required_if:estado,pagada', 'date'],
            'notas' => ['nullable', 'string', 'max:255'],
        ];
    }
}