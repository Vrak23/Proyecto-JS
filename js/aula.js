document.addEventListener("DOMContentLoaded", () => {
    listarAula();

    document.getElementById("formAula")
        .addEventListener("submit", e => {
            e.preventDefault();
            guardarAula();
        });
});

// LISTAR
function listarAula() {
    fetch('php/procesar_aula.php', {
        method: 'POST',
        body: new URLSearchParams({ opcion: '4' })
    })
    .then(res => res.json())
    .then(data => {

        let html = "";

        data.forEach(a => {
            html += `
            <tr>
                <td>${a.ID_AULA}</td>
                <td>${a.NIVEL}</td>
                <td>${a.GRADO}</td>
                <td>${a.SECCION}</td>
                <td>${a.VACANTES_TOTALES}</td>
                <td>${a.VACANTES_DISPONIBLES}</td>
                <td>
                    <button onclick="editar(${a.ID_AULA})" class="btn btn-warning btn-sm">Editar</button>
                    <button onclick="eliminar(${a.ID_AULA})" class="btn btn-danger btn-sm">Eliminar</button>
                </td>
            </tr>`;
        });

        document.getElementById("lista_aula").innerHTML = html;
    });
}

// GUARDAR
function guardarAula() {
    let form = new FormData(document.getElementById("formAula"));
    let id = document.getElementById("id_aula").value;

    form.append("opcion", id ? "5" : "1");

    fetch('php/procesar_aula.php', {
        method: 'POST',
        body: form
    })
    .then(res => res.json())
    .then(() => {
        listarAula();
        document.getElementById("formAula").reset();
    });
}

// ELIMINAR
function eliminar(id) {
    fetch('php/procesar_aula.php', {
        method: 'POST',
        body: new URLSearchParams({
            opcion: '3',
            id_aula: id
        })
    })
    .then(() => listarAula());
}

// EDITAR
function editar(id) {
    fetch('php/procesar_aula.php', {
        method: 'POST',
        body: new URLSearchParams({
            opcion: '2',
            id_aula: id
        })
    })
    .then(res => res.json())
    .then(a => {

        document.getElementById("id_aula").value = a.ID_AULA;
        document.querySelector("[name='nivel']").value = a.NIVEL;
        document.querySelector("[name='grado']").value = a.GRADO;
        document.querySelector("[name='seccion']").value = a.SECCION;
        document.querySelector("[name='vacantes_totales']").value = a.VACANTES_TOTALES;
        document.querySelector("[name='vacantes_disponibles']").value = a.VACANTES_DISPONIBLES;

        new bootstrap.Modal(document.getElementById("modalAula")).show();
    });
}