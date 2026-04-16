document.addEventListener('DOMContentLoaded', () => {
    cargarDatosMatriculas();
    listarMatriculas();

    document.getElementById('formMatricula').addEventListener('submit', function (e) {
        e.preventDefault();
        const id = document.getElementById('id_matricula').value.trim();
        const opcion = id ? '5' : '1';
        const datos = new FormData(this);
        datos.append('opcion', opcion);

        fetch('php/procesar_matricula.php', {
            method: 'POST',
            body: new URLSearchParams(datos)
        })
        .then(res => res.json())
        .then(resp => {
            if (!resp.exito) {
                alert('Error: ' + resp.mensaje);
                return;
            }

            const modal = bootstrap.Modal.getInstance(document.getElementById('modalMatricula'));
            if (modal) modal.hide();
            limpiarFormMatricula();
            listarMatriculas();
        })
        .catch(err => console.error('Error guardar matricula:', err));
    });

    document.getElementById('buscarMatricula').addEventListener('input', listarMatriculas);
    document.getElementById('filtroEstado').addEventListener('change', listarMatriculas);
    document.getElementById('filtroPeriodo').addEventListener('change', listarMatriculas);
});

function cargarDatosMatriculas() {
    fetch('php/procesar_matricula.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ opcion: '6' })
    })
    .then(res => res.json())
    .then(resp => {
        if (!resp.exito) {
            console.error('Error cargar datos matriculas:', resp.mensaje);
            return;
        }

        llenarSelect('matricula_alumno', resp.alumnos, 'ID_ALUMNO', 'NOMBRE_COMPLETO');
        llenarSelect('matricula_aula', resp.aulas, 'ID_AULA', 'nombre_aula');
        llenarSelect('matricula_curso', resp.cursos, 'ID_CURSO', 'NOMBRE');
    })
    .catch(err => console.error('Error fetch datos matriculas:', err));
}

function llenarSelect(selectId, datos, valueKey, textKey) {
    const select = document.getElementById(selectId);
    if (!select) return;
    select.innerHTML = '<option value="">Seleccionar</option>';
    datos.forEach(item => {
        const option = document.createElement('option');
        option.value = item[valueKey];
        option.textContent = item[textKey];
        select.appendChild(option);
    });
}

function listarMatriculas() {
    const busqueda = document.getElementById('buscarMatricula').value.toLowerCase();
    const estado = document.getElementById('filtroEstado').value;
    const periodo = document.getElementById('filtroPeriodo').value;

    fetch('php/procesar_matricula.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ opcion: '4' })
    })
    .then(res => res.json())
    .then(resp => {
        if (!resp.exito) {
            console.error('Error listar matriculas:', resp.mensaje);
            return;
        }

        let matriculas = resp.matriculas;

        if (busqueda) {
            matriculas = matriculas.filter(item =>
                item.CODIGO.toLowerCase().includes(busqueda) ||
                item.ESTUDIANTE.toLowerCase().includes(busqueda) ||
                item.CURSO.toLowerCase().includes(busqueda) ||
                item.AULA.toLowerCase().includes(busqueda)
            );
        }
        if (estado) {
            matriculas = matriculas.filter(item => item.ESTADO === estado);
        }
        if (periodo) {
            matriculas = matriculas.filter(item => item.PERIODO === periodo);
        }

        renderizarMatriculas(matriculas);
        actualizarKPIs(resp.matriculas);
    })
    .catch(err => console.error('Error fetch matriculas:', err));
}

function renderizarMatriculas(matriculas) {
    const tbody = document.getElementById('tabla_matriculas');
    if (!tbody) return;

    if (!Array.isArray(matriculas) || matriculas.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">No hay matrículas registradas</td></tr>';
        return;
    }

    tbody.innerHTML = matriculas.map(item => `
        <tr>
            <td>${item.CODIGO}</td>
            <td>${item.ESTUDIANTE}</td>
            <td>${item.AULA}</td>
            <td>${item.CURSO}</td>
            <td>${item.FECHA_MATRICULA}</td>
            <td><span class="badge ${item.ESTADO === 'Activa' ? 'bg-success' : item.ESTADO === 'Pendiente' ? 'bg-warning text-dark' : 'bg-danger'}">${item.ESTADO}</span></td>
            <td class="text-center">
                <button class="btn btn-warning btn-sm me-1" onclick="editarMatricula(${item.ID_MATRICULA})"><i class="fas fa-edit"></i></button>
                <button class="btn btn-danger btn-sm" onclick="eliminarMatricula(${item.ID_MATRICULA})"><i class="fas fa-trash"></i></button>
            </td>
        </tr>`).join('');
}

function actualizarKPIs(matriculas) {
    const totales = matriculas.length;
    const activas = matriculas.filter(m => m.ESTADO === 'Activa').length;
    const pendientes = matriculas.filter(m => m.ESTADO === 'Pendiente').length;

    document.getElementById('kpiTotalMatriculas').textContent = totales;
    document.getElementById('kpiActivas').textContent = activas;
    document.getElementById('kpiPendientes').textContent = pendientes;
}

function limpiarFormMatricula() {
    document.getElementById('formMatricula').reset();
    document.getElementById('id_matricula').value = '';
}

function editarMatricula(id) {
    fetch('php/procesar_matricula.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ opcion: '2', id_matricula: id })
    })
    .then(res => res.json())
    .then(resp => {
        if (!resp.exito) {
            alert('Matricula no encontrada');
            return;
        }

        const m = resp.matricula;
        document.getElementById('id_matricula').value = m.ID_MATRICULA;
        document.getElementById('matricula_alumno').value = m.ID_ALUMNO;
        document.getElementById('matricula_aula').value = m.ID_AULA;
        document.getElementById('matricula_curso').value = m.ID_CURSO;
        document.getElementById('matricula_fecha').value = m.FECHA_MATRICULA;
        document.getElementById('matricula_periodo').value = m.PERIODO;
        document.getElementById('matricula_estado').value = m.ESTADO;

        new bootstrap.Modal(document.getElementById('modalMatricula')).show();
    })
    .catch(err => console.error('Error editar matricula:', err));
}

function eliminarMatricula(id) {
    if (!confirm('¿Eliminar esta matrícula?')) return;

    fetch('php/procesar_matricula.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ opcion: '3', id_matricula: id })
    })
    .then(res => res.json())
    .then(resp => {
        if (!resp.exito) {
            alert('Error al eliminar: ' + resp.mensaje);
            return;
        }
        listarMatriculas();
    })
    .catch(err => console.error('Error eliminar matricula:', err));
}
