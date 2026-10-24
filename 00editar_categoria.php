<?php
include("00ConexionDB.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $categoriaId = intval($_POST['categoriaId']);
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);

    // Validar los campos obligatorios
    if ($categoriaId <= 0 || empty($nombre) || empty($descripcion)) {
        echo json_encode(["success" => false, "message" => "Todos los campos son obligatorios."]);
        exit;
    }

    // Llamar al procedimiento almacenado para editar la categoría
    $stmt = $conex->prepare("CALL EditarCategoria(?, ?, ?)");
    $stmt->bind_param("iss", $categoriaId, $nombre, $descripcion);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Categoría actualizada correctamente."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al actualizar la categoría."]);
    }

    $stmt->close();
}
?>
