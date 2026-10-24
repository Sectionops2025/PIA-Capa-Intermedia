<?php
include("00ConexionDB.php");
session_start();

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['idUsuario'])) {
    header("Location: login.php"); // Redirigir al login si no está autenticado
    exit();
}

$mensaje = ""; // Variable para almacenar el mensaje

// Procesar el formulario si se envía
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $calificacion = intval($_POST['calificacion']);
    $idCurso = intval($_POST['idCurso']);
    $idUsuario = intval($_SESSION['idUsuario']); // ID del usuario de la sesión

    // Validar la calificación
    if ($calificacion >= 1 && $calificacion <= 5 && $idCurso > 0 && $idUsuario > 0) {
        // Insertar o actualizar la calificación en la tabla usuario_curso
        $queryInsert = "INSERT INTO usuario_curso (ID_USUARIO, ID_CURSO, CALIFICACION) 
                        VALUES (?, ?, ?)
                        ON DUPLICATE KEY UPDATE CALIFICACION = ?";
        $stmtInsert = $conex->prepare($queryInsert);
        $stmtInsert->bind_param("iiii", $idUsuario, $idCurso, $calificacion, $calificacion);

        if ($stmtInsert->execute()) {
            // Llamar al procedimiento almacenado para calcular el promedio
            $queryPromedio = "CALL CALCULAR_PROMEDIO_CURSO(?)";
            $stmtPromedio = $conex->prepare($queryPromedio);
            $stmtPromedio->bind_param("i", $idCurso);

            if ($stmtPromedio->execute()) {
                $mensaje = "Calificación enviada correctamente y promedio actualizado.";
            } else {
                $mensaje = "Error al calcular el promedio.";
            }

            $stmtPromedio->close();
        } else {
            $mensaje = "Error al guardar la calificación.";
        }

        $stmtInsert->close();
    } else {
        $mensaje = "Por favor, selecciona una calificación válida.";
    }
}

$conex->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Curso</title>
    <link rel="stylesheet" href="DetallesCurso.css">
</head>
<body>
    <header>
        <div class="navbar">
            <a href="inicio.php" class="logo">OmIso</a>
            <nav>
                <a href="inicio.php">Inicio</a>
                <a href="cursos.php">Cursos</a>
                <a href="perfil.php">Perfil</a>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <h1 class="course-title">Detalles del Curso</h1>
        <!-- Mostrar mensaje -->
        <?php if (!empty($mensaje)): ?>
            <div class="message">
                <p><?= htmlspecialchars($mensaje) ?></p>
            </div>
        <?php endif; ?>

        <!-- Formulario de calificación -->
        <div class="ratings-section">
            <h2>Califica este curso</h2>
            <form method="POST" action="DetallesCurso.php">
                <select name="calificacion" required>
                    <option value="1">1 - Muy malo</option>
                    <option value="2">2 - Malo</option>
                    <option value="3">3 - Regular</option>
                    <option value="4">4 - Bueno</option>
                    <option value="5">5 - Excelente</option>
                </select>
                <input type="hidden" name="idCurso" value="<?= htmlspecialchars($_GET['id']) ?>">
                <button type="submit">Enviar Calificación</button>
            </form>
        </div>
    </main>
</body>
</html>
