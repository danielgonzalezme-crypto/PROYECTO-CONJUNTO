# ConjuntoSmart

Sistema de gestión de un conjunto residencial, hecho con tres aplicaciones en tres lenguajes que se comunican por HTTP:

- **residentes-api** (Java 21 + Spring Boot): API REST de residentes (torre, apartamento y deuda) sobre su propia base MySQL (`residentes_db`).
- **propietarios-api** (Node.js + TypeScript): API REST con los datos de contacto y detalles de cada propietario o residente (teléfono, correo, contacto de emergencia, placa, mascotas) sobre su propia base MySQL (`propietarios_db`).
- **admin-web** (PHP + Laravel): interfaz web, empleados por torre y búsqueda de residentes, sobre su propia base MySQL (`administrativo_db`). Consume las dos APIs.

```
                         ┌──HTTP──► residentes-api (Spring :8080) ──► residentes_db
Navegador ─► admin-web ──┤
              (Laravel   └──HTTP──► propietarios-api (Node :3000) ──► propietarios_db
               :8000)
                 │
          administrativo_db
```

Cada aplicación es dueña de su base de datos: ninguna lee la base de otra, solo se llaman por sus APIs. La **cédula** es la llave que une a un residente (Spring) con su ficha de contacto (Node), y es Laravel quien junta ambos datos.

## Aplicación desplegada

La aplicación está publicada en una máquina de AWS:

**http://34.236.177.9:8000**

- Es una instancia EC2 (región us-east-1, Ubuntu en ARM) con los seis contenedores del proyecto. La dirección es una IP elástica, así que no cambia si la máquina se apaga y se enciende.
- Hacia internet solo están abiertos el SSH (22) y la web (8000). Las APIs (8080 y 3000) y las bases de datos no se exponen.
- Todas las pantallas piden iniciar sesión. Las credenciales de revisión se entregan por aparte: la contraseña de demostración de este README se cambió en la máquina de AWS.
- Si la dirección no abre, la instancia puede estar apagada.

### Cómo se desplegó (resumen)

1. Instancia EC2 con Ubuntu, al menos 4 GB de memoria, disco de unos 30 GB y 2 GB de swap.
2. Grupo de seguridad con los puertos 22 y 8000.
3. Docker instalado, el repositorio clonado y un archivo `.env` con contraseñas aleatorias, más estas dos variables:
   ```
   APP_URL=http://IP_PUBLICA:8000
   ADMIN_WEB_BIND=0.0.0.0
   ```
   `ADMIN_WEB_BIND=0.0.0.0` publica la web fuera de la máquina; sin ella queda solo en `127.0.0.1`.
4. `docker compose up -d --build` (unos 10 a 15 minutos la primera vez).
5. Cambio de la contraseña del usuario de demostración y carga de datos de prueba por las APIs.

## Requisitos

- Docker Desktop (con Docker Compose).

## Puesta en marcha

1. Copia `.env.example` como `.env` y cambia las contraseñas.
2. Levanta todo (la primera vez tarda varios minutos porque compila):

   ```
   docker compose up -d --build
   ```

3. Comprueba que los seis contenedores estén arriba (tres aplicaciones y tres bases): `docker compose ps`
4. Abre http://127.0.0.1:8000 e inicia sesión con el usuario de demostración.

### Servicios y puertos

| Servicio | Tecnología | Puerto del equipo | Base de datos (puerto del equipo) |
|---|---|---|---|
| `admin-web` | PHP + Laravel | 8000 | `administrativo-db` (3309) |
| `residentes-api` | Java 21 + Spring Boot | 8080 (`API_PORT`) | `residentes-db` (3308) |
| `propietarios-api` | Node.js 22 + Express | 3000 (`PROPIETARIOS_API_PORT`) | `propietarios-db` (3310) |

Las bases son MySQL 8.0 y los puertos de las APIs y de las bases quedan enlazados a `127.0.0.1`. Los datos se guardan en volúmenes de Docker.

## Inicio de sesión

Todas las pantallas exigen iniciar sesión. Al arrancar, el contenedor crea (si no existe) un usuario fijo de demostración:

| Correo | Contraseña |
|---|---|
| `admin@conjuntosmart.test` | `Admin12345` |

Está definido en `admin-web/database/seeders/DatabaseSeeder.php`. Como esa contraseña es pública en el repositorio, **cámbiala antes de publicar la aplicación en internet** (en la instancia de AWS ya se cambió).

No hay registro público. Para crear más usuarios (pide nombre, correo y contraseña):

```
docker compose exec admin-web php artisan app:crear-admin
```

Tras 5 intentos fallidos con el mismo correo, el acceso se bloquea un minuto para frenar ataques de fuerza bruta.

## Qué ofrece la aplicación

- **Dashboard:** resumen por torre con residentes, morosos y deuda total.
- **Empleados:** crear y editar el personal asignado a cada torre.
- **Residentes:** lista con teléfono y correo de cada residente (datos de la API de propietarios), **búsqueda por nombre, cédula, teléfono o correo** y filtro por torre. La búsqueda ignora mayúsculas y tildes, y en el teléfono ignora espacios y guiones.

Si una API se cae, la web muestra un aviso rojo y sigue funcionando con lo demás (ver la demostración más abajo).

## Datos de demostración

Los residentes y sus fichas de contacto se crean por las APIs. Para cargar datos ficticios:

```
powershell -ExecutionPolicy Bypass -File .\scripts\datos-demo.ps1
powershell -ExecutionPolicy Bypass -File .\scripts\datos-demo-propietarios.ps1
```

El primero crea cuatro residentes en Spring y el segundo carga fichas de contacto en Node.

Para cargar muchos registros desde otra máquina (por ejemplo 1000, para pruebas de volumen), hay un script en bash que crea, por cada cédula, un residente en Spring y su ficha en Node:

```
bash scripts/cargar-registros.sh IP_PRIVADA_DE_LA_MAQUINA_DEL_PROYECTO 1000
```

Para que funcione, las dos APIs deben ser alcanzables desde esa máquina: hay que abrir los puertos 8080 y 3000 solo para ella en el grupo de seguridad y publicarlos en el compose (vienen enlazados a `127.0.0.1`). Volver a ejecutarlo no duplica nada: las cédulas repetidas se omiten.

Los empleados se crean desde la interfaz (menú **Empleados**). Usa el mismo nombre de torre (`A`, `B`...) en ambos lados para que el dashboard los cruce; mayúsculas y minúsculas no importan.

## API de residentes (Spring Boot)

| Método | Ruta | Uso |
|---|---|---|
| GET | `/api/residentes` | Listar. Filtros opcionales y combinables: `?torre=A` y `?deudaMinima=100000` (mayor o igual) |
| GET | `/api/residentes/morosos` | Residentes con deuda mayor a 0 |
| GET | `/api/residentes/{cedula}` | Consultar uno |
| POST | `/api/residentes` | Crear |
| PUT | `/api/residentes/{cedula}` | Actualizar |
| DELETE | `/api/residentes/{cedula}` | Eliminar |

Ejemplo de cuerpo para crear:

```json
{
  "cedula": "1234567890",
  "nombre": "Ana Perez",
  "torre": "A",
  "apartamento": "101",
  "deudaActual": 150000.00,
  "tieneParqueadero": true
}
```

## API de propietarios (Node.js)

| Método | Ruta | Uso |
|---|---|---|
| GET | `/health` | Estado del servicio |
| GET | `/api/propietarios` | Listar (filtro opcional `?tipo=PROPIETARIO` o `?tipo=ARRENDATARIO`) |
| GET | `/api/propietarios/{cedula}` | Consultar uno |
| POST | `/api/propietarios` | Crear (la cédula va en el cuerpo) |
| PUT | `/api/propietarios/{cedula}` | Actualizar (reemplaza todos los campos) |
| DELETE | `/api/propietarios/{cedula}` | Eliminar |

Códigos de respuesta: `200`, `201` (creado), `204` (eliminado), `400` (datos inválidos), `404` (no existe) y `409` (cédula ya registrada). Solo `cedula` y `tipoResidente` son obligatorios.

```json
{
  "cedula": "1234567890",
  "telefono": "3001234567",
  "correo": "ana@example.com",
  "tipoResidente": "PROPIETARIO"
}
```

El detalle completo (todos los campos, formato de errores y variables de entorno) está en [`propietarios-api/README.md`](propietarios-api/README.md). Esta API no consulta a Spring ni comprueba que la cédula exista como residente: esa verificación le corresponde a Laravel.

## Roles de la base de datos

Cada una de las tres bases tiene tres roles de MySQL y un usuario de prueba por rol:

| Rol | Permisos | Usuario de prueba |
|---|---|---|
| `rol_admin` | Todos los privilegios sobre la base | `usr_admin` |
| `rol_escritura` | Leer, insertar, actualizar y borrar | `usr_escritura` |
| `rol_lectura` | Solo leer | `usr_lectura` |

Se crean con un script que se puede repetir (con los contenedores encendidos, desde una terminal bash):

```
bash scripts/aplicar-roles.sh            # genera una clave al azar y la muestra al final
bash scripts/aplicar-roles.sh MiClave123 # o usa la clave indicada (sin / ni &)
```

El script usa `scripts/roles.sql`. Para comprobarlo, con `usr_lectura` se puede consultar pero crear una tabla responde `ERROR 1142 ... CREATE command denied`; con `usr_admin` sí se puede. Los usuarios solo existen dentro de MySQL y los puertos de las bases no están abiertos a internet.

## Calidad y pruebas

Cada push a GitHub ejecuta el flujo de integración continua (`.github/workflows/ci.yml`) con tres trabajos:

| Trabajo | Qué verifica |
|---|---|
| API Spring Boot | PMD, CPD (código duplicado), pruebas JUnit y cobertura con JaCoCo |
| Web Laravel | Estilo con Pint, PHPStan + Larastan (nivel 6) y PHPUnit con cobertura |
| Sistema completo | Levanta todo con Docker Compose, ejecuta las pruebas de Postman con Newman y comprueba que, sin sesión, la web redirige al login |

El tercer trabajo solo corre si pasan los dos primeros. Para ejecutar todas las pruebas en tu equipo (con el proyecto encendido):

```
powershell -ExecutionPolicy Bypass -File .\scripts\ejecutar-pruebas.ps1
```

Agrega `-SinCarga` para omitir la prueba de rendimiento con k6, que tarda cerca de un minuto. Los resultados quedan en `pruebas/resultados/` y su resumen en `pruebas/RESULTADOS.md`. El documento de calidad de la entrega es `CALIDAD_DE_SOFTWARE_SEGUNDA_ENTREGA.md`.

## Uso diario

Cada vez que enciendas el equipo:

1. Abre Docker Desktop y espera a que esté en marcha.
2. En la carpeta del proyecto:

   ```
   docker compose up -d
   docker compose ps
   ```

3. Abre http://127.0.0.1:8000. Si el dashboard muestra un aviso rojo, Spring aún está arrancando: espera unos segundos y recarga.
4. Antes de trabajar en el código, trae los cambios del equipo con `git pull`.
5. Para apagar: `docker compose stop` (conserva los datos).

## Demostración: ¿qué pasa si se cae una API?

Los residentes viven en la base de Spring Boot y los contactos en la de Node; Laravel solo los consulta por sus APIs.

```
docker compose stop residentes-api       # apaga solo Spring
docker compose start residentes-api      # lo vuelve a encender
docker compose stop propietarios-api     # apaga solo Node
docker compose start propietarios-api    # lo vuelve a encender
```

- **Con Spring apagado:** el dashboard y la pantalla de Residentes muestran un aviso rojo y no aparecen residentes, pero Laravel sigue funcionando y los empleados (que están en su propia base) siguen visibles y editables.
- **Con Node apagado:** Residentes sigue mostrando la lista, con un aviso rojo y las columnas de Teléfono y Correo en «—».

## Comandos de Docker

Todos se ejecutan desde la carpeta del proyecto (donde está `docker-compose.yml`). Los servicios se llaman `residentes-db`, `administrativo-db`, `propietarios-db`, `residentes-api`, `propietarios-api` y `admin-web`.

### Encender y apagar

```
docker compose up -d        # enciende todo (crea lo que falte)
docker compose stop         # apaga todo, conserva los datos
docker compose start        # vuelve a encender lo apagado con stop
docker compose restart      # reinicia todo
```

Para un solo servicio, agrega su nombre: `docker compose stop residentes-api`, `docker compose start propietarios-api`, etc.

### Estado y registros

```
docker compose ps                                # servicios en marcha
docker compose ps -a                             # incluye los apagados
docker stats                                     # CPU y memoria en vivo (Ctrl + C para salir)
docker compose logs --tail 50 residentes-api     # últimas líneas de Spring
docker compose logs --tail 50 propietarios-api   # últimas líneas de Node
docker compose logs --tail 50 admin-web          # últimas líneas de Laravel
docker compose logs -f residentes-api            # en vivo
```

Los registros son lo primero que hay que mirar cuando algo falla.

### Reconstruir después de cambiar código

```
docker compose up -d --build                        # todo
docker compose up -d --build residentes-api         # solo Spring
docker compose up -d --build propietarios-api       # solo Node
docker compose up -d --build admin-web              # solo Laravel
docker compose up -d --force-recreate admin-web     # recrea el contenedor sin reconstruir la imagen
```

La imagen guarda una copia del código, por eso un cambio en Spring, Node o la configuración de Laravel necesita `--build`.

### Comandos dentro de un contenedor

```
docker compose exec admin-web php artisan route:list
docker compose exec admin-web php artisan migrate
docker compose exec admin-web sh        # terminal dentro (salir con exit)
docker compose exec residentes-api sh
```

### Entrar a las bases de datos

```
docker exec -it residentes-db mysql -uresidentes_user -p residentes_db
docker exec -it administrativo-db mysql -uadmin_user -p administrativo_db
docker exec -it propietarios-db mysql -upropietarios_user -p propietarios_db
```

Pide la contraseña del `.env`. Dentro, las sentencias terminan en `;` (por ejemplo `SHOW TABLES;`) y se sale con `exit`.

### Validar y limpiar

```
docker compose config --quiet    # valida el compose (no imprime nada si está bien)
docker volume ls                 # volúmenes, donde viven los datos
docker image prune               # borra imágenes sin usar
```

### Comandos peligrosos

| Comando | Qué hace |
|---|---|
| `docker compose down` | Apaga y elimina contenedores y red. Los datos se conservan en los volúmenes. |
| `docker compose down -v` | Lo anterior **y borra los volúmenes: se pierden todas las bases de datos**. |
| `docker volume rm ...` | Borra un volumen concreto con sus datos. |
| `docker system prune -a --volumes` | Borra casi todo lo de Docker, incluidos volúmenes de otros proyectos. Evitarlo. |

Para el uso normal bastan `up -d`, `stop` y `start`.

## Compartir temporalmente por internet

Para una demostración desde tu equipo, sin desplegar en AWS, puedes usar un túnel rápido de Cloudflare (no requiere cuenta) sobre el puerto de Laravel:

```
cloudflared tunnel --url http://127.0.0.1:8000
```

Solo se expone Laravel; las APIs y las bases quedan dentro de la red de Docker y enlazadas a `127.0.0.1`. Aunque la aplicación pide inicio de sesión, usa solo datos ficticios, comparte la dirección por un canal privado y cierra el túnel al terminar.

## Estructura

```
conjuntosmart/
├── docker-compose.yml
├── .env.example
├── residentes-api/      (Spring Boot)
├── propietarios-api/    (Node.js + TypeScript)
├── admin-web/           (Laravel)
├── scripts/             (datos de demostración, roles de MySQL, carga de registros, pruebas)
├── pruebas/             (colección de Postman, prueba de carga con k6 y resultados)
└── .github/workflows/   (integración continua)
```

## Problemas frecuentes

- **Puerto 8000, 8080 o 3000 ocupado:** cierra cualquier Spring, Node o `php artisan serve` que tengas corriendo fuera de Docker. Si el 8080 o el 3000 los usa otro programa que no puedes cerrar, pon `API_PORT=8081` o `PROPIETARIOS_API_PORT=3001` (u otro libre) en tu `.env` y ejecuta `docker compose up -d`.
- **El dashboard o Residentes muestran el aviso rojo:** la API correspondiente aún está arrancando; espera unos segundos y recarga.
- **Cambié una contraseña en `.env` y no se aplicó:** las bases ya existen con la contraseña anterior. Solo se puede aplicar con `docker compose down -v`, que borra los datos.
- **`docker compose up` dice que falta una variable `PROPIETARIOS_...`:** tu `.env` es anterior a la API de Node. Agrega `PROPIETARIOS_DB_ROOT_PASSWORD` y `PROPIETARIOS_DB_PASSWORD`, copiándolas de `.env.example`.
- **Cambié la configuración de Laravel (`admin-web/config`) y no se nota:** esa carpeta va dentro de la imagen; usa `docker compose up -d --build admin-web`.

## Autores

Daniel Estiben Gonzalez Medina  
Diego Jose Sandoval de la pava  
María Sofía Campos
