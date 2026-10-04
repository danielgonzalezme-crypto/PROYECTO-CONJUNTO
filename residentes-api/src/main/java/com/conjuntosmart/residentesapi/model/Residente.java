package com.conjuntosmart.residentesapi.model;

import jakarta.persistence.*;
import jakarta.validation.constraints.NotBlank;
import java.math.BigDecimal;

@Entity
@Table(name = "residentes")
public class Residente {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @NotBlank
    @Column(nullable = false, unique = true)
    private String cedula;

    @NotBlank
    @Column(nullable = false)
    private String nombre;

    private String torre;
    private String apartamento;

    @Column(name = "deuda_actual", nullable = false)
    private BigDecimal deudaActual = BigDecimal.ZERO;

    @Column(name = "tiene_parqueadero", nullable = false)
    private boolean tieneParqueadero;

    public Long getId() {
        return id;
    }

    public String getCedula() {
        return cedula;
    }

    public void setCedula(String cedula) {
        this.cedula = cedula;
    }

    public String getNombre() {
        return nombre;
    }

    public void setNombre(String nombre) {
        this.nombre = nombre;
    }

    public String getTorre() {
        return torre;
    }

    public void setTorre(String torre) {
        this.torre = torre;
    }

    public String getApartamento() {
        return apartamento;
    }

    public void setApartamento(String apartamento) {
        this.apartamento = apartamento;
    }

    public BigDecimal getDeudaActual() {
        return deudaActual;
    }

    public void setDeudaActual(BigDecimal deudaActual) {
        this.deudaActual = deudaActual;
    }

    public boolean isTieneParqueadero() {
        return tieneParqueadero;
    }

    public void setTieneParqueadero(boolean tieneParqueadero) {
        this.tieneParqueadero = tieneParqueadero;
    }
}