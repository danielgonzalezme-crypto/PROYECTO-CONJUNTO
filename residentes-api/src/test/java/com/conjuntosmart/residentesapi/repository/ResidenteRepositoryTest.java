package com.conjuntosmart.residentesapi.repository;

import com.conjuntosmart.residentesapi.model.Residente;
import org.junit.jupiter.api.Test;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.data.jpa.test.autoconfigure.DataJpaTest;
import org.springframework.dao.DataIntegrityViolationException;

import java.math.BigDecimal;

import static org.assertj.core.api.Assertions.assertThat;
import static org.assertj.core.api.Assertions.assertThatThrownBy;

@DataJpaTest
class ResidenteRepositoryTest {

    @Autowired
    private ResidenteRepository repository;

    private Residente guardar(String cedula, String torre, String deuda) {
        Residente r = new Residente();
        r.setCedula(cedula);
        r.setNombre("Residente " + cedula);
        r.setTorre(torre);
        r.setApartamento("101");
        r.setDeudaActual(new BigDecimal(deuda));
        return repository.saveAndFlush(r);
    }

    @Test
    void buscaPorCedula() {
        guardar("1", "A", "0");

        assertThat(repository.findByCedula("1")).isPresent();
        assertThat(repository.findByCedula("2")).isEmpty();
        assertThat(repository.existsByCedula("1")).isTrue();
    }

    @Test
    void filtraPorTorre() {
        guardar("1", "A", "0");
        guardar("2", "B", "0");

        assertThat(repository.findByTorre("A")).extracting(Residente::getCedula).containsExactly("1");
    }

    @Test
    void encuentraSoloLosQueTienenDeuda() {
        guardar("1", "A", "0");
        guardar("2", "A", "0.01");
        guardar("3", "A", "150000");

        assertThat(repository.findByDeudaActualGreaterThan(BigDecimal.ZERO))
                .extracting(Residente::getCedula)
                .containsExactlyInAnyOrder("2", "3");
    }

    @Test
    void laBaseDeDatosImpideCedulasRepetidas() {
        guardar("1", "A", "0");

        assertThatThrownBy(() -> guardar("1", "B", "0"))
                .isInstanceOf(DataIntegrityViolationException.class);
    }
}
