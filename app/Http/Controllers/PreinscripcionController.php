<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePreinscripcionRequest;
use App\Models\Preinscripcion;
use App\Support\Phone;

class PreinscripcionController extends Controller
{
    public function store(StorePreinscripcionRequest $request)
    {
        $data = $request->validated();

        Preinscripcion::create([
            'nombre'    => $data['nombre'],
            'apellidos' => $data['apellidos'] ?? null,
            'email'     => $data['email'],
            'telefono'  => Phone::normalize($data['telefono'] ?? null),
            'edad'      => $data['edad'] ?? null,
            'modalidad' => $data['modalidad'],
            'nivel'     => $data['nivel'] ?? null,
            'objetivo'  => $data['objetivo'] ?? null,
            'mensaje'   => $data['mensaje'] ?? null,
            'estado'    => 'nueva',
        ]);

        return back()->with('ok', '¡Preinscripción enviada! Te contactaremos lo antes posible.');
    }
}