@extends('layouts.app')

@section('titulo', 'Editar empleado')

@section('contenido')
    <h1>Editar empleado</h1>
    <form action="{{ route('empleados.update', $empleado) }}" method="POST">
        @csrf
        @method('PUT')
        @include('empleados._form')
    </form>
@endsection