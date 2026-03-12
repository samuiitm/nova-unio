@extends('layouts.panel')

@section('title', 'Crear usuario | Nova Unió')

@section('content')
<div class="flex items-start justify-between gap-4">
    <div>
        <h1 class="text-2xl font-semibold">Crear usuario</h1>
        <p class="mt-1 panel-muted">Alta de administradores y entrenadores con acceso al panel.</p>
    </div>

    <a href="{{ route('panel.usuarios.index') }}" class="panel-icon-btn px-5 py-3">Volver</a>
</div>

@if($errors->any())
    <div class="mt-5 panel-card p-4">
        <div class="text-sm font-medium">Revisa estos campos:</div>
        <ul class="mt-2 text-sm panel-muted list-disc pl-5">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('panel.usuarios.store') }}" class="mt-5">
    @csrf

    @include('panel.usuarios._form')

    <div class="mt-6 flex gap-3">
        <button class="panel-btn px-6 py-3">Guardar</button>
        <a href="{{ route('panel.usuarios.index') }}" class="panel-icon-btn px-6 py-3">Cancelar</a>
    </div>
</form>
@endsection