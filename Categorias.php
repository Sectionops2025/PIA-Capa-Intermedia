<?php
include("00ConexionDB.php");
?>

<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Categorías - BitAcademy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="categorias.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="container">
                <a href="inicio.php" class="logo">BitAcademy</a>
                <ul class="nav-links">
                    <li><a href="inicio.php">Inicio</a></li>
                    <li><a href="cursos.php">Cursos</a></li>
                    <li><a href="Admin.php">Perfil</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main class="profile-container">
        <div class="sidebar">
            <ul>
                <li><a href="UsuariosDes.php">Usuarios Deshabilitados</a></li>
                <li><a href="Categorias.php" class="active">Categorías</a></li>
                <li><a href="ReporteAdmin.php">Reportes de Usuario</a></li>
            </ul>
        </div>

        <div class="profile-content">
            <h1>Gestión de Categorías</h1>
            <button id="nuevaCatBtn" class="btn-nueva-cat" data-bs-toggle="modal" data-bs-target="#modalNuevaCat">Nueva Categoría</button>

            <!-- Tabla de categorías -->
            <table id="categorias-table" class="table table-striped">
                <thead>
                    <tr>
                        <th>Categoría</th>
                        <th>Descripción</th>
                        <th>Editar</th>
                        <th>Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT ID_CAT, NOMCAT, DESCRIP FROM categoria where CATELIM = 0";
                    $result = $conex->query($query);

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['NOMCAT']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['DESCRIP']) . "</td>";
                            echo "<td><button class='editar-btn btn btn-warning' data-id='" . $row['ID_CAT'] . "'>Editar</button></td>";
                            echo "<td><button class='eliminar-btn btn btn-danger' data-id='" . $row['ID_CAT'] . "'>Eliminar</button></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No hay categorías registradas.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Modal para crear nueva categoría -->
    <div class="modal fade" id="modalNuevaCat" tabindex="-1" aria-labelledby="modalNuevaCatLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNuevaCatLabel">Nueva Categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="nuevaCategoriaForm">
                        <div class="mb-3">
                            <label for="nombreCat" class="form-label">Nombre de la Categoría</label>
                            <input type="text" class="form-control" id="nombreCat" placeholder="Nombre de la categoría">
                        </div>
                        <div class="mb-3">
                            <label for="descCat" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descCat" rows="3" placeholder="Descripción de la categoría"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="crearCatBtn">Crear</button>
                </div>
            </div>
        </div>
    </div>

        <!-- Modal para editar categoría -->
    <div class="modal fade" id="modalEditarCat" tabindex="-1" aria-labelledby="modalEditarCatLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarCatLabel">Editar Categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editarCategoriaForm">
                        <input type="hidden" id="editarCategoriaId"> <!-- Campo oculto para el ID -->
                        <div class="mb-3">
                            <label for="editarNombreCat" class="form-label">Nombre de la Categoría</label>
                            <input type="text" class="form-control" id="editarNombreCat" placeholder="Nombre de la categoría">
                        </div>
                        <div class="mb-3">
                            <label for="editarDescCat" class="form-label">Descripción</label>
                            <textarea class="form-control" id="editarDescCat" rows="3" placeholder="Descripción de la categoría"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="guardarCambiosCatBtn">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="categorias.js"></script>
</body>
</html>