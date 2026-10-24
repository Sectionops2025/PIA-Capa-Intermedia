<?php
include("00ConexionDB.php");
session_start();

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $curso_id = intval($_GET['id']); 

    $sqlCompra = $conex->prepare("SELECT ID_USUARIO FROM usuario_curso WHERE ID_CURSO = ? AND ID_USUARIO = ?");
    $sqlCompra->bind_param("ii", $curso_id, $_SESSION['idUsuario']);
    $sqlCompra->execute();
    $resultCompra = $sqlCompra->get_result();

    if ($resultCompra->num_rows == 0) {
        header("Location: inicio.php");
        exit;
    }
    $sqlCompra->close();

    $sqlCurso = $conex->prepare("SELECT TITULO, DESCRIPCURSO, IMAGEN FROM CURSO WHERE ID_CURSO = ?");
    $sqlCurso->bind_param("i", $curso_id);
    $sqlCurso->execute();
    $resultCurso = $sqlCurso->get_result();

    if ($resultCurso->num_rows > 0) {
        $curso = $resultCurso->fetch_assoc();
    } else {
        echo "No se encontraron detalles para este curso.";
        exit;
    }
    $sqlCurso->close();

    $sqlNiveles = $conex->prepare("SELECT ID_NIV, TITULO, CONTENIDO, VIDEO FROM NIVEL WHERE ID_CURSO = ? ORDER BY ID_NIV ASC");
    $sqlNiveles->bind_param("i", $curso_id);
    $sqlNiveles->execute();
    $resultNiveles = $sqlNiveles->get_result();

    $nivelesCompletados = [];
    $sqlCompletados = $conex->prepare("SELECT ID_NIV FROM niveles_completados WHERE ID_CURSO = ? AND ID_USUARIO = ?");
    $sqlCompletados->bind_param("ii", $curso_id, $_SESSION['idUsuario']);
    $sqlCompletados->execute();
    $resultCompletados = $sqlCompletados->get_result();

    while ($fila = $resultCompletados->fetch_assoc()) {
        $nivelesCompletados[] = $fila['ID_NIV'];
    }
    $sqlCompletados->close();

    $ultimoCompletado = !empty($nivelesCompletados) ? max($nivelesCompletados) : 0;
    $totalNiveles = $resultNiveles->num_rows;
    $nivelesCompletadosCount = count($nivelesCompletados);
    
    // Evita división por cero si el curso no tiene niveles
    $progreso = ($totalNiveles > 0) ? ($nivelesCompletadosCount / $totalNiveles) * 100 : 0;
} else {
    echo "ID de curso no proporcionado.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($curso['TITULO']); ?> - BitAcademy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CursoCom.css">
</head>
<body>
    <header>
        <nav class="navbar container">
            <a href="inicio.php" class="logo">BitAcademy</a>
            <ul class="nav-links">
                <li><a href="inicio.php">Inicio</a></li>
                <li><a href="cursos.php">Cursos</a></li>
                <?php if (isset($_SESSION['rol'])): ?>
                    <?php if ($_SESSION['rol'] == 1): ?><li><a href="Admin.php">Perfil</a></li>
                    <?php elseif ($_SESSION['rol'] == 2): ?><li><a href="perfil.php">Perfil</a></li>
                    <?php elseif ($_SESSION['rol'] == 3): ?><li><a href="perfil_instructor.php">Perfil</a></li>
                    <?php endif; ?>
                    <li><a href="00CerrarSesion.php">Cerrar sesión</a></li>
                <?php else: ?>
                    <li><a href="login.php">Iniciar sesión</a></li>
                    <li><a href="registro.php">Registrarse</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <div class="container mt-5">
        <div class="row">
            <main class="col-md-8">
                <section class="mb-5">
                    <h1 class="mb-3"><?php echo htmlspecialchars($curso['TITULO']); ?></h1>
                    <p class="lead mb-4"><?php echo htmlspecialchars($curso['DESCRIPCURSO']); ?></p>
                    <?php if (!empty($curso['IMAGEN'])): ?>
                        <img src="data:image/jpeg;base64,<?php echo $curso['IMAGEN']; ?>" alt="Portada" class="img-fluid rounded border border-secondary mb-4 w-100" style="max-height: 400px; object-fit: cover;">
                    <?php endif; ?>
                </section>

                <section class="mb-4">
                    <h2 class="mb-4">Módulos del curso</h2>
                    <div class="niveles-container">
                        <?php
                        $nivelIndex = 1;
                        if ($resultNiveles->num_rows > 0): 
                            while ($nivel = $resultNiveles->fetch_assoc()) {
                                $completado = in_array($nivel['ID_NIV'], $nivelesCompletados); 
                                $deshabilitado = $completado ? "disabled" : ""; 
                                $visible = ($nivelIndex == 1 || $completado || $nivel['ID_NIV'] == $ultimoCompletado + 1) ? "" : "style='display: none;'";
                        ?>
                                <div class="level" data-nivel="<?php echo $nivel['ID_NIV']; ?>" <?php echo $visible; ?>>
                                    <h4>Módulo <?php echo $nivelIndex; ?>: <?php echo htmlspecialchars($nivel['TITULO']); ?></h4>
                                    <video controls preload="metadata">
                                        <source src="<?php echo htmlspecialchars($nivel['VIDEO']); ?>" type="video/mp4">
                                        Tu navegador no soporta este video.
                                    </video>
                                    <button class="btn-completar mt-3" <?php echo $deshabilitado; ?> data-nivel="<?php echo $nivel['ID_NIV']; ?>" data-curso="<?php echo $curso_id; ?>">
                                        <?php echo $completado ? "Módulo Completado" : "Marcar como Completado"; ?>
                                    </button>
                                </div>
                        <?php
                                $nivelIndex++;
                            }
                        else:
                        ?>
                            <p class="text-muted">No hay contenido subido para este curso todavía.</p>
                        <?php endif; ?>
                    </div>
                </section>
            </main>

            <aside class="col-md-4">
                <section>
                    <h3 class="mb-3">Tu Progreso</h3>
                    <div class="progress-container">
                        <div id="progress-bar" class="progress-bar" style="width: <?php echo round($progreso); ?>%;">
                            <?php echo round($progreso); ?>%
                        </div>
                    </div>
                </section>
            </aside>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 BitAcademy - Todos los derechos reservados</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="CursoCom.js"></script>
</body>
</html>