<?php
include("00ConexionDB.php");
session_start();

header("Content-Type: application/json");

$response = [];

if (!isset($_SESSION['idUsuario'])) {
    $response['error'] = "Usuario no autenticado.";
    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input['destinatario']) || !isset($input['mensaje'])) {
        $response['error'] = "Faltan datos en la solicitud.";
        echo json_encode($response);
        exit;
    }

    $remitente = $_SESSION['idUsuario'];
    $destinatario = $input['destinatario'];
    $mensaje = $input['mensaje'];

    $stmt = $conex->prepare("INSERT INTO mensaje (TEXT, FECHORMEN) VALUES (?, NOW())");
    $stmt->bind_param("s", $mensaje);
    $stmt->execute();
    $messageId = $stmt->insert_id;

    $stmt = $conex->prepare("INSERT INTO mensaje_usuario (ID_M, REMITENTE, DESTINATARIO) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $messageId, $remitente, $destinatario);
    $stmt->execute();

    $response['success'] = true;
    echo json_encode($response);
}
?>
