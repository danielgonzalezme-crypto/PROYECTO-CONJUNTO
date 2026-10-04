package com.conjuntosmart.residentesapi.dto;

import java.math.BigDecimal;

public record ResidenteResponse(
        Long id,
        String cedula,
        String nombre,
        String torre,
        String apartamento,
        BigDecimal deudaActual,
        boolean tieneParqueadero) {
}