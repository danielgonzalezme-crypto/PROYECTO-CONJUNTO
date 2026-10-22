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

    /**
     * @return array<string, mixed>|null
     */
    public function obtener(string $cedula): ?array
    {
        try {
            $respuesta = Http::baseUrl($this->baseUrl)
                ->timeout($this->timeout)
                ->acceptJson()
                ->get('/api/propietarios/'.rawurlencode($cedula));
        } catch (ConnectionException $e) {
            throw new PropietariosApiNoDisponible(
                'No se pudo conectar con la API de propietarios.', 0, $e
            );
        }

        if ($respuesta->status() === 404) {
            return null;
        }

        if ($respuesta->failed()) {
            throw new PropietariosApiNoDisponible(
                'La API de propietarios respondió con error '.$respuesta->status().'.'
            );
        }

        /** @var array<string, mixed>|null $json */
        $json = $respuesta->json();

        return $json;
    }
}
