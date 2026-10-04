@extends('layouts.app')

@section('titulo', 'Nuevo empleado')

@section('contenido')
    <h1>Nuevo empleado</h1>
    <form action="{{ route('empleados.store') }}" method="POST">
        @csrf
        @include('empleados._form')
    </form>
@endsection