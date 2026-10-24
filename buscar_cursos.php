<?php
include("00ConexionDB.php");

// Capturar los parámetros de los filtros
$categoria = isset($_GET['categoria']) ? intval($_GET['categoria']) : null;
$instructor = isset($_GET['instructor']) ? intval($_GET['instructor']) : null;
$fechaInicio = isset($_GET['fechaInicio']) ? $_GET['fechaInicio'] : null;
$fechaFin = isset($_GET['fechaFin']) ? $_GET['fechaFin'] : null;

// Construir la consulta base
$query = "SELECT ID_CURSO, TITULO, DESCRIPCURSO, COSTO, IMAGEN FROM CURSO WHERE 1=1";

$params = [];
$types = "";

// Agregar condiciones según los filtros de forma segura
if ($categoria) {
    $query .= " AND ID_CAT = ?";
    $params[] = $categoria;
    $types .= "i"; // 'i' para integer
}

if ($instructor) {
    $query .= " AND ID_INSTRUCTOR = ?";
    $params[] = $instructor;
    $types .= "i";
}

if ($fechaInicio && $fechaFin) {
    $query .= " AND FECHA_CREACION BETWEEN ? AND ?";
    $params[] = $fechaInicio;
    $params[] = $fechaFin;
    $types .= "ss"; // 's' para string (fechas)
}

// Preparar y ejecutar la consulta
$cursos = [];
if ($stmt = $conex->prepare($query)) {
    // Si hay parámetros, los vinculamos dinámicamente
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $cursos[] = $row;
        }
    }
    $stmt->close();
}

// Devolver los datos como JSON
header("Content-Type: application/json");
echo json_encode($cursos);
?>