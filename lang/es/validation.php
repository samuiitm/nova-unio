<?php

return [
    'required' => 'El campo :attribute es obligatorio.',
    'email'    => 'El campo :attribute debe ser un email válido.',
    'in'       => 'El campo :attribute seleccionado no es válido.',
    'accepted' => 'El campo :attribute debe ser aceptado.',
    'integer'  => 'El campo :attribute debe ser un número entero.',
    'max' => [
        'numeric' => 'El campo :attribute no puede ser mayor que :max.',
        'string'  => 'El campo :attribute no puede tener más de :max caracteres.',
    ],
    'min' => [
        'numeric' => 'El campo :attribute debe ser como mínimo :min.',
        'string'  => 'El campo :attribute debe tener al menos :min caracteres.',
    ],
    'string'   => 'El campo :attribute debe ser texto.',

    'attributes' => [
        'nombre' => 'nombre',
        'email' => 'email',
        'telefono' => 'teléfono',
        'asunto' => 'asunto',
        'mensaje' => 'mensaje',
        'empresa' => 'empresa',
        'apellidos'  => 'apellidos',
        'edad'       => 'edad',
        'modalidad'  => 'modalidad',
        'nivel'      => 'nivel',
        'objetivo'   => 'objetivo',
        'privacidad' => 'política de privacidad',
    ],
];