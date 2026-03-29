<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCuotaAlumnoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo_cuota_id' => ['required', 'exists:tipos_cuota,id'],
            'estado' => ['required', 'in:pendiente,pagada'],

            'metodo' => ['required_if:estado,pagada', 'in:efectivo,bizum,tarjeta,transferencia,otro'],
            'fecha_pago' => ['required_if:estado,pagada', 'date', 'before_or_equal:today'],
            'notas' => ['required_if:estado,pagada', 'string', 'max:255'],
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