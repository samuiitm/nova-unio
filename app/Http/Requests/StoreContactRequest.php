<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'   => ['required', 'string', 'max:80'],
            'email'    => ['required', 'email', 'max:120'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'asunto'   => ['required', 'string', 'max:120'],
            'mensaje'  => ['required', 'string', 'min:10', 'max:2000'],
            // anti-spam
            'empresa'  => ['nullable', 'string', 'max:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'empresa.max' => 'Error al enviar el formulario.',
        ];
    }
}