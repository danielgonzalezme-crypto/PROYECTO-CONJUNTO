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

    public ResidenteResponse toResponse(Residente r) {
        return new ResidenteResponse(r.getId(), r.getCedula(), r.getNombre(),
                r.getTorre(), r.getApartamento(), r.getDeudaActual(),
                r.isTieneParqueadero());
    }
}