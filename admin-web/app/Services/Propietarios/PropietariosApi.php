<?php

namespace App\Services\Propietarios;

interface PropietariosApi
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function listar(): array;

    /**
     * @return array<string, mixed>|null
     */
    public function obtener(string $cedula): ?array;
}
