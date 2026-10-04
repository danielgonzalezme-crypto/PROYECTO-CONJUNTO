package com.conjuntosmart.residentesapi;

import org.springframework.boot.SpringApplication;
import org.springframework.boot.autoconfigure.SpringBootApplication;

// Falso positivo de PMD: Spring necesita poder crear esta clase de configuración,
// así que no se le puede poner un constructor privado como sugiere la regla.
@SuppressWarnings("PMD.UseUtilityClass")
@SpringBootApplication
public class ResidentesApiApplication {

	public static void main(String[] args) {
		SpringApplication.run(ResidentesApiApplication.class, args);
	}

}
