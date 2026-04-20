// ─── LISTAR ───────────────────────────────────────────────
function listarConfiguraciones() {
    fetch('php/procesar_configuracion.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ opcion: '4' })
    })
    .then(res => res.json())
    .then(datos => {
        if (!Array.isArray(datos)) {
            console.error("Error al listar configuraciones:", datos);
            return;
        }

        let html = "";
        datos.forEach(config => {
            html += `
                <tr>
                    <td>${config.ID_CONFIGURACION}</td>
                    <td><strong>${config.CLAVE}</strong></td>
                    <td>${config.VALOR}</td>
                    <td>${config.DESCRIPCION || '-'}</td>
                    <td class="text-center">
                        <button class="btn btn-warning btn-sm me-1" onclick="editarConfiguracion(${config.ID_CONFIGURACION})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="eliminarConfiguracion(${config.ID_CONFIGURACION})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>`;
        });

        document.getElementById("lista_configuracion").innerHTML =
            html || '<tr><td colspan="5" class="text-center text-muted py-4">No hay configuraciones registradas</td></tr>';
    })
    .catch(err => console.error("Error fetch configuraciones:", err));
}

// ─── AL CARGAR ────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    listarConfiguraciones();

    // ─── GUARDAR ──────────────────────────────────────────
    document.getElementById('formConfiguracion').addEventListener('submit', function (e) {
        e.preventDefault();

        const id     = document.getElementById('id_configuracion').value.trim();
        const opcion = id ? '5' : '1';
        const datos  = new FormData(this);
        datos.append('opcion', opcion);

        fetch('php/procesar_configuracion.php', {
            method: 'POST',
            body: new URLSearchParams(datos)
        })
        .then(res => res.json())
        .then(resp => {
            if (resp.exito) {
                bootstrap.Modal.getInstance(document.getElementById('modalConfiguracion')).hide();
                limpiarFormConfiguracion();
                listarConfiguraciones();
            } else {
                alert("Error: " + resp.mensaje);
            }
        })
        .catch(err => console.error("Error guardar configuracion:", err));
    });
});

// ─── EDITAR ───────────────────────────────────────────────
function editarConfiguracion(id) {
    fetch('php/procesar_configuracion.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ opcion: '2', id_configuracion: id })
    })
    .then(res => res.json())
    .then(config => {
        if (!config || config.exito === false) { alert("No encontrado"); return; }

        document.getElementById('id_configuracion').value                          = config.ID_CONFIGURACION;
        document.querySelector('#formConfiguracion [name="clave"]').value         = config.CLAVE;
        document.querySelector('#formConfiguracion [name="valor"]').value         = config.VALOR;
        document.querySelector('#formConfiguracion [name="descripcion"]').value  = config.DESCRIPCION;

        document.getElementById('modalConfiguracionLabel').textContent = "Editar Configuracion";
        new bootstrap.Modal(document.getElementById('modalConfiguracion')).show();
    })
    .catch(err => console.error("Error editar configuracion:", err));
}

// ─── ELIMINAR ─────────────────────────────────────────────
function eliminarConfiguracion(id) {
    if (!confirm("¿Eliminar esta configuracion?")) return;

    fetch('php/procesar_configuracion.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ opcion: '3', id_configuracion: id })
    })
    .then(res => res.json())
    .then(resp => {
        if (resp.exito) listarConfiguraciones();
        else alert("Error al eliminar: " + resp.mensaje);
    })
    .catch(err => console.error("Error eliminar configuracion:", err));
}

// ─── LIMPIAR FORM ─────────────────────────────────────────
function limpiarFormConfiguracion() {
    document.getElementById('formConfiguracion').reset();
    document.getElementById('id_configuracion').value = '';
    document.getElementById('modalConfiguracionLabel').textContent = "Nueva Configuracion";
}