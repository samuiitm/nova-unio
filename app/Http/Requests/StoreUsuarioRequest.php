<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->esAdmin();
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:120'],
            'apellidos' => ['nullable', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:190', 'unique:usuarios,email'],
            'telefono' => ['nullable', 'string', 'max:30'],

            'rol' => ['required', 'in:admin,entrenador_admin,entrenador'],
            'activo' => ['nullable', 'boolean'],

            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }

    public function messages(): array
    {
        return [
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
        ];
    }
}