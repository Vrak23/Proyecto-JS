function listarAlumnos() {
    fetch('php/procesar_alumnos.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({ opcion: '4' })
    })
    .then(res => res.json())
    .then(datos => {

        console.log("DATOS:", datos);

        if (!Array.isArray(datos)) {
            console.error("No es array:", datos);
            return;
        }

        let html = "";

        datos.forEach(alumno => {
            html += `
                <tr>
                    <td>${alumno.ID_ALUMNO}</td>
                    <td>${alumno.NOMBRES}</td>
                    <td>${alumno.APELLIDOS}</td>
                    <td>${new Date(alumno.FECHA_NACIMIENTO).toLocaleDateString()}</td>
                    <td>${alumno.CELULAR}</td>
                    <td>${alumno.CORREO}</td>
                    <td>
                        ${alumno.ESTADO === 'ACTIVO' 
                            ? '<span class="badge bg-success">Activo</span>' 
                            : '<span class="badge bg-danger">Inactivo</span>'}
                    </td>
                    <td class="text-center">
                        <button class="btn btn-warning btn-sm" onclick="editarAlu(${alumno.ID_ALUMNO})">Editar</button>
                        <button class="btn btn-danger btn-sm" onclick="eliminarAlu(${alumno.ID_ALUMNO})">Eliminar</button>
                    </td>
                </tr>`;
        });

        document.getElementById("lista_alumnos").innerHTML = html;
    })
    .catch(error => console.error("Error listar:", error));
}