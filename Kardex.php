<?php
include("00ConexionDB.php");
session_start();

if (!isset($_SESSION['idUsuario'])) {
    die("Error: Usuario no autenticado.");
}

$filtroProgreso = isset($_GET['progreso']) ? $_GET['progreso'] : '';
$filtroFechaInicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
$filtroFechaFin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';
$filtroCategoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';

$queryCategorias = "SELECT ID_CAT, NOMCAT FROM categoria";
$resultCategorias = mysqli_query($conex, $queryCategorias);

$queryKardex = "
    SELECT 
        c.TITULO AS nombre_curso, 
        c.ID_CURSO,
        uc.FECHA_INSCRIPCION, 
        MAX(nc.FECHA_COMPLETADO) AS fecha_fin,
        MAX(nc.FECHA_COMPLETADO) AS ultima_actividad,
        COUNT(nc.ID_NIV) AS niveles_completados,
        COUNT(n.ID_NIV) AS total_niveles,
        ROUND((COUNT(nc.ID_NIV) / COUNT(n.ID_NIV)) * 100, 2) AS progreso
    FROM usuario_curso uc
    INNER JOIN curso c ON uc.ID_CURSO = c.ID_CURSO
    LEFT JOIN nivel n ON c.ID_CURSO = n.ID_CURSO
    LEFT JOIN niveles_completados nc ON n.ID_NIV = nc.ID_NIV AND nc.ID_USUARIO = uc.ID_USUARIO
    WHERE uc.ID_USUARIO = ?
";

if (!empty($filtroFechaInicio) && !empty($filtroFechaFin)) {
    $queryKardex .= " AND uc.FECHA_INSCRIPCION BETWEEN ? AND ?";
}
if (!empty($filtroCategoria)) {
    $queryKardex .= " AND c.ID_CAT = ?";
}
$queryKardex .= " GROUP BY c.TITULO, uc.FECHA_INSCRIPCION";

if ($filtroProgreso == 'incompleto') {
    $queryKardex .= " HAVING progreso < 100";
} elseif ($filtroProgreso == 'completo') {
    $queryKardex .= " HAVING progreso = 100";
}

$stmtKardex = $conex->prepare($queryKardex);

$parametros = [$_SESSION['idUsuario']];
$tipos = "i";

if (!empty($filtroFechaInicio) && !empty($filtroFechaFin)) {
    $parametros[] = $filtroFechaInicio;
    $parametros[] = $filtroFechaFin;
    $tipos .= "ss";
}
if (!empty($filtroCategoria)) {
    $parametros[] = $filtroCategoria;
    $tipos .= "i";
}

$stmtKardex->bind_param($tipos, ...$parametros);
$stmtKardex->execute();
$resultKardex = $stmtKardex->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kardex - BitAcademy</title>
    <link rel="stylesheet" href="Kardex.css">
</head>
<body>
    <header>
        <nav class="navbar container">
            <a href="inicio.php" class="logo">BitAcademy</a>
            <ul class="nav-links">
                <li><a href="inicio.php">Inicio</a></li>
                <li><a href="cursos.php">Cursos</a></li>
                <li><a href="perfil.php">Perfil</a></li>
                <li><a href="00Cerrarsesion.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h1>Reporte de Kardex</h1>

        <form method="GET" action="Kardex.php" class="filtros-kardex">
            <div class="filtro-estado">
                <label for="progreso">Progreso:</label>
                <select name="progreso" id="progreso">
                    <option value="" <?= $filtroProgreso == '' ? 'selected' : '' ?>>-- Todos --</option>
                    <option value="incompleto" <?= $filtroProgreso == 'incompleto' ? 'selected' : '' ?>>En curso</option>
                    <option value="completo" <?= $filtroProgreso == 'completo' ? 'selected' : '' ?>>Terminado</option>
                </select>
            </div>

            <div class="filtro-fechas">
                <label for="fecha_inicio">Fecha inicio:</label>
                <input type="date" name="fecha_inicio" id="fecha_inicio" value="<?= htmlspecialchars($filtroFechaInicio) ?>">
            </div>
            
            <div class="filtro-fechas">
                <label for="fecha_fin">Fecha fin:</label>
                <input type="date" name="fecha_fin" id="fecha_fin" value="<?= htmlspecialchars($filtroFechaFin) ?>">
            </div>

            <div class="filtro-categoria">
                <label for="categoria">Categoría:</label>
                <select name="categoria" id="categoria">
                    <option value="" <?= $filtroCategoria == '' ? 'selected' : '' ?>>-- Todas --</option>
                    <?php if ($resultCategorias && mysqli_num_rows($resultCategorias) > 0): ?>
                        <?php while ($categoria = mysqli_fetch_assoc($resultCategorias)): ?>
                            <option value="<?= $categoria['ID_CAT'] ?>" <?= $filtroCategoria == $categoria['ID_CAT'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($categoria['NOMCAT']) ?>
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>

            <button type="submit" class="btn-filtrar">Aplicar Filtros</button>
        </form>

        <div id="kardex-table">
            <h2>Progreso de Cursos</h2>
            <table>
                <thead>
                    <tr>
                        <th>Curso</th>
                        <th>Progreso</th>
                        <th>Última Actividad</th>
                        <th>Fecha de Inscripción</th>
                        <th>Fecha Fin</th>
                        <th>Certificado</th>
                    </tr>
                </thead>
                <tbody id="tabla-resultados">
                    <?php if ($resultKardex && $resultKardex->num_rows > 0): ?>
                        <?php while ($row = $resultKardex->fetch_assoc()): ?>
                            <tr>
                                <td style="font-weight: 600; color: var(--text-main);"><?= htmlspecialchars($row['nombre_curso']) ?></td>
                                <td><?= $row['progreso'] ?>%</td>
                                <td><?= $row['ultima_actividad'] ? $row['ultima_actividad'] : "Sin actividad" ?></td>
                                <td><?= htmlspecialchars($row['FECHA_INSCRIPCION']) ?></td>
                                <td><?= $row['fecha_fin'] ? $row['fecha_fin'] : "Sin fecha" ?></td>
                                <td>
                                <?php if ($row['progreso'] == 100): ?>
                                    <a href="certificado.php?id=<?= urlencode($row['ID_CURSO']) ?>" class="btn-success">Ver Certificado</a>
                                <?php else: ?>
                                    <span class="text-muted">En progreso</span>
                                <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">No se encontraron datos en el Kardex.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2026 BitAcademy. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="Kardex.js"></script>
</body>
</html>