package com.conjuntosmart.residentesapi.service;

import com.conjuntosmart.residentesapi.dto.ResidenteRequest;
import com.conjuntosmart.residentesapi.dto.ResidenteResponse;
import com.conjuntosmart.residentesapi.exception.CedulaDuplicadaException;
import com.conjuntosmart.residentesapi.exception.ResidenteNotFoundException;
import com.conjuntosmart.residentesapi.mapper.ResidenteMapper;
import com.conjuntosmart.residentesapi.model.Residente;
import com.conjuntosmart.residentesapi.repository.ResidenteRepository;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;
import java.math.BigDecimal;
import java.util.List;

@Service
@Transactional
public class ResidenteServiceImpl implements ResidenteService {

    private final ResidenteRepository repository;
    private final ResidenteMapper mapper;

    public ResidenteServiceImpl(ResidenteRepository repository, ResidenteMapper mapper) {
        this.repository = repository;
        this.mapper = mapper;
    }

    @Override
    @Transactional(readOnly = true)
    public List<ResidenteResponse> listar(String torre, BigDecimal deudaMinima) {
        boolean hayTorre = torre != null && !torre.isBlank();
        boolean hayDeuda = deudaMinima != null;
        List<Residente> residentes;
        if (hayTorre && hayDeuda) {
            residentes = repository.findByTorreAndDeudaActualGreaterThanEqual(torre, deudaMinima);
        } else if (hayTorre) {
            residentes = repository.findByTorre(torre);
        } else if (hayDeuda) {
            residentes = repository.findByDeudaActualGreaterThanEqual(deudaMinima);
        } else {
            residentes = repository.findAll();
        }
        return residentes.stream().map(mapper::toResponse).toList();
    }

    @Override
    @Transactional(readOnly = true)
    public List<ResidenteResponse> listarMorosos() {
        return repository.findByDeudaActualGreaterThan(BigDecimal.ZERO)
                .stream().map(mapper::toResponse).toList();
    }

    @Override
    @Transactional(readOnly = true)
    public ResidenteResponse obtenerPorCedula(String cedula) {
        return mapper.toResponse(buscar(cedula));
    }

    @Override
    public ResidenteResponse crear(ResidenteRequest request) {
        if (repository.existsByCedula(request.cedula())) {
            throw new CedulaDuplicadaException(request.cedula());
        }
        return mapper.toResponse(repository.save(mapper.toEntity(request)));
    }

    @Override
    public ResidenteResponse actualizar(String cedula, ResidenteRequest request) {
        Residente residente = buscar(cedula);
        boolean cambiaCedula = !cedula.equals(request.cedula());
        if (cambiaCedula && repository.existsByCedula(request.cedula())) {
            throw new CedulaDuplicadaException(request.cedula());
        }
        mapper.updateEntity(residente, request);
        return mapper.toResponse(repository.save(residente));
    }

    @Override
    public void eliminar(String cedula) {
        repository.delete(buscar(cedula));
    }

    private Residente buscar(String cedula) {
        return repository.findByCedula(cedula)
                .orElseThrow(() -> new ResidenteNotFoundException(cedula));
    }
}
