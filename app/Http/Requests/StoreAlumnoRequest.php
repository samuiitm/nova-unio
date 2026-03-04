<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAlumnoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:120'],
            'apellidos' => ['required', 'string', 'max:180'],

            'catsalut' => ['nullable', 'string', 'max:50', 'unique:alumnos,catsalut'],

            'fecha_nacimiento' => ['nullable', 'date', 'before:today'],
            'lugar_nacimiento' => ['nullable', 'string', 'max:120'],

            'dni' => ['nullable', 'string', 'max:25', 'unique:alumnos,dni'],

            'direccion' => ['nullable', 'string', 'max:200'],
            'cp' => ['nullable', 'string', 'max:10'],
            'poblacion' => ['nullable', 'string', 'max:120'],

            'telefono' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:190', 'unique:alumnos,email'],

            'notas' => ['nullable', 'string'],
        ];
    }
}