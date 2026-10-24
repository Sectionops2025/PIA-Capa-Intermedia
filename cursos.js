function buscarCurso() {
    const searchTerm = document.getElementById('buscar').value.toLowerCase();
    const cursos = document.querySelectorAll('.course-card');

    cursos.forEach(curso => {
        const titulo = curso.querySelector('h3').textContent.toLowerCase();
        curso.style.display = titulo.includes(searchTerm) ? 'block' : 'none';
    });
}

function filtrarPorEtiqueta(idCategoria) {
    fetch(`buscar_cursos.php?categoria=${idCategoria}`)
        .then(response => response.json())
        .then(data => {
            mostrarCursos(data);
        })
        .catch(error => console.error("Error al filtrar por categoría:", error));
}
// Filtro por instructor usando el ID
function filtrarPorInstructor() {
    const idInstructor = document.getElementById("instructor").value;
    fetch(`buscar_cursos.php?instructor=${idInstructor}`)
        .then(response => response.json())
        .then(data => {
            mostrarCursos(data);
        })
        .catch(error => console.error("Error al filtrar por instructor:", error));
}

function mostrarCursos(cursos) {
    const contenedor = document.getElementById("cursos-container");
    contenedor.innerHTML = ""; // Limpiar los cursos existentes

    if (cursos.length > 0) {
        cursos.forEach(curso => {
            const card = `
                <div class="course-card">
                    <img src="${curso.IMAGEN ? `data:image/jpeg;base64,${curso.IMAGEN}` : 'default-course.jpg'}" alt="${curso.TITULO}">
                    <div class="course-content">
                        <h3>${curso.TITULO}</h3>
                        <p>${curso.DESCRIPCURSO}</p>
                        <span class="price">$${curso.COSTO}</span>
                        <a href="curso.php?id=${curso.ID_CURSO}" class="btn-buy">Comprar ahora</a>
                    </div>
                </div>
            `;
            contenedor.insertAdjacentHTML("beforeend", card);
        });
    } else {
        contenedor.innerHTML = "<p>No hay cursos disponibles para este filtro.</p>";
    }
}

// Filtro por fecha
function filtrarPorFecha() {
    const fechaInicio = document.getElementById("fechaInicio").value;
    const fechaFin = document.getElementById("fechaFin").value;

    // Validar que se seleccionen ambas fechas
    if (!fechaInicio || !fechaFin) {
        alert("Por favor, selecciona ambas fechas.");
        return;
    }

    fetch(`buscar_cursos.php?fechaInicio=${fechaInicio}&fechaFin=${fechaFin}`)
        .then(response => response.json())
        .then(data => {
            mostrarCursos(data);
        })
        .catch(error => console.error("Error al filtrar por fecha:", error));
}

