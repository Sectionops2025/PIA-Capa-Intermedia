<?php
include("00ConexionDB.php");
session_start();

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['idUsuario'])) {
    echo json_encode(["error" => "Usuario no autenticado"]);
    exit;
}

$idUsuario = $_SESSION['idUsuario'];
$input = json_decode(file_get_contents("php://input"), true);
$destinatario = $input['destinatario'] ?? null;

if (!$destinatario) {
    echo json_encode(["error" => "Destinatario no especificado"]);
    exit;
}

try {
    // Consulta para obtener el historial de mensajes entre los usuarios
    $query = "
        SELECT 
            m.TEXT, 
            m.FECHORMEN, 
            mu.REMITENTE, 
            mu.DESTINATARIO,
            u_remitente.NOMBRE AS NOMBRE_REMITENTE,
            u_destinatario.NOMBRE AS NOMBRE_DESTINATARIO
        FROM mensaje_usuario mu
        INNER JOIN mensaje m ON mu.ID_M = m.ID_M
        INNER JOIN usuario u_remitente ON mu.REMITENTE = u_remitente.ID_USUARIO
        INNER JOIN usuario u_destinatario ON mu.DESTINATARIO = u_destinatario.ID_USUARIO
        WHERE 
            (mu.REMITENTE = ? AND mu.DESTINATARIO = ?) 
            OR 
            (mu.REMITENTE = ? AND mu.DESTINATARIO = ?)
        ORDER BY m.FECHORMEN ASC
    ";

    $stmt = $conex->prepare($query);
    $stmt->bind_param("iiii", $idUsuario, $destinatario, $destinatario, $idUsuario);
    $stmt->execute();
    $result = $stmt->get_result();

    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }

    echo json_encode($messages);
} catch (Exception $e) {
    echo json_encode(["error" => "Error al cargar el historial: " . $e->getMessage()]);
}
?>
