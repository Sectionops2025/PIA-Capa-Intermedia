<?php
include("00ConexionDB.php");
session_start();

header("Content-Type: application/json");

// Verificar que el usuario haya iniciado sesión como administrador
if (!isset($_SESSION['idUsuario']) || $_SESSION['rol'] != 1) {
    echo json_encode(["error" => "Usuario no autorizado."]);
    exit;
}

// Verificar datos recibidos
$data = json_decode(file_get_contents("php://input"), true);
if (empty($data['idUsuario'])) {
    echo json_encode(["error" => "Datos incompletos."]);
    exit;
}

$idUsuario = $data['idUsuario'];

// Actualizar el estado del usuario
$query = "UPDATE usuario SET INHABILITADO = 0 WHERE ID_USUARIO = ?";
$stmt = $conex->prepare($query);
$stmt->bind_param("i", $idUsuario);
if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => "Error al habilitar usuario."]);
}
$stmt->close();
?>
