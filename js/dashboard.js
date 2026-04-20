let chartGenero  = null;
let chartNiveles = null;


$(document).ready(function () {
    cargarDatosDashboard(); 
});

function cargarDatosDashboard() {
    fetch('php/dashboard_datos.php')
        .then(res => res.json())
        .then(resp => {
            if (!resp.exito) {
                console.error("Error dashboard:", resp.mensaje);
                return;
            }

            const datos = resp.datos;

            // â”€â”€ Actualizar KPIs â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
            document.getElementById('kpiTotalAlumnos').textContent = datos.kpis.totalAlumnos ?? 0;
            document.getElementById('kpiTotalAulas').textContent   = datos.kpis.totalAulas   ?? 0;
            document.getElementById('kpiVacantesDisp').textContent = datos.kpis.vacantesDisp ?? 0;

            // â”€â”€ GrÃ¡fico 1: Alumnos por GÃ©nero â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
            const generoLabels = datos.graficos.genero.map(g =>
                g.GENERO === 'M' ? 'Masculino' : 'Femenino'
            );
            const generoData = datos.graficos.genero.map(g => g.cantidad);
            dibujarGraficoGenero(generoLabels, generoData);

            // â”€â”€ GrÃ¡fico 2: Vacantes por Nivel â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
            const nivelesLabels     = datos.graficos.niveles.map(n => n.NIVEL);
            const nivelesTotales    = datos.graficos.niveles.map(n => n.totales);
            const nivelesDisponibles= datos.graficos.niveles.map(n => n.disponibles);
            dibujarGraficoNiveles(nivelesLabels, nivelesTotales, nivelesDisponibles);
        })
        .catch(err => console.error("Error fetch dashboard:", err));
}



// â€” GrÃ¡fico Dona: Alumnos por GÃ©nero â€”
function dibujarGraficoGenero(etiquetas, datos) {
    let ctx = document.getElementById('graficoGenero').getContext('2d');

    // Destruir instancia anterior para evitar superposiciÃ³n
    if (chartGenero) { chartGenero.destroy(); }

    chartGenero = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: etiquetas,
            datasets: [{
                data: datos,
                backgroundColor: ['#3498DB', '#E74C3C', '#F1C40F'],
                borderWidth: 2,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
}

// â€” GrÃ¡fico Barras: Vacantes por Nivel Educativo â€”
function dibujarGraficoNiveles(etiquetas, totales, disponibles) {
    let ctx = document.getElementById('graficoNiveles').getContext('2d');

    if (chartNiveles) { chartNiveles.destroy(); }

    chartNiveles = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: etiquetas,
            datasets: [
                {
                    label: 'Vacantes Totales',
                    data: totales,
                    backgroundColor: '#95A5A6',
                    borderRadius: 4
                },
                {
                    label: 'Vacantes Disponibles',
                    data: disponibles,
                    backgroundColor: '#2ECC71',
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
}

function limpiarForm() {
    document.getElementById('formAlumno').reset();
    document.getElementById('id_alumno').value = '';
}

function listarAlumnos() {
    fetch('php/procesar_alumno.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ opcion: '4' })
    })
    .then(res => res.json())
    .then(datos => {
        if (!Array.isArray(datos)) return;

        let html = "";
        datos.forEach(alumno => {
            html += `
                <tr>
                    <td>${alumno.ID_ALUMNO}</td>
                    <td>${alumno.NOMBRE}</td>
                    <td>${alumno.APELLIDO}</td>
                    <td>${alumno.FECHA_NACIMIENTO
                            ? new Date(alumno.FECHA_NACIMIENTO).toLocaleDateString('es-PE')
                            : '-'}</td>
                    <td>${alumno.CELULAR ?? '-'}</td>
                    <td>${alumno.CORREO}</td>
                    <td>${alumno.ESTADO === 'ACTIVO'
                            ? '<span class="badge bg-success">Activo</span>'
                            : '<span class="badge bg-danger">Inactivo</span>'}</td>
                    <td class="text-center">
                        <button class="btn btn-warning btn-sm me-1" onclick="editarAlu(${alumno.ID_ALUMNO})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="eliminarAlu(${alumno.ID_ALUMNO})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>`;
        });

        document.getElementById("lista_alumnos").innerHTML = html;
    })
    .catch(err => console.error("Error listar:", err));
}

// Submit del formulario (INSERT o UPDATE)
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('formAlumno').addEventListener('submit', function (e) {
        e.preventDefault();

        const id     = document.getElementById('id_alumno').value.trim();
        const opcion = id ? '5' : '1';
        const datos  = new FormData(this);
        datos.append('opcion', opcion);

        fetch('php/procesar_alumno.php', {
            method: 'POST',
            body: new URLSearchParams(datos)
        })
        .then(res => res.json())
        .then(resp => {
            if (resp.exito) {
                bootstrap.Modal.getInstance(document.getElementById('modalAlumno')).hide();
                listarAlumnos();
                cargarDatosDashboard(); // Actualizar KPIs tras guardar
            } else {
                alert("Error: " + resp.mensaje);
            }
        })
        .catch(err => console.error("Error guardar:", err));
    });
});

function editarAlu(id) {
    fetch('php/procesar_alumno.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ opcion: '2', id_alumno: id })
    })
    .then(res => res.json())
    .then(alumno => {
        if (!alumno || alumno.exito === false) { alert("No encontrado"); return; }

        document.getElementById('id_alumno').value         = alumno.ID_ALUMNO;
        document.getElementById('dni').value               = alumno.DNI_ALUMNO;
        document.getElementById('nombres').value           = alumno.NOMBRE;
        document.getElementById('apellidos').value         = alumno.APELLIDO;
        document.getElementById('fecha_nacimiento').value  = alumno.FECHA_NACIMIENTO;
        document.getElementById('edad').value              = alumno.EDAD;
        document.getElementById('genero').value            = alumno.GENERO;
        document.getElementById('direccion').value         = alumno.DIRECCION;
        document.getElementById('correo').value            = alumno.CORREO;
        document.getElementById('celular').value           = alumno.CELULAR;
        document.getElementById('nombre_apoderado').value  = alumno.NOMBRE_APODERADO;
        document.getElementById('celular_apoderado').value = alumno.CELULAR_APODERADO;
        document.getElementById('estado').value            = alumno.ESTADO;

        new bootstrap.Modal(document.getElementById('modalAlumno')).show();
    })
    .catch(err => console.error("Error editar:", err));
}

function eliminarAlu(id) {
    if (!confirm("Â¿Eliminar este alumno?")) return;

    fetch('php/procesar_alumno.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ opcion: '3', id_alumno: id })
    })
    .then(res => res.json())
    .then(resp => {
        if (resp.exito) {
            listarAlumnos();
            cargarDatosDashboard();
        } else {
            alert("Error: " + resp.mensaje);
        }
    })
    .catch(err => console.error("Error eliminar:", err));
}
