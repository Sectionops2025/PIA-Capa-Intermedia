<?php
header('Content-Type: application/json');

// Habilitar el reporte de errores (solo en desarrollo)
error_reporting(E_ALL);
ini_set('display_errors', 1);

$response = ['success' => false, 'message' => ''];

// Validar si se recibió el contenido esperado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['id']) || !isset($input['razon'])) {
        $response['message'] = 'Datos incompletos.';
        echo json_encode($response);
        exit;
    }

    $idComentario = $input['id'];
    $razon = $input['razon'];

    // Conexión a la base de datos
    include('00ConexionDB.php');

    // Actualizar el comentario para dar de baja lógica
    $query = "UPDATE comentario SET ELIM = 1, CAUSELIM = ?, FECHELIM = NOW() WHERE ID_COM = ?";
    $stmt = $conex->prepare($query);

    if ($stmt) {
        $stmt->bind_param("si", $razon, $idComentario);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Comentario eliminado correctamente.';
        } else {
            $response['message'] = 'Error al ejecutar la consulta.';
        }

        $stmt->close();
    } else {
        $response['message'] = 'Error al preparar la consulta.';
    }
} else {
    $response['message'] = 'Método no permitido.';
}

echo json_encode($response);
?>
