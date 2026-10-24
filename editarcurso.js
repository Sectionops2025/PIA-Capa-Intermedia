document.addEventListener("DOMContentLoaded", function () {
    const queryParams = new URLSearchParams(window.location.search);
    const cursoId = queryParams.get("curso_id");

    if (!cursoId) {
        alert("No se encontró el ID del curso.");
        window.location.href = "perfil_instructor.php";
        return;
    }

    // Cargar los datos del curso desde el servidor
    fetch(`00DetallesCursoEditar.php?curso_id=${cursoId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                window.location.href = "perfil_instructor.php";
                return;
            }

            // Cargar datos del curso en los campos
            document.getElementById("curso_id").value = data.curso.ID_CURSO;
            document.getElementById("titulo").value = data.curso.TITULO;
            document.getElementById("descripcion").value = data.curso.DESCRIPCURSO;
            document.getElementById("costo").value = data.curso.COSTO;

        });

    // Función para crear el HTML de un nivel
    function crearNivelHTML(id, titulo = "", contenido = "", costo = "") {
        const nivelDiv = document.createElement("div");
        nivelDiv.classList.add("nivel");
        nivelDiv.id = `nivel-${id}`;
        nivelDiv.innerHTML = `
            <input type="hidden" name="niveles[${id}][id]" value="${id}">
            <label>Título:</label>
            <input type="text" name="niveles[${id}][titulo]" value="${titulo}" class="form-control">
            <label>Contenido:</label>
            <textarea name="niveles[${id}][texto]" class="form-control">${contenido}</textarea>
            <label>Costo:</label>
            <input type="number" name="niveles[${id}][costo]" value="${costo}" class="form-control">
            <button type="button" class="btn btn-danger mt-2 eliminar-btn" onclick="eliminarNivel('${id}')">Eliminar Nivel</button>
        `;
        return nivelDiv;
    }

    // Agregar un nuevo nivel
    document.getElementById("agregarNivelBtn").addEventListener("click", function () {
        const nuevoId = `nuevo-${Date.now()}`; // Generar un ID único para nuevos niveles
        const nivelDiv = crearNivelHTML(nuevoId);
        document.getElementById("nivelesContainer").appendChild(nivelDiv);
    });

    // Eliminar un nivel
    window.eliminarNivel = function (id) {
        const nivelDiv = document.getElementById(`nivel-${id}`);
        if (nivelDiv) nivelDiv.remove();
    };

    // Cancelar edición
    document.getElementById("btnCancelar").addEventListener("click", function () {
        window.location.href = "perfil_instructor.php";
    });

    // Manejo del envío del formulario
    document.getElementById("editarCursoForm").addEventListener("submit", async function (event) {
        event.preventDefault();
        const formData = new FormData(this);

        try {
            const response = await fetch("00EditarCurso.php", {
                method: "POST",
                body: formData,
            });

            const result = await response.text();
            if (response.ok) {
                alert(result);
                window.location.href = "perfil_instructor.php";
            } else {
                alert("Error al guardar los cambios: " + result);
            }
        } catch (error) {
            console.error("Error:", error);
            alert("Hubo un problema al guardar los cambios. Intenta nuevamente.");
        }
    });
});