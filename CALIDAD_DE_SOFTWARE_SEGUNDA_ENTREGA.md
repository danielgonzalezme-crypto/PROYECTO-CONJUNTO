# Calidad de Software – CUN

## Segunda entrega: ajustes avanzados del proyecto
### Cómo evaluar y mejorar la calidad del software

---

## ¿De qué se trata esta entrega?

En esta segunda entrega hay que presentar un **documento en PDF** que muestre cómo se ha mejorado el proyecto que se empezó en la primera entrega.

La idea es demostrar que el proyecto no solo "funciona", sino que está **bien hecho**: que el código es ordenado, que se revisó con herramientas, que se probó y que hay un plan para seguir mejorándolo.

El documento debe tener dos partes que van de la mano:

- **Parte teórica:** explicar las normas, estándares y métricas de calidad que se usaron.
- **Parte práctica:** mostrar qué se hizo realmente en el proyecto (revisión del código, pruebas, automatización, herramientas).

> 📌 **Regla de oro:** cada sección debe tener explicación **y** evidencia (capturas de pantalla, fragmentos de código o reportes generados por herramientas). No basta con decir que se hizo algo; hay que mostrarlo.

---

## Contenido que debe tener el PDF

### 1. Los puntos de la primera entrega

Se incluye todo lo que se entregó en la primera parte del proyecto. Esta segunda entrega es una continuación, así que el documento final debe tener ambas partes juntas.

**Qué hacer:** copiar y, si es necesario, corregir o actualizar el contenido de la primera entrega.

---

### 2. Código limpio (Clean Code)

"Código limpio" significa escribir código que cualquier persona pueda leer, entender y modificar fácilmente, no solo quien lo escribió.

**Qué se debe incluir:**

- **Los principios de código limpio aplicados** en el proyecto, explicados con palabras sencillas.
- **Ejemplos reales del proyecto** de buenas prácticas, por ejemplo:
  - **Nombres que se entienden:** usar `calcularTotalFactura()` en lugar de `calc()` o `x1`.
  - **Código dividido en partes reutilizables:** en vez de una función gigante que hace todo, varias funciones pequeñas, cada una con una sola tarea.
  - **Eliminar repeticiones:** si el mismo bloque de código aparece en varios lugares, se convierte en una sola función que se llama desde donde se necesite.
- **Capturas de "antes y después":** mostrar un fragmento de código como estaba y cómo quedó después de mejorarlo.
- **Comparación entre dos formas de programar**, aplicada al proyecto:
  - **Programación estructurada:** el programa es una secuencia de pasos e instrucciones (funciones y procedimientos).
  - **Programación orientada a objetos (POO):** el programa se organiza en "objetos" que representan cosas reales (por ejemplo `Usuario`, `Producto`, `Pedido`), cada uno con sus datos y acciones.
  - Explicar cuál se usa en el proyecto y por qué conviene.

---

### 3. Evaluación de la calidad del código

Aquí se usa una **herramienta de análisis estático**: un programa que revisa el código automáticamente (sin ejecutarlo) y detecta problemas.

**Herramientas sugeridas:** SonarQube, PMD, ESLint u otras similares (según el lenguaje del proyecto).

**Qué se debe incluir:**

- **Evidencia de que se usó la herramienta** (capturas de la herramienta funcionando).
- **Capturas del reporte** con sus indicadores. Los más comunes son:
  - **Code smells ("malos olores"):** partes del código que funcionan pero están mal organizadas o son difíciles de mantener.
  - **Bugs:** errores que pueden hacer que el programa falle.
  - **Vulnerabilidades:** fallas que podrían ser aprovechadas para atacar el sistema.
- **Interpretación de los resultados:** explicar qué significan los números, no solo pegarlos.
- **Qué se corrigió** a partir de lo que encontró la herramienta.

> 💡 **Ejemplo:** "Se detectaron 5 partes de código duplicado que se reorganizaron en una sola función, lo que facilita el mantenimiento."

---

### 4. Métricas de calidad

Las métricas son **números que permiten medir la calidad** del software de forma objetiva.

**Métricas que se deben calcular:**

| Métrica | ¿Qué mide? | ¿Cómo se calcula? (idea general) |
|---|---|---|
| **Densidad de defectos** (*Defect Density*) | Cuántos errores hay en relación con el tamaño del código. | Número de defectos ÷ tamaño del código (por ejemplo, por cada 1.000 líneas). |
| **Complejidad ciclomática** (*Cyclomatic Complexity*) | Qué tan "enredada" es una función: cuántos caminos posibles tiene (if, for, while, etc.). Entre más alta, más difícil de entender y probar. | Se cuenta cada decisión (if, else, for, while, case…) y se suma 1. Muchas herramientas la calculan solas. |
| **Cobertura de pruebas** | Qué porcentaje del código es revisado por las pruebas. | Líneas probadas ÷ líneas totales × 100. |
| **Errores corregidos por iteración** | Cuántos errores se arreglan en cada ciclo o etapa de trabajo. | Se cuentan los errores resueltos en cada iteración. |

**Qué se debe incluir:**

- La **definición** de cada métrica y el **cálculo** con los datos del proyecto.
- **De dónde salieron los datos** (qué herramienta o método se usó) y **qué significan** los resultados.
- Un **plan de mejora** basado en esos números (por ejemplo: "la función X tiene complejidad 15, se dividirá en funciones más pequeñas para bajarla a menos de 10").

---

### 5. Pruebas de software

Las pruebas sirven para comprobar que el programa hace lo que debe hacer y detectar errores antes de que lleguen al usuario.

**Qué se debe incluir:**

- **El ciclo de pruebas** que se siguió, en sus cuatro etapas:
  1. **Planificación:** qué se va a probar y cómo.
  2. **Diseño:** crear los casos de prueba.
  3. **Ejecución:** correr las pruebas.
  4. **Cierre:** revisar resultados y sacar conclusiones.
- **Pruebas funcionales** (verifican que cada función haga lo que debe), manuales o automáticas:
  - Capturas de herramientas como **Postman** (para probar APIs), **Selenium** (para probar páginas web) o **JUnit** (pruebas de código en Java), entre otras.
  - **Casos de prueba** con el resultado **esperado** y el resultado **obtenido**. Ejemplo:

    | Caso | Entrada | Resultado esperado | Resultado obtenido | ¿Pasó? |
    |---|---|---|---|---|
    | Inicio de sesión correcto | Usuario y contraseña válidos | Entra al sistema | Entra al sistema | ✅ |
    | Contraseña incorrecta | Contraseña errónea | Mensaje de error | Mensaje de error | ✅ |

- **Pruebas no funcionales** (si aplican), que revisan *cómo* funciona el sistema:
  - **Rendimiento:** qué tan rápido responde.
  - **Seguridad:** qué tan protegido está.
  - **Usabilidad:** qué tan fácil es de usar.
- **Análisis de resultados:** qué errores se encontraron, cómo se solucionaron y cómo mejoró la cobertura de pruebas.

---

### 6. Automatización y DevOps

**DevOps** es una forma de trabajar donde muchas tareas (como probar y publicar el software) se hacen **automáticamente** cada vez que se cambia el código.

- **CI – Integración Continua:** cada vez que se sube un cambio, el sistema compila el proyecto y corre las pruebas solo.
- **CD – Entrega/Despliegue Continuo:** si todo sale bien, el sistema puede publicar la nueva versión automáticamente.
- Al conjunto de pasos automáticos se le llama **pipeline**.

**Herramientas sugeridas:** GitHub Actions, Jenkins, GitLab CI, entre otras.

**Qué se debe incluir:**

- **Evidencia** de que se usa una herramienta de automatización.
- **Explicación** de cómo funciona el proceso en el proyecto (qué pasa cuando se sube un cambio).
- **Capturas del pipeline** funcionando y de las pruebas ejecutándose.
- **Análisis del impacto:** cómo la automatización ayuda a la calidad (se detectan errores más rápido, se evitan errores humanos, cada versión se prueba igual, etc.).

---

### 7. Plan de próximas etapas

Aquí se explica **qué se hará después** para que el proyecto siga mejorando.

**Qué se debe incluir:**

- **Pruebas o métricas nuevas** que se planean agregar.
- **Plan de mejora continua** del código y de la forma de trabajar.
- **Estrategias para mantener la calidad** en versiones futuras (por ejemplo: revisar el código entre compañeros, exigir que las pruebas pasen antes de aceptar cambios, mantener una cobertura mínima).

---

## Requisitos de entrega

| Requisito | Detalle |
|---|---|
| **Formato** | PDF siguiendo las [normas APA](https://cdigital.cun.edu.co/mod/page/view.php?id=6937989) (portada, tipo de letra, citas y referencias). |
| **Redacción** | Clara, técnica y sin errores de ortografía. |
| **Evidencias** | Capturas de pantalla, fragmentos de código, métricas y reportes. |
| **Modalidad** | Individual. |

---

## ⚠️ Lo que más se valora

- **No basta con usar herramientas o mostrar números.** Lo importante es **interpretar** los resultados: explicar qué significan y qué se hizo con ellos.
- Se debe ver una **evaluación crítica**: reconocer los problemas del proyecto y mostrar **mejoras reales** en el código o en el proceso.
- La **calidad del documento** y la **claridad de las explicaciones** también cuentan en la nota final.

---

## ✅ Lista de verificación antes de entregar

- [ ] Incluí todo lo de la primera entrega.
- [ ] Expliqué los principios de código limpio con ejemplos del proyecto.
- [ ] Puse capturas de código **antes y después** de las mejoras.
- [ ] Comparé programación estructurada y orientada a objetos.
- [ ] Usé una herramienta de análisis estático y adjunté capturas del reporte.
- [ ] Interpreté los resultados y expliqué qué corregí.
- [ ] Calculé densidad de defectos, complejidad ciclomática y cobertura (o errores por iteración).
- [ ] Escribí un plan de mejora basado en las métricas.
- [ ] Describí el ciclo de pruebas y mostré casos de prueba con resultados.
- [ ] Incluí capturas de herramientas de prueba (Postman, Selenium, JUnit, etc.).
- [ ] Mostré el pipeline de CI/CD con capturas y expliqué su impacto.
- [ ] Escribí el plan de próximas etapas.
- [ ] El documento está en PDF con normas APA y revisé la ortografía.
