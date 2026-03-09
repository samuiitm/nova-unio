@extends('layouts.panel')

@section('title', 'Editar usuario | Nova Unió')

@section('content')
<div class="flex items-start justify-between gap-4">
    <div>
        <h1 class="text-2xl font-semibold">Editar usuario</h1>
        <p class="mt-1 panel-muted">{{ $usuario->nombre_completo }}</p>
    </div>

    <a href="{{ route('panel.usuarios.index') }}" class="panel-icon-btn px-5 py-3">Volver</a>
</div>

@if(session('error'))
    <div class="mt-5 panel-card p-4">
        <div class="text-sm">{{ session('error') }}</div>
    </div>
@endif

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

<form method="POST" action="{{ route('panel.usuarios.update', $usuario) }}" class="mt-5">
    @csrf
    @method('PATCH')

    @include('panel.usuarios._form', ['usuario' => $usuario])

    <div class="mt-6 flex gap-3">
        <button class="panel-btn px-6 py-3">Guardar cambios</button>
        <a href="{{ route('panel.usuarios.index') }}" class="panel-icon-btn px-6 py-3">Cancelar</a>
    </div>
</form>
@endsection