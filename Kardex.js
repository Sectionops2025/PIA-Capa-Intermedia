document.addEventListener("DOMContentLoaded", () => {
    // Evento para cargar y aplicar filtros
    document.getElementById("filtrar-btn").addEventListener("click", () => {
        // Obtener valores de los filtros
        const progreso = document.getElementById("progreso").value;
        const fechaInicio = document.getElementById("fecha_inicio").value;
        const fechaFin = document.getElementById("fecha_fin").value;
        const categoria = document.getElementById("categoria").value;

        // Construir parámetros de consulta
        const params = new URLSearchParams();
        if (progreso) params.append("progreso", progreso);
        if (fechaInicio) params.append("fecha_inicio", fechaInicio);
        if (fechaFin) params.append("fecha_fin", fechaFin);
        if (categoria) params.append("categoria", categoria);

        // Realizar la solicitud al servidor
        fetch(`procesar_filtros.php?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                // Limpiar tabla antes de agregar datos
                const tableBody = document.getElementById("tabla-resultados");
                tableBody.innerHTML = "";

                // Renderizar los datos en la tabla
                data.forEach(curso => {
                    const row = document.createElement("tr");

                    // Crear celdas para cada campo
                    const idCurso = document.createElement("td");
                    idCurso.textContent = curso.ID_CURSO;

                    const categoria = document.createElement("td");
                    categoria.textContent = curso.NOMCAT;

                    const titulo = document.createElement("td");
                    titulo.textContent = curso.TITULO;

                    const costo = document.createElement("td");
                    costo.textContent = curso.COSTO;

                    const descripcion = document.createElement("td");
                    descripcion.textContent = curso.DESCRIPCURSO;

                    const calificacion = document.createElement("td");
                    calificacion.textContent = curso.CALIFICACION;

                    const imagen = document.createElement("td");
                    const imgElement = document.createElement("img");
                    imgElement.src = curso.IMAGEN; // Convertir base64 a imagen
                    imgElement.alt = 'Imagen del curso';
                    imgElement.style.width = '100px'; // Ajustar tamaño
                    imagen.appendChild(imgElement);

                    // Agregar celdas a la fila
                    row.appendChild(idCurso);
                    row.appendChild(categoria);
                    row.appendChild(titulo);
                    row.appendChild(costo);
                    row.appendChild(descripcion);
                    row.appendChild(calificacion);
                    row.appendChild(imagen);

                    // Agregar fila a la tabla
                    tableBody.appendChild(row);
                });
            })
            .catch(error => console.error("Error al obtener los datos:", error));
    });
});
