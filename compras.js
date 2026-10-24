document.addEventListener("DOMContentLoaded", function() {
    fetch("00MisCursos.php")
        .then(response => response.json())
        .then(data => {
            const tbody = document.querySelector("#compras-table tbody");
            tbody.innerHTML = "";

            if (data.data && data.data.length > 0) {
                data.data.forEach(curso => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td><a href="CursoCom.php?id=${curso.ID_CURSO}" class="curso-link">${curso.TITULO}</a></td>
                        <td>${curso.FECHA_INSCRIPCION || "Fecha no disponible"}</td>
                        <td><a href="DetallesCurso.php?id=${curso.ID_CURSO}" class="btn-action">Ver Curso</a></td>
                    `;
                    tbody.appendChild(row);
                });
            } else {
                tbody.innerHTML = "<tr><td colspan='3' style='text-align: center;'>No tienes cursos comprados.</td></tr>";
            }
        })
        .catch(error => console.error("Error al cargar los cursos:", error));
});