<?php

namespace App\Services\Residentes;

interface ResidentesApi
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function listar(?string $torre = null): array;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function morosos(): array;

    /**
     * @return array<string, mixed>|null
     */
    public function obtener(string $cedula): ?array;
}
