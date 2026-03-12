<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGrupoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:120'],
            'color' => ['required', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'activo' => ['nullable', 'boolean'],
        ];
    }
}