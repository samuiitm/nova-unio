@extends('layouts.panel-error')

@section('title', '403 | Panel | Nova Unió')

@section('content')
@php
    $panelHome = \Illuminate\Support\Facades\Route::has('panel.home') ? route('panel.home') : url('/panel');
    $calendario = \Illuminate\Support\Facades\Route::has('panel.calendario') ? route('panel.calendario') : $panelHome;
@endphp

<div class="w-full max-w-4xl">
    <div class="panel-card overflow-hidden">
        <div class="p-8 sm:p-10 lg:p-12">
            <div class="grid gap-8 lg:grid-cols-[auto,1fr] items-center">
                <div class="select-none leading-none font-black text-white/10 text-[6rem] sm:text-[8rem] md:text-[10rem]">
                    403
                </div>

                <div>
                    <p class="uppercase tracking-wider text-sm panel-muted">
                        Acceso restringido
                    </p>

                    <h1 class="mt-3 text-3xl sm:text-4xl font-semibold">
                        No tienes permiso para entrar aquí
                    </h1>

                    <p class="mt-4 max-w-2xl text-sm sm:text-base panel-muted">
                        Esta sección del panel no está disponible para tu usuario actual.
                        Puede ser una página solo para administradores o una ruta fuera de tus permisos.
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ $panelHome }}" class="panel-btn px-5 py-3">
                            Volver al dashboard
                        </a>

                        <a href="{{ $calendario }}" class="panel-icon-btn px-5 py-3">
                            Ir al calendario
                        </a>
                    </div>

                    <p class="mt-6 text-xs panel-muted">
                        Código: 403
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection