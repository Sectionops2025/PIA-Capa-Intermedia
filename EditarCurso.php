<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Curso - OmIso</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="altacurso.css">
</head>
<body>
    <div class="container mt-4 mb-5">
        <h1>Editar Curso</h1>

        <form id="editarCursoForm" action="00EditarCurso.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" id="curso_id" name="curso_id">

            <div class="mb-4">
                <label for="titulo" class="form-label">Título del Curso</label>
                <input type="text" class="form-control" id="titulo" name="titulo" placeholder="Título del curso" required>
            </div>

            <div class="mb-4">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="4" placeholder="Descripción del curso" required></textarea>
            </div>

            <div class="row mb-4">
                <div class="col-md-6 mb-3 mb-md-0">
                    <label for="costo" class="form-label">Costo (MXN)</label>
                    <input type="number" class="form-control" id="costo" name="costo" placeholder="0.00" required>
                </div>
                <div class="col-md-6">
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
                <label for="imagen" class="form-label">Cargar Nueva Imagen (Opcional)</label>
                <input class="form-control" type="file" id="imagen" name="imagen" accept="image/*">
            </div>

            <div id="nivelesContainer"></div>

            <div class="mb-5 d-flex justify-content-center">
                <button type="button" class="btn btn-primary" id="agregarNivelBtn">
                    + Agregar Módulo / Nivel
                </button>
            </div>

            <div class="d-flex justify-content-between pt-3 border-top" style="border-color: var(--border-color) !important;">
                <button type="button" class="btn btn-secondary" id="btnCancelar">Cancelar</button>
                <button type="submit" class="btn btn-success" id="btnGuardar">Guardar Cambios</button>
            </div>
        </form>
    </div>

    <script src="editarcurso.js"></script>
</body>
</html>