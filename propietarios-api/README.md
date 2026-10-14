# propietarios-api

API en Node.js (Express + TypeScript) que guarda los **datos de contacto y detalles de cada propietario o residente** de ConjuntoSmart. Tiene su propia base de datos MySQL (`propietarios_db`) y se enlaza con los residentes de `residentes-api` por la **cédula**.

Qué guarda esta API y qué no:

- Aquí: teléfono, correo, tipo (propietario o arrendatario), contacto de emergencia, placa, número de parqueadero y mascotas.
- En `residentes-api` (Spring Boot): nombre, torre, apartamento, deuda y si tiene parqueadero (sí/no). Esa información no se duplica aquí.

Esta API no consulta a Spring: no valida que la cédula exista como residente. Esa comprobación la hace la aplicación que las orquesta (Laravel).

## Endpoints

| Método | Ruta | Uso |
|---|---|---|
| GET | `/health` | Estado del servicio |
| GET | `/api/propietarios` | Listar (filtro opcional `?tipo=PROPIETARIO` o `?tipo=ARRENDATARIO`) |
| GET | `/api/propietarios/{cedula}` | Consultar uno |
| POST | `/api/propietarios` | Crear (la cédula va en el cuerpo) |
| PUT | `/api/propietarios/{cedula}` | Actualizar (reemplaza todos los campos) |
| DELETE | `/api/propietarios/{cedula}` | Eliminar |

Códigos de respuesta: `200`, `201` (creado), `204` (eliminado), `400` (datos inválidos), `404` (no existe) y `409` (cédula ya registrada).

## Cuerpo para crear

```json
{
  "cedula": "1000000001",
  "telefono": "3001234567",
  "correo": "ana@example.com",
  "tipoResidente": "PROPIETARIO",
  "contactoEmergenciaNombre": "Contacto de ejemplo",
  "contactoEmergenciaTelefono": "3109876543",
  "placaVehiculo": "ABC123",
  "numeroParqueadero": "P-12",
  "tieneMascotas": true,
  "observaciones": "Datos ficticios"
}
```

Solo `cedula` y `tipoResidente` son obligatorios. Las cadenas vacías se guardan como "sin dato" y la placa se guarda en mayúsculas. El `PUT` lleva el mismo cuerpo sin `cedula`.

## Formato de los errores

```json
{
  "status": 400,
  "detail": "Datos inválidos",
  "errores": [
    { "campo": "correo", "mensaje": "El correo no es válido" }
  ]
}
```

## Estructura

```
src/
├── index.ts                    arranque
├── app.ts                      Express, rutas y manejo de errores
├── config.ts                   variables de entorno
├── db.ts                       conexión y creación de la tabla
├── validation.ts               reglas de validación (zod)
├── errors.ts                   errores HTTP
├── propietario.model.ts        tipos y conversión de filas
├── propietario.repository.ts   acceso a la base
├── propietario.service.ts      reglas de negocio
└── propietario.routes.ts       endpoints
```

## Variables de entorno

| Variable | Valor por defecto | Uso |
|---|---|---|
| `PORT` | `3000` | Puerto |
| `HOST` | `127.0.0.1` | Dirección donde escucha (en Docker es `0.0.0.0`) |
| `DB_HOST` | `127.0.0.1` | Servidor MySQL |
| `DB_PORT` | `3310` | Puerto MySQL |
| `DB_NAME` | `propietarios_db` | Base de datos |
| `DB_USER` | `propietarios_user` | Usuario |
| `DB_PASSWORD` | (vacío) | Contraseña |

## Ejecutarla sin Docker (opcional)

Con Node.js 22 y la base de datos encendida (`docker compose up -d propietarios-db`):

```
npm install
$env:DB_PASSWORD="la_contraseña_del_env"; npm run dev
```

Con Docker no hace falta instalar nada: `docker compose up -d --build propietarios-api` desde la raíz del proyecto.
