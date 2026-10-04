package com.conjuntosmart.residentesapi.exception;

public class CedulaDuplicadaException extends RuntimeException {
    public CedulaDuplicadaException(String cedula) {
        super("Ya existe un residente con cédula " + cedula);
    }
}