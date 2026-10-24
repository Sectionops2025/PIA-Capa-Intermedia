<?php
include("00ConexionDB.php");
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['curso_id'], $_POST['titulo'], $_POST['descripcion'], $_POST['costo'], $_POST['categoria'])) {
        $cursoId = intval($_POST['curso_id']);
        $titulo = trim($_POST['titulo']);
        $descripcion = trim($_POST['descripcion']);
        $costo = floatval($_POST['costo']);
        $categoria = intval($_POST['categoria']);
       
        // Validar datos obligatorios
        if (empty($titulo) || empty($descripcion) || $costo <= 0 || $categoria <= 0) {
            echo "Todos los campos del curso son obligatorios.";
            exit;
        }

        // Procesar imagen (si se subió una nueva)
        $Foto = null;
        $actualizarImagen = false;
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $Foto = base64_encode(file_get_contents($_FILES['imagen']['tmp_name']));
            $actualizarImagen = true;
        }

        // Actualizar información del curso
        if ($actualizarImagen) {
            $queryUpdateCurso = "UPDATE curso SET TITULO = ?, DESCRIPCURSO = ?, COSTO = ?, ID_CAT = ?, IMAGEN = ? WHERE ID_CURSO = ?";
            $stmtCurso = $conex->prepare($queryUpdateCurso);
            $stmtCurso->bind_param("ssdisi", $titulo, $descripcion, $costo, $categoria, $Foto, $cursoId);
        } else {
            $queryUpdateCurso = "UPDATE curso SET TITULO = ?, DESCRIPCURSO = ?, COSTO = ?, ID_CAT = ? WHERE ID_CURSO = ?";
            $stmtCurso = $conex->prepare($queryUpdateCurso);
            $stmtCurso->bind_param("ssdis", $titulo, $descripcion, $costo, $categoria, $cursoId);
        }

        if (!$stmtCurso->execute()) {
            echo "Error al actualizar el curso";
            exit;
        }
        $stmtCurso->close();

        // Manejo de niveles nuevos
        if (isset($_POST['niveles']) && is_array($_POST['niveles'])) {
            foreach ($_POST['niveles'] as $nivelData) {
                $nivelTitulo = trim($nivelData['titulo']);
                $nivelContenido = trim($nivelData['texto']);
                $nivelCosto = floatval($nivelData['costo']);

                // Insertar nivel nuevo
                $queryInsertNivel = "INSERT INTO nivel (ID_CURSO, TITULO, CONTENIDO, COSTO, VIDEO) VALUES (?, ?, ?, ?, ?)";
                $stmtInsertNivel = $conex->prepare($queryInsertNivel);

                $rutaVideo = null;
                if (
                    isset($_FILES['niveles']['name']['video']) &&
                    $_FILES['niveles']['error']['video'] === UPLOAD_ERR_OK
                ) {
                    $directorio = "uploads/";
                    $nombreArchivo = basename($_FILES['niveles']['name']['video']);
                    $rutaVideo = $directorio . $nombreArchivo;

                    // Mover archivo al servidor
                    if (!move_uploaded_file($_FILES['niveles']['tmp_name']['video'], $rutaVideo)) {
                        echo "Error al subir el video: $nombreArchivo";
                        $rutaVideo = null;
                    }
                }

                $stmtInsertNivel->bind_param("issds", $cursoId, $nivelTitulo, $nivelContenido, $nivelCosto, $rutaVideo);
                $stmtInsertNivel->execute();
                $stmtInsertNivel->close();
            }
        }

        echo "¡Curso actualizado y niveles nuevos agregados exitosamente!";
    } else {
        echo "Datos incompletos. Por favor, verifica el formulario.";
    }
}
?>