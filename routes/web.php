<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PreinscripcionController;
use App\Http\Controllers\ProfileController;

// -- Parte Pública
Route::get('/', [PublicController::class, 'home'])->name('public.home');
Route::get('/el-club', [PublicController::class, 'elclub'])->name('public.elclub');
Route::get('/profesores', [PublicController::class, 'profesores'])->name('public.profesores');
Route::get('/horarios', [PublicController::class, 'horarios'])->name('public.horarios');
Route::get('/planes', [PublicController::class, 'planes'])->name('public.planes');
Route::get('/faq', [PublicController::class, 'faq'])->name('public.faq');
Route::get('/contacto', [PublicController::class, 'contacto'])->name('public.contacto');
Route::get('/preinscripcion', [PublicController::class, 'preinscripcion'])->name('public.preinscripcion');
Route::get('/aviso-legal', [PublicController::class, 'avisoLegal'])->name('public.aviso-legal');
Route::get('/politica-privacidad', [PublicController::class, 'politicaPrivacidad'])->name('public.politica-privacidad');
Route::get('/politica-cookies', [PublicController::class, 'politicaCookies'])->name('public.politica-cookies');

Route::post('/contacto', [ContactController::class, 'store'])->name('public.contacto.enviar');
Route::post('/preinscripcion', [PreinscripcionController::class, 'store'])
    ->name('public.preinscripcion.enviar')
    ->middleware('throttle:10,1');

Route::get('/sitemap.xml', function () {
    $today = now()->toDateString();

    $urls = [
        ['loc' => route('public.home'),                 'changefreq' => 'weekly',  'priority' => '1.0'],
        ['loc' => route('public.elclub'),               'changefreq' => 'monthly', 'priority' => '0.8'],
        ['loc' => route('public.planes'),               'changefreq' => 'monthly', 'priority' => '0.8'],
        ['loc' => route('public.horarios'),             'changefreq' => 'weekly',  'priority' => '0.8'],
        ['loc' => route('public.profesores'),           'changefreq' => 'monthly', 'priority' => '0.7'],
        ['loc' => route('public.faq'),                  'changefreq' => 'monthly', 'priority' => '0.6'],
        ['loc' => route('public.contacto'),             'changefreq' => 'yearly',  'priority' => '0.5'],
        ['loc' => route('public.preinscripcion'),       'changefreq' => 'yearly',  'priority' => '0.6'],
        ['loc' => route('public.aviso-legal'),          'changefreq' => 'yearly',  'priority' => '0.2'],
        ['loc' => route('public.politica-privacidad'),  'changefreq' => 'yearly',  'priority' => '0.2'],
        ['loc' => route('public.politica-cookies'),     'changefreq' => 'yearly',  'priority' => '0.2'],
    ];

    $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    foreach ($urls as $u) {
        $xml .= "  <url>\n";
        $xml .= "    <loc>" . $u['loc'] . "</loc>\n";
        $xml .= "    <lastmod>" . $today . "</lastmod>\n";
        $xml .= "    <changefreq>" . $u['changefreq'] . "</changefreq>\n";
        $xml .= "    <priority>" . $u['priority'] . "</priority>\n";
        $xml .= "  </url>\n";
    }

    $xml .= "</urlset>\n";

    return response($xml, 200)->header('Content-Type', 'application/xml');
})->name('seo.sitemap');


// -- Parte Privada (TODO cuelga de /panel)
Route::prefix('panel')
    ->middleware(['auth', 'panel.access'])
    ->group(function () {

        // Rutas del panel (con nombre panel.*)
        Route::name('panel.')->group(function () {
            Route::view('/', 'panel.dashboard')->name('home');

            // =========================
            // PREINSCRIPCIONES
            // =========================
            Route::get('preinscripciones', [\App\Http\Controllers\Panel\PreinscripcionController::class, 'index'])
                ->name('preinscripciones.index');

            Route::get('preinscripciones/{preinscripcion}', [\App\Http\Controllers\Panel\PreinscripcionController::class, 'show'])
                ->name('preinscripciones.show');

            Route::get('preinscripciones/{preinscripcion}/convertir', [\App\Http\Controllers\Panel\PreinscripcionController::class, 'convertir'])
                ->name('preinscripciones.convertir');

            // =========================
            // ALUMNOS
            // =========================
            Route::patch('alumnos/{alumno}/baja', [\App\Http\Controllers\Panel\AlumnoController::class, 'baja'])
                ->name('alumnos.baja');

            Route::patch('alumnos/{alumno}/activar', [\App\Http\Controllers\Panel\AlumnoController::class, 'activar'])
                ->name('alumnos.activar');

            Route::resource('alumnos', \App\Http\Controllers\Panel\AlumnoController::class);

            // =========================
            // GRUPOS Y PROGRAMACIÓN
            // =========================
            Route::post('grupos/{grupo}/programaciones', [\App\Http\Controllers\Panel\GrupoProgramacionController::class, 'store'])
                ->name('grupos.programaciones.store');

            Route::patch('grupos/{grupo}/programaciones/{programacion}', [\App\Http\Controllers\Panel\GrupoProgramacionController::class, 'update'])
                ->name('grupos.programaciones.update');

            Route::delete('grupos/{grupo}/programaciones/{programacion}', [\App\Http\Controllers\Panel\GrupoProgramacionController::class, 'destroy'])
                ->name('grupos.programaciones.destroy');

            Route::post('grupos/{grupo}/alumnos', [\App\Http\Controllers\Panel\GrupoController::class, 'asignarAlumno'])
                ->name('grupos.alumnos.asignar');

            Route::patch('grupos/{grupo}/alumnos/{alumno}/baja', [\App\Http\Controllers\Panel\GrupoController::class, 'bajaAlumno'])
                ->name('grupos.alumnos.baja');

            Route::patch('grupos/{grupo}/alumnos/{alumno}/activar', [\App\Http\Controllers\Panel\GrupoController::class, 'activarAlumno'])
                ->name('grupos.alumnos.activar');

            Route::resource('grupos', \App\Http\Controllers\Panel\GrupoController::class);

            // =========================
            // CLASES
            // =========================
            Route::post('grupos/{grupo}/generar-clases', [\App\Http\Controllers\Panel\GrupoController::class, 'generarClases'])
                ->name('grupos.generar-clases');

            // Calendario
            Route::get('calendario', [\App\Http\Controllers\Panel\CalendarioController::class, 'index'])
                ->name('calendario');

            Route::get('asistencias', [\App\Http\Controllers\Panel\AsistenciaController::class, 'index'])
                ->name('asistencias.index');

            // Ver clase / pasar lista
            Route::get('clases/{clase}', [\App\Http\Controllers\Panel\ClaseController::class, 'show'])
                ->name('clases.show');

            // Guardar asistencia (pasar lista)
            Route::post('clases/{clase}/asistencia', [\App\Http\Controllers\Panel\ClaseController::class, 'guardarAsistencia'])
                ->name('clases.asistencia');

            // =========================
            // PAGOS Y CUOTAS
            // =========================
            Route::prefix('pagos')->name('pagos.')->group(function () {

                // Secciones principales
                Route::get('vencidas', [\App\Http\Controllers\Panel\PagoController::class, 'vencidas'])
                    ->name('vencidas');

                Route::get('pendientes', [\App\Http\Controllers\Panel\PagoController::class, 'pendientes'])
                    ->name('pendientes');

                Route::get('historial', [\App\Http\Controllers\Panel\PagoController::class, 'historial'])
                    ->name('historial');

                // TIPOS DE CUOTA
                Route::get('tipos', [\App\Http\Controllers\Panel\TipoCuotaController::class, 'index'])
                    ->name('tipos');

                Route::post('tipos', [\App\Http\Controllers\Panel\TipoCuotaController::class, 'store'])
                    ->name('tipos.store');

                Route::patch('tipos/{tipoCuota}', [\App\Http\Controllers\Panel\TipoCuotaController::class, 'update'])
                    ->name('tipos.update');

                Route::delete('tipos/{tipoCuota}', [\App\Http\Controllers\Panel\TipoCuotaController::class, 'destroy'])
                    ->name('tipos.destroy');

                // ---- Cuotas (asignar/cobrar/editar/anular/eliminar) ----

                // Asignar cuota a un alumno
                Route::get('cuotas/{alumno}/crear', [\App\Http\Controllers\Panel\CuotaController::class, 'create'])
                    ->name('cuotas.crear');

                Route::post('cuotas/{alumno}', [\App\Http\Controllers\Panel\CuotaController::class, 'store'])
                    ->name('cuotas.store');

                // Cobrar cuota pendiente
                Route::get('cuotas/{cuota}/cobrar', [\App\Http\Controllers\Panel\CuotaController::class, 'cobrar'])
                    ->name('cuotas.cobrar');

                Route::post('cuotas/{cuota}/cobrar', [\App\Http\Controllers\Panel\CuotaController::class, 'guardarCobro'])
                    ->name('cuotas.cobrar.guardar');

                // Editar cuota (solo pendiente)
                Route::get('cuotas/{cuota}/editar', [\App\Http\Controllers\Panel\CuotaController::class, 'edit'])
                    ->name('cuotas.edit');

                Route::patch('cuotas/{cuota}', [\App\Http\Controllers\Panel\CuotaController::class, 'update'])
                    ->name('cuotas.update');

                // Anular / eliminar
                Route::patch('cuotas/{cuota}/anular', [\App\Http\Controllers\Panel\CuotaController::class, 'anular'])
                    ->name('cuotas.anular');

                Route::delete('cuotas/{cuota}', [\App\Http\Controllers\Panel\CuotaController::class, 'destroy'])
                    ->name('cuotas.destroy');

                // Borrar un pago (ojo: siempre al final para que no choque con /vencidas, /tipos, etc)
                Route::delete('{pago}', [\App\Http\Controllers\Panel\PagoController::class, 'destroy'])
                    ->name('destroy');
            });
        });

        // Perfil dentro de /panel pero con nombres de Breeze (profile.*)
        Route::get('/perfil',   [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/perfil', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/perfil',[\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
    });

require __DIR__.'/auth.php';