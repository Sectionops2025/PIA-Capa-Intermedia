<?php
include("00ConexionDB.php");
session_start();

if (!isset($_SESSION['idUsuario'])) {
    die("Error: Usuario no autenticado.");
}

$queryUsuarios = "
    SELECT u.ID_USUARIO, u.NOMBRE, c.TITULO, uc.FECHA_INSCRIPCION 
    FROM usuario u
    JOIN usuario_curso uc ON u.ID_USUARIO = uc.ID_USUARIO
    JOIN curso c ON uc.ID_CURSO = c.ID_CURSO
    WHERE u.INHABILITADO = 0
";
$resultUsuarios = $conex->query($queryUsuarios);

$queryCursos = "
    SELECT c.TITULO, u.NOMBRE AS INSTRUCTOR, c.COSTO, c.DESCRIPCURSO, c.CALIFICACION 
    FROM curso c
    JOIN usuario u ON c.ID_INSTRUCTOR = u.ID_USUARIO
    WHERE c.BAJA = 0
";
$resultCursos = $conex->query($queryCursos);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes de Usuarios y Cursos</title>
    <link rel="stylesheet" href="reportes.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <a href="inicio.php" class="logo">BitAcademy</a>
            <ul class="nav-links">
                <li><a href="inicio.php">Inicio</a></li>
                <li><a href="cursos.php">Cursos</a></li>
                <li><a href="perfil.php">Perfil</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h1>Reportes de Usuarios y Cursos</h1>
        <div class="report-buttons">
            <button id="usuarios-btn">Ver Usuarios</button>
            <button id="cursos-btn">Ver Cursos Activos</button>
        </div>

        <div id="usuarios-table" class="hidden">
            <h2>Usuarios Activos</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Curso</th>
                        <th>Fecha de Inscripción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($resultUsuarios && $resultUsuarios->num_rows > 0): ?>
                        <?php while ($row = $resultUsuarios->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['ID_USUARIO']) ?></td>
                                <td style="color: var(--text-main); font-weight: 600;"><?= htmlspecialchars($row['NOMBRE']) ?></td>
                                <td><?= htmlspecialchars($row['TITULO']) ?></td>
                                <td><?= htmlspecialchars($row['FECHA_INSCRIPCION']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No hay usuarios activos relacionados con cursos.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div id="cursos-table" class="hidden">
            <h2>Cursos Activos</h2>
            <table>
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Instructor</th>
                        <th>Costo</th>
                        <th>Descripción</th>
                        <th>Calificación</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($resultCursos && $resultCursos->num_rows > 0): ?>
                        <?php while ($row = $resultCursos->fetch_assoc()): ?>
                            <tr>
                                <td style="color: var(--text-main); font-weight: 600;"><?= htmlspecialchars($row['TITULO']) ?></td>
                                <td><?= htmlspecialchars($row['INSTRUCTOR']) ?></td>
                                <td style="color: #10b981;">$<?= htmlspecialchars($row['COSTO']) ?></td>
                                <td><?= htmlspecialchars($row['DESCRIPCURSO']) ?></td>
                                <td><?= htmlspecialchars($row['CALIFICACION']) ?> / 5</td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No hay cursos activos disponibles.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script src="reportes.js"></script>
</body>
</html>