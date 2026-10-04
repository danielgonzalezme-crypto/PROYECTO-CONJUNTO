package com.conjuntosmart.residentesapi.exception;

public class ResidenteNotFoundException extends RuntimeException {
    public ResidenteNotFoundException(String cedula) {
        super("No existe un residente con cédula " + cedula);
    }
}