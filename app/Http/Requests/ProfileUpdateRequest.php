<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $id = $this->user()?->id;

        return [
            'nombre' => ['required', 'string', 'max:120'],
            'apellidos' => ['nullable', 'string', 'max:150'],
            'email' => [
                'required',
                'email',
                'max:190',
                Rule::unique((new User())->getTable(), 'email')->ignore($id),
            ],
            'telefono' => ['nullable', 'string', 'max:30'],

            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'quitar_foto' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'nombre',
            'apellidos' => 'apellidos',
            'email' => 'email',
            'telefono' => 'teléfono',
            'foto' => 'foto de perfil',
        ];
    }
}