<?php

namespace App\Services\Residentes;

/**
 * Consulta la API de residentes sin romper la pantalla si la API no responde:
 * en ese caso devuelve una lista vacía y el mensaje de error para mostrar un aviso.
 */
class ConsultaResidentes
{
    public function __construct(private readonly ResidentesApi $api) {}

    /**
     * @return array{residentes: array<int, array<string, mixed>>, error: ?string}
     */
    public function listar(?string $torre = null): array
    {
        try {
            return ['residentes' => $this->api->listar($torre), 'error' => null];
        } catch (ResidentesApiNoDisponible $e) {
            return ['residentes' => [], 'error' => $e->getMessage()];
        }
    }

    /**
     * @return array{residente: array<string, mixed>|null, error: ?string}
     */
    public function obtener(string $cedula): array
    {
        try {
            return ['residente' => $this->api->obtener($cedula), 'error' => null];
        } catch (ResidentesApiNoDisponible $e) {
            return ['residente' => null, 'error' => $e->getMessage()];
        }
    }
}
