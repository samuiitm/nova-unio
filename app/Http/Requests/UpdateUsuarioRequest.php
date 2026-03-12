<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->esAdmin();
    }

    public function rules(): array
    {
        $id = $this->route('usuario')?->id ?? null;

        return [
            'nombre' => ['required', 'string', 'max:120'],
            'apellidos' => ['nullable', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:190', Rule::unique('usuarios', 'email')->ignore($id)],
            'telefono' => ['nullable', 'string', 'max:30'],

            'rol' => ['required', 'in:admin,entrenador_admin,entrenador'],
            'activo' => ['nullable', 'boolean'],

            'password' => ['nullable', 'confirmed', Password::defaults()],
        ];
    }

    public function messages(): array
    {
        return [
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
        ];
    }
}