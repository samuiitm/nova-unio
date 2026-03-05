<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCuotaAlumnoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'tipo_cuota_id' => ['required', 'exists:tipos_cuota,id'],
            'fecha_inicio' => ['required', 'date'],
            'estado' => ['required', 'in:pendiente,pagada'],

            'metodo' => ['required_if:estado,pagada', 'in:efectivo,bizum,tarjeta,transferencia,otro'],
            'fecha_pago' => ['required_if:estado,pagada', 'date', 'before_or_equal:today'],
            'notas' => ['nullable', 'string', 'max:255'],
        ];
    }
}