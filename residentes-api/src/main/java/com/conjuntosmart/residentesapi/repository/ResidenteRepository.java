package com.conjuntosmart.residentesapi.repository;

import com.conjuntosmart.residentesapi.model.Residente;
import org.springframework.data.jpa.repository.JpaRepository;
import java.math.BigDecimal;
import java.util.List;
import java.util.Optional;

public interface ResidenteRepository extends JpaRepository<Residente, Long> {
    Optional<Residente> findByCedula(String cedula);
    boolean existsByCedula(String cedula);
    List<Residente> findByTorre(String torre);
    List<Residente> findByDeudaActualGreaterThan(BigDecimal valor);
}