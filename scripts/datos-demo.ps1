# Crea residentes FICTICIOS en la API de Spring.
# Uso:  powershell -ExecutionPolicy Bypass -File .\scripts\datos-demo.ps1
# Toma el puerto de API_PORT en el archivo .env (por defecto 8080).

$puerto = 8080
$envFile = Join-Path (Split-Path -Parent $PSScriptRoot) '.env'
if (Test-Path $envFile) {
    $linea = Select-String -Path $envFile -Pattern '^\s*API_PORT\s*=\s*(\d+)' | Select-Object -Last 1
    if ($linea) { $puerto = $linea.Matches[0].Groups[1].Value }
}
$url = "http://127.0.0.1:$puerto/api/residentes"

$residentes = @(
    @{ cedula = "1000000001"; nombre = "Ana Torres (demo)";    torre = "A"; apartamento = "101"; deudaActual = 150000; tieneParqueadero = $true  },
    @{ cedula = "1000000002"; nombre = "Luis Rojas (demo)";    torre = "A"; apartamento = "202"; deudaActual = 0;      tieneParqueadero = $false },
    @{ cedula = "1000000003"; nombre = "Marta Gomez (demo)";   torre = "B"; apartamento = "301"; deudaActual = 90000;  tieneParqueadero = $true  },
    @{ cedula = "1000000004"; nombre = "Pedro Salazar (demo)"; torre = "b"; apartamento = "402"; deudaActual = 250000; tieneParqueadero = $false }
)

foreach ($r in $residentes) {
    try {
        Invoke-RestMethod -Uri $url -Method Post -ContentType "application/json" -Body ($r | ConvertTo-Json) | Out-Null
        Write-Host "Creado: $($r.nombre)"
    } catch {
        Write-Host "Omitido (puede que ya exista): $($r.nombre)"
    }
}
