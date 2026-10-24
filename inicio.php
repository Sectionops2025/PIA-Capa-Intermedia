<?php
include("00ConexionDB.php");
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - BitAcademy</title>
    <link rel="stylesheet" href="inicio.css">
</head>
<body>
    <header>
        <nav class="navbar container">
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

                <?php if (isset($_SESSION['rol'])): ?>
                    <?php if ($_SESSION['rol'] == 2): ?>
                        <li><a href="chat_alumno.php">Chat</a></li>
                    <?php elseif ($_SESSION['rol'] == 3): ?>
                        <li><a href="chat_instructor.php">Chat</a></li>
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

    <main>
        <div class="container">
            <h1>Descubre Nuestros Cursos</h1>
            <p>Explora una variedad de cursos diseñados para llevar tus habilidades al siguiente nivel.</p>

            <div class="slide">
                <div class="slide-inner">
                    <input class="slide-open" type="radio" id="slide-1" name="slide" hidden checked>
                    <div class="slide-item">
                        <img src="4.jpg" alt="Imagen 1">
                    </div>
                    <input class="slide-open" type="radio" id="slide-2" name="slide" hidden>
                    <div class="slide-item">
                        <img src="5.jpg" alt="Imagen 2">
                    </div>
                    <input class="slide-open" type="radio" id="slide-3" name="slide" hidden>
                    <div class="slide-item">
                        <img src="6.jpg" alt="Imagen 3">
                    </div>
                    
                    <label for="slide-3" class="slide-control prev control-1">‹</label>
                    <label for="slide-2" class="slide-control next control-1">›</label>
                    <label for="slide-1" class="slide-control prev control-2">‹</label>
                    <label for="slide-3" class="slide-control next control-2">›</label>
                    <label for="slide-2" class="slide-control prev control-3">‹</label>
                    <label for="slide-1" class="slide-control next control-3">›</label>
                    
                    <ol class="slide-indicador">
                        <li><label for="slide-1" class="slide-circulo">•</label></li>
                        <li><label for="slide-2" class="slide-circulo">•</label></li>
                        <li><label for="slide-3" class="slide-circulo">•</label></li>
                    </ol>
                </div>
            </div>

            <?php
            $secciones = [
                "Más Vendidos" => "
                    SELECT c.ID_CURSO, c.TITULO, c.DESCRIPCURSO, c.COSTO, c.IMAGEN, COUNT(uc.ID_USUARIO) AS COMPRAS
                    FROM CURSO c
                    LEFT JOIN usuario_curso uc ON c.ID_CURSO = uc.ID_CURSO
                    WHERE c.BAJA = 0
                    GROUP BY c.ID_CURSO
                    ORDER BY COMPRAS DESC
                    LIMIT 3
                ",
                "Mejor Calificados" => "
                    SELECT ID_CURSO, TITULO, DESCRIPCURSO, COSTO, IMAGEN, CALIFICACION 
                    FROM CURSO 
                    WHERE BAJA = 0 
                    ORDER BY CALIFICACION DESC 
                    LIMIT 3
                ",
                "Más Recientes" => "
                    SELECT ID_CURSO, TITULO, DESCRIPCURSO, COSTO, IMAGEN 
                    FROM CURSO 
                    WHERE BAJA = 0 
                    ORDER BY FECHA_CREACION DESC 
                    LIMIT 3
                "
            ];

            foreach ($secciones as $titulo => $query) {
                $resultado = $conex->query($query);
                echo '<div class="courses-section">';
                echo '<h2>' . htmlspecialchars($titulo) . '</h2>';
                echo '<div class="courses-row">';

                if ($resultado && $resultado->num_rows > 0) {
                    while ($curso = $resultado->fetch_assoc()) {
                        $imagenSrc = !empty($curso["IMAGEN"]) 
                            ? 'data:image/jpeg;base64,' . htmlspecialchars($curso["IMAGEN"]) 
                            : 'default-course.jpg';

                        echo '<div class="course-card">';
                        echo '    <img src="' . $imagenSrc . '" alt="Curso ' . htmlspecialchars($curso["TITULO"]) . '">';
                        echo '    <div class="course-info">';
                        echo '        <h3>' . htmlspecialchars($curso["TITULO"]) . '</h3>';
                        echo '        <p>' . htmlspecialchars($curso["DESCRIPCURSO"]) . '</p>';
                        echo '        <span class="price">$' . htmlspecialchars($curso["COSTO"]) . '</span>';
                        echo '        <a href="curso.php?id=' . htmlspecialchars($curso["ID_CURSO"]) . '" class="btn-comprar">Ver detalles</a>';
                        echo '    </div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p style="color: var(--text-muted);">No hay cursos disponibles en esta categoría.</p>';
                }

                echo '</div>'; 
                echo '</div>'; 
            }
            ?>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2026 BitAcademy. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>