# Crea residentes FICTICIOS en la API de Spring (http://127.0.0.1:8081).
# Uso:  powershell -ExecutionPolicy Bypass -File .\scripts\datos-demo.ps1

$url = "http://127.0.0.1:8081/api/residentes"

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
