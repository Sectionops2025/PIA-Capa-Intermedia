document.addEventListener("DOMContentLoaded", () => {
    const niveles = document.querySelectorAll(".niveles-container .level");
    const progressBar = document.getElementById("progress-bar");
    let progreso = parseInt(progressBar.style.width) || 0; // Leer progreso inicial del backend

    // Función para actualizar la barra de progreso
    function actualizarProgreso(nivelesCompletados) {
        const totalNiveles = niveles.length;
        const porcentaje = (nivelesCompletados / totalNiveles) * 100;
        progressBar.style.width = porcentaje + "%";
        progressBar.textContent = Math.round(porcentaje) + "%";
    }

    // Función para manejar el evento de completar nivel
    function completarNivel(boton, nivelId) {
        const idCurso = parseInt(boton.getAttribute("data-curso"));

        fetch("completarNivel.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ idNivel: nivelId, idCurso: idCurso }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    boton.disabled = true;
                    boton.textContent = "Completado";

                    // Mostrar el siguiente nivel
                    const siguienteNivel = document.querySelector(`.level[data-nivel="${nivelId + 1}"]`);
                    if (siguienteNivel) {
                        siguienteNivel.style.display = "block";
                    }else {
                        // Si no hay más niveles, mostrar alerta de curso completado
                        alert("¡Felicidades, Curso completado!");
                    }

                    // Actualizar barra de progreso
                    actualizarProgreso(data.nivelesCompletados);
                    const scrollPosition = window.scrollY; 
                    localStorage.setItem("scrollPosition", scrollPosition);
                    window.location.reload();
                } else {
                    console.error("Error al completar el nivel:", data.error);
                }
            })
            .catch((error) => console.error("Error al conectar con el servidor:", error));
    }

    // Asignar eventos a los botones de completar nivel
    niveles.forEach((nivel) => {
        const boton = nivel.querySelector(".btn-completar");
        if (!boton.disabled) {
            boton.addEventListener("click", () => {
                const nivelId = parseInt(nivel.getAttribute("data-nivel"));
                completarNivel(boton, nivelId);
            });
        }
    });
});
