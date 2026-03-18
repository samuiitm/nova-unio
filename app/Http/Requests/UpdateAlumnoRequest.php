<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAlumnoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('alumno')?->id ?? null;

        return [
            'nombre' => ['required', 'string', 'max:120'],
            'apellidos' => ['required', 'string', 'max:180'],

            'catsalut' => ['nullable', 'string', 'max:50', 'unique:alumnos,catsalut,' . $id],

            'fecha_nacimiento' => ['nullable', 'date', 'before:today'],
            'lugar_nacimiento' => ['nullable', 'string', 'max:120'],

            'dni' => ['nullable', 'string', 'max:25', 'unique:alumnos,dni,' . $id],

            'direccion' => ['nullable', 'string', 'max:200'],
            'cp' => ['nullable', 'string', 'max:10'],
            'poblacion' => ['nullable', 'string', 'max:120'],

            'telefono' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:190', 'unique:alumnos,email,' . $id],

            'tutor_legal_nombre' => ['nullable', 'string', 'max:180'],
            'tutor_legal_dni' => ['nullable', 'string', 'max:25'],
            'tutor_legal_relacion' => ['nullable', 'in:padre,madre,tutor'],

            'telefonos_contacto' => ['nullable', 'array'],
            'telefonos_contacto.*.contacto' => ['nullable', 'string', 'max:120'],
            'telefonos_contacto.*.telefono' => ['nullable', 'string', 'max:30'],

            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'quitar_foto' => ['nullable', 'boolean'],

            'notas' => ['nullable', 'string'],

            'grupos' => ['nullable', 'array'],
            'grupos.*' => ['integer', 'exists:grupos,id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $fechaNacimiento = $this->input('fecha_nacimiento');

            $esMenor = false;

            if ($fechaNacimiento) {
                try {
                    $esMenor = Carbon::parse($fechaNacimiento)->age < 18;
                } catch (\Throwable $e) {
                    $esMenor = false;
                }
            }

            $tutorNombre = trim((string) $this->input('tutor_legal_nombre'));
            $tutorDni = trim((string) $this->input('tutor_legal_dni'));
            $tutorRelacion = trim((string) $this->input('tutor_legal_relacion'));

            $hayDatosTutor = $tutorNombre !== '' || $tutorDni !== '' || $tutorRelacion !== '';

            if ($esMenor || $hayDatosTutor) {
                if ($tutorNombre === '') {
                    $validator->errors()->add(
                        'tutor_legal_nombre',
                        'El nombre del tutor legal es obligatorio si el alumno es menor de edad.'
                    );
                }

                if ($tutorDni === '') {
                    $validator->errors()->add(
                        'tutor_legal_dni',
                        'El DNI del tutor legal es obligatorio si el alumno es menor de edad.'
                    );
                }

                if ($tutorRelacion === '') {
                    $validator->errors()->add(
                        'tutor_legal_relacion',
                        'La relación del tutor legal es obligatoria si el alumno es menor de edad.'
                    );
                }
            }

            foreach ((array) $this->input('telefonos_contacto', []) as $i => $fila) {
                $contacto = trim((string) ($fila['contacto'] ?? ''));
                $telefono = trim((string) ($fila['telefono'] ?? ''));

                if (($contacto !== '' && $telefono === '') || ($contacto === '' && $telefono !== '')) {
                    $validator->errors()->add(
                        "telefonos_contacto.$i.contacto",
                        'Cada teléfono adicional debe tener contacto y teléfono.'
                    );
                }
            }
        });
    }
}