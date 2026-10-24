<?php
include("00ConexionDB.php");
session_start(); // Iniciar la sesión

header("Content-Type: application/json"); 


$idUsuario = $_SESSION['idUsuario'] ?? $_GET['idUsuario'] ?? null;

if ($idUsuario) {
    
    $sql = $conex->query("SELECT CURSO.ID_CURSO, CURSO.TITULO, CURSO.DESCRIPCURSO, CURSO.COSTO, CURSO.CALIFICACION, 
                          CURSO.IMAGEN, CURSO.NIVEL, CURSO.BAJA, CATEGORIA.NOMCAT AS CATEGORIA
                          FROM USUARIO_CURSO
                          INNER JOIN CURSO ON USUARIO_CURSO.ID_CURSO = CURSO.ID_CURSO
                          INNER JOIN CATEGORIA ON CURSO.ID_CAT = CATEGORIA.ID_CAT
                          WHERE USUARIO_CURSO.ID_USUARIO = $idUsuario AND CURSO.BAJA = 0");

    if ($sql) {
        $cursos = $sql->fetch_all(MYSQLI_ASSOC);
        echo json_encode(["data" => $cursos]);
    } else {
        echo json_encode(["message" => "Error al obtener los cursos: " . $conex->error]);
    }
} else {
    echo json_encode(["message" => "ID de usuario no proporcionado o usuario no autenticado"]);
}
?>
