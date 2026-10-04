package com.conjuntosmart.residentesapi.controller;

import com.conjuntosmart.residentesapi.dto.ResidenteRequest;
import com.conjuntosmart.residentesapi.dto.ResidenteResponse;
import com.conjuntosmart.residentesapi.service.ResidenteService;
import jakarta.validation.Valid;
import org.springframework.http.HttpStatus;
import org.springframework.web.bind.annotation.*;
import java.util.List;

@RestController
@RequestMapping("/api/residentes")
public class ResidenteController {

    private final ResidenteService service;

    public ResidenteController(ResidenteService service) {
        this.service = service;
    }

    @GetMapping
    public List<ResidenteResponse> listar(@RequestParam(name = "torre", required = false) String torre) {
        return service.listar(torre);
    }

    @GetMapping("/morosos")
    public List<ResidenteResponse> morosos() {
        return service.listarMorosos();
    }

    @GetMapping("/{cedula}")
    public ResidenteResponse obtener(@PathVariable("cedula") String cedula) {
        return service.obtenerPorCedula(cedula);
    }

    @PostMapping
    @ResponseStatus(HttpStatus.CREATED)
    public ResidenteResponse crear(@Valid @RequestBody ResidenteRequest request) {
        return service.crear(request);
    }

    @PutMapping("/{cedula}")
    public ResidenteResponse actualizar(@PathVariable("cedula") String cedula,
                                        @Valid @RequestBody ResidenteRequest request) {
        return service.actualizar(cedula, request);
    }

    @DeleteMapping("/{cedula}")
    @ResponseStatus(HttpStatus.NO_CONTENT)
    public void eliminar(@PathVariable("cedula") String cedula) {
        service.eliminar(cedula);
    }
}