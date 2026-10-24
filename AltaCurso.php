<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Curso - OmIso</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="altacurso.css">
</head>
<body>
    <div class="container mt-4 mb-5">
        <h1>Crear Nuevo Curso</h1>

        <!-- Formulario que envía los datos a 00guardar_curso.php -->
        <form id="nuevoCursoForm" action="00guardar_curso.php" method="POST" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="titulo" class="form-label">Título del Curso</label>
                <input type="text" class="form-control" id="titulo" name="titulo" placeholder="Ej. Introducción al Modelado 3D" required>
            </div>

            <div class="mb-4">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="4" placeholder="Describe lo que aprenderán los alumnos..." required></textarea>
            </div>

            <!-- Usamos un Grid de Bootstrap para organizar mejor estos tres campos -->
            <div class="row mb-4">
                <div class="col-md-4 mb-3 mb-md-0">
                    <label for="costo" class="form-label">Costo (MXN)</label>
                    <input type="number" class="form-control" id="costo" name="costo" placeholder="0.00" required>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <label for="niveles" class="form-label">Número de Niveles</label>
                    <input type="number" class="form-control" id="niveles" name="niveles" value="0" readonly>
                </div>
                <div class="col-md-4">
                    <label for="categoria" class="form-label">Categoría</label>
                    <select class="form-control" id="categoria" name="categoria" required>
                        <option value="" disabled selected>Seleccione una categoría...</option>
                        <?php
                        include("00ConexionDB.php");

                        $query = "SELECT ID_CAT, NOMCAT FROM categoria";
                        $resultado = mysqli_query($conex, $query);

                        if ($resultado && mysqli_num_rows($resultado) > 0) {
                            while ($fila = mysqli_fetch_assoc($resultado)) {
                                echo '<option value="' . htmlspecialchars($fila['ID_CAT']) . '">' . htmlspecialchars($fila['NOMCAT']) . '</option>';
                            }
                        } else {
                            echo '<option value="">No hay categorías disponibles</option>';
                        }
                        mysqli_close($conex);
                        ?>
                    </select>
                </div>
            </div>

            <div class="mb-5">
                <label for="imagen" class="form-label">Portada del Curso</label>
                <input class="form-control" type="file" id="imagen" name="imagen" accept="image/*" required>
            </div>

            <!-- Niveles dinámicos -->
            <div id="nivelesContainer"></div>

            <div class="mb-5 d-flex justify-content-center">
                <button type="button" class="btn btn-primary" onclick="agregarNiveles()">
                    + Agregar Módulo / Nivel
                </button>
            </div>

            <!-- Botones de Guardar y Cancelar -->
            <div class="d-flex justify-content-between pt-3 border-top" style="border-color: var(--border-color) !important;">
                <button type="button" class="btn btn-secondary" id="btnCancelar">Cancelar</button>
                <button type="submit" class="btn btn-success" id="btnGuardar">Guardar Curso</button>
            </div>
        </form>
    </div>

    <script src="altacurso.js"></script>
</body>
</html>