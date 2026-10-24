<?php
include("00ConexionDB.php");
session_start();

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['idUsuario'])) {
    die("Error: Usuario no autenticado.");
}

// Validar los datos enviados por el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $comentario = trim($_POST['comentario']);
    $idCurso = intval($_POST['idCurso']);
    $idUsuario = intval($_POST['idUsuario']);
    $fechaActual = date("Y-m-d H:i:s");

    // Verificar que no estén vacíos
    if (!empty($comentario) && $idCurso > 0 && $idUsuario > 0) {
        // Insertar el comentario en la base de datos
        $query = "INSERT INTO comentario (ID_CURSO, ID_USUARIO, COMEN, FECHCRE) VALUES (?, ?, ?, ?)";
        $stmt = $conex->prepare($query);
        $stmt->bind_param("iiss", $idCurso, $idUsuario, $comentario, $fechaActual);

        if ($stmt->execute()) {
            echo "Comentario enviado correctamente.";
            header("Location: DetallesCurso.php?id=$idCurso"); // Redirigir de vuelta al curso
        } else {
            echo "Error al guardar el comentario: " . $conex->error;
        }

        $stmt->close();
    } else {
        echo "Por favor, completa todos los campos.";
    }
} else {
    echo "Método de solicitud no válido.";
}

$conex->close();
?>
