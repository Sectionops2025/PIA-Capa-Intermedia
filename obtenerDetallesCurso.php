<?php
include("00ConexionDB.php");

if (isset($_GET['curso_id'])) {
    $cursoId = $_GET['curso_id'];

    // Obtener los detalles del curso
    $queryCurso = "SELECT TITULO, ID_CURSO FROM CURSO WHERE ID_CURSO = ?";
    $stmtCurso = $conex->prepare($queryCurso);
    $stmtCurso->bind_param("i", $cursoId);
    $stmtCurso->execute();
    $resultCurso = $stmtCurso->get_result();
    $curso = $resultCurso->fetch_assoc();

    // Obtener los usuarios con rol 2 inscritos en el curso
    $queryAlumnos = "
        SELECT USUARIO.NOMBRE AS NOMBRE, USUARIO_CURSO.FECHA_INSCRIPCION, USUARIO_CURSO.ID_USUARIO
        FROM USUARIO_CURSO
        INNER JOIN USUARIO ON USUARIO_CURSO.ID_USUARIO = USUARIO.ID_USUARIO
        WHERE USUARIO_CURSO.ID_CURSO = ? AND USUARIO.ROL = 2";
    $stmtAlumnos = $conex->prepare($queryAlumnos);
    $stmtAlumnos->bind_param("i", $cursoId);
    $stmtAlumnos->execute();
    $resultAlumnos = $stmtAlumnos->get_result();

    $alumnos = [];
    while ($alumno = $resultAlumnos->fetch_assoc()) {
        $alumnos[] = $alumno;
    }

    echo json_encode([
        "curso" => $curso,
        "alumnos" => $alumnos
    ]);
} else {
    echo json_encode(["error" => "ID del curso no proporcionado"]);
}
?>
