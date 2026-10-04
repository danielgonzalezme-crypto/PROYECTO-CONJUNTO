# ConjuntoSmart

Sistema de gestión de un conjunto residencial, hecho con dos aplicaciones que se comunican:

- **residentes-api** (Java 21 + Spring Boot): API REST de residentes sobre su propia base MySQL (`residentes_db`).
- **admin-web** (PHP + Laravel): interfaz web y administración de empleados sobre su propia base MySQL (`administrativo_db`). Consume la API de residentes para armar el dashboard por torre.

```
Navegador ──► admin-web (Laravel :8000) ──HTTP──► residentes-api (Spring :8080)
                     │                                    │
              administrativo_db                     residentes_db
                  (MySQL)                              (MySQL)
```

Cada aplicación es dueña de su base de datos. Laravel nunca lee la base de Spring: solo llama a su API.

## Requisitos

- Docker Desktop (con Docker Compose).

## Puesta en marcha

1. Copia `.env.example` como `.env` y cambia las contraseñas.
2. Levanta todo (la primera vez tarda varios minutos porque compila):

   ```
   docker compose up -d --build
   ```

3. Comprueba que los cuatro servicios estén arriba: `docker compose ps`
4. Abre http://127.0.0.1:8000

## Datos de demostración

Los residentes se crean por la API. Para cargar cuatro residentes ficticios:

```
powershell -ExecutionPolicy Bypass -File .\scripts\datos-demo.ps1
```

Los empleados se crean desde la interfaz (menú **Empleados**). Usa el mismo nombre de torre (`A`, `B`...) en ambos lados para que el dashboard los cruce; mayúsculas y minúsculas no importan.

## Endpoints de la API de residentes

| Método | Ruta | Uso |
|---|---|---|
| GET | `/api/residentes` | Listar (filtro opcional `?torre=A`) |
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

## Comandos útiles

```
docker compose logs -f residentes-api    # registros de Spring
docker compose logs -f admin-web         # registros de Laravel
docker compose stop                      # apagar conservando los datos
docker compose up -d                     # volver a encender
docker compose down -v                   # CUIDADO: borra también los datos
```

## Compartir temporalmente por internet

Con un túnel rápido de Cloudflare (no requiere cuenta) sobre el puerto de Laravel:

```
cloudflared tunnel --url http://127.0.0.1:8000
```

Solo se expone Laravel; Spring y las bases quedan dentro de la red de Docker y enlazadas a 127.0.0.1. La aplicación **no tiene login**: usa solo datos ficticios, comparte la dirección por un canal privado y cierra el túnel al terminar.

## Estructura

```
conjuntosmart/
├── docker-compose.yml
├── .env.example
├── residentes-api/     (Spring Boot)
├── admin-web/          (Laravel)
└── scripts/
```

## Problemas frecuentes

- **Puerto 8000 u 8080 ocupado:** cierra cualquier Spring o `php artisan serve` que tengas corriendo fuera de Docker.
- **El dashboard muestra el aviso rojo:** Spring aún está arrancando; espera unos segundos y recarga.
- **Cambié una contraseña en `.env` y no se aplicó:** las bases ya existen con la contraseña anterior. Solo se puede aplicar con `docker compose down -v`, que borra los datos.

## Autores
Daniel Estiben Gonzalez Medina
Diego Jose Sandoval de la pava
María Sofía Campos 

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

## Demostración: ¿qué pasa si la API de residentes se cae?

Los residentes viven en la base de Spring Boot; Laravel solo los consulta por la API.

```
docker compose stop residentes-api     # apaga solo Spring
docker compose start residentes-api    # lo vuelve a encender
```

Con Spring apagado, el dashboard y la pantalla de Residentes muestran un aviso rojo y no aparecen residentes, pero Laravel sigue funcionando y los empleados (que están en su propia base) siguen visibles y editables.

## Comandos de Docker

Todos se ejecutan desde la carpeta del proyecto (donde está `docker-compose.yml`). Los servicios se llaman `residentes-db`, `administrativo-db`, `residentes-api` y `admin-web`.

### Encender y apagar

```
docker compose up -d        # enciende todo (crea lo que falte)
docker compose stop         # apaga todo, conserva los datos
docker compose start        # vuelve a encender lo apagado con stop
docker compose restart      # reinicia todo
```

### Un solo servicio

```
docker compose stop residentes-api       # apaga solo Spring
docker compose start residentes-api      # lo enciende
docker compose restart residentes-api    # lo reinicia
```

Funciona igual con `admin-web`, `residentes-db` y `administrativo-db`.

### Estado y registros

```
docker compose ps                              # servicios en marcha
docker compose ps -a                           # incluye los apagados
docker stats                                   # CPU y memoria en vivo (Ctrl + C para salir)
docker compose logs --tail 50 residentes-api   # últimas líneas de Spring
docker compose logs --tail 50 admin-web        # últimas líneas de Laravel
docker compose logs -f residentes-api          # en vivo
```

Los registros son lo primero que hay que mirar cuando algo falla.

### Reconstruir después de cambiar código

```
docker compose up -d --build                      # todo
docker compose up -d --build residentes-api       # solo Spring
docker compose up -d --build admin-web            # solo Laravel
docker compose up -d --force-recreate admin-web   # recrea el contenedor sin reconstruir la imagen
```

La imagen guarda una copia del código, por eso un cambio en Spring o Laravel necesita `--build`.

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