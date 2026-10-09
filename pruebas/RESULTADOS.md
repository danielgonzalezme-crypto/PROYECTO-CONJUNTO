# Resultados de pruebas y métricas – ConjuntoSmart

Fecha de ejecución: 4 de octubre de 2026. Este archivo reúne los datos reales para las secciones 4 (métricas) y 5 (pruebas) del documento de Calidad de Software.

Para repetir todas las pruebas y regenerar las evidencias (Docker Desktop encendido y el proyecto levantado):

```
powershell -ExecutionPolicy Bypass -File .\scripts\ejecutar-pruebas.ps1
```

Las salidas quedan en `pruebas/resultados/`; los reportes de cobertura en `residentes-api/target/site/jacoco/index.html` y `admin-web/storage/coverage-html/index.html` (abrirlos en el navegador para tomar capturas).

---

## 1. Resumen

| Tipo de prueba | Herramienta | Qué se prueba | Pruebas | Resultado |
|---|---|---|---|---|
| Unitarias | JUnit 5 + Mockito | Lógica del servicio de residentes (Spring) | 15 | ✅ Todas pasan |
| Integración | JUnit 5 + MockMvc + H2 | API completa: controlador, validaciones, errores y base de datos | 16 | ✅ Todas pasan |
| Integración (datos) | JUnit 5 + `@DataJpaTest` | Consultas del repositorio y restricción de cédula única | 4 | ✅ Todas pasan |
| Arranque | JUnit 5 | La aplicación Spring inicia correctamente | 1 | ✅ |
| Funcionales (web) | PHPUnit | Login, empleados, dashboard, residentes, comando de administrador | 48 | ✅ Todas pasan |
| Funcionales (API real) | Postman + Newman | 12 casos contra la API con MySQL real | 35 verificaciones | ✅ Todas pasan |
| No funcional: rendimiento | k6 | 1000 residentes, 20 usuarios simultáneos, 30 s | 4 umbrales | ✅ Todos se cumplen |
| No funcional: seguridad | PHPUnit | Acceso sin sesión, fuerza bruta, XSS, contraseñas cifradas | 16 (incluidas en las 48 de PHPUnit) | ✅ Todas pasan |

**Total de pruebas automatizadas: 84** (36 en Java + 48 en PHP), más 12 casos en Postman y la prueba de carga.

### Antes y después

| | Antes | Después |
|---|---|---|
| Pruebas automatizadas | 2 (1 fallaba) | 84 (todas pasan) |
| Cobertura de líneas – API (Spring) | No medible (la única prueba necesitaba MySQL encendido) | **97,8 %** (88/90 líneas) |
| Cobertura de ramas – API (Spring) | – | **100 %** (12/12 decisiones) |
| Cobertura de líneas – Web (Laravel, carpeta `app/`) | – | **100 %** (160/160 líneas) |
| Pruebas contra la API real | 0 | 12 casos en Postman |
| Prueba de rendimiento | No existía | k6 con 1000 registros |

---

## 2. Ciclo de pruebas aplicado

1. **Planificación.** Se definió qué probar en cada parte: la lógica y los endpoints de la API (Spring), las pantallas y reglas de la web (Laravel), la comunicación entre ambas y el rendimiento de la API. Herramientas: JUnit, PHPUnit, Postman/Newman y k6. Criterio de aceptación: el 100 % de las pruebas debe pasar y la cobertura debe superar el 80 %.
2. **Diseño.** Para cada función se diseñaron casos del camino feliz (datos válidos) y casos de error (datos vacíos, duplicados, inexistentes, JSON mal formado, API caída). Las pruebas no dependen de servicios externos: Spring usa una base H2 en memoria y Laravel simula la API con `Http::fake()`.
3. **Ejecución.** Las pruebas se ejecutan en contenedores Docker (Java 21 y PHP 8.4), igual que lo hará el pipeline de integración continua. El script `scripts/ejecutar-pruebas.ps1` corre todo en un paso.
4. **Cierre.** Las pruebas detectaron 2 defectos nuevos en la API (D-01 y D-02). Se corrigieron, se agregaron pruebas que los cubren (regresión) y se volvió a ejecutar todo con éxito. El reporte de cobertura mostró además un caso sin probar (cambiar la cédula de un residente por una nueva libre); se agregó su prueba y la cobertura de ramas de la API pasó de 91,7 % a 100 %.

---

## 3. Casos de prueba funcionales (Postman, contra la API real)

| ID | Caso | Resultado esperado | Obtenido | Estado |
|---|---|---|---|---|
| CP-01 | Crear residente con datos válidos | 201 y el residente con su id | 201, residente creado | ✅ |
| CP-02 | Crear residente con cédula duplicada | 409 con mensaje "Ya existe un residente…" | 409 con ese mensaje | ✅ |
| CP-03 | Crear residente con campos vacíos y deuda negativa | 400 con los 5 campos con error | 400, 5 campos listados | ✅ |
| CP-04 | Enviar un JSON mal formado | 400 con mensaje claro | 400 "no es un JSON válido…" | ✅ (tras corregir D-02) |
| CP-05 | Consultar residente por cédula | 200 con los datos guardados | 200, datos correctos | ✅ |
| CP-06 | Listar filtrando por torre | Solo residentes de esa torre | Solo torre Z | ✅ |
| CP-07 | Listar morosos | Solo residentes con deuda > 0 | Correcto, incluye el de prueba | ✅ |
| CP-08 | Actualizar residente (paga la deuda) | 200 con los cambios | 200, deuda 0 | ✅ |
| CP-09 | Consultar morosos tras pagar | Ya no aparece | No aparece | ✅ |
| CP-10 | Eliminar residente | 204 | 204 | ✅ |
| CP-11 | Consultar residente eliminado | 404 "No existe un residente…" | 404 con ese mensaje | ✅ |
| CP-12 | Eliminar residente que no existe | 404 | 404 | ✅ |

Además, cada petición verifica que la respuesta llegue en menos de 1 segundo. Tiempo promedio: 70 ms.

### Casos destacados de la web (PHPUnit)

| Caso | Resultado esperado | Estado |
|---|---|---|
| Entrar a cualquier pantalla sin sesión | Redirige al login | ✅ |
| Login con contraseña correcta / incorrecta | Entra / muestra error y no entra | ✅ |
| 5 intentos fallidos seguidos | Bloquea el acceso aunque luego se use la contraseña correcta | ✅ |
| Crear empleado válido | Se guarda y muestra "Empleado creado." | ✅ |
| Crear empleado con cédula repetida | Error "Ya existe un empleado con esa cédula." | ✅ |
| Cargo o turno que no existen | Rechazado | ✅ |
| Dashboard con la API caída | Sigue funcionando y muestra un aviso | ✅ |
| "a", "A" y "Torre A" | Se cuentan como la misma torre | ✅ |
| Nombre con `<script>` | Se muestra como texto, no se ejecuta (XSS) | ✅ |

---

## 4. Pruebas no funcionales

### Rendimiento (k6)

Escenario: se cargan 1000 residentes de prueba y 20 usuarios simultáneos consultan la API durante 30 segundos (listar todos, filtrar por torre y buscar por cédula). Al final se borran los datos de prueba.

| Indicador | Umbral definido | Resultado | Estado |
|---|---|---|---|
| Peticiones con error | < 1 % | 0 % (0 de 5480) | ✅ |
| Listar todos (1000+ registros), percentil 95 | < 800 ms | 10,8 ms | ✅ |
| Filtrar por torre, percentil 95 | < 300 ms | 10,4 ms | ✅ |
| Buscar por cédula, percentil 95 | < 200 ms | 4,2 ms | ✅ |
| Peticiones por segundo | – | ~148 | – |

"Percentil 95" significa que el 95 % de las respuestas tardó eso o menos. Valores de la última ejecución; en tres ejecuciones los resultados fueron muy parecidos (por ejemplo, listar todos entre 10,8 y 12,9 ms).

**Interpretación:** con este volumen la API responde con holgura. Sin embargo, listar todos devuelve los 1000+ registros en cada petición: en 30 segundos se transfirieron unos 305 MB. Con más residentes eso crecerá, por lo que se planea agregar paginación (ver plan de mejora).

### Seguridad

| Prueba | Resultado |
|---|---|
| Acceso a pantallas sin iniciar sesión | Redirige al login (antes cualquiera podía borrar empleados) |
| Fuerza bruta en el login | Bloqueo tras 5 intentos fallidos |
| Inyección de HTML/JavaScript (XSS) en el nombre de un empleado | Blade lo escapa; no se ejecuta |
| Contraseñas | Se guardan cifradas (bcrypt), nunca en texto plano |
| Inyección SQL | Las consultas las arman Spring Data JPA y Eloquent con parámetros; no se concatena SQL |

---

## 5. Registro de defectos

| ID | Dónde | Descripción | Cómo se detectó | Corrección | Estado |
|---|---|---|---|---|---|
| D-01 | API (Spring) | Si una petición no enviaba `tieneParqueadero`, la API la rechazaba con un error técnico ("Cannot map null into type boolean") sin decir qué campo fallaba. | Prueba de integración JUnit | El campo pasa a ser opcional y se asume `false`, como una casilla sin marcar (`ResidenteRequest.java`). | ✅ Corregido |
| D-02 | API (Spring) | Un JSON mal formado devolvía 400 sin explicar el problema. | Prueba de integración JUnit | Se agregó un manejador que responde con un mensaje claro (`GlobalExceptionHandler.java`). | ✅ Corregido |
| D-03 | Configuración | `.env.example` traía `APP_KEY=docker compose exec admin-web` (texto inválido): la web respondía error 500 al instalarla. | Prueba manual al instalar | Se dejó vacío para que se genere solo. | ✅ Corregido |
| D-04 | Web (Laravel) | La única prueba existente fallaba: esperaba 200 en `/` pero la portada redirige (302). | Ejecución de PHPUnit | Se corrigió la prueba para validar la redirección. | ✅ Corregido |
| D-05 | Web (Laravel) | Sin inicio de sesión: cualquiera con la dirección podía crear, editar o borrar empleados. | Revisión de seguridad | Se agregó login con bloqueo por fuerza bruta. | ✅ Corregido |

---

## 6. Métricas

### Tamaño del código (líneas no vacías)

| Parte | Código de la aplicación | Código de pruebas |
|---|---|---|
| API (Java, `src/main`) | 305 | 353 |
| Web (PHP, carpeta `app/`) | 419 | 464 |
| **Total** | **724 (0,724 KLOC)** | **817** |

Hay más líneas de pruebas que de código: cada función tiene sus casos de éxito y de error.

### Densidad de defectos

Densidad = defectos encontrados / KLOC = 5 / 0,724 = **6,9 defectos por cada 1000 líneas**.

Los 5 defectos están corregidos y cubiertos por pruebas, así que la densidad de defectos abiertos es **0**. La cifra sirve de línea base para comparar en la siguiente iteración.

### Complejidad ciclomática

Mide cuántos caminos distintos puede seguir una función (cada `if`, `for`, `&&`, etc. suma uno). Hasta 10 se considera fácil de entender y probar. Datos obtenidos de JaCoCo (Java) y del reporte Clover de PHPUnit (PHP).

| Función | V(G) | Evaluación |
|---|---|---|
| `ResumenTorresService.generar` (PHP) | 6 | Aceptable; es la más compleja del proyecto |
| `ResidenteServiceImpl.actualizar` (Java) | 4 | Baja |
| `ResidenteServiceImpl.listar` (Java) | 3 | Baja |
| `LoginController.store` (PHP) | 3 | Baja |
| `SpringResidentesApi.get` (PHP) | 3 | Baja |
| `CrearAdmin.handle` (PHP) | 3 | Baja |
| Resto de funciones | 1–2 | Muy baja |

Complejidad total de la API: 52, repartida en métodos pequeños. Ninguna función supera 10.

### Cobertura de pruebas

| Parte | Líneas | Ramas / decisiones |
|---|---|---|
| API (JaCoCo) | 97,8 % | 100 % |
| Web (PHPUnit + PCOV, `app/`) | 100 % | – |

Las 2 líneas sin cubrir de la API son el método `main` que arranca la aplicación, que no se ejecuta en las pruebas. La cobertura de la web mide la lógica (carpeta `app/`), no las vistas Blade.

### Errores por iteración

| Iteración | Errores hallados | Errores corregidos | Pruebas | Cobertura API / Web |
|---|---|---|---|---|
| Inicial (antes de esta entrega) | – | – | 2 (1 fallaba) | no medible / no medible |
| Iteración 1 (instalación, login y pruebas) | 5 | 5 | 84 | 97,8 % / 100 % |

---

## 7. Plan de mejora a partir de los resultados

1. **Paginación en `GET /api/residentes`**: la prueba de carga mostró 305 MB transferidos en 30 s porque se devuelve todo en cada petición.
2. **Mantener la cobertura por encima del 90 %** y que el pipeline de integración continua falle si baja.
3. **Mantener la complejidad por debajo de 10**. Si `ResumenTorresService.generar` crece, dividirla en "contar residentes" y "asignar personal".
4. **Registrar cada defecto nuevo** en esta tabla, o en issues de GitHub, para calcular la densidad por iteración.
5. **Mockito**: agregarlo como agente de Java en el `pom.xml`. Las versiones futuras del JDK dejarán de permitir la forma actual (aparece una advertencia al ejecutar las pruebas).
