<?php
include("00ConexionDB.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $usuarioCrea = $_SESSION['nom'] ; // Nombre del usuario de la sesión

    $stmt = $conex->prepare("CALL InsertarCategoria(?, ?, ?)");
    $stmt->bind_param("sss", $nombre, $descripcion, $usuarioCrea);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Categoría creada correctamente."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al crear la categoría."]);
    }

    $stmt->close();
}
?>
