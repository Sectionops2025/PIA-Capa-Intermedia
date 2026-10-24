<?php
include("00ConexionDB.php");

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['id'])) {
    $categoriaId = intval($_GET['id']);

    $stmt = $conex->prepare("SELECT ID_CAT, NOMCAT, DESCRIP FROM categoria WHERE ID_CAT = ?");
    $stmt->bind_param("i", $categoriaId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $categoria = $result->fetch_assoc();
        echo json_encode(["success" => true, "categoria" => $categoria]);
    } else {
        echo json_encode(["success" => false, "message" => "Categoría no encontrada."]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Solicitud inválida."]);
}
?>
