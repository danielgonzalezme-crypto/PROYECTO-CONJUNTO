<?php

namespace App\Services\Propietarios;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class NodePropietariosApi implements PropietariosApi
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly int $timeout,
    ) {}

    public function listar(): array
    {
        try {
            $respuesta = Http::baseUrl($this->baseUrl)
                ->timeout($this->timeout)
                ->acceptJson()
                ->get('/api/propietarios');
        } catch (ConnectionException $e) {
            throw new PropietariosApiNoDisponible(
                'No se pudo conectar con la API de propietarios.', 0, $e
            );
        }

        if ($respuesta->failed()) {
            throw new PropietariosApiNoDisponible(
                'La API de propietarios respondió con error '.$respuesta->status().'.'
            );
        }

        return $respuesta->json() ?? [];
    }
}
