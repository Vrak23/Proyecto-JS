// ─── LISTAR ───────────────────────────────────────────────
function listarAlumnos() {
    fetch('php/procesar_alumno.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ opcion: '4' })
    })
    .then(res => res.json())
    .then(datos => {

        console.log("DATOS:", datos);

        if (!Array.isArray(datos)) {
            console.error("Respuesta inesperada:", datos);
            return;
        }

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
                    <td>
                        ${alumno.ESTADO === 'ACTIVO'
                            ? '<span class="badge bg-success">Activo</span>'
                            : '<span class="badge bg-danger">Inactivo</span>'}
                    </td>
                    <td class="text-center">
                        <button class="btn btn-warning btn-sm me-1" onclick="editarAlu(${alumno.ID_ALUMNO})">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="eliminarAlu(${alumno.ID_ALUMNO})">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </td>
                </tr>`;
        });

        document.getElementById("lista_alumnos").innerHTML = html;
    })
    .catch(error => console.error("Error al listar:", error));
}

// ─── LIMPIAR FORM ─────────────────────────────────────────
function limpiarForm() {
    document.getElementById('formAlumno').reset();
    document.getElementById('id_alumno').value = '';
}

// ─── AL CARGAR LA PÁGINA ──────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {

    listarAlumnos();

    // ─── GUARDAR (INSERT opcion=1 / UPDATE opcion=5) ──────
    document.getElementById('formAlumno').addEventListener('submit', function (e) {
        e.preventDefault();

        const id     = document.getElementById('id_alumno').value.trim();
        const opcion = id ? '5' : '1';

        const datos = new FormData(this);
        datos.append('opcion', opcion);

        fetch('php/procesar_alumno.php', {
            method: 'POST',
            body: new URLSearchParams(datos)
        })
        .then(res => res.json())
        .then(resp => {
            console.log("Respuesta guardar:", resp);
            if (resp.exito) {
                const modalEl = document.getElementById('modalAlumno');
                const modal   = bootstrap.Modal.getInstance(modalEl);
                modal.hide();
                listarAlumnos();
            } else {
                alert("Error: " + resp.mensaje);
            }
        })
        .catch(err => console.error("Error al guardar:", err));
    });
});

// ─── EDITAR ───────────────────────────────────────────────
function editarAlu(id) {
    fetch('php/procesar_alumno.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ opcion: '2', id_alumno: id })
    })
    .then(res => res.json())
    .then(alumno => {
        if (!alumno || alumno.exito === false) {
            alert("No se encontró el alumno");
            return;
        }

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

        const modal = new bootstrap.Modal(document.getElementById('modalAlumno'));
        modal.show();
    })
    .catch(err => console.error("Error al editar:", err));
}

// ─── ELIMINAR ─────────────────────────────────────────────
function eliminarAlu(id) {
    if (!confirm("¿Eliminar este alumno?")) return;

    fetch('php/procesar_alumno.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ opcion: '3', id_alumno: id })
    })
    .then(res => res.json())
    .then(resp => {
        if (resp.exito) {
            listarAlumnos();
        } else {
            alert("Error al eliminar: " + resp.mensaje);
        }
    })
    .catch(err => console.error("Error al eliminar:", err));
}