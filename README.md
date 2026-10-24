# ConjuntoSmart

Sistema de gestión de un conjunto residencial, hecho con tres aplicaciones en tres lenguajes que se comunican por HTTP:

- **residentes-api** (Java 21 + Spring Boot): API REST de residentes (torre, apartamento y deuda) sobre su propia base MySQL (`residentes_db`).
- **propietarios-api** (Node.js 22 + TypeScript): API REST con los datos de contacto y detalles de cada propietario o residente (teléfono, correo, contacto de emergencia, placa, mascotas) sobre su propia base MySQL (`propietarios_db`).
- **admin-web** (PHP 8.4 + Laravel): interfaz web, empleados por torre, búsqueda de residentes y parqueaderos, sobre su propia base MySQL (`administrativo_db`). Consume las dos APIs.

```
                         ┌──HTTP──► residentes-api (Spring :8080) ──► residentes_db
Navegador ─► admin-web ──┤
              (Laravel   └──HTTP──► propietarios-api (Node :3000) ──► propietarios_db
               :8000)
                 │
          administrativo_db
```

Cada aplicación es dueña de su base de datos: ninguna lee la base de otra, solo se llaman por sus APIs. La **cédula** es la llave que une a un residente (Spring) con su ficha de contacto (Node), y es Laravel quien junta ambos datos.

## Contenido

1. [Aplicación desplegada](#aplicación-desplegada)
2. [Requisitos](#requisitos)
3. [Puesta en marcha en Windows (local), paso a paso](#puesta-en-marcha-en-windows-local-paso-a-paso)
4. [Despliegue en producción en Ubuntu, paso a paso](#despliegue-en-producción-en-ubuntu-paso-a-paso)
5. [Pruebas](#pruebas)
   - [Preparación (una sola vez)](#preparación-de-las-pruebas-una-sola-vez)
   - [Pruebas en Windows](#pruebas-en-windows)
   - [Pruebas en Ubuntu (producción)](#pruebas-en-ubuntu-producción)
6. [Colecciones de Postman](#colecciones-de-postman)
7. [Inicio de sesión](#inicio-de-sesión) · [Qué ofrece la aplicación](#qué-ofrece-la-aplicación) · [Datos de demostración](#datos-de-demostración)
8. [API de residentes](#api-de-residentes-spring-boot) · [API de propietarios](#api-de-propietarios-nodejs) · [Roles de la base de datos](#roles-de-la-base-de-datos)
9. [Uso diario](#uso-diario) · [Comandos de Docker](#comandos-de-docker) · [Problemas frecuentes](#problemas-frecuentes)

## Aplicación desplegada

La aplicación está publicada en una máquina de AWS:

**http://34.236.177.9:8000**

- Es una instancia EC2 (región us-east-1, Ubuntu en ARM) con los seis contenedores del proyecto. La dirección es una IP elástica, así que no cambia si la máquina se apaga y se enciende.
- Hacia internet solo están abiertos el SSH (22) y la web (8000). Las APIs (8080 y 3000) y las bases de datos no se exponen.
- Todas las pantallas piden iniciar sesión. Las credenciales de revisión se entregan por aparte: la contraseña de demostración de este README se cambió en la máquina de AWS.
- Si la dirección no abre, la instancia puede estar apagada.

El procedimiento completo para montarla está en [Despliegue en producción en Ubuntu](#despliegue-en-producción-en-ubuntu-paso-a-paso).

## Requisitos

| | Windows (local) | Ubuntu (producción) |
|---|---|---|
| Sistema | Windows 10/11 de 64 bits con WSL 2 | Ubuntu 22.04 o 24.04 (x86_64 o ARM) |
| Memoria / disco | 8 GB de RAM, 15 GB libres | 4 GB de RAM + 2 GB de swap, 30 GB de disco |
| Para levantar el sistema | Git y Docker Desktop | Git, Docker Engine y el plugin Docker Compose |
| Para las pruebas | Nada más (todo corre en contenedores), salvo Playwright: Node.js 22 y Microsoft Edge (viene con Windows) | Nada más, salvo Playwright: Node.js 22 |

No hace falta instalar Java, Maven, PHP ni Composer: las aplicaciones se compilan dentro de Docker y las pruebas unitarias se ejecutan en contenedores.

---

## Puesta en marcha en Windows (local), paso a paso

Todos los comandos son de **PowerShell**. Los que van en la carpeta del proyecto suponen que estás en ella (la que tiene `docker-compose.yml`).

### Paso 1. Instalar WSL 2 (solo la primera vez)

Docker Desktop necesita WSL 2. Abre PowerShell **como administrador** y ejecuta:

```powershell
wsl --install
```

Reinicia el equipo cuando lo pida. Si ya tenías WSL, `wsl --update` lo actualiza.

### Paso 2. Instalar Git y Docker Desktop (solo la primera vez)

1. Git: descarga e instala desde https://git-scm.com/download/win (opciones por defecto).
2. Docker Desktop: descarga e instala desde https://www.docker.com/products/docker-desktop/. Deja marcada la opción **Use WSL 2 instead of Hyper-V**.
3. Opcional, solo para las pruebas con navegador (Playwright): Node.js 22 LTS desde https://nodejs.org/.

### Paso 3. Arrancar Docker Desktop

1. Abre **Docker Desktop** desde el menú Inicio y espera a que abajo a la izquierda diga **Engine running**.
2. Comprueba en PowerShell que responde:

   ```powershell
   docker version
   docker compose version
   ```

   Si `docker version` muestra un error en la parte de **Server**, Docker Desktop todavía está arrancando.

### Paso 4. Clonar el repositorio

```powershell
cd $HOME\Desktop
git clone https://github.com/danielgonzalezme-crypto/PROYECTO-CONJUNTO.git
cd PROYECTO-CONJUNTO
```

### Paso 5. Crear el archivo `.env`

El `.env` guarda las contraseñas de las bases y **no se sube al repositorio**. Elige una de las dos formas:

**a) A mano:** copia la plantilla y cambia cada valor `cambia_esto_...` por una contraseña tuya.

```powershell
Copy-Item .env.example .env
notepad .env
```

**b) Automática** (contraseñas aleatorias de 20 caracteres):

```powershell
$c = Get-Content .env.example -Raw
foreach ($k in 'cambia_esto_root1','cambia_esto_res1','cambia_esto_root2','cambia_esto_adm1','cambia_esto_root3','cambia_esto_prop3') {
    $c = $c.Replace($k, -join ((48..57)+(65..90)+(97..122) | Get-Random -Count 20 | ForEach-Object {[char]$_}))
}
[IO.File]::WriteAllText("$PWD\.env", $c)
```

> **Importante:** si en este equipo ya levantaste antes otra copia del proyecto, las bases ya existen con las contraseñas de esa copia (el proyecto se llama siempre `conjuntosmart` en Docker y comparte los volúmenes). En ese caso usa **las mismas contraseñas** del `.env` de la otra copia; si no, las aplicaciones fallan con `Access denied`. Ver [Problemas frecuentes](#problemas-frecuentes).

Variables opcionales del `.env`:

| Variable | Para qué | Por defecto |
|---|---|---|
| `API_PORT` | Puerto del equipo para la API de Spring | `8080` |
| `PROPIETARIOS_API_PORT` | Puerto del equipo para la API de Node | `3000` |
| `APP_KEY` | Clave de Laravel. Si se deja vacía se genera al arrancar, pero cambia en cada reinicio y cierra las sesiones | vacía |
| `APP_URL` | Dirección pública de la web | `http://127.0.0.1:8000` |
| `ADMIN_WEB_BIND` | `0.0.0.0` publica la web fuera del equipo | `127.0.0.1` |

### Paso 6. Construir y levantar los contenedores

```powershell
docker compose up -d --build
```

La primera vez tarda entre 5 y 15 minutos porque descarga imágenes y compila Spring, Node y Laravel. Las siguientes veces basta `docker compose up -d`.

### Paso 7. Comprobar que todo está arriba

```powershell
docker compose ps
```

Deben aparecer **seis** servicios en estado `Up`: `residentes-db`, `administrativo-db` y `propietarios-db` como `(healthy)`, y `residentes-api`, `propietarios-api` y `admin-web`. Spring tarda unos 20 a 40 segundos más en responder. Para comprobarlo:

```powershell
Invoke-WebRequest http://127.0.0.1:8080/api/residentes -UseBasicParsing | Select-Object StatusCode
Invoke-WebRequest http://127.0.0.1:3000/health -UseBasicParsing | Select-Object StatusCode
Invoke-WebRequest http://127.0.0.1:8000/login -UseBasicParsing | Select-Object StatusCode
```

Los tres deben responder `200`. Si un contenedor aparece como `Restarting`, mira su registro con `docker compose logs --tail 50 <servicio>`.

### Paso 8. (Recomendado) Fijar la clave de Laravel

Para que las sesiones no se cierren cada vez que se reinicia el contenedor:

```powershell
docker compose exec admin-web php artisan key:generate --show
```

Copia el resultado (empieza por `base64:`) en la línea `APP_KEY=` del `.env` y aplica el cambio:

```powershell
docker compose up -d admin-web
```

### Paso 9. Entrar a la aplicación

Abre http://127.0.0.1:8000 e inicia sesión con el usuario de demostración (`admin@conjuntosmart.test` / `Admin12345`, ver [Inicio de sesión](#inicio-de-sesión)).

### Paso 10. Cargar datos de demostración

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\datos-demo.ps1
powershell -ExecutionPolicy Bypass -File .\scripts\datos-demo-propietarios.ps1
```

El primero crea cuatro residentes ficticios en Spring y el segundo sus fichas de contacto en Node. Los empleados se crean desde la web (menú **Empleados**).

---

## Despliegue en producción en Ubuntu, paso a paso

Así está montada la instancia de AWS. Todos los comandos son de **bash** en el servidor.

### Paso 1. Crear la máquina

1. Instancia EC2 (u otro servidor) con Ubuntu 22.04 o 24.04, **al menos 4 GB de memoria** y unos **30 GB de disco**. Sirve x86_64 o ARM (Graviton).
2. Grupo de seguridad (firewall) con entrada solo por:
   - **22/TCP** (SSH), idealmente solo desde tu IP.
   - **8000/TCP** (la web), desde cualquier lugar.

   No abras 8080, 3000 ni los puertos de MySQL (3308 a 3310).
3. Asocia una **IP elástica** para que la dirección no cambie al apagar y encender.

### Paso 2. Conectarse por SSH

```bash
ssh -i ruta/a/tu-llave.pem ubuntu@IP_PUBLICA
```

### Paso 3. Actualizar el sistema y crear 2 GB de swap

La compilación de Spring y Laravel necesita más memoria de la que trae una máquina de 4 GB:

```bash
sudo apt-get update && sudo apt-get upgrade -y
sudo fallocate -l 2G /swapfile
sudo chmod 600 /swapfile
sudo mkswap /swapfile
sudo swapon /swapfile
echo '/swapfile none swap sw 0 0' | sudo tee -a /etc/fstab
free -h
```

`free -h` debe mostrar `Swap: 2.0Gi`.

### Paso 4. Instalar Docker Engine y Docker Compose

Desde el repositorio oficial de Docker:

```bash
sudo apt-get install -y ca-certificates curl git openssl
sudo install -m 0755 -d /etc/apt/keyrings
sudo curl -fsSL https://download.docker.com/linux/ubuntu/gpg -o /etc/apt/keyrings/docker.asc
sudo chmod a+r /etc/apt/keyrings/docker.asc
echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] https://download.docker.com/linux/ubuntu $(. /etc/os-release && echo "${UBUNTU_CODENAME:-$VERSION_CODENAME}") stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
sudo apt-get update
sudo apt-get install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
```

Deja que tu usuario use Docker sin `sudo` y que Docker arranque con la máquina:

```bash
sudo usermod -aG docker $USER
sudo systemctl enable --now docker
exit
```

Vuelve a conectarte por SSH (para que el grupo `docker` se aplique) y comprueba:

```bash
docker version
docker compose version
docker run --rm hello-world
```

### Paso 5. Clonar el repositorio

```bash
cd ~
git clone https://github.com/danielgonzalezme-crypto/PROYECTO-CONJUNTO.git
cd PROYECTO-CONJUNTO
```

### Paso 6. Crear el `.env` de producción

Con contraseñas aleatorias, la clave fija de Laravel y la web publicada hacia afuera. Cambia `IP_PUBLICA` por la IP elástica:

```bash
cat > .env <<EOF
RESIDENTES_DB_ROOT_PASSWORD=$(openssl rand -hex 16)
RESIDENTES_DB_PASSWORD=$(openssl rand -hex 16)
ADMIN_DB_ROOT_PASSWORD=$(openssl rand -hex 16)
ADMIN_DB_PASSWORD=$(openssl rand -hex 16)
PROPIETARIOS_DB_ROOT_PASSWORD=$(openssl rand -hex 16)
PROPIETARIOS_DB_PASSWORD=$(openssl rand -hex 16)
APP_KEY=base64:$(openssl rand -base64 32)
APP_URL=http://IP_PUBLICA:8000
ADMIN_WEB_BIND=0.0.0.0
API_PORT=8080
PROPIETARIOS_API_PORT=3000
EOF
chmod 600 .env
```

`ADMIN_WEB_BIND=0.0.0.0` publica la web (puerto 8000) fuera de la máquina; sin ella queda solo en `127.0.0.1`. Las APIs y las bases siguen enlazadas a `127.0.0.1` del servidor.

Guarda una copia del `.env` en un lugar seguro: si se pierde, no hay forma de recuperar las contraseñas de las bases.

### Paso 7. Construir y levantar

```bash
docker compose up -d --build
```

La primera vez tarda de 10 a 15 minutos. Todos los servicios tienen `restart: unless-stopped`, así que vuelven solos si el servidor se reinicia.

### Paso 8. Comprobar

```bash
docker compose ps
curl -s -o /dev/null -w "Spring: %{http_code}\n" http://127.0.0.1:8080/api/residentes
curl -s -o /dev/null -w "Node:   %{http_code}\n" http://127.0.0.1:3000/health
curl -s -o /dev/null -w "Web:    %{http_code}\n" http://127.0.0.1:8000/login
```

Los tres deben dar `200` (Spring puede tardar unos segundos más). Desde tu navegador abre `http://IP_PUBLICA:8000`.

### Paso 9. Cambiar la contraseña del usuario de demostración

La contraseña `Admin12345` es pública en el repositorio. Cámbiala antes de compartir la dirección (pon la tuya en lugar de `NuevaClaveSegura`):

```bash
docker compose exec -T admin-web php artisan tinker --execute='$u = App\Models\User::firstWhere("email", "admin@conjuntosmart.test"); $u->password = "NuevaClaveSegura"; $u->save(); echo "Contrasena actualizada\n";'
```

El cambio se conserva: el seeder solo crea el usuario si no existe. Para crear usuarios adicionales: `docker compose exec admin-web php artisan app:crear-admin`.

### Paso 10. Cargar datos de prueba

Los scripts `.ps1` son de Windows; en el servidor usa el de bash, que crea en las dos APIs un residente y su ficha por cada cédula (las repetidas se omiten):

```bash
bash scripts/cargar-registros.sh 127.0.0.1 50
```

### Paso 11. (Opcional) Roles de MySQL

```bash
bash scripts/aplicar-roles.sh
```

Ver [Roles de la base de datos](#roles-de-la-base-de-datos).

### Actualizar el servidor con una versión nueva

```bash
cd ~/PROYECTO-CONJUNTO
git pull
docker compose up -d --build
docker compose ps
```

Los datos se conservan en los volúmenes de Docker.

---

## Pruebas

### Qué pruebas hay

| Prueba | Qué verifica | Dónde corre | Toca las bases reales |
|---|---|---|---|
| **JUnit + JaCoCo** (Spring) | 39 pruebas unitarias y de integración del servicio, el repositorio y el controlador, con cobertura | Contenedor `maven:3.9-eclipse-temurin-21`, base H2 en memoria | No |
| **PMD y CPD** (Spring) | Análisis estático y código duplicado | Mismo contenedor de Maven | No |
| **Pint** (Laravel) | Estilo del código PHP | Contenedor `php:8.4-cli` | No |
| **PHPStan + Larastan nivel 6** (Laravel) | Análisis estático | Contenedor `php:8.4-cli` | No |
| **PHPUnit** (Laravel) | 61 pruebas (login, empleados, búsqueda, parqueaderos, comandos), con cobertura | Contenedor `php:8.4-cli`, SQLite en memoria y APIs simuladas | No |
| **Postman / Newman**, colección de residentes | 12 casos sobre la API de Spring | Contra el sistema encendido | Sí (crea y borra la cédula `9990000001`) |
| **Postman / Newman**, colección completa | 42 peticiones sobre Spring, Node y Laravel | Contra el sistema encendido | Sí (crea y borra la cédula `9990000101`) |
| **Playwright** | 21 pruebas en un navegador real (login, empleados, dashboard, búsqueda, parqueaderos) | Contra el sistema encendido | Sí (cada prueba crea sus datos y los borra) |
| **k6** | Carga: 20 usuarios simultáneos durante 30 s sobre 1000 residentes | Contra el sistema encendido | Sí (crea 1000 residentes en la torre `P` y los borra al terminar) |

La API de Node no tiene pruebas unitarias propias; se prueba con la colección completa de Postman y con Playwright.

> **En producción:** las pruebas unitarias y el análisis estático usan bases en memoria y se pueden correr sin riesgo. Newman, Playwright y k6 escriben en las bases reales (aunque limpian al final) y k6 genera carga; córrelos en un momento de poco uso o en una máquina de pruebas.

### Preparación de las pruebas (una sola vez)

Un clon nuevo no trae las dependencias de PHP ni de Playwright (están en `.gitignore`). Antes de la primera ejecución, desde la carpeta del proyecto y con Docker encendido:

**Windows (PowerShell):**

```powershell
# 1. Dependencias de PHP (Composer dentro de Docker)
docker run --rm -v "${PWD}\admin-web:/app" -w /app composer:2 install --no-interaction --no-progress --prefer-dist --ignore-platform-reqs

# 2. Archivo .env de Laravel para las pruebas, con su clave (sin esto PHPUnit falla con MissingAppKeyException)
Copy-Item admin-web\.env.example admin-web\.env
docker run --rm -v "${PWD}\admin-web:/app" -w /app php:8.4-cli php artisan key:generate --force

# 3. Dependencias de Playwright (requiere Node.js 22)
cd pruebas\e2e
npm ci
cd ..\..
```

**Ubuntu (bash):**

```bash
# 1. Dependencias de PHP
docker run --rm -v "$PWD/admin-web:/app" -w /app composer:2 install --no-interaction --no-progress --prefer-dist --ignore-platform-reqs

# 2. Archivo .env de Laravel para las pruebas, con su clave
cp admin-web/.env.example admin-web/.env
docker run --rm -v "$PWD/admin-web:/app" -w /app php:8.4-cli php artisan key:generate --force

# 3. Los contenedores escriben como root: devuelve los archivos a tu usuario
sudo chown -R "$USER:$USER" admin-web
```

`admin-web/.env` solo lo usan las pruebas: el contenedor de la web toma su configuración del `docker-compose.yml`, y `.dockerignore` deja ese archivo fuera de la imagen.

### Pruebas en Windows

#### Todo de una vez

Con el sistema encendido (`docker compose up -d`) y la preparación hecha:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\ejecutar-pruebas.ps1
```

Ejecuta, en orden: JUnit + JaCoCo + PMD + CPD, Pint + PHPStan + PHPUnit con cobertura, la colección de Postman de residentes y la prueba de carga k6. Al final muestra un resumen `APROBADO` / `FALLÓ` por bloque. Agrega `-SinCarga` para omitir k6 (ahorra cerca de un minuto). La primera vez tarda más porque descarga las imágenes de Maven, PHP, Newman y k6.

El script no incluye Playwright ni la colección completa de Postman; esos van aparte (ver abajo).

#### Cada prueba por separado

**1. Pruebas unitarias de Spring (JUnit) con cobertura y análisis estático:**

```powershell
docker run --rm -v "${PWD}\residentes-api:/app" -v conjuntosmart_m2:/root/.m2 -w /app maven:3.9-eclipse-temurin-21 mvn -B verify pmd:check pmd:cpd-check
```

Solo las pruebas, sin análisis: cambia el final por `mvn -B test`. Para una sola clase: `mvn -B test -Dtest=ResidenteServiceImplTest`. El volumen `conjuntosmart_m2` guarda las descargas de Maven para que la siguiente vez sea más rápida.

**2. Pruebas unitarias de Laravel (PHPUnit), sin cobertura (rápido):**

```powershell
docker run --rm -v "${PWD}\admin-web:/app" -w /app php:8.4-cli php vendor/bin/phpunit --testdox
```

**3. Estilo y análisis estático de Laravel:**

```powershell
docker run --rm -v "${PWD}\admin-web:/app" -w /app php:8.4-cli php vendor/bin/pint --test
docker run --rm -v "${PWD}\admin-web:/app" -w /app php:8.4-cli php vendor/bin/phpstan analyse --no-progress --memory-limit=1G
```

**4. PHPUnit con cobertura** (instala PCOV dentro del contenedor, tarda un poco más):

```powershell
docker run --rm -v "${PWD}\admin-web:/app" -w /app php:8.4-cli sh -c 'apt-get update -qq >/dev/null && apt-get install -y -qq $PHPIZE_DEPS >/dev/null 2>&1 && pecl install pcov >/dev/null 2>&1 && docker-php-ext-enable pcov >/dev/null && php vendor/bin/phpunit --coverage-text --coverage-html storage/coverage-html'
```

**5. Postman, colección de residentes (la que usa el CI):**

```powershell
docker run --rm --network conjuntosmart_default -v "${PWD}\pruebas:/etc/newman" postman/newman:6-alpine run postman/ConjuntoSmart-API.postman_collection.json --env-var baseUrl=http://residentes-api:8080
```

**6. Postman, colección completa (Spring + Node + Laravel):**

```powershell
docker run --rm --network conjuntosmart_default -v "${PWD}\pruebas:/etc/newman" postman/newman:6-alpine run postman/ConjuntoSmart-Completo.postman_collection.json --env-var springUrl=http://residentes-api:8080 --env-var nodeUrl=http://propietarios-api:3000 --env-var webUrl=http://admin-web:8000
```

Si cambiaste la contraseña del usuario de demostración, agrega `--env-var adminPassword=TU_CLAVE`. Si tienes Newman instalado (`npm install -g newman`), también sirve `newman run pruebas\postman\ConjuntoSmart-Completo.postman_collection.json` sin Docker.

**7. Playwright (navegador real, usa Microsoft Edge):**

```powershell
cd pruebas\e2e
npx playwright test          # todas, sin ver el navegador
npm run test:ver             # con el navegador visible y más despacio (para exposiciones)
npm run reporte              # abre el reporte HTML de la última ejecución
cd ..\..
```

Para una sola prueba: `npx playwright test tests/login.spec.js`. Playwright lee los puertos de las APIs del `.env` y la contraseña de demostración de `admin-web/database/seeders/DatabaseSeeder.php`.

**8. k6 (carga):**

```powershell
docker run --rm -u root --network conjuntosmart_default -v "${PWD}\pruebas:/pruebas" -e BASE_URL=http://residentes-api:8080 grafana/k6:latest run /pruebas/rendimiento/carga-api.js
```

### Pruebas en Ubuntu (producción)

Desde `~/PROYECTO-CONJUNTO`, con el sistema encendido y la [preparación](#preparación-de-las-pruebas-una-sola-vez) hecha. El script `ejecutar-pruebas.ps1` es de PowerShell, así que en Linux se ejecuta cada bloque con estos comandos (son los mismos que usa el script y el CI).

**1. Spring: JUnit + JaCoCo + PMD + CPD**

```bash
docker run --rm -v "$PWD/residentes-api:/app" -v conjuntosmart_m2:/root/.m2 -w /app maven:3.9-eclipse-temurin-21 mvn -B verify pmd:check pmd:cpd-check 2>&1 | tee pruebas/resultados/junit-salida.txt | grep -E "Tests run:|PMD Failure|CPD Failure|BUILD"
```

Debe terminar con `Tests run: 39, Failures: 0, Errors: 0` y `BUILD SUCCESS`. Solo pruebas: `mvn -B test`.

**2. Laravel: Pint + PHPStan + PHPUnit con cobertura**

```bash
docker run --rm -v "$PWD/admin-web:/app" -w /app php:8.4-cli sh -c 'apt-get update -qq >/dev/null && apt-get install -y -qq $PHPIZE_DEPS >/dev/null 2>&1 && pecl install pcov >/dev/null 2>&1 && docker-php-ext-enable pcov >/dev/null && php vendor/bin/pint --test && php vendor/bin/phpstan analyse --no-progress --memory-limit=1G && php vendor/bin/phpunit --testdox --coverage-text --coverage-html storage/coverage-html --colors=never' 2>&1 | tee pruebas/resultados/phpunit-salida.txt | grep -E "PASS|FAIL|No errors|error|OK \(|Tests:|^\s+Lines:"
```

Debe mostrar `PASS ... 52 files`, `[OK] No errors` y `OK (61 tests, ...)`. Solo PHPUnit, rápido:

```bash
docker run --rm -v "$PWD/admin-web:/app" -w /app php:8.4-cli php vendor/bin/phpunit --testdox
```

**3. Postman (las dos colecciones)**

```bash
docker run --rm --network conjuntosmart_default -v "$PWD/pruebas:/etc/newman" postman/newman:6-alpine run postman/ConjuntoSmart-API.postman_collection.json --env-var baseUrl=http://residentes-api:8080

docker run --rm --network conjuntosmart_default -v "$PWD/pruebas:/etc/newman" postman/newman:6-alpine run postman/ConjuntoSmart-Completo.postman_collection.json --env-var springUrl=http://residentes-api:8080 --env-var nodeUrl=http://propietarios-api:3000 --env-var webUrl=http://admin-web:8000 --env-var adminPassword=TU_CLAVE
```

En el servidor de producción la contraseña de demostración ya se cambió (paso 9 del despliegue): pásala en `adminPassword`.

**4. k6 (carga)**

```bash
docker run --rm -u root --network conjuntosmart_default -v "$PWD/pruebas:/pruebas" -e BASE_URL=http://residentes-api:8080 grafana/k6:latest run --summary-export /pruebas/resultados/k6-resumen.json /pruebas/rendimiento/carga-api.js
```

Los umbrales (menos de 1 % de errores y tiempos p95 de 200 a 800 ms) aparecen con ✓ si se cumplen.

**5. Playwright en Ubuntu**

Instala Node.js 22 (una sola vez):

```bash
curl -fsSL https://deb.nodesource.com/setup_22.x | sudo -E bash -
sudo apt-get install -y nodejs
node --version
```

Instala dependencias y el Chromium de Playwright (una sola vez) y ejecuta. En Linux no hay Edge: la variable `CI=1` hace que use Chromium, igual que en GitHub Actions.

```bash
cd pruebas/e2e
npm ci
npx playwright install --with-deps chromium
CI=1 npx playwright test
cd ../..
```

Playwright inicia sesión con la contraseña del seeder (`Admin12345`). Si ya la cambiaste en el servidor, el login de las pruebas falla: córrelas antes del cambio, o en una máquina de pruebas.

**6. Devolver los archivos a tu usuario**

Los contenedores escriben como root en `residentes-api/target`, `admin-web/storage` y `pruebas/resultados`. Para poder borrarlos o hacer `git pull` sin problemas:

```bash
sudo chown -R "$USER:$USER" residentes-api admin-web pruebas
```

### Resultados y reportes

| Qué | Dónde |
|---|---|
| Salidas de texto (JUnit, PHPUnit, Newman, k6) | `pruebas/resultados/` (el script de Windows las escribe ahí) |
| Resumen documentado | `pruebas/RESULTADOS.md` |
| Cobertura de Spring (JaCoCo) | `residentes-api/target/site/jacoco/index.html` |
| Cobertura de Laravel (PHPUnit) | `admin-web/storage/coverage-html/index.html` |
| Reporte de Playwright | `pruebas/e2e/playwright-report/index.html` (`npm run reporte`) |
| Documento de calidad de la entrega | `CALIDAD_DE_SOFTWARE_SEGUNDA_ENTREGA.md` |

### Integración continua (GitHub Actions)

Cada push o pull request ejecuta `.github/workflows/ci.yml` con tres trabajos:

| Trabajo | Qué verifica |
|---|---|
| API Spring Boot | PMD, CPD (código duplicado), pruebas JUnit y cobertura con JaCoCo |
| Web Laravel | Estilo con Pint, PHPStan + Larastan (nivel 6) y PHPUnit con cobertura |
| Sistema completo | Levanta todo con Docker Compose, ejecuta la colección de Postman de residentes con Newman y las pruebas de Playwright en Chromium, y comprueba que, sin sesión, la web redirige al login |

El tercer trabajo solo corre si pasan los dos primeros. Los reportes de cobertura y de Playwright quedan como artefactos de cada ejecución.

---

## Colecciones de Postman

Están en `pruebas/postman/`:

| Archivo | Contenido |
|---|---|
| `ConjuntoSmart-API.postman_collection.json` | 12 casos de prueba de la API de Spring (la usa el CI). Variable `baseUrl`. |
| `ConjuntoSmart-Completo.postman_collection.json` | Todas las rutas: Spring, Node y el endpoint JSON de Laravel que cruza ambas APIs (42 peticiones, 76 comprobaciones). Variables `springUrl`, `nodeUrl`, `webUrl`, `cedula`, `adminEmail` y `adminPassword`. |

La colección completa está organizada en carpetas que se ejecutan en orden:

1. **Preparación:** estado de la API de Node y limpieza de restos de ejecuciones anteriores.
2. **API de residentes (Spring):** crear, consultar, listar (por torre, por deuda mínima y combinando filtros), morosos, actualizar, y los errores 400, 404 y 409.
3. **API de propietarios (Node):** crear, consultar, listar por tipo, actualizar, y los errores 400, 404 y 409, más una ruta que no existe.
4. **Web Laravel:** obtiene el token CSRF, inicia sesión, consulta `/parqueaderos/api/{cedula}` (une Spring y Node) y cierra sesión.
5. **Limpieza:** borra todo lo que creó, así que se puede repetir cuantas veces se quiera.

Para usarla en Postman: **Import** → elige el archivo → abre la colección → **Run collection**. Los valores por defecto apuntan a `127.0.0.1` con los puertos 8080, 3000 y 8000; cámbialos en la pestaña **Variables** de la colección si usas otros.

## Inicio de sesión

Todas las pantallas exigen iniciar sesión. Al arrancar, el contenedor crea (si no existe) un usuario fijo de demostración:

| Correo | Contraseña |
|---|---|
| `admin@conjuntosmart.test` | `Admin12345` |

Está definido en `admin-web/database/seeders/DatabaseSeeder.php`. Como esa contraseña es pública en el repositorio, **cámbiala antes de publicar la aplicación en internet** (paso 9 del [despliegue en Ubuntu](#paso-9-cambiar-la-contraseña-del-usuario-de-demostración); en la instancia de AWS ya se cambió). En Windows, para cambiarla abre la consola de Laravel y escribe las líneas una por una:

```powershell
docker compose exec admin-web php artisan tinker
```

```php
$u = App\Models\User::firstWhere('email', 'admin@conjuntosmart.test');
$u->password = 'NuevaClaveSegura';
$u->save();
exit
```

No hay registro público. Para crear más usuarios (pide nombre, correo y contraseña):

```
docker compose exec admin-web php artisan app:crear-admin
```

Tras 5 intentos fallidos con el mismo correo, el acceso se bloquea un minuto para frenar ataques de fuerza bruta.

## Qué ofrece la aplicación

- **Dashboard:** resumen por torre con residentes, morosos y deuda total.
- **Empleados:** crear, editar y eliminar el personal asignado a cada torre.
- **Residentes:** lista con teléfono y correo de cada residente (datos de la API de propietarios), **búsqueda por nombre, cédula, teléfono o correo** y filtro por torre. La búsqueda ignora mayúsculas y tildes, y en el teléfono ignora espacios y guiones.
- **Parqueaderos:** tabla que cruza el cupo de parqueadero de Spring con la bahía y la placa registradas en Node, más una consulta en vivo por cédula (`/parqueaderos/api/{cedula}` devuelve JSON con los datos de ambas APIs).

Si una API se cae, la web muestra un aviso rojo y sigue funcionando con lo demás (ver la [demostración](#demostración-qué-pasa-si-se-cae-una-api)).

## Datos de demostración

Los residentes y sus fichas de contacto se crean por las APIs.

En **Windows**:

```
powershell -ExecutionPolicy Bypass -File .\scripts\datos-demo.ps1
powershell -ExecutionPolicy Bypass -File .\scripts\datos-demo-propietarios.ps1
```

El primero crea cuatro residentes en Spring y el segundo carga sus fichas de contacto en Node.

En **Linux** (o para cargar muchos registros, por ejemplo 1000 para pruebas de volumen), el script de bash crea, por cada cédula, un residente en Spring y su ficha en Node:

```
bash scripts/cargar-registros.sh 127.0.0.1 1000
```

Desde la misma máquina del proyecto usa `127.0.0.1`. Para lanzarlo desde **otra** máquina, pon la IP privada de la del proyecto: en ese caso las dos APIs deben ser alcanzables desde ella (abrir 8080 y 3000 solo para esa máquina en el grupo de seguridad y publicarlos en el compose, que vienen enlazados a `127.0.0.1`). Volver a ejecutarlo no duplica nada: las cédulas repetidas se omiten.

Los empleados se crean desde la interfaz (menú **Empleados**). Usa el mismo nombre de torre (`A`, `B`...) en ambos lados para que el dashboard los cruce; mayúsculas y minúsculas no importan.

## API de residentes (Spring Boot)

Base: `http://127.0.0.1:8080` (o el puerto de `API_PORT`).

| Método | Ruta | Uso |
|---|---|---|
| GET | `/api/residentes` | Listar. Filtros opcionales y combinables: `?torre=A` y `?deudaMinima=100000` (mayor o igual) |
| GET | `/api/residentes/morosos` | Residentes con deuda mayor a 0 |
| GET | `/api/residentes/{cedula}` | Consultar uno |
| POST | `/api/residentes` | Crear |
| PUT | `/api/residentes/{cedula}` | Actualizar |
| DELETE | `/api/residentes/{cedula}` | Eliminar |

Códigos de respuesta: `200`, `201` (creado), `204` (eliminado), `400` (datos inválidos o JSON mal formado, con el detalle por campo en `errores`), `404` (no existe) y `409` (cédula ya registrada). Son obligatorios `cedula`, `nombre`, `torre`, `apartamento` y `deudaActual` (mayor o igual a 0); `tieneParqueadero` es opcional y por defecto `false`.

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

Base: `http://127.0.0.1:3000` (o el puerto de `PROPIETARIOS_API_PORT`).

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

Se crean con un script que se puede repetir (con los contenedores encendidos, desde bash: en Ubuntu directamente y en Windows con **Git Bash**):

```
bash scripts/aplicar-roles.sh            # genera una clave al azar y la muestra al final
bash scripts/aplicar-roles.sh MiClave123 # o usa la clave indicada (sin / ni &)
```

El script usa `scripts/roles.sql`. Para comprobarlo, con `usr_lectura` se puede consultar pero crear una tabla responde `ERROR 1142 ... CREATE command denied`; con `usr_admin` sí se puede. Los usuarios solo existen dentro de MySQL y los puertos de las bases no están abiertos a internet.

## Uso diario

Cada vez que enciendas el equipo (Windows):

1. Abre Docker Desktop y espera a que diga **Engine running**.
2. En la carpeta del proyecto:

   ```
   docker compose up -d
   docker compose ps
   ```

3. Abre http://127.0.0.1:8000. Si el dashboard muestra un aviso rojo, Spring aún está arrancando: espera unos segundos y recarga.
4. Antes de trabajar en el código, trae los cambios del equipo con `git pull`. Si cambiaron Spring, Node o la configuración de Laravel, reconstruye con `docker compose up -d --build`.
5. Para apagar: `docker compose stop` (conserva los datos).

En el servidor Ubuntu no hace falta nada: Docker arranca con la máquina y los contenedores vuelven solos.

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

La imagen guarda una copia del código, por eso un cambio en Spring, Node o la configuración de Laravel necesita `--build`. En Laravel, las carpetas `app`, `resources`, `routes` y `public` están montadas en el contenedor: sus cambios se ven sin reconstruir.

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
PROYECTO-CONJUNTO/
├── docker-compose.yml
├── .env.example
├── residentes-api/      (Spring Boot; pruebas en src/test)
├── propietarios-api/    (Node.js + TypeScript)
├── admin-web/           (Laravel; pruebas en tests/)
├── scripts/             (datos de demostración, roles de MySQL, carga de registros, pruebas)
├── pruebas/
│   ├── postman/         (colecciones de Postman)
│   ├── e2e/             (pruebas de Playwright)
│   ├── rendimiento/     (prueba de carga con k6)
│   └── resultados/      (salidas de las pruebas)
└── .github/workflows/   (integración continua)
```

## Problemas frecuentes

- **Una aplicación se reinicia sin parar y el registro dice `Access denied for user ...`:** las bases ya existían con otras contraseñas. Pasa si en el mismo equipo hay otra copia del proyecto: todas se llaman `conjuntosmart` en Docker y comparten los volúmenes (`conjuntosmart_residentes_data`, etc.). Solución sin perder datos: copia las seis contraseñas del `.env` de la otra copia al tuyo y ejecuta `docker compose up -d`. Si no te importan los datos, `docker compose down -v` borra las bases y se crean de nuevo con tu `.env`. Por la misma razón, no se pueden tener dos copias encendidas a la vez.
- **Cambié una contraseña en `.env` y no se aplicó:** las bases ya existen con la contraseña anterior. Solo se puede aplicar con `docker compose down -v`, que borra los datos.
- **Puerto 8000, 8080 o 3000 ocupado:** cierra cualquier Spring, Node o `php artisan serve` que tengas corriendo fuera de Docker. Si el 8080 o el 3000 los usa otro programa que no puedes cerrar, pon `API_PORT=8081` o `PROPIETARIOS_API_PORT=3001` (u otro libre) en tu `.env` y ejecuta `docker compose up -d`. Recuerda pasar el puerto nuevo a Postman (`springUrl` / `nodeUrl`).
- **El dashboard o Residentes muestran el aviso rojo:** la API correspondiente aún está arrancando; espera unos segundos y recarga.
- **`docker compose up` dice que falta una variable `PROPIETARIOS_...`:** tu `.env` es anterior a la API de Node. Agrega `PROPIETARIOS_DB_ROOT_PASSWORD` y `PROPIETARIOS_DB_PASSWORD`, copiándolas de `.env.example`.
- **Cambié la configuración de Laravel (`admin-web/config`) y no se nota:** esa carpeta va dentro de la imagen; usa `docker compose up -d --build admin-web`.
- **PHPUnit falla con `MissingAppKeyException`:** falta `admin-web/.env` con su clave. Haz el paso 2 de la [preparación de las pruebas](#preparación-de-las-pruebas-una-sola-vez).
- **PHPUnit o Pint dicen que no existe `vendor/bin/...`:** faltan las dependencias de PHP. Haz el paso 1 de la preparación.
- **Playwright en Linux dice que no encuentra `msedge`:** ejecútalo con `CI=1 npx playwright test` para que use Chromium.
- **En Ubuntu, `git pull` o borrar archivos da `Permission denied`:** los contenedores de prueba dejaron archivos de root. `sudo chown -R "$USER:$USER" residentes-api admin-web pruebas`.
- **En Ubuntu, `permission denied while trying to connect to the Docker daemon`:** tu usuario no está en el grupo `docker`. `sudo usermod -aG docker $USER` y vuelve a conectarte por SSH.
- **La compilación se detiene sin error claro en una máquina de 4 GB:** falta memoria. Revisa que el swap esté activo con `free -h` (paso 3 del despliegue).

## Autores

Daniel Estiben Gonzalez Medina  
Diego Jose Sandoval de la pava  
María Sofía Campos
