# Crea fichas FICTICIAS de propietarios en la API de Node (http://127.0.0.1:3000).
# Las cédulas coinciden con los residentes de demostración de datos-demo.ps1.
# Uso:  powershell -ExecutionPolicy Bypass -File .\scripts\datos-demo-propietarios.ps1

$url = "http://127.0.0.1:3000/api/propietarios"

$fichas = @(
    @{ cedula = "1000000001"; telefono = "3000000001"; correo = "ana.demo@example.com";   tipoResidente = "PROPIETARIO";  contactoEmergenciaNombre = "Contacto Demo Uno";    contactoEmergenciaTelefono = "3100000001"; placaVehiculo = "ABC123"; numeroParqueadero = "P-01"; tieneMascotas = $true;  observaciones = "Datos ficticios" },
    @{ cedula = "1000000002"; telefono = "3000000002"; correo = "luis.demo@example.com";  tipoResidente = "ARRENDATARIO"; contactoEmergenciaNombre = "Contacto Demo Dos";    contactoEmergenciaTelefono = "3100000002"; tieneMascotas = $false; observaciones = "Datos ficticios" },
    @{ cedula = "1000000003"; telefono = "3000000003"; correo = "marta.demo@example.com"; tipoResidente = "PROPIETARIO";  contactoEmergenciaNombre = "Contacto Demo Tres";   contactoEmergenciaTelefono = "3100000003"; placaVehiculo = "XYZ789"; numeroParqueadero = "P-07"; tieneMascotas = $false; observaciones = "Datos ficticios" },
    @{ cedula = "1000000004"; telefono = "3000000004"; correo = "pedro.demo@example.com"; tipoResidente = "ARRENDATARIO"; tieneMascotas = $true; observaciones = "Datos ficticios" }
)

foreach ($f in $fichas) {
    $cedula = $f.cedula
    try {
        Invoke-RestMethod -Uri $url -Method Post -ContentType "application/json" -Body ($f | ConvertTo-Json) | Out-Null
        Write-Host "Creada la ficha de la cedula $cedula"
    } catch {
        # Si ya existe, se actualiza (el PUT lleva la cedula en la URL y no en el cuerpo).
        $cambios = $f.Clone()
        $cambios.Remove("cedula")
        try {
            Invoke-RestMethod -Uri "$url/$cedula" -Method Put -ContentType "application/json" -Body ($cambios | ConvertTo-Json) | Out-Null
            Write-Host "Actualizada la ficha de la cedula $cedula"
        } catch {
            Write-Host "No se pudo crear ni actualizar la ficha de la cedula $cedula : $($_.Exception.Message)"
        }
    }
}
