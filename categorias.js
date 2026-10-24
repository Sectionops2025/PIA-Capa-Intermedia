document.getElementById("crearCatBtn").addEventListener("click", function () {
    const nombre = document.getElementById("nombreCat").value.trim();
    const descripcion = document.getElementById("descCat").value.trim();

    if (!nombre || !descripcion) {
        alert("Por favor, completa todos los campos.");
        return;
    }

    fetch("00nueva_categoria.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `nombre=${encodeURIComponent(nombre)}&descripcion=${encodeURIComponent(descripcion)}`
    })
    .then(response => response.json())
    .then(data => {
        
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error("Error:", error);
        alert("Hubo un problema al crear la categoría.");
    });
});

document.querySelectorAll(".eliminar-btn").forEach(button => {
    button.addEventListener("click", function () {
        const categoriaId = this.dataset.id;

        if (confirm("¿Estás seguro de eliminar esta categoría?")) {
            fetch("00eliminar_categoria.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `categoriaId=${encodeURIComponent(categoriaId)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("Hubo un problema al eliminar la categoría.");
            });
        }
    });
});

document.querySelectorAll('.editar-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const categoriaId = btn.getAttribute('data-id');

        // Fetch para cargar los datos de la categoría
        fetch(`00obtener_categoria.php?id=${categoriaId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('editarCategoriaId').value = data.categoria.ID_CAT;
                    document.getElementById('editarNombreCat').value = data.categoria.NOMCAT;
                    document.getElementById('editarDescCat').value = data.categoria.DESCRIP;

                    // Mostrar el modal
                    const modal = new bootstrap.Modal(document.getElementById('modalEditarCat'));
                    modal.show();
                } else {
                    alert(data.message || "Error al cargar la categoría.");
                }
            })
            .catch(error => {
                console.error("Error al cargar la categoría:", error);
            });
    });
});

// Guardar cambios al editar la categoría
document.getElementById('guardarCambiosCatBtn').addEventListener('click', function () {
    const categoriaId = document.getElementById('editarCategoriaId').value;
    const nombre = document.getElementById('editarNombreCat').value.trim();
    const descripcion = document.getElementById('editarDescCat').value.trim();

    if (!nombre || !descripcion) {
        alert("Por favor, completa todos los campos.");
        return;
    }

    fetch("00editar_categoria.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded",
        },
        body: `categoriaId=${encodeURIComponent(categoriaId)}&nombre=${encodeURIComponent(nombre)}&descripcion=${encodeURIComponent(descripcion)}`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload(); // Recargar la página para actualizar la tabla
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error("Error al editar la categoría:", error);
            alert("Hubo un problema al editar la categoría.");
        });
});
