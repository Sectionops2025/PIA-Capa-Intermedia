<?php
session_start();
require '00ConexionDB.php'; 

if (isset($_GET['id']) && is_numeric($_GET['id']) && isset($_SESSION['idUsuario'])) {
    $idCurso = intval($_GET['id']); 
    $idUsuario = intval($_SESSION['idUsuario']); 

    $query = "SELECT 1 FROM usuario_curso WHERE ID_USUARIO = ? AND ID_CURSO = ?";
    $stmt = $conex->prepare($query);
    $stmt->bind_param("ii", $idUsuario, $idCurso);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header("Location: CursoCom.php?id=$idCurso");
        exit;
    } else {

        $queryCurso = "SELECT TITULO, DESCRIPCURSO, COSTO, IMAGEN FROM curso WHERE ID_CURSO = ?";
        $stmt = $conex->prepare($queryCurso);
        $stmt->bind_param("i", $idCurso);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $curso = $result->fetch_assoc();
            $tituloCurso = htmlspecialchars($curso['TITULO']); 
            $descripcionCurso = htmlspecialchars($curso['DESCRIPCURSO']); 
            $precioCurso = htmlspecialchars($curso['COSTO']); 
            $imagenCurso = $curso['IMAGEN']; 
        } else {
            echo "Curso no encontrado.";
            exit;
        }

        $queryNiveles = "SELECT ID_NIV, TITULO, CONTENIDO, VIDEO FROM nivel WHERE ID_CURSO = ?";
        $stmt = $conex->prepare($queryNiveles);
        $stmt->bind_param("i", $idCurso); 
        $stmt->execute();
        $resultNiveles = $stmt->get_result();

        $niveles = [];
        if ($resultNiveles && $resultNiveles->num_rows > 0) {
            while ($nivel = $resultNiveles->fetch_assoc()) {
                $niveles[] = $nivel;
            }
        }

        $queryCategoria = "SELECT categoria.NOMCAT FROM curso INNER JOIN categoria ON curso.ID_CAT = categoria.ID_CAT WHERE curso.ID_CURSO = ?";
        $stmt = $conex->prepare($queryCategoria);
        $stmt->bind_param("i", $idCurso);
        $stmt->execute();
        $resultCategoria = $stmt->get_result();

        $categorias = [];
        if ($resultCategoria && $resultCategoria->num_rows > 0) {
            while ($categoria = $resultCategoria->fetch_assoc()) {
                $categorias[] = $categoria['NOMCAT'];
            }
        }

        $queryComentarios = "
            SELECT c.ID_COM, c.ID_USUARIO, c.CALIF AS CALIFICACION_COMENTARIO, c.COMEN, c.FECHCRE, c.ELIM,
                   u.NOMBRE, uc.CALIFICACION AS CALIFICACION_USUARIO
            FROM comentario c
            INNER JOIN usuario u ON c.ID_USUARIO = u.ID_USUARIO
            LEFT JOIN usuario_curso uc ON c.ID_USUARIO = uc.ID_USUARIO AND c.ID_CURSO = uc.ID_CURSO
            WHERE c.ID_CURSO = ?
        ";
        $stmt = $conex->prepare($queryComentarios);
        $stmt->bind_param("i", $idCurso);
        $stmt->execute();
        $resultComentarios = $stmt->get_result();

        $comentarios = [];
        if ($resultComentarios && $resultComentarios->num_rows > 0) {
            while ($comentario = $resultComentarios->fetch_assoc()) {
                $comentarios[] = $comentario;
            }
        }
    }
} else {
    echo "ID de curso no válido o usuario no autenticado.";
    exit;
}

if ($precioCurso == 0) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['idUsuario'])) {
        $idUsuario = intval($_SESSION['idUsuario']);
        $fechaHoy = date("Y-m-d");
        $formaPago = "GRATIS";

        $queryCheck = "SELECT 1 FROM usuario_curso WHERE ID_USUARIO = ? AND ID_CURSO = ?";
        $stmtCheck = $conex->prepare($queryCheck);
        $stmtCheck->bind_param("ii", $idUsuario, $idCurso);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();

        if ($resultCheck->num_rows === 0) {
            $queryInsert = "INSERT INTO usuario_curso (ID_USUARIO, ID_CURSO, FECHA_INSCRIPCION, FROMAPAGO) VALUES (?, ?, ?, ?)";
            $stmtInsert = $conex->prepare($queryInsert);
            $stmtInsert->bind_param("iiss", $idUsuario, $idCurso, $fechaHoy, $formaPago);

            if ($stmtInsert->execute()) {
                $message = "¡Te has inscrito exitosamente al curso!";
            } else {
                $message = "Error al inscribirte al curso. Inténtalo más tarde.";
            }
        } else {
            $message = "Ya estás inscrito en este curso.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $tituloCurso; ?> - BitAcademy</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="Curso.css">
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
                <?php endif; ?>
                <?php if (!isset($_SESSION['rol'])): ?>
                    <li><a href="login.php">Iniciar sesión</a></li>
                    <li><a href="registro.php">Registrarse</a></li>
                <?php else: ?>
                    <li><a href="00Cerrarsesion.php">Cerrar Sesión</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    
    <div class="container mt-5">
        <div class="row">
            <main class="col-md-8">
                <section class="mb-5">
                    <h2><?php echo $tituloCurso; ?></h2>
                    <p class="lead"><?php echo $descripcionCurso; ?></p>
                </section>

                <section class="mb-5">
                    <h3 class="mb-4">Contenido del curso</h3>
                    <div class="accordion" id="courseAccordion">
                        <?php if (!empty($niveles)): ?>
                            <?php foreach ($niveles as $index => $nivel): ?>
                                <div class="accordion-item">
                                    <h4 class="accordion-header" id="level<?php echo $index; ?>Heading">
                                        <button class="accordion-button <?php echo $precioCurso > 0 ? 'disabled' : 'collapsed'; ?>" type="button" 
                                            <?php if ($precioCurso == 0): ?> data-bs-toggle="collapse" data-bs-target="#level<?php echo $index; ?>Content" aria-expanded="false" aria-controls="level<?php echo $index; ?>Content" <?php endif; ?>>
                                            <?php echo htmlspecialchars($nivel['TITULO']); ?>
                                            <?php if ($precioCurso > 0): ?><span class="text-danger ms-2">(Requiere inscripción)</span><?php endif; ?>
                                        </button>
                                    </h4>
                                    <?php if ($precioCurso == 0): ?>
                                        <div id="level<?php echo $index; ?>Content" class="accordion-collapse collapse" aria-labelledby="level<?php echo $index; ?>Heading" data-bs-parent="#courseAccordion">
                                            <div class="accordion-body">
                                                <?php if (!empty($nivel['VIDEO'])): ?>
                                                    <video controls class="w-100 mb-3 rounded border border-secondary">
                                                        <source src="<?php echo htmlspecialchars($nivel['VIDEO']); ?>" type="video/mp4">
                                                        Tu navegador no soporta video.
                                                    </video>
                                                <?php endif; ?>
                                                <p><?php echo htmlspecialchars($nivel['CONTENIDO']); ?></p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">Aún no hay niveles disponibles.</p>
                        <?php endif; ?>
                    </div>
                </section>

                <section class="mb-5">
                    <h3 class="mb-4">Reseñas de la comunidad</h3>
                    <div id="contenedor-comentarios">
                        <?php if (!empty($comentarios)): ?>
                            <?php foreach ($comentarios as $comentario): ?>
                                <div class="comment d-flex justify-content-between align-items-center" id="comment<?php echo htmlspecialchars($comentario['ID_COM']); ?>">
                                    <?php if ($comentario['ELIM'] == 1): ?>
                                        <p class="text-muted fst-italic">Este comentario fue eliminado.</p>
                                    <?php else: ?>
                                        <div>
                                            <strong><?php echo htmlspecialchars($comentario['NOMBRE']); ?></strong>
                                            <p><?php echo htmlspecialchars($comentario['COMEN']); ?></p>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="rating"><i class="bi bi-star-fill me-1"></i><?php echo !empty($comentario['CALIFICACION_USUARIO']) ? htmlspecialchars($comentario['CALIFICACION_USUARIO']) : htmlspecialchars($comentario['CALIFICACION_COMENTARIO']); ?>/5</span>
                                            <?php if ($_SESSION['rol'] == 1): ?> 
                                                <button class="btn btn-outline-danger btn-sm ms-3" onclick="iniciarBajaLogica(<?php echo htmlspecialchars($comentario['ID_COM']); ?>)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">Este curso aún no tiene reseñas.</p>
                        <?php endif; ?>
                    </div>
                </section>    
            </main>

            <aside class="col-md-4">
                <section class="mb-4">
                    <?php if (!empty($imagenCurso)): ?>
                        <img src="data:image/jpeg;base64,<?php echo $imagenCurso; ?>" alt="Portada del curso" class="w-100 mb-4">
                    <?php else: ?>
                        <img src="default-course.jpg" alt="Imagen por defecto" class="w-100 mb-4">
                    <?php endif; ?>
                    
                    <h3 class="mb-4">Total: <span class="text-primary">$<?php echo number_format($precioCurso, 2); ?></span></h3>
                    
                    <?php if ($precioCurso == 0): ?>
                        <form method="POST" action="">
                            <button type="submit" name="inscripcion_gratis" class="btn btn-success btn-lg w-100">
                                Inscribirse Gratis
                            </button>
                        </form>
                        <?php if (isset($message)): ?>
                            <p class="mt-3 text-success text-center"><?php echo htmlspecialchars($message); ?></p>
                        <?php endif; ?>
                    <?php else: ?>
                        <button class="btn btn-primary btn-lg w-100" onclick="window.location.href='checkout.php?id=<?php echo $idCurso; ?>';">
                            Adquirir Curso
                        </button>
                    <?php endif; ?>
                </section>

                <section>
                    <h3 class="mb-3">Categorías</h3>
                    <ul class="list-group">
                        <?php if (!empty($categorias)): ?>
                            <?php foreach ($categorias as $categoria): ?>
                                <li class="list-group-item bg-transparent border-secondary text-light"><?php echo htmlspecialchars($categoria); ?></li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="list-group-item bg-transparent border-secondary text-muted">Sin clasificar.</li>
                        <?php endif; ?>
                    </ul>
                </section>
            </aside>
        </div>
    </div>

    <!-- Modal Baja Lógica -->
    <div id="modalBajaLogica" class="modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title">Eliminar Comentario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Por qué deseas eliminar este comentario?</p>
                    <textarea id="razonEliminacion" class="form-control bg-dark text-light border-secondary" rows="4" placeholder="Escribe la razón..."></textarea>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmarBajaLogica">Confirmar Eliminación</button>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 BitAcademy - Todos los derechos reservados</p>
    </footer>

    <script src="Curso.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>