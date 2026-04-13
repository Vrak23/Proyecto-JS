document.addEventListener("DOMContentLoaded", () => {
    listarCursos();

    document.getElementById("formCurso")
        .addEventListener("submit", e => {
            e.preventDefault();
            guardarCurso();
        });
});

// LISTAR
function listarCursos() {
    fetch('php/procesar_curso.php', {
        method: 'POST',
        body: new URLSearchParams({ opcion: '4' })
    })
    .then(res => res.json())
    .then(data => {

        let html = "";

        data.forEach(c => {
            html += `
            <tr>
                <td>${c.ID_CURSO}</td>
                <td>${c.NOMBRE}</td>
                <td>${c.DESCRIPCION}</td>
                <td>${c.CREDITOS}</td>
                <td>
                    <button onclick="editar(${c.ID_CURSO})" class="btn btn-warning btn-sm">Editar</button>
                    <button onclick="eliminar(${c.ID_CURSO})" class="btn btn-danger btn-sm">Eliminar</button>
                </td>
            </tr>`;
        });

        document.getElementById("lista_cursos").innerHTML = html;
    });
}

// GUARDAR
function guardarCurso() {
    let form = new FormData(document.getElementById("formCurso"));
    let id = document.getElementById("id_curso").value;

    form.append("opcion", id ? "5" : "1");

    fetch('php/procesar_curso.php', {
        method: 'POST',
        body: form
    })
    .then(res => res.json())
    .then(() => {
        listarCursos();
        document.getElementById("formCurso").reset();
    });
}

// ELIMINAR
function eliminar(id) {
    fetch('php/procesar_curso.php', {
        method: 'POST',
        body: new URLSearchParams({
            opcion: '3',
            id_curso: id
        })
    })
    .then(() => listarCursos());
}

// EDITAR
function editar(id) {
    fetch('php/procesar_curso.php', {
        method: 'POST',
        body: new URLSearchParams({
            opcion: '2',
            id_curso: id
        })
    })
    .then(res => res.json())
    .then(c => {

        document.getElementById("id_curso").value = c.ID_CURSO;
        document.querySelector("[name='nombre']").value = c.NOMBRE;
        document.querySelector("[name='descripcion']").value = c.DESCRIPCION;
        document.querySelector("[name='creditos']").value = c.CREDITOS;

        new bootstrap.Modal(document.getElementById("modalCurso")).show();
    });
}