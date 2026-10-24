<?php
include("00ConexionDB.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['curso_id'])) {
    $cursoId = intval($_POST['curso_id']); // Aseguramos que sea un entero

    try {
        // Llamar al procedimiento almacenado para la baja lógica
        $query = "CALL sp_bajacurso(?)";
        $stmt = $conex->prepare($query);

        if ($stmt) {
            $stmt->bind_param("i", $cursoId);
            $stmt->execute();
            echo "El curso fue marcado como baja lógica.";
            $stmt->close();
        } else {
            throw new Exception("Error al preparar la consulta: " . $conex->error);
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }

    $conex->close();
} else {
    echo "Solicitud inválida.";
}
?>
