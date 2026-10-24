<?php
include("00ConexionDB.php");

if (isset($_GET['curso_id'])) {
    $cursoId = intval($_GET['curso_id']);

    // Consulta para obtener datos del curso
    $queryCurso = "SELECT * FROM curso WHERE ID_CURSO = ?";
    $stmtCurso = $conex->prepare($queryCurso);
    $stmtCurso->bind_param("i", $cursoId);

    if ($stmtCurso->execute()) {
        $resultadoCurso = $stmtCurso->get_result();
        if ($resultadoCurso->num_rows > 0) {
            $curso = $resultadoCurso->fetch_assoc();

            // Consulta para obtener los niveles del curso
            $queryNiveles = "SELECT * FROM nivel WHERE ID_CURSO = ?";
            $stmtNiveles = $conex->prepare($queryNiveles);
            $stmtNiveles->bind_param("i", $cursoId);

            if ($stmtNiveles->execute()) {
                $resultadoNiveles = $stmtNiveles->get_result();
                $niveles = [];

                while ($nivel = $resultadoNiveles->fetch_assoc()) {
                    $niveles[] = $nivel;
                }

                // Respuesta en formato JSON
                echo json_encode([
                    "curso" => $curso,
                    "niveles" => $niveles
                ]);
            } else {
                echo json_encode(["error" => "Error al obtener los niveles del curso."]);
            }

            $stmtNiveles->close();
        } else {
            echo json_encode(["error" => "Curso no encontrado."]);
        }
    } else {
        echo json_encode(["error" => "Error al obtener el curso."]);
    }

    $stmtCurso->close();
} else {
    echo json_encode(["error" => "ID del curso no proporcionado."]);
}
?>