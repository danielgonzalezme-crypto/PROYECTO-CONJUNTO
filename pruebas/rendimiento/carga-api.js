// Prueba de carga de la API de residentes con k6.
// 1) setup: crea 1000 residentes de prueba en la torre "P".
// 2) 20 usuarios simultáneos consultan la API durante 30 segundos.
// 3) teardown: elimina los residentes de prueba.
import http from 'k6/http';
import { check, sleep } from 'k6';

const BASE = __ENV.BASE_URL || 'http://127.0.0.1:8081';
const API = `${BASE}/api/residentes`;
const TOTAL = 1000;
const PREFIJO = '777';
const JSON_HEADERS = { headers: { 'Content-Type': 'application/json' } };

export const options = {
    setupTimeout: '120s',
    teardownTimeout: '120s',
    scenarios: {
        consultas: { executor: 'constant-vus', vus: 20, duration: '30s' },
    },
    thresholds: {
        http_req_failed: ['rate<0.01'],            // menos del 1 % de errores
        'http_req_duration{tipo:listar}': ['p(95)<800'],
        'http_req_duration{tipo:torre}': ['p(95)<300'],
        'http_req_duration{tipo:cedula}': ['p(95)<200'],
    },
};

const cedula = (i) => `${PREFIJO}${String(i).padStart(7, '0')}`;

function porLotes(construir) {
    for (let inicio = 0; inicio < TOTAL; inicio += 50) {
        const lote = [];
        for (let i = inicio; i < Math.min(inicio + 50, TOTAL); i++) lote.push(construir(i));
        http.batch(lote);
    }
}

export function setup() {
    porLotes((i) => ['POST', API, JSON.stringify({
        cedula: cedula(i), nombre: `Carga ${i}`, torre: 'P', apartamento: String(100 + (i % 400)),
        deudaActual: i % 3 === 0 ? 50000 : 0, tieneParqueadero: i % 2 === 0,
    }), JSON_HEADERS]);
}

export default function () {
    const todos = http.get(API, { tags: { tipo: 'listar' } });
    check(todos, { 'listar: 200': (r) => r.status === 200, 'listar: >= 1000': (r) => r.json().length >= TOTAL });

    const torre = http.get(`${API}?torre=P`, { tags: { tipo: 'torre' } });
    check(torre, { 'torre: 200': (r) => r.status === 200 });

    const uno = http.get(`${API}/${cedula(Math.floor(Math.random() * TOTAL))}`, { tags: { tipo: 'cedula' } });
    check(uno, { 'cédula: 200': (r) => r.status === 200 });

    sleep(0.5);
}

export function teardown() {
    porLotes((i) => ['DELETE', `${API}/${cedula(i)}`]);
}
