#!/bin/bash
# Genera registros ficticios y los envia a las APIs del proyecto (Spring y Node),
# normalmente desde otra maquina. Uso:
#   bash scripts/cargar-registros.sh IP_DE_LA_MAQUINA_DEL_PROYECTO [cantidad]
# Cada registro crea un residente en Spring y su ficha de contacto en Node (misma cedula).
HOST=${1:?Uso: bash scripts/cargar-registros.sh IP_DE_LA_MAQUINA_DEL_PROYECTO [cantidad]}
N=${2:-1000}
NOMBRES=(Carlos Maria Juan Laura Andres Camila Sofia Diego Valentina Santiago Daniela Felipe Natalia Sebastian Paula Mateo Luisa Jorge Ana Pedro)
APELLIDOS=(Gomez Rodriguez Martinez Lopez Garcia Hernandez Perez Sanchez Ramirez Torres Diaz Vargas Castro Rojas Moreno Jimenez Ortiz Cardenas Salazar Mejia)
TORRES=(A B C D E)
TIPOS=(PROPIETARIO ARRENDATARIO)
OK_R=0; OK_P=0; FALLAS=0; REPETIDOS=0

cuenta() {
  case "$1" in
    201) return 0 ;;
    409) REPETIDOS=$((REPETIDOS+1)); return 1 ;;
    *) FALLAS=$((FALLAS+1)); return 1 ;;
  esac
}

for i in $(seq 1 "$N"); do
  CED=$((2000000000 + i))
  NOM=${NOMBRES[RANDOM % ${#NOMBRES[@]}]}
  APE=${APELLIDOS[RANDOM % ${#APELLIDOS[@]}]}
  TORRE=${TORRES[RANDOM % ${#TORRES[@]}]}
  APTO="$((RANDOM % 9 + 1))0$((RANDOM % 4 + 1))"
  if [ $((RANDOM % 10)) -lt 4 ]; then DEUDA=$(( (RANDOM % 50 + 1) * 10000 )); else DEUDA=0; fi
  if [ $((RANDOM % 2)) -eq 0 ]; then PARQ=true; else PARQ=false; fi
  TEL=$(printf '3%09d' $((RANDOM * 30000 + RANDOM)))
  TIPO=${TIPOS[RANDOM % 2]}
  MAIL=$(echo "$NOM.$APE.$CED" | tr 'A-Z' 'a-z')@example.com

  C1=$(curl -s -o /dev/null -w '%{http_code}' --max-time 15 -X POST "http://$HOST:8080/api/residentes" -H 'Content-Type: application/json' \
    -d "{\"cedula\":\"$CED\",\"nombre\":\"$NOM $APE\",\"torre\":\"$TORRE\",\"apartamento\":\"$APTO\",\"deudaActual\":$DEUDA,\"tieneParqueadero\":$PARQ}")
  cuenta "$C1" && OK_R=$((OK_R+1))

  C2=$(curl -s -o /dev/null -w '%{http_code}' --max-time 15 -X POST "http://$HOST:3000/api/propietarios" -H 'Content-Type: application/json' \
    -d "{\"cedula\":\"$CED\",\"telefono\":\"$TEL\",\"correo\":\"$MAIL\",\"tipoResidente\":\"$TIPO\"}")
  cuenta "$C2" && OK_P=$((OK_P+1))

  if [ $((i % 100)) -eq 0 ]; then echo "$i/$N  residentes=$OK_R  fichas=$OK_P  repetidos=$REPETIDOS  fallas=$FALLAS"; fi
done
echo "TERMINADO: residentes=$OK_R fichas=$OK_P repetidos=$REPETIDOS fallas=$FALLAS"
