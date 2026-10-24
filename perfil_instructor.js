function mostrarDetallesCurso(cursoId) {
    fetch(`obtenerDetallesCurso.php?curso_id=${cursoId}`)
        .then(response => response.json())
        .then(data => {
            let detallesCurso = document.getElementById('detalles-curso');
            let contenido = `
                <div class="curso-detalle-header">
                    <h2>${data.curso.TITULO}</h2>
                    <div class="curso-detalle-botones">
                        <button class="btn-editar" onclick="editarCurso(${data.curso.ID_CURSO})">Editar Curso</button>
                        <button class="btn-eliminar" onclick="eliminarCurso(${data.curso.ID_CURSO})">Eliminar Curso</button>
                    </div>
                </div>
                <p>Alumnos inscritos:</p>
                <ul>`;
            
            if (data.alumnos.length > 0) {
                data.alumnos.forEach(alumno => {
                    contenido += `
                        <li>
                            <button class="btn-alumno" onclick="mostrarDetallesAlumno(${cursoId}, ${alumno.ID_USUARIO})">
                                ${alumno.NOMBRE}
                            </button>
                        </li>`;
                });
            } else {
                contenido += `<li>No hay alumnos inscritos en este curso.</li>`;
            }
            contenido += `</ul>`;

            detallesCurso.innerHTML = contenido;
        })
        .catch(error => {
            console.error("Error al obtener los detalles del curso:", error);
        });
}


function mostrarDetallesAlumno(cursoId, usuarioId) {
    fetch(`obtenerDetallesAlumno.php?curso_id=${cursoId}&usuario_id=${usuarioId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error(data.error);
                return;
            }

            let detallesAlumno = document.getElementById('detalles-alumno');
            let contenido = `
                <h3>Detalles del Alumno: ${data.nombre_alumno}</h3>
                <p>Fecha de inscripción: ${data.fecha_inscripcion}</p>
                <p>Curso terminado: ${data.curso_terminado}</p>
                <p>Precio del curso: $${parseFloat(data.precio_curso).toFixed(2)}</p>
                <p>Forma de pago: ${data.forma_pago}</p>
            `;

            detallesAlumno.innerHTML = contenido;
        })
        .catch(error => {
            console.error("Error al obtener los detalles del alumno:", error);
        });
}


function eliminarCurso(cursoId) {
    console.log("Intentando eliminar curso con ID:", cursoId);
    if (confirm("¿Estás seguro de que deseas eliminar este curso?")) {
        fetch('00eliminar_curso.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `curso_id=${cursoId}`, // Enviar curso_id en el cuerpo
        })
        .then(response => response.text())
        .then(data => {
            alert(data); // Mostrar mensaje del servidor
            location.reload(); // Recargar la página para actualizar la lista
        })
        .catch(error => {
            console.error("Error al eliminar el curso:", error);
        });
    }
}

function editarCurso(cursoId) {
    window.location.href = `EditarCurso.php?curso_id=${cursoId}`;
}

document.getElementById("btnReportes").addEventListener("click", function () {
    const modal = new bootstrap.Modal(document.getElementById("modalReportes"));
    modal.show();
});

function aplicarFiltros() {
    const categoria = document.getElementById("categoriaCurso").value;
    const fechaInicio = document.getElementById("fechaInicio").value;
    const fechaFin = document.getElementById("fechaFin").value;
    const soloActivos = document.getElementById("soloActivos").checked ? 1 : 0;

    // Realizar una petición AJAX al servidor para filtrar los datos
    fetch("filtros_reportes.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            categoria,
            fechaInicio,
            fechaFin,
            soloActivos,
        }),
    })
        .then((response) => response.json())
        .then((data) => {
            // Actualizar las tablas con los datos filtrados
            document.getElementById("tablaCursos").innerHTML = data.cursosHTML;
            document.getElementById("tablaIngresos").innerHTML = data.ingresosHTML;
            document.getElementById("totalIngresos").textContent = `$${data.totalIngresos.toFixed(2)}`;
        })
        .catch((error) => console.error("Error:", error));
}

