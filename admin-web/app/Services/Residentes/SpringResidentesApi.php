<?php

namespace App\Services\Residentes;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class SpringResidentesApi implements ResidentesApi
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly int $timeout,
    ) {}

    public function listar(?string $torre = null): array
    {
        return $this->get('/api/residentes', array_filter(['torre' => $torre]));
    }

    public function morosos(): array
    {
        return $this->get('/api/residentes/morosos');
    }

    /**
     * @param  array<string, string>  $consulta
     * @return array<int, array<string, mixed>>
     */
    private function get(string $ruta, array $consulta = []): array
    {
        try {
            $respuesta = Http::baseUrl($this->baseUrl)
                ->timeout($this->timeout)
                ->acceptJson()
                ->get($ruta, $consulta);
        } catch (ConnectionException $e) {
            throw new ResidentesApiNoDisponible(
                'No se pudo conectar con la API de residentes.', 0, $e
            );
        }

        if ($respuesta->failed()) {
            throw new ResidentesApiNoDisponible(
                'La API de residentes respondió con error '.$respuesta->status().'.'
            );
        }

        return $respuesta->json() ?? [];
    }
}
