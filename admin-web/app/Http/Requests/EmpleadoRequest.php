<?php

namespace App\Http\Requests;

use App\Models\Empleado;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmpleadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cedula' => [
                'required', 'string', 'max:20',
                Rule::unique('empleados', 'cedula')->ignore($this->route('empleado')),
            ],
            'nombre' => ['required', 'string', 'max:255'],
            'cargo' => ['required', Rule::in(Empleado::CARGOS)],
            'turno' => ['required', Rule::in(Empleado::TURNOS)],
            'torre_asignada' => ['required', 'string', 'max:10'],
            'activo' => ['nullable', 'boolean'],
        ];
    }
        public function attributes(): array
    {
        return [
            'cedula' => 'cédula',
            'nombre' => 'nombre',
            'cargo' => 'cargo',
            'turno' => 'turno',
            'torre_asignada' => 'torre asignada',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser texto.',
            'max' => 'El campo :attribute no puede superar :max caracteres.',
            'in' => 'El valor elegido para :attribute no es válido.',
            'cedula.unique' => 'Ya existe un empleado con esa cédula.',
        ];
    }
}