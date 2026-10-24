<?php
include("00ConexionDB.php");

header("Content-Type: application/json"); // Configurar la salida en formato JSON

// Consulta para obtener todos los cursos y sus categorías
$sql = $conex->query("SELECT CURSO.ID_CURSO, CURSO.TITULO, CURSO.COSTO, CURSO.DESCRIPCURSO, 
                      CURSO.CALIFICACION, CURSO.IMAGEN, CURSO.NIVEL, CURSO.BAJA, 
                      CATEGORIA.NOMCAT AS CATEGORIA
                      FROM CURSO
                      INNER JOIN CATEGORIA ON CURSO.ID_CAT = CATEGORIA.ID_CAT
                      WHERE CURSO.BAJA = 0"); // Incluye solo los cursos activos

if ($sql) {
    $cursos = $sql->fetch_all(MYSQLI_ASSOC);
    echo json_encode(["data" => $cursos]);
} else {
    echo json_encode(["message" => "Error al obtener los cursos: " . $conex->error]);
}
?>
