<?php
include("00ConexionDB.php");
session_start();
?>

<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Cursos - BitAcademy</title>
    <link rel="stylesheet" href="cursos.css">
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
                    <?php if ($_SESSION['rol'] == 2): ?><li><a href="chat_alumno.php">Chat</a></li>
                    <?php elseif ($_SESSION['rol'] == 3): ?><li><a href="chat_instructor.php">Chat</a></li>
                    <?php endif; ?>
                    <li><a href="00Cerrarsesion.php">Cerrar Sesión</a></li>
                <?php else: ?>
                    <li><a href="login.php">Iniciar sesión</a></li>
                    <li><a href="registro.php">Registrarse</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <div class="container mt-5">
            <h1>Explorar Cursos</h1>
            
            <div class="busqueda">
                <input type="text" id="buscar" placeholder="¿Qué deseas aprender hoy?" onkeyup="buscarCurso()">
            </div>

            <!-- Contenedor Flex para alinear filtros -->
            <div class="filtros-container">
                <div class="categoria">
                    <button class="categoria-btn">Categoría</button>
                    <div class="dropdown-content">
                        <?php
                        $queryCategorias = "SELECT ID_CAT, NOMCAT FROM CATEGORIA";
                        $resultCategorias = $conex->query($queryCategorias);
                        if ($resultCategorias && $resultCategorias->num_rows > 0) {
                            while ($categoria = $resultCategorias->fetch_assoc()) {
                                echo '<button class="btn-etiqueta" onclick="filtrarPorEtiqueta(' . htmlspecialchars($categoria['ID_CAT']) . ')">' . htmlspecialchars($categoria['NOMCAT']) . '</button>';
                            }
                        } else {
                            echo '<p class="btn-etiqueta">No hay categorías</p>';
                        }
                        ?>
                    </div>
                </div>

                <div class="filtro-fechas">
                    <button class="fecha-btn">Filtrar por fecha</button>
                    <div class="dropdown-content-fecha">
                        <label for="fechaInicio">Desde:</label>
                        <input type="date" id="fechaInicio">
                        <label for="fechaFin">Hasta:</label>
                        <input type="date" id="fechaFin">
                        <button class="btn-filtrar-fecha" onclick="filtrarPorFecha()">Aplicar Filtro</button>
                    </div>
                </div>

                <div class="filtro-instructor">
                    <button class="instructor-btn">Filtrar por instructor</button>
                    <div class="dropdown-content-instructor">
                        <label for="instructor">Seleccionar instructor:</label>
                        <select id="instructor" class="form-control" onchange="filtrarPorInstructor()">
                            <option value="" disabled selected>Elige un instructor</option>
                            <?php
                            $queryInstructores = "SELECT ID_USUARIO, NOMBRE FROM USUARIO WHERE ROL = 3";
                            $resultInstructores = $conex->query($queryInstructores);
                            if ($resultInstructores && $resultInstructores->num_rows > 0) {
                                while ($instructor = $resultInstructores->fetch_assoc()) {
                                    echo '<option value="' . htmlspecialchars($instructor['ID_USUARIO']) . '">' . htmlspecialchars($instructor['NOMBRE']) . '</option>';
                                }
                            } else {
                                echo '<option disabled>No hay instructores</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <div id="cursos-container">
            <?php
            $queryCursos = "SELECT CURSO.ID_CURSO, CURSO.TITULO, CURSO.DESCRIPCURSO, CURSO.COSTO, CURSO.IMAGEN FROM CURSO WHERE CURSO.BAJA = 0";
            $resultCursos = $conex->query($queryCursos);

            if ($resultCursos && $resultCursos->num_rows > 0) {
                while ($curso = $resultCursos->fetch_assoc()) {
                    echo '<div class="course-card">';
                    if (!empty($curso['IMAGEN'])) {
                        echo '<img src="data:image/jpeg;base64,' . $curso['IMAGEN'] . '" alt="' . htmlspecialchars($curso['TITULO']) . '">';
                    } else {
                        echo '<img src="default-course.jpg" alt="Imagen predeterminada">';
                    }
                    echo '<div class="course-content">';
                    echo '<h3>' . htmlspecialchars($curso["TITULO"]) . '</h3>';
                    echo '<p>' . htmlspecialchars($curso["DESCRIPCURSO"]) . '</p>';
                    echo '<span class="price">$' . htmlspecialchars($curso["COSTO"]) . '</span>';
                    echo '<a href="curso.php?id=' . htmlspecialchars($curso["ID_CURSO"]) . '" class="btn-buy">Ver detalles</a>';
                    echo '</div>'; 
                    echo '</div>'; 
                }
            } else {
                echo '<p style="color: var(--text-muted); grid-column: 1 / -1; text-align: center;">No hay cursos disponibles en este momento.</p>';
            }
            ?>
            </div>
        </div>
    </main>

    <footer style="background-color: var(--bg-surface); padding: 20px; text-align: center; border-top: 1px solid var(--border-color); color: var(--text-muted); margin-top: 50px;">
        <p>&copy; 2026 BitAcademy - Todos los derechos reservados</p>
    </footer>

    <script src="cursos.js"></script>
</body>
</html>