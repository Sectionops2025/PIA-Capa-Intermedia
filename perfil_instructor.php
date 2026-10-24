<?php
include("00ConexionDB.php");
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 3 || !isset($_SESSION['idUsuario'])) {
    header('Location: login.php');
    exit;
}

$idInstructor = $_SESSION['idUsuario'];

$queryInstructor = "SELECT NOMBRE, EMAIL, FOTO FROM usuario WHERE ID_USUARIO = ?";
$stmtInstructor = $conex->prepare($queryInstructor);
$stmtInstructor->bind_param("i", $idInstructor);
$stmtInstructor->execute();
$resultInstructor = $stmtInstructor->get_result();

if ($resultInstructor->num_rows > 0) {
    $instructor = $resultInstructor->fetch_assoc();
} else {
    die("No se encontraron datos del instructor.");
}
$stmtInstructor->close();

$queryCursos = "SELECT ID_CURSO, TITULO FROM curso WHERE ID_INSTRUCTOR = ? AND BAJA = 0";
$stmtCursos = $conex->prepare($queryCursos);
$stmtCursos->bind_param("i", $idInstructor);
$stmtCursos->execute();
$resultCursos = $stmtCursos->get_result();

$queryGeneral = "
    SELECT 
        c.TITULO AS nombre_curso,
        COUNT(uc.ID_USUARIO) AS alumnos_inscritos,
        COALESCE(SUM((SELECT COSTO FROM curso WHERE ID_CURSO = uc.ID_CURSO)), 0) AS ingresos_totales
    FROM curso c
    LEFT JOIN usuario_curso uc ON c.ID_CURSO = uc.ID_CURSO
    WHERE c.BAJA = 0 AND c.ID_INSTRUCTOR = ?
    GROUP BY c.TITULO;
";
$stmtGeneral = $conex->prepare($queryGeneral);
$stmtGeneral->bind_param("i", $idInstructor);
$stmtGeneral->execute();
$resultGeneral = $stmtGeneral->get_result();

$queryDetalle = "
    SELECT 
        c.TITULO AS nombre_curso,
        uc.FECHA_INSCRIPCION,
        uc.FORMAPAGO AS forma_pago,
        (SELECT COSTO FROM curso WHERE ID_CURSO = uc.ID_CURSO) AS ingreso
    FROM usuario_curso uc
    INNER JOIN curso c ON uc.ID_CURSO = c.ID_CURSO
    WHERE c.ID_INSTRUCTOR = ?
";
$stmtDetalle = $conex->prepare($queryDetalle);
$stmtDetalle->bind_param("i", $idInstructor);
$stmtDetalle->execute();
$resultDetalle = $stmtDetalle->get_result();

$queryTotalIngresos = "
    SELECT SUM((SELECT COSTO FROM curso WHERE ID_CURSO = uc.ID_CURSO)) AS total_ingresos
    FROM usuario_curso uc
    INNER JOIN curso c ON uc.ID_CURSO = c.ID_CURSO
    WHERE c.ID_INSTRUCTOR = ?
";
$stmtTotal = $conex->prepare($queryTotalIngresos);
$stmtTotal->bind_param("i", $idInstructor);
$stmtTotal->execute();
$resultTotal = $stmtTotal->get_result();
$totalIngresos = $resultTotal->fetch_assoc()['total_ingresos'] ?? 0;

$stmtGeneral->close();
$stmtDetalle->close();
$stmtTotal->close();
?>

<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil Instructor - BitAcademy</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="perfil_instructor.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <a href="inicio.php" class="logo">BitAcademy</a>
            <ul class="nav-links">
                <li><a href="inicio.php">Inicio</a></li>
                <li><a href="perfil_instructor.php" class="active">Perfil</a></li>
                <li><a href="cursos.php">Cursos</a></li>
                <li><a href="modificar.php">Editar Perfil</a></li>
                <?php if (isset($_SESSION['rol'])): ?>
                    <?php if ($_SESSION['rol'] == 2): ?>
                        <li><a href="chat_alumno.php">Chat</a></li>
                    <?php elseif ($_SESSION['rol'] == 3): ?>
                        <li><a href="chat_instructor.php">Chat</a></li>
                    <?php endif; ?>
                <?php endif; ?>
                <li><a href="00CerrarSesion.php">Cerrar sesión</a></li>
            </ul>
        </nav>
    </header>

    <main class="profile-container">
        <div class="sidebar">
            <h3>Tus Cursos Activos</h3>
            <ul style="padding:0;">
                <?php
                if ($resultCursos && $resultCursos->num_rows > 0) {
                    while ($curso = $resultCursos->fetch_assoc()) {
                        echo '<li><button class="btn-curso" onclick="mostrarDetallesCurso(' . htmlspecialchars($curso['ID_CURSO']) . ')">' . htmlspecialchars($curso['TITULO']) . '</button></li>';
                    }
                } else {
                    echo '<li style="color:var(--text-muted);">No tienes cursos registrados.</li>';
                }
                ?>
            </ul>
            <button class="btn-alta-curso" onclick="window.location.href='AltaCurso.php'">+ Nuevo Curso</button>
        </div>

        <div class="profile-content">
            <h1>Bienvenido, <?php echo htmlspecialchars($instructor['NOMBRE']); ?></h1>
            <p><?php echo htmlspecialchars($instructor['EMAIL']); ?></p>

            <?php if (!empty($instructor['FOTO'])): ?>
                <img src="data:image/jpeg;base64,<?php echo htmlspecialchars($instructor['FOTO']); ?>" alt="Foto de perfil" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover;">
            <?php else: ?>
                <img src="default-profile.png" alt="Foto predeterminada" style="width: 150px; height: 150px; border-radius: 50%;">
            <?php endif; ?>

            <p style="margin-top: 15px;">Gestiona el progreso de tus alumnos y los ingresos de tus cursos.</p>
            <button id="btnReportes">Ver Reporte General</button>

            <!-- Modal Bootstrap -->
            <div id="modalReportes" class="modal fade" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Reporte General de Cursos</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <h6 class="mb-3 text-light">Cursos Ofrecidos</h6>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Curso</th>
                                        <th>Alumnos Inscritos</th>
                                        <th>Ingresos Totales</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($resultGeneral)): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['nombre_curso']) ?></td>
                                            <td><?= $row['alumnos_inscritos'] ?></td>
                                            <td class="text-success">$<?= number_format($row['ingresos_totales'], 2) ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>

                            <h6 class="mt-4 mb-3 text-light">Ingresos Desglosados</h6>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Curso</th>
                                        <th>Fecha Inscripción</th>
                                        <th>Forma de Pago</th>
                                        <th>Ingreso</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($resultDetalle)): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['nombre_curso']) ?></td>
                                            <td><?= $row['FECHA_INSCRIPCION'] ?></td>
                                            <td><?= $row['forma_pago'] ?></td>
                                            <td class="text-success">$<?= number_format($row['ingreso'], 2) ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                            <h5 class="mt-4 text-end text-light">Total de Ingresos: <strong class="text-success">$<?= number_format($totalIngresos, 2) ?></strong></h5>
                        </div>
                    </div>
                </div>
            </div>

            <div id="detalles-curso" class="detalles-curso" style="display:none;"></div>
            <div id="detalles-alumno" class="detalles-alumno" style="display:none;"></div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="perfil_instructor.js"></script>
    <script>
        // Pequeño script visual para mostrar los contenedores dinámicos al hacer clic
        const cursoButtons = document.querySelectorAll('.btn-curso');
        cursoButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('detalles-curso').style.display = 'block';
                document.getElementById('detalles-alumno').style.display = 'block';
            });
        });
    </script>
</body>
</html>