package com.conjuntosmart.residentesapi.controller;

import com.conjuntosmart.residentesapi.repository.ResidenteRepository;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.Test;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.test.context.SpringBootTest;
import org.springframework.boot.webmvc.test.autoconfigure.AutoConfigureMockMvc;
import org.springframework.http.MediaType;
import org.springframework.test.web.servlet.MockMvc;
import org.springframework.test.web.servlet.ResultActions;

import static org.hamcrest.Matchers.*;
import static org.springframework.test.web.servlet.request.MockMvcRequestBuilders.*;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.*;

/**
 * Prueba la API completa (controlador, validaciones, manejo de errores y base de datos H2)
 * tal como la consumen Laravel o Postman.
 */
@SpringBootTest
@AutoConfigureMockMvc
class ResidenteApiIntegracionTest {

    private static final String URL = "/api/residentes";

    @Autowired
    private MockMvc mvc;

    @Autowired
    private ResidenteRepository repository;

    @BeforeEach
    void limpiar() {
        repository.deleteAll();
    }

    private static String json(String cedula, String torre, String deuda) {
        return """
                {"cedula":"%s","nombre":"Ana Torres","torre":"%s","apartamento":"101",
                 "deudaActual":%s,"tieneParqueadero":true}
                """.formatted(cedula, torre, deuda);
    }

    private ResultActions crear(String cuerpo) throws Exception {
        return mvc.perform(post(URL).contentType(MediaType.APPLICATION_JSON).content(cuerpo));
    }

    @Test
    void creaUnResidenteYResponde201() throws Exception {
        crear(json("1000000001", "A", "150000"))
                .andExpect(status().isCreated())
                .andExpect(jsonPath("$.id").isNumber())
                .andExpect(jsonPath("$.cedula").value("1000000001"))
                .andExpect(jsonPath("$.tieneParqueadero").value(true));
    }

    @Test
    void consultaUnResidentePorCedula() throws Exception {
        crear(json("1000000001", "A", "150000"));

        mvc.perform(get(URL + "/1000000001"))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$.nombre").value("Ana Torres"));
    }

    @Test
    void consultarUnResidenteQueNoExisteResponde404() throws Exception {
        mvc.perform(get(URL + "/999"))
                .andExpect(status().isNotFound())
                .andExpect(jsonPath("$.detail").value("No existe un residente con cédula 999"));
    }

    @Test
    void crearConCedulaDuplicadaResponde409() throws Exception {
        crear(json("1000000001", "A", "0"));

        crear(json("1000000001", "B", "0"))
                .andExpect(status().isConflict())
                .andExpect(jsonPath("$.detail").value("Ya existe un residente con cédula 1000000001"));
    }

    @Test
    void crearConDatosInvalidosResponde400ConLosCampos() throws Exception {
        crear("""
                {"cedula":"","nombre":"","torre":"","apartamento":"","deudaActual":-5}
                """)
                .andExpect(status().isBadRequest())
                .andExpect(jsonPath("$.detail").value("Datos inválidos"))
                .andExpect(jsonPath("$.errores", aMapWithSize(5)))
                .andExpect(jsonPath("$.errores.deudaActual").exists());
    }

    @Test
    void crearSinDeudaResponde400() throws Exception {
        crear("""
                {"cedula":"1","nombre":"Ana","torre":"A","apartamento":"101"}
                """)
                .andExpect(status().isBadRequest())
                .andExpect(jsonPath("$.errores.deudaActual").exists());
    }

    @Test
    void sinElCampoParqueaderoSeAsumeQueNoTiene() throws Exception {
        // Defecto D-01: antes respondía 400 con un error técnico de Jackson.
        crear("""
                {"cedula":"1","nombre":"Ana","torre":"A","apartamento":"101","deudaActual":0}
                """)
                .andExpect(status().isCreated())
                .andExpect(jsonPath("$.tieneParqueadero").value(false));
    }

    @Test
    void unJsonMalFormadoResponde400ConUnMensajeClaro() throws Exception {
        // Defecto D-02: antes no explicaba el problema.
        crear("{esto no es json")
                .andExpect(status().isBadRequest())
                .andExpect(jsonPath("$.detail", containsString("no es un JSON válido")));
    }

    @Test
    void unTipoDeDatoIncorrectoResponde400() throws Exception {
        crear(json("1", "A", "\"mucho\"")).andExpect(status().isBadRequest());
    }

    @Test
    void listaTodosOFiltraPorTorre() throws Exception {
        crear(json("1", "A", "0"));
        crear(json("2", "A", "0"));
        crear(json("3", "B", "0"));

        mvc.perform(get(URL)).andExpect(jsonPath("$", hasSize(3)));
        mvc.perform(get(URL).param("torre", "A"))
                .andExpect(jsonPath("$", hasSize(2)))
                .andExpect(jsonPath("$[*].torre", everyItem(is("A"))));
    }

    @Test
    void listaSoloLosMorosos() throws Exception {
        crear(json("1", "A", "150000"));
        crear(json("2", "A", "0"));

        mvc.perform(get(URL + "/morosos"))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$", hasSize(1)))
                .andExpect(jsonPath("$[0].cedula").value("1"));
    }

    @Test
    void actualizaUnResidente() throws Exception {
        crear(json("1", "A", "150000"));

        mvc.perform(put(URL + "/1").contentType(MediaType.APPLICATION_JSON).content(json("1", "C", "0")))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$.torre").value("C"))
                .andExpect(jsonPath("$.deudaActual").value(0));
    }

    @Test
    void actualizaLaCedulaSiLaNuevaEstaLibre() throws Exception {
        crear(json("1", "A", "0"));

        mvc.perform(put(URL + "/1").contentType(MediaType.APPLICATION_JSON).content(json("5", "A", "0")))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$.cedula").value("5"));
        mvc.perform(get(URL + "/1")).andExpect(status().isNotFound());
        mvc.perform(get(URL + "/5")).andExpect(status().isOk());
    }

    @Test
    void actualizarALaCedulaDeOtroResponde409() throws Exception {
        crear(json("1", "A", "0"));
        crear(json("2", "A", "0"));

        mvc.perform(put(URL + "/1").contentType(MediaType.APPLICATION_JSON).content(json("2", "A", "0")))
                .andExpect(status().isConflict());
    }

    @Test
    void eliminaUnResidenteYLuegoYaNoExiste() throws Exception {
        crear(json("1", "A", "0"));

        mvc.perform(delete(URL + "/1")).andExpect(status().isNoContent());
        mvc.perform(get(URL + "/1")).andExpect(status().isNotFound());
    }

    @Test
    void eliminarUnResidenteQueNoExisteResponde404() throws Exception {
        mvc.perform(delete(URL + "/999")).andExpect(status().isNotFound());
    }
}
