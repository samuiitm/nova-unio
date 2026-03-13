<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTipoCuotaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'duracion_meses' => $this->filled('duracion_meses') ? $this->input('duracion_meses') : null,
            'venta_inicio_mes' => $this->filled('venta_inicio_mes') ? $this->input('venta_inicio_mes') : null,
            'venta_fin_mes' => $this->filled('venta_fin_mes') ? $this->input('venta_fin_mes') : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:120'],
            'importe' => ['required', 'numeric', 'min:0'],
            'tipo_vigencia' => ['required', 'in:meses,temporada'],

            'duracion_meses' => [
                'nullable',
                'integer',
                'min:1',
                'max:24',
                'required_if:tipo_vigencia,meses',
            ],

            'venta_inicio_mes' => [
                'nullable',
                'integer',
                'min:1',
                'max:12',
                'required_if:tipo_vigencia,temporada',
            ],

            'venta_fin_mes' => [
                'nullable',
                'integer',
                'min:1',
                'max:12',
                'required_if:tipo_vigencia,temporada',
            ],

            'activo' => ['nullable', 'boolean'],
        ];
    }
}