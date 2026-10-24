<?php
include("00ConexionDB.php");
session_start();

if (!isset($_SESSION['idUsuario'])) {
    header("Location: login.php");
    exit();
}

$idUsuario = $_SESSION['idUsuario'];

$query = "
    SELECT uc.ID_CURSO, c.TITULO, uc.FECHA_INSCRIPCION 
    FROM usuario_curso uc
    JOIN curso c ON uc.ID_CURSO = c.ID_CURSO
    WHERE uc.ID_USUARIO = ?
";

$stmt = $conex->prepare($query);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Compras - BitAcademy</title>
    <link rel="stylesheet" href="Compras.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="container">
                <a href="inicio.php" class="logo">BitAcademy</a>
                <ul class="nav-links">
                    <li><a href="inicio.php">Inicio</a></li>
                    <li><a href="cursos.php">Cursos</a></li>
                    <?php if (isset($_SESSION['rol'])): ?>
                        <?php if ($_SESSION['rol'] == 1): ?>
                            <li><a href="Admin.php">Perfil</a></li>
                        <?php elseif ($_SESSION['rol'] == 2): ?>
                            <li><a href="perfil.php">Perfil</a></li>
                        <?php elseif ($_SESSION['rol'] == 3): ?>
                            <li><a href="perfil_instructor.php">Perfil</a></li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (!isset($_SESSION['rol'])): ?>
                        <li><a href="login.php">Iniciar sesión</a></li>
                        <li><a href="registro.php">Registrarse</a></li>
                    <?php else: ?>
                        <li><a href="00Cerrarsesion.php">Cerrar Sesión</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </header>

    <main class="profile-container">
        <div class="sidebar">
            <ul>
                <li><a href="Kardex.php">Kardex</a></li>
                <li><a href="Compras.php" class="active">Cursos Comprados</a></li>
            </ul>
        </div>

        <div class="Compras-content">
            <h1>Cursos Comprados</h1>
            <table id="compras-table">
                <thead>
                    <tr>
                        <th>Curso</th>
                        <th>Fecha de Compra</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td><a href='CursoCom.php?id=" . htmlspecialchars($row['ID_CURSO']) . "' class='curso-link'>" . htmlspecialchars($row['TITULO']) . "</a></td>";
                            echo "<td>" . (!empty($row['FECHA_INSCRIPCION']) ? htmlspecialchars($row['FECHA_INSCRIPCION']) : "Fecha no disponible") . "</td>";
                            echo "<td><a href='DetallesCurso.php?id=" . htmlspecialchars($row['ID_CURSO']) . "' class='btn-action'>Ver Curso</a></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3' style='text-align: center;'>No has comprado ningún curso.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2026 BitAcademy. Todos los derechos reservados.</p>
        </div>
    </footer>

    
</body>
</html>