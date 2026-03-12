<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $datos = $request->validated();

        if (($user->email ?? null) !== ($datos['email'] ?? null)) {
            $user->email_verified_at = null;
        }

        if ($request->hasFile('foto')) {
            $datos['foto_perfil'] = $this->guardarFotoOptimizada(
                $request->file('foto'),
                $user->foto_perfil
            );
        } elseif ($request->boolean('quitar_foto') && $user->foto_perfil) {
            if ($user->foto_perfil && file_exists(public_path($user->foto_perfil))) {
                @unlink(public_path($user->foto_perfil));
            }
            $datos['foto_perfil'] = null;
        }

        unset($datos['foto'], $datos['quitar_foto']);

        $user->update($datos);

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        return Redirect::route('profile.edit')->with('error', 'No se permite eliminar la cuenta desde el panel.');
    }

    private function guardarFotoOptimizada(UploadedFile $file, ?string $fotoAnterior = null): ?string
    {
        if (!extension_loaded('gd')) {
            return $fotoAnterior;
        }

        $binario = file_get_contents($file->getRealPath());

        if ($binario === false) {
            return $fotoAnterior;
        }

        $imagenOrigen = imagecreatefromstring($binario);

        if (!$imagenOrigen) {
            return $fotoAnterior;
        }

        $anchoOrigen = imagesx($imagenOrigen);
        $altoOrigen = imagesy($imagenOrigen);

        $maxLado = 360;
        $escala = min($maxLado / max($anchoOrigen, $altoOrigen), 1);

        $anchoDestino = max(1, (int) round($anchoOrigen * $escala));
        $altoDestino = max(1, (int) round($altoOrigen * $escala));

        $imagenDestino = imagecreatetruecolor($anchoDestino, $altoDestino);

        imagealphablending($imagenDestino, true);
        imagesavealpha($imagenDestino, true);

        $transparente = imagecolorallocatealpha($imagenDestino, 0, 0, 0, 127);
        imagefill($imagenDestino, 0, 0, $transparente);

        imagecopyresampled(
            $imagenDestino,
            $imagenOrigen,
            0,
            0,
            0,
            0,
            $anchoDestino,
            $altoDestino,
            $anchoOrigen,
            $altoOrigen
        );

        $carpeta = 'uploads/usuarios/perfil/' . now()->format('Y/m');
        $nombreBase = Str::uuid()->toString();

        if (function_exists('imagewebp')) {
            $ruta = $carpeta . '/' . $nombreBase . '.webp';

            ob_start();
            imagewebp($imagenDestino, null, 80);
            $contenido = ob_get_clean();
        } else {
            $ruta = $carpeta . '/' . $nombreBase . '.jpg';

            ob_start();
            imagejpeg($imagenDestino, null, 80);
            $contenido = ob_get_clean();
        }

        imagedestroy($imagenOrigen);
        imagedestroy($imagenDestino);

        if ($contenido === false || $contenido === null) {
            return $fotoAnterior;
        }

        $directorio = dirname(public_path($ruta));

        if (!is_dir($directorio)) {
            mkdir($directorio, 0755, true);
        }

        file_put_contents(public_path($ruta), $contenido);

        if ($fotoAnterior && file_exists(public_path($fotoAnterior))) {
            @unlink(public_path($fotoAnterior));
        }

        return $ruta;
    }
}