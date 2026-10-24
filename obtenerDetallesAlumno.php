<?php
include("00ConexionDB.php");

if (isset($_GET['curso_id']) && isset($_GET['usuario_id'])) {
    $cursoId = $_GET['curso_id'];
    $usuarioId = $_GET['usuario_id'];

    // Consulta para obtener los detalles del alumno
    $queryAlumno = "
        SELECT 
            usuario.NOMBRE AS nombre_alumno,
            usuario_curso.FECHA_INSCRIPCION AS fecha_inscripcion,
            IF(EXISTS(SELECT 1 FROM niveles_completados 
                      WHERE niveles_completados.ID_CURSO = ? 
                        AND niveles_completados.ID_USUARIO = ?), 'Sí', 'No') AS curso_terminado,
            curso.COSTO AS precio_curso,
            usuario_curso.FROMAPAGO AS forma_pago
        FROM usuario_curso
        INNER JOIN usuario ON usuario.ID_USUARIO = usuario_curso.ID_USUARIO
        INNER JOIN curso ON curso.ID_CURSO = usuario_curso.ID_CURSO
        WHERE usuario_curso.ID_CURSO = ? AND usuario_curso.ID_USUARIO = ?";
    $stmtAlumno = $conex->prepare($queryAlumno);
    $stmtAlumno->bind_param("iiii", $cursoId, $usuarioId, $cursoId, $usuarioId);
    $stmtAlumno->execute();
    $resultAlumno = $stmtAlumno->get_result();

    if ($resultAlumno->num_rows > 0) {
        $alumno = $resultAlumno->fetch_assoc();
        echo json_encode($alumno);
    } else {
        echo json_encode(["error" => "No se encontraron detalles del alumno."]);
    }
} else {
    echo json_encode(["error" => "Datos incompletos."]);
}
?>
