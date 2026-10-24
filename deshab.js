function habilitarUsuario(button) {
    const userId = button.getAttribute("data-id");
    if (!userId) {
        alert("ID de usuario no encontrado.");
        return;
    }

    fetch("00habilitar_usuario.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ idUsuario: userId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Usuario habilitado correctamente.");
            button.closest("tr").remove(); // Elimina la fila del usuario habilitado
        } else {
            alert("Error al habilitar usuario: " + data.error);
        }
    })
    .catch(error => console.error("Error:", error));
}
