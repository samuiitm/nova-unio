<?php

use App\Services\GeneradorClasesService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('clases:generar-proximas-dos-semanas', function (GeneradorClasesService $generador) {
    $resultado = $generador->generarAutomaticoDomingo();

    $this->info(
        'Clases aseguradas del '
        . $resultado['inicio']->format('d/m/Y')
        . ' al '
        . $resultado['fin']->format('d/m/Y')
        . '. Nuevas: '
        . $resultado['creadas']
        . '.'
    );
})->purpose('Asegura clases para las 2 próximas semanas');

Schedule::command('clases:generar-proximas-dos-semanas')
    ->sundays()
    ->at('03:00')
    ->timezone('Europe/Malta')
    ->withoutOverlapping();