<?php
include("00ConexionDB.php");
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        isset($_POST['titulo'], $_POST['descripcion'], $_POST['costo'], $_SESSION['idUsuario'])
    ) {
        $titulo = trim($_POST['titulo']);
        $descripcion = trim($_POST['descripcion']);
        $costo = trim($_POST['costo']);
        $instructorId = $_SESSION['idUsuario'];
        $categoria = trim($_POST['categoria']);
        $calificacion = 0; 
        $baja = 0;

        if (isset($_POST['niveles']) && is_numeric($_POST['niveles'])) {
            $nivelCount = (int)$_POST['niveles']; // Convertir el valor a entero
        } else {
            $nivelCount = 0; // Valor predeterminado si no está definido o no es válido
        }

        // Procesar imagen
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $Foto = base64_encode(file_get_contents($_FILES['imagen']['tmp_name']));
        } else {
            echo "No se seleccionó imagen";
            exit;
        }

        // Llamar al procedimiento almacenado para insertar curso
        $insertar = "CALL sp_InsertarCurso(?, ?, ?, ?, ?, ?, ?, ?, ?, @p_id_curso)";
        $sql = $conex->prepare($insertar);

        if (!$sql) {
            echo "Error en la preparación del SQL";
            exit;
        }

        $sql->bind_param("isdsdsiii", $categoria, $titulo, $costo, $descripcion, $calificacion, $Foto, $nivelCount, $baja, $instructorId);

        if (!$sql->execute()) {
            echo "Error en la ejecución del SQL";
            exit;
        }

        // Recuperar el ID del curso insertado
        $result = $conex->query("SELECT @p_id_curso AS id");
        if ($result) {
            $row = $result->fetch_assoc();
            $curso_id = $row['id'];
        } else {
            echo "No se pudo recuperar el ID del curso";
            exit;
        }

        // Insertar niveles, si hay
        if (isset($_POST['niveles']) && is_array($_POST['niveles'])) {
            foreach ($_POST['niveles'] as $index => $nivel) {
                $nivelTitulo = trim($nivel['titulo']);
                $nivelContenido = trim($nivel['texto']);
                $nivelCosto = trim($nivel['costo']);

                $rutaVideo = null;
                if (
                    isset($_FILES['niveles']['name'][$index]['video']) &&
                    !empty($_FILES['niveles']['name'][$index]['video'])
                ) {
                    $directorio = "uploads/";
                    if (!is_dir($directorio)) {
                        mkdir($directorio, 0777, true); // Crear carpeta si no existe
                    }

                    $nombreArchivo = basename($_FILES['niveles']['name'][$index]['video']);
                    $rutaVideo = $directorio . $nombreArchivo;

                    // Validaciones para el archivo de video
                    $uploadOk = 1;
                    $fileType = strtolower(pathinfo($rutaVideo, PATHINFO_EXTENSION));

                    // Verificar el tipo de archivo
                    $tiposPermitidos = ['mp4', 'avi', 'mkv', 'mov'];
                    if (!in_array($fileType, $tiposPermitidos)) {
                        echo "El archivo $nombreArchivo tiene un formato no permitido. Solo se permiten MP4, AVI, MKV y MOV.";
                        $uploadOk = 0;
                    }

                    // Intentar mover el archivo si pasa todas las validaciones
                    if ($uploadOk == 1) {
                        if (!move_uploaded_file($_FILES['niveles']['tmp_name'][$index]['video'], $rutaVideo)) {
                            echo "Error al subir el archivo $nombreArchivo al servidor.";
                            $rutaVideo = null; // No guardar ruta si no se subió correctamente
                        } else {
                            echo "El archivo $nombreArchivo se subió correctamente.";
                        }
                    } else {
                        $rutaVideo = null; // No guardar ruta si hubo algún problema
                    }
                }

                $sqlNivel = $conex->prepare("CALL sp_InsertarNivel(?, ?, ?, ?, ?)");
                if (!$sqlNivel) {
                    echo "<h2 class='Error'>Error en la preparación del SQL de nivel: " . $conex->error . "</h2>";
                    exit;
                }

                $sqlNivel->bind_param("issds", $curso_id, $nivelTitulo, $nivelContenido, $nivelCosto, $rutaVideo);

                if (!$sqlNivel->execute()) {
                    echo "Error al ejecutar el SQL de nivel";
                    exit;
                }

                $sqlNivel->close();
            }
        }

        echo "¡Curso y niveles guardados exitosamente!";
    } else {
        echo "Datos incompletos o usuario no autenticado";
    }
}
?>
