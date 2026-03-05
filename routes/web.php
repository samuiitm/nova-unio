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

            // Calendario
            Route::get('calendario', [\App\Http\Controllers\Panel\CalendarioController::class, 'index'])
                ->name('calendario');

            // Clases (entrar a una clase y pasar lista)
            Route::get('clases/{clase}', [\App\Http\Controllers\Panel\ClaseController::class, 'show'])
                ->name('clases.show');

            Route::post('clases/{clase}/asistencia', [\App\Http\Controllers\Panel\ClaseController::class, 'guardarAsistencia'])
                ->name('clases.asistencia');

            // Alumnos
            Route::patch('alumnos/{alumno}/baja', [\App\Http\Controllers\Panel\AlumnoController::class, 'baja'])
                ->name('alumnos.baja');

            Route::patch('alumnos/{alumno}/activar', [\App\Http\Controllers\Panel\AlumnoController::class, 'activar'])
                ->name('alumnos.activar');

            Route::resource('alumnos', \App\Http\Controllers\Panel\AlumnoController::class);

            // Grupos y programación
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

            // Clases (generación)
            Route::post('grupos/{grupo}/generar-clases', [\App\Http\Controllers\Panel\GrupoController::class, 'generarClases'])
                ->name('grupos.generar-clases');
        });

        // Perfil dentro de /panel pero con nombres de Breeze (profile.*)
        Route::get('/perfil',   [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/perfil', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/perfil',[\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
    });

require __DIR__.'/auth.php';