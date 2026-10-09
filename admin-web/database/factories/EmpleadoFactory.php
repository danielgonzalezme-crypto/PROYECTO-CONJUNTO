<?php

namespace Database\Factories;

use App\Models\Empleado;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Empleado>
 */
class EmpleadoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'cedula' => (string) fake()->unique()->numberBetween(10000000, 1999999999),
            'nombre' => fake()->name(),
            'cargo' => fake()->randomElement(Empleado::CARGOS),
            'turno' => fake()->randomElement(Empleado::TURNOS),
            'torre_asignada' => fake()->randomElement(['A', 'B', 'C']),
            'activo' => true,
        ];
    }

    public function inactivo(): static
    {
        return $this->state(['activo' => false]);
    }
}
