package com.conjuntosmart.residentesapi.dto;

import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.NotNull;
import jakarta.validation.constraints.PositiveOrZero;
import java.math.BigDecimal;

public record ResidenteRequest(
        @NotBlank String cedula,
        @NotBlank String nombre,
        @NotBlank String torre,
        @NotBlank String apartamento,
        @NotNull @PositiveOrZero BigDecimal deudaActual,
        boolean tieneParqueadero) {
}