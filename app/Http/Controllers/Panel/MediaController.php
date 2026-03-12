<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function miAvatar(Request $request)
    {
        $user = $request->user();

        if (!$user || !$user->foto_perfil) {
            abort(404);
        }

        return $this->devolverArchivoPrivado($user->foto_perfil);
    }

    public function fotoAlumno(Request $request, Alumno $alumno)
    {
        $user = $request->user();

        if (!$user) {
            abort(403);
        }

        // Si quieres, aquí puedes endurecer permisos más adelante.
        if (!$alumno->foto_path) {
            abort(404);
        }

        return $this->devolverArchivoPrivado($alumno->foto_path);
    }

    private function devolverArchivoPrivado(string $ruta)
    {
        if (!Storage::disk('private_uploads')->exists($ruta)) {
            abort(404);
        }

        $path = Storage::disk('private_uploads')->path($ruta);
        $mime = Storage::disk('private_uploads')->mimeType($ruta) ?: 'application/octet-stream';

        return response()->file($path, [
            'Content-Type' => $mime,
            'Cache-Control' => 'private, max-age=86400',
        ]);
    }
}