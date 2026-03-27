<?php

namespace App\Http\Requests;

use App\Services\CalculadorVigenciaSeguroService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSeguroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tipos = array_keys(app(CalculadorVigenciaSeguroService::class)->tiposDisponibles());

        return [
            'alumno_id' => ['required', 'integer', 'exists:alumnos,id'],
            'tipo' => ['required', 'string', Rule::in($tipos)],
            'estado' => ['required', Rule::in(['pendiente', 'pagado'])],
            'fecha_pago' => ['nullable', 'date', 'before_or_equal:today', 'required_if:estado,pagado'],
            'metodo' => ['nullable', Rule::in(['efectivo', 'bizum', 'tarjeta', 'transferencia', 'otro']), 'required_if:estado,pagado'],
            'notas' => ['nullable', 'string', 'max:2000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'fecha_pago' => $this->input('fecha_pago') ?: null,
            'metodo' => $this->input('metodo') ?: null,
            'notas' => ($notas = trim((string) $this->input('notas'))) === '' ? null : $notas,
        ]);
    }
}