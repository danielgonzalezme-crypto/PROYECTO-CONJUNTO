package com.conjuntosmart.residentesapi.service;

import com.conjuntosmart.residentesapi.dto.ResidenteRequest;
import com.conjuntosmart.residentesapi.dto.ResidenteResponse;
import com.conjuntosmart.residentesapi.exception.CedulaDuplicadaException;
import com.conjuntosmart.residentesapi.exception.ResidenteNotFoundException;
import com.conjuntosmart.residentesapi.mapper.ResidenteMapper;
import com.conjuntosmart.residentesapi.model.Residente;
import com.conjuntosmart.residentesapi.repository.ResidenteRepository;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.Test;
import org.junit.jupiter.api.extension.ExtendWith;
import org.junit.jupiter.params.ParameterizedTest;
import org.junit.jupiter.params.provider.NullAndEmptySource;
import org.junit.jupiter.params.provider.ValueSource;
import org.mockito.Mock;
import org.mockito.junit.jupiter.MockitoExtension;

import java.math.BigDecimal;
import java.util.List;
import java.util.Optional;

import static org.assertj.core.api.Assertions.assertThat;
import static org.assertj.core.api.Assertions.assertThatThrownBy;
import static org.mockito.ArgumentMatchers.any;
import static org.mockito.Mockito.*;

@ExtendWith(MockitoExtension.class)
class ResidenteServiceImplTest {

    @Mock
    private ResidenteRepository repository;

    private ResidenteServiceImpl service;

    @BeforeEach
    void setUp() {
        service = new ResidenteServiceImpl(repository, new ResidenteMapper());
    }

    private static ResidenteRequest request(String cedula) {
        return new ResidenteRequest(cedula, "Ana Torres", "A", "101", new BigDecimal("150000"), true);
    }

    private static Residente residente(String cedula) {
        return new ResidenteMapper().toEntity(request(cedula));
    }

    @ParameterizedTest
    @NullAndEmptySource
    @ValueSource(strings = {"   "})
    void listarSinTorreDevuelveTodos(String torre) {
        when(repository.findAll()).thenReturn(List.of(residente("1"), residente("2")));

        assertThat(service.listar(torre)).hasSize(2);
        verify(repository, never()).findByTorre(any());
    }

    @Test
    void listarConTorreFiltra() {
        when(repository.findByTorre("B")).thenReturn(List.of(residente("1")));

        assertThat(service.listar("B")).extracting(ResidenteResponse::cedula).containsExactly("1");
        verify(repository, never()).findAll();
    }

    @Test
    void listarMorososBuscaDeudaMayorACero() {
        when(repository.findByDeudaActualGreaterThan(BigDecimal.ZERO)).thenReturn(List.of(residente("1")));

        assertThat(service.listarMorosos()).hasSize(1);
    }

    @Test
    void obtenerPorCedulaDevuelveElResidente() {
        when(repository.findByCedula("1")).thenReturn(Optional.of(residente("1")));

        ResidenteResponse respuesta = service.obtenerPorCedula("1");

        assertThat(respuesta.nombre()).isEqualTo("Ana Torres");
        assertThat(respuesta.deudaActual()).isEqualByComparingTo("150000");
        assertThat(respuesta.tieneParqueadero()).isTrue();
    }

    @Test
    void obtenerPorCedulaInexistenteLanzaExcepcion() {
        when(repository.findByCedula("9")).thenReturn(Optional.empty());

        assertThatThrownBy(() -> service.obtenerPorCedula("9"))
                .isInstanceOf(ResidenteNotFoundException.class)
                .hasMessage("No existe un residente con cédula 9");
    }

    @Test
    void crearGuardaElResidente() {
        when(repository.existsByCedula("1")).thenReturn(false);
        when(repository.save(any(Residente.class))).thenAnswer(inv -> inv.getArgument(0));

        ResidenteResponse respuesta = service.crear(request("1"));

        assertThat(respuesta.cedula()).isEqualTo("1");
        verify(repository).save(any(Residente.class));
    }

    @Test
    void crearConCedulaDuplicadaNoGuarda() {
        when(repository.existsByCedula("1")).thenReturn(true);

        assertThatThrownBy(() -> service.crear(request("1")))
                .isInstanceOf(CedulaDuplicadaException.class)
                .hasMessage("Ya existe un residente con cédula 1");
        verify(repository, never()).save(any());
    }

    @Test
    void actualizarConLaMismaCedulaNoRevisaDuplicados() {
        Residente existente = residente("1");
        when(repository.findByCedula("1")).thenReturn(Optional.of(existente));
        when(repository.save(existente)).thenReturn(existente);

        ResidenteRequest cambios = new ResidenteRequest("1", "Ana T.", "B", "202", BigDecimal.ZERO, false);
        ResidenteResponse respuesta = service.actualizar("1", cambios);

        assertThat(respuesta.nombre()).isEqualTo("Ana T.");
        assertThat(respuesta.torre()).isEqualTo("B");
        verify(repository, never()).existsByCedula(any());
    }

    @Test
    void actualizarAUnaCedulaNuevaLibreLaCambia() {
        Residente existente = residente("1");
        when(repository.findByCedula("1")).thenReturn(Optional.of(existente));
        when(repository.existsByCedula("3")).thenReturn(false);
        when(repository.save(existente)).thenReturn(existente);

        assertThat(service.actualizar("1", request("3")).cedula()).isEqualTo("3");
    }

    @Test
    void actualizarALaCedulaDeOtroResidenteLanzaExcepcion() {
        when(repository.findByCedula("1")).thenReturn(Optional.of(residente("1")));
        when(repository.existsByCedula("2")).thenReturn(true);

        assertThatThrownBy(() -> service.actualizar("1", request("2")))
                .isInstanceOf(CedulaDuplicadaException.class);
        verify(repository, never()).save(any());
    }

    @Test
    void actualizarUnResidenteInexistenteLanzaExcepcion() {
        when(repository.findByCedula("9")).thenReturn(Optional.empty());

        assertThatThrownBy(() -> service.actualizar("9", request("9")))
                .isInstanceOf(ResidenteNotFoundException.class);
    }

    @Test
    void eliminarBorraElResidente() {
        Residente existente = residente("1");
        when(repository.findByCedula("1")).thenReturn(Optional.of(existente));

        service.eliminar("1");

        verify(repository).delete(existente);
    }

    @Test
    void eliminarUnResidenteInexistenteLanzaExcepcion() {
        when(repository.findByCedula("9")).thenReturn(Optional.empty());

        assertThatThrownBy(() -> service.eliminar("9")).isInstanceOf(ResidenteNotFoundException.class);
        verify(repository, never()).delete(any());
    }
}
