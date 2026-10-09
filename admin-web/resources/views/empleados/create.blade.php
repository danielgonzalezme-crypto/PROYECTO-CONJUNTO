@extends('layouts.app')

@section('titulo', 'Registrar Nuevo Empleado')

@section('contenido')
<div style="max-width: 680px; margin: 0 auto 3rem;">
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('empleados.index') }}" style="display: inline-flex; align-items: center; gap: 0.4rem; color: #64748b; font-size: 0.88rem; font-weight: 600; text-decoration: none; margin-bottom: 0.75rem; transition: color 0.15s ease;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Volver al directorio de empleados
        </a>
        <h1 style="font-size: 1.75rem; font-weight: 800; color: #0f172a; margin: 0; letter-spacing: -0.025em;">Registrar Nuevo Empleado</h1>
        <p style="color: #64748b; margin: 0.35rem 0 0; font-size: 0.92rem;">Ingresa los datos del colaborador y asigna su puesto, turno y torre.</p>
    </div>

    <form action="{{ route('empleados.store') }}" method="POST">
        @csrf
        @include('empleados._form')
    </form>
</div>
@endsection