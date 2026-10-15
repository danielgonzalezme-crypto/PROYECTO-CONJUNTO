package com.conjuntosmart.residentesapi.service;

import com.conjuntosmart.residentesapi.dto.ResidenteRequest;
import com.conjuntosmart.residentesapi.dto.ResidenteResponse;
import java.math.BigDecimal;
import java.util.List;

public interface ResidenteService {
    List<ResidenteResponse> listar(String torre, BigDecimal deudaMinima);
    List<ResidenteResponse> listarMorosos();
    ResidenteResponse obtenerPorCedula(String cedula);
    ResidenteResponse crear(ResidenteRequest request);
    ResidenteResponse actualizar(String cedula, ResidenteRequest request);
    void eliminar(String cedula);
}
