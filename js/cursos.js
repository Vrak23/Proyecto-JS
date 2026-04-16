// ─── LISTAR ───────────────────────────────────────────────
function listarCursos() {
    fetch('php/procesar_curso.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ opcion: '4' })
    })
    .then(res => res.json())
    .then(datos => {
        if (!Array.isArray(datos)) {
            console.error("Error al listar cursos:", datos);
            return;
        }

        let html = "";
        datos.forEach(curso => {
            html += `
                <tr>
                    <td>${curso.ID_CURSO}</td>
                    <td><strong>${curso.NOMBRE}</strong></td>
                    <td>${curso.DESCRIPCION ?? '-'}</td>
                    <td>
                        <span class="badge bg-primary">${curso.CREDITOS} cr.</span>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-warning btn-sm me-1" onclick="editarCurso(${curso.ID_CURSO})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="eliminarCurso(${curso.ID_CURSO})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>`;
        });

        document.getElementById("lista_cursos").innerHTML =
            html || '<tr><td colspan="5" class="text-center text-muted py-4">No hay cursos registrados</td></tr>';
    })
    .catch(err => console.error("Error fetch cursos:", err));
}

// ─── AL CARGAR ────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    listarCursos();

    // ─── GUARDAR ──────────────────────────────────────────
    document.getElementById('formCurso').addEventListener('submit', function (e) {
        e.preventDefault();

        const id     = document.getElementById('id_curso').value.trim();
        const opcion = id ? '5' : '1';
        const datos  = new FormData(this);
        datos.append('opcion', opcion);

        fetch('php/procesar_curso.php', {
            method: 'POST',
            body: new URLSearchParams(datos)
        })
        .then(res => res.json())
        .then(resp => {
            if (resp.exito) {
                bootstrap.Modal.getInstance(document.getElementById('modalCurso')).hide();
                limpiarFormCurso();
                listarCursos();
            } else {
                alert("Error: " + resp.mensaje);
            }
        })
        .catch(err => console.error("Error guardar curso:", err));
    });
});

// ─── EDITAR ───────────────────────────────────────────────
function editarCurso(id) {
    fetch('php/procesar_curso.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ opcion: '2', id_curso: id })
    })
    .then(res => res.json())
    .then(curso => {
        if (!curso || curso.exito === false) { alert("No encontrado"); return; }

        document.getElementById('id_curso').value                             = curso.ID_CURSO;
        document.querySelector('#formCurso [name="nombre"]').value            = curso.NOMBRE;
        document.querySelector('#formCurso [name="descripcion"]').value       = curso.DESCRIPCION;
        document.querySelector('#formCurso [name="creditos"]').value          = curso.CREDITOS;

        document.getElementById('modalCursoLabel').textContent = "Editar Curso";
        new bootstrap.Modal(document.getElementById('modalCurso')).show();
    })
    .catch(err => console.error("Error editar curso:", err));
}

// ─── ELIMINAR ─────────────────────────────────────────────
function eliminarCurso(id) {
    if (!confirm("¿Eliminar este curso?")) return;

    fetch('php/procesar_curso.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ opcion: '3', id_curso: id })
    })
    .then(res => res.json())
    .then(resp => {
        if (resp.exito) listarCursos();
        else alert("Error al eliminar: " + resp.mensaje);
    })
    .catch(err => console.error("Error eliminar curso:", err));
}

// ─── LIMPIAR FORM ─────────────────────────────────────────
function limpiarFormCurso() {
    document.getElementById('formCurso').reset();
    document.getElementById('id_curso').value = '';
    document.getElementById('modalCursoLabel').textContent = "Nuevo Curso";
}
