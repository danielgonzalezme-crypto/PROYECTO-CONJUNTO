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