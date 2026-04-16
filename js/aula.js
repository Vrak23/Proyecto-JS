// ─── LISTAR ───────────────────────────────────────────────
function listarAulas() {
    fetch('php/procesar_aula.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ opcion: '4' })
    })
    .then(res => res.json())
    .then(datos => {
        if (!Array.isArray(datos)) {
            console.error("Error al listar aulas:", datos);
            return;
        }

        let html = "";
        datos.forEach(aula => {
            const pct = aula.VACANTES_TOTALES > 0
                ? Math.round(((aula.VACANTES_TOTALES - aula.VACANTES_DISPONIBLES) / aula.VACANTES_TOTALES) * 100)
                : 0;

            html += `
                <tr>
                    <td>${aula.ID_AULA}</td>
                    <td><span class="badge bg-secondary">${aula.NIVEL}</span></td>
                    <td>${aula.GRADO}°</td>
                    <td><strong>${aula.SECCION}</strong></td>
                    <td>${aula.VACANTES_TOTALES}</td>
                    <td>
                        <span class="badge ${aula.VACANTES_DISPONIBLES > 0 ? 'bg-success' : 'bg-danger'}">
                            ${aula.VACANTES_DISPONIBLES}
                        </span>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-warning btn-sm me-1" onclick="editarAula(${aula.ID_AULA})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="eliminarAula(${aula.ID_AULA})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>`;
        });

        document.getElementById("lista_aula").innerHTML =
            html || '<tr><td colspan="7" class="text-center text-muted py-4">No hay aulas registradas</td></tr>';
    })
    .catch(err => console.error("Error fetch aulas:", err));
}

// ─── AL CARGAR ────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    listarAulas();

    // ─── GUARDAR ──────────────────────────────────────────
    document.getElementById('formAula').addEventListener('submit', function (e) {
        e.preventDefault();

        const id     = document.getElementById('id_aula').value.trim();
        const opcion = id ? '5' : '1';
        const datos  = new FormData(this);
        datos.append('opcion', opcion);

        fetch('php/procesar_aula.php', {
            method: 'POST',
            body: new URLSearchParams(datos)
        })
        .then(res => res.json())
        .then(resp => {
            if (resp.exito) {
                bootstrap.Modal.getInstance(document.getElementById('modalAula')).hide();
                limpiarFormAula();
                listarAulas();
            } else {
                alert("Error: " + resp.mensaje);
            }
        })
        .catch(err => console.error("Error guardar aula:", err));
    });
});

// ─── EDITAR ───────────────────────────────────────────────
function editarAula(id) {
    fetch('php/procesar_aula.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ opcion: '2', id_aula: id })
    })
    .then(res => res.json())
    .then(aula => {
        if (!aula || aula.exito === false) { alert("No encontrado"); return; }

        document.getElementById('id_aula').value                                         = aula.ID_AULA;
        document.querySelector('#formAula [name="nivel"]').value                         = aula.NIVEL;
        document.querySelector('#formAula [name="grado"]').value                         = aula.GRADO;
        document.querySelector('#formAula [name="seccion"]').value                       = aula.SECCION;
        document.querySelector('#formAula [name="vacantes_totales"]').value              = aula.VACANTES_TOTALES;
        document.querySelector('#formAula [name="vacantes_disponibles"]').value          = aula.VACANTES_DISPONIBLES;

        document.getElementById('modalAulaLabel').textContent = "Editar Aula";
        new bootstrap.Modal(document.getElementById('modalAula')).show();
    })
    .catch(err => console.error("Error editar aula:", err));
}

// ─── ELIMINAR ─────────────────────────────────────────────
function eliminarAula(id) {
    if (!confirm("¿Eliminar esta aula?")) return;

    fetch('php/procesar_aula.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ opcion: '3', id_aula: id })
    })
    .then(res => res.json())
    .then(resp => {
        if (resp.exito) listarAulas();
        else alert("Error al eliminar: " + resp.mensaje);
    })
    .catch(err => console.error("Error eliminar aula:", err));
}

// ─── LIMPIAR FORM ─────────────────────────────────────────
function limpiarFormAula() {
    document.getElementById('formAula').reset();
    document.getElementById('id_aula').value = '';
    document.getElementById('modalAulaLabel').textContent = "Nueva Aula";
}