let comentarioSeleccionado = null;

// Función para abrir el modal y almacenar el ID del comentario
function iniciarBajaLogica(commentId) {
    comentarioSeleccionado = commentId; // Guardamos el ID
    const modal = new bootstrap.Modal(document.getElementById('modalBajaLogica'));
    modal.show();
}

// Función para confirmar la baja lógica
document.getElementById('confirmarBajaLogica').addEventListener('click', () => {
    const razon = document.getElementById('razonEliminacion').value.trim();

    if (!razon) {
        alert("Por favor, escribe una razón para la eliminación.");
        return;
    }

    // Enviar al servidor para actualizar la base de datos
    fetch('eliminarComentario.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            id: comentarioSeleccionado,
            razon: razon,
        }),
    })
    
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log("Comentario eliminado correctamente en el servidor.");
                // Recargar la página
                location.reload();
            } else {
                alert("Error al eliminar el comentario. Inténtalo de nuevo.");
                console.error("Error reportado por el servidor:", data.error);
            }
        })
        .catch(error => {
            console.error("Error al eliminar el comentario:", error);
            alert("Error al conectar con el servidor. Revisa la consola para más detalles.");
        });
        
        
        
        
});


function toggleContent(id) {
    const content = document.getElementById(id);
    if (content.style.display === "block") {
        content.style.display = "none";
    } else {
        content.style.display = "block";
    }
}



function mostrarAviso(mensaje) {
        alert(mensaje);
}

