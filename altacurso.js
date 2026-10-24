document.addEventListener("DOMContentLoaded", function () {
    let nivelCount = 0; // Contador de niveles inicial

    // Función para actualizar el número de niveles en el input oculto
    function actualizarNumeroDeNiveles() {
        document.getElementById('niveles').value = nivelCount;
    }

    // Función para agregar un nuevo nivel
    window.agregarNiveles = function () {
        const nivelesContainer = document.getElementById('nivelesContainer');
        const nivelDiv = document.createElement('div');
        nivelDiv.classList.add('nivel');

        nivelDiv.innerHTML = `
            <h4>Nivel ${nivelCount + 1}</h4>
            <label for="nivel${nivelCount + 1}Titulo" class="form-label">Título</label>
            <input type="text" class="form-control" name="niveles[${nivelCount}][titulo]" placeholder="Título del Nivel">

            <label for="nivel${nivelCount + 1}Texto" class="form-label">Contenido</label>
            <textarea class="form-control" name="niveles[${nivelCount}][texto]" rows="3" placeholder="Contenido del Nivel"></textarea>

            <label for="nivel${nivelCount + 1}Costo" class="form-label">Costo del nivel</label>
            <input type="number" class="form-control" name="niveles[${nivelCount}][costo]" placeholder="Costo del nivel si es independiente">

            <label for="nivel${nivelCount + 1}Video" class="form-label">Video del nivel</label>
            <input type="file" class="form-control" name="niveles[${nivelCount}][video]" accept="video/*">

            <button type="button" class="btn btn-danger mt-2 eliminar-btn" onclick="eliminarNivel(${nivelCount + 1})">Eliminar Nivel</button>
        `;
        nivelDiv.id = `nivel-${nivelCount + 1}`;
        nivelesContainer.appendChild(nivelDiv);

        nivelCount++; // Incrementamos el contador de niveles
        actualizarNumeroDeNiveles(); // Actualizamos el número de niveles en el campo oculto
    };

    // Función para eliminar un nivel
    window.eliminarNivel = function (nivelId) {
        const nivel = document.getElementById(`nivel-${nivelId}`);
        if (nivel) {
            nivel.remove(); // Eliminamos el nivel del DOM
            nivelCount--; // Reducimos el contador de niveles
            actualizarNumeroDeNiveles(); // Actualizamos el número de niveles
            reorganizarNiveles(); // Reorganizamos los números de los niveles
        }
    };

    // Función para reorganizar los números de los niveles
    function reorganizarNiveles() {
        const niveles = document.querySelectorAll('.nivel');
        niveles.forEach((nivel, index) => {
            const nivelNumero = index + 1;
            nivel.querySelector('h4').textContent = `Nivel ${nivelNumero}`;
            nivel.querySelector('label[for^="nivel"]').setAttribute('for', `nivel${nivelNumero}Titulo`);
            nivel.querySelector('input[type="text"]').name = `niveles[${index}][titulo]`;
            nivel.querySelector('label[for^="nivel"]').setAttribute('for', `nivel${nivelNumero}Texto`);
            nivel.querySelector('textarea').name = `niveles[${index}][texto]`;
            nivel.querySelector('label[for^="nivel"]').setAttribute('for', `nivel${nivelNumero}Costo`);
            nivel.querySelector('input[type="number"]').name = `niveles[${index}][costo]`;
            nivel.querySelector('label[for^="nivel"]').setAttribute('for', `nivel${nivelNumero}Video`);
            nivel.querySelector('input[type="file"]').name = `niveles[${index}][video]`;
            nivel.querySelector('.eliminar-btn').setAttribute('onclick', `eliminarNivel(${nivelNumero})`);
            nivel.id = `nivel-${nivelNumero}`;
        });
    }

    // Manejo del envío del formulario
    document.getElementById("nuevoCursoForm").addEventListener("submit", async function (event) {
        event.preventDefault(); // Prevenimos el comportamiento por defecto

        const formData = new FormData(this);

        try {
            const response = await fetch("00guardar_curso.php", {
                method: "POST",
                body: formData
            });

            const result = await response.text(); // Leer la respuesta como texto

            if (response.ok) {
                // Mostrar respuesta del servidor como alerta
                alert(result);
                // Redirigir al perfil del instructor si el mensaje indica éxito
                if (result.includes("exitoso") || result.includes("guardado")) {
                    window.location.href = 'perfil_instructor.php';
                }
            } else {
                alert("Error al guardar el curso: " + result);
            }
        } catch (error) {
            console.error("Error al guardar el curso:", error);
            alert("Error de conexión. Intenta nuevamente.");
        }
    });

    // Manejo del botón "Cancelar"
    document.getElementById('btnCancelar').addEventListener('click', function () {
        window.location.href = 'perfil_instructor.php';
    });
});
