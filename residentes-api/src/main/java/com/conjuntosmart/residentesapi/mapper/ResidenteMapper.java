package com.conjuntosmart.residentesapi.mapper;

import com.conjuntosmart.residentesapi.dto.ResidenteRequest;
import com.conjuntosmart.residentesapi.dto.ResidenteResponse;
import com.conjuntosmart.residentesapi.model.Residente;
import org.springframework.stereotype.Component;

@Component
public class ResidenteMapper {

    public Residente toEntity(ResidenteRequest request) {
        Residente residente = new Residente();
        updateEntity(residente, request);
        return residente;
    }

    public void updateEntity(Residente residente, ResidenteRequest request) {
        residente.setCedula(request.cedula());
        residente.setNombre(request.nombre());
        residente.setTorre(request.torre());
        residente.setApartamento(request.apartamento());
        residente.setDeudaActual(request.deudaActual());
        residente.setTieneParqueadero(request.tieneParqueadero());
    }

    public ResidenteResponse toResponse(Residente residente) {
        return new ResidenteResponse(residente.getId(), residente.getCedula(), residente.getNombre(),
                residente.getTorre(), residente.getApartamento(), residente.getDeudaActual(),
                residente.isTieneParqueadero());
    }
}