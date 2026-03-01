<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePreinscripcionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'    => ['required','string','max:80'],
            'apellidos' => ['nullable','string','max:120'],
            'email'     => ['required','email','max:120'],
            'telefono'  => ['nullable','string','max:30'],
            'edad'      => ['nullable','integer','min:3','max:80'],

            'modalidad' => ['required', Rule::in([
                'Sambo Kids',
                'MMA',
                'Sambo',
                'Combat Sambo',
                'MMA-Sambo'
            ])],

            'nivel'     => ['nullable', Rule::in(['Principiante','Intermedio','Avanzado'])],
            'objetivo'  => ['nullable', Rule::in(['Aprender','Ponerme en forma','Competir'])],
            'mensaje'   => ['nullable','string','max:2000'],

            'privacidad' => ['accepted'],

            // campo trampa anti-bots
            'empresa' => ['nullable','string','max:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'privacidad.accepted' => 'Tienes que aceptar la política de privacidad.',
            'empresa.max' => 'Error al enviar el formulario.',
        ];
    }
}