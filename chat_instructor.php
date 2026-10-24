<?php
include("00ConexionDB.php");
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 3) {
    header("Location: inicio.php");
    exit;
}

$idUsuario = $_SESSION['idUsuario'];

// Consulta segura con Prepared Statements
$query = "
    SELECT DISTINCT u.ID_USUARIO, u.NOMBRE
    FROM usuario u
    JOIN usuario_curso uc ON u.ID_USUARIO = uc.ID_USUARIO
    JOIN curso c ON uc.ID_CURSO = c.ID_CURSO
    WHERE u.ROL = 2 AND c.ID_INSTRUCTOR = ?
";

if ($stmt = $conex->prepare($query)) {
    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();
    $result = $stmt->get_result();

    $alumnos = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $alumnos[] = $row;
        }
    }
    $stmt->close();
} else {
    die("Error en la consulta SQL: " . $conex->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Instructor - BitAcademy</title>
    <link rel="stylesheet" href="chat.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <a href="inicio.php" class="logo">BitAcademy</a>
            <ul class="nav-links">
                <li><a href="inicio.php">Inicio</a></li>
                <li><a href="cursos.php">Cursos</a></li>
                <li><a href="00Cerrarsesion.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>
    <div class="chat-container">
        <div class="users-list">
            <h3>Alumnos</h3>
            <ul id="user-list">
                <?php foreach ($alumnos as $alumno): ?>
                    <li>
                        <button class="user-btn" data-id="<?= htmlspecialchars($alumno['ID_USUARIO']) ?>">
                            <?= htmlspecialchars($alumno['NOMBRE']) ?>
                        </button>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="chat-box">
            <div class="chat-header">
                <h3 id="chat-title">Selecciona un alumno</h3>
            </div>
            <div class="chat-messages" id="chat-messages"></div>
            <div class="chat-input">
                <textarea id="message-input" placeholder="Escribe tu mensaje..."></textarea>
                <button id="send-message">Enviar</button>
            </div>
        </div>
    </div>
    <script>
        const userId = <?= json_encode($idUsuario) ?>;
    </script>
    <script src="chat_instructor.js"></script>
</body>
</html>