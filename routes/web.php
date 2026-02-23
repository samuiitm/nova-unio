<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;

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

require __DIR__.'/auth.php';