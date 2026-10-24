<?php
// Desactivar errores visibles en HTML para proteger el formato JSON
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
include("00ConexionDB.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $categoriaId = intval($_POST['categoriaId'] ?? 0);

    // Verifica si el ID de la categoría es válido
    if ($categoriaId <= 0) {
        echo json_encode(["success" => false, "message" => "ID de categoría inválido."]);
        exit;
    }

    // Consulta directa de actualización (baja lógica)
    $stmt = $conex->prepare("UPDATE categoria SET CATELIM = 1 WHERE ID_CAT = ?");
    
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Error en la preparación: " . $conex->error]);
        exit;
    }

    $stmt->bind_param("i", $categoriaId);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Categoría eliminada correctamente."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al ejecutar la baja lógica en la base de datos."]);
    }

    $stmt->close();
    $conex->close();
} else {
    echo json_encode(["success" => false, "message" => "Método no permitido."]);
}
?>