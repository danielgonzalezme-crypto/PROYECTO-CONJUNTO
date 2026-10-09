<?php

namespace App\Models;

use Database\Factories\EmpleadoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    /** @use HasFactory<EmpleadoFactory> */
    use HasFactory;

    public const CARGOS = ['Vigilante', 'Aseador', 'Mantenimiento', 'Administrativo'];

    public const TURNOS = ['Mañana', 'Tarde', 'Noche'];

    protected $fillable = [
        'cedula',
        'nombre',
        'cargo',
        'turno',
        'torre_asignada',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }
}
