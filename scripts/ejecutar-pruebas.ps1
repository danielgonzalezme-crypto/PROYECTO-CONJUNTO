# Ejecuta todas las pruebas del proyecto y guarda los resultados en pruebas\resultados\
# Requisitos: Docker Desktop encendido y el proyecto levantado (docker compose up -d).
# Uso:  powershell -ExecutionPolicy Bypass -File .\scripts\ejecutar-pruebas.ps1
#       (agrega -SinCarga para omitir la prueba de rendimiento, que tarda ~1 minuto)

param([switch]$SinCarga)

$ErrorActionPreference = 'Continue'
$raiz = Split-Path -Parent $PSScriptRoot
$resultados = Join-Path $raiz 'pruebas\resultados'
$red = 'conjuntosmart_default'
New-Item -ItemType Directory -Force $resultados | Out-Null

if (-not (Get-Command docker -ErrorAction SilentlyContinue)) {
    $env:Path += ';C:\Program Files\Docker\Docker\resources\bin'
}

$resumen = [ordered]@{}

function Paso($titulo) { Write-Host "`n=== $titulo ===" -ForegroundColor Cyan }

Paso '1/4 API: pruebas JUnit + JaCoCo y análisis estático PMD/CPD (Java 21)'
docker run --rm -v "${raiz}\residentes-api:/app" -v conjuntosmart_m2:/root/.m2 -w /app `
    maven:3.9-eclipse-temurin-21 mvn -B verify pmd:check pmd:cpd-check 2>&1 | Tee-Object "$resultados\junit-salida.txt" |
    Select-String 'Tests run:|PMD Failure|CPD Failure|BUILD'
$resumen['JUnit + PMD (API)'] = $LASTEXITCODE -eq 0

Paso '2/4 Web: Pint, PHPStan y PHPUnit con cobertura (PCOV)'
docker run --rm -v "${raiz}\admin-web:/app" -w /app php:8.4-cli sh -c `
    'apt-get update -qq >/dev/null && apt-get install -y -qq $PHPIZE_DEPS >/dev/null 2>&1 && pecl install pcov >/dev/null 2>&1 && docker-php-ext-enable pcov >/dev/null && php vendor/bin/pint --test && php vendor/bin/phpstan analyse --no-progress --memory-limit=1G && php vendor/bin/phpunit --testdox --coverage-text --coverage-html storage/coverage-html --coverage-clover storage/coverage.xml --colors=never' 2>&1 |
    Tee-Object "$resultados\phpunit-salida.txt" | Select-String 'PASS|FAIL|No errors|Found \d+ error|OK \(|FAILURES|Tests:|Lines:'
$resumen['Pint+PHPStan+PHPUnit'] = $LASTEXITCODE -eq 0

Paso '3/4 Pruebas funcionales de la API con Postman (Newman)'
docker run --rm --network $red -v "${raiz}\pruebas:/etc/newman" postman/newman:6-alpine run `
    postman/ConjuntoSmart-API.postman_collection.json --env-var 'baseUrl=http://residentes-api:8080' `
    --reporters cli,junit --reporter-junit-export resultados/newman-resultados.xml --color off 2>&1 |
    Tee-Object "$resultados\newman-salida.txt" | Select-Object -Last 18
$resumen['Postman (API real)'] = $LASTEXITCODE -eq 0

if (-not $SinCarga) {
    Paso '4/4 Prueba de rendimiento con k6 (1000 residentes, 20 usuarios, 30 s)'
    docker run --rm -u root --network $red -v "${raiz}\pruebas:/pruebas" -e BASE_URL=http://residentes-api:8080 `
        grafana/k6:latest run --quiet --summary-export /pruebas/resultados/k6-resumen.json /pruebas/rendimiento/carga-api.js 2>&1 |
        Tee-Object "$resultados\k6-salida.txt" | Select-String "p\(95\)<|rate<|http_req_failed\.|http_reqs\."
    $resumen['k6 (rendimiento)'] = $LASTEXITCODE -eq 0
}

Paso 'Resumen'
foreach ($p in $resumen.GetEnumerator()) {
    $estado = if ($p.Value) { 'APROBADO' } else { 'FALLÓ' }
    $color = if ($p.Value) { 'Green' } else { 'Red' }
    Write-Host ("{0,-22} {1}" -f $p.Key, $estado) -ForegroundColor $color
}
Write-Host "`nReportes de cobertura:"
Write-Host "  API: residentes-api\target\site\jacoco\index.html"
Write-Host "  Web: admin-web\storage\coverage-html\index.html"
Write-Host "Salidas guardadas en: $resultados"

if ($resumen.Values -contains $false) { exit 1 }
