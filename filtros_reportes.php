<?php
include("00ConexionDB.php");

$data = json_decode(file_get_contents("php://input"), true);

$categoria = $data['categoria'] ?? '';
$fechaInicio = $data['fechaInicio'] ?? '';
$fechaFin = $data['fechaFin'] ?? '';
$soloActivos = $data['soloActivos'] ?? 1;

$whereClauses = [];

if ($categoria) {
    $whereClauses[] = "c.ID_CAT = " . intval($categoria);
}

if ($fechaInicio) {
    $whereClauses[] = "c.FECHA_CREACION >= '" . mysqli_real_escape_string($conex, $fechaInicio) . "'";
}

if ($fechaFin) {
    $whereClauses[] = "c.FECHA_CREACION <= '" . mysqli_real_escape_string($conex, $fechaFin) . "'";
}

if ($soloActivos) {
    $whereClauses[] = "c.BAJA = 0";
}

$whereSQL = count($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";

// Consultas
$queryCursos = "
    SELECT c.TITULO, COUNT(uc.ID_USUARIO) AS alumnos_inscritos, SUM(uc.CALIFICACION) AS ingresos_totales
    FROM curso c
    LEFT JOIN usuario_curso uc ON c.ID_CURSO = uc.ID_CURSO
    $whereSQL
    GROUP BY c.TITULO
";

$queryIngresos = "
    SELECT c.TITULO, uc.FECHA_INSCRIPCION, uc.FROMAPAGO, c.COSTO
    FROM usuario_curso uc
    INNER JOIN curso c ON uc.ID_CURSO = c.ID_CURSO
    $whereSQL
";

$resultCursos = mysqli_query($conex, $queryCursos);
$resultIngresos = mysqli_query($conex, $queryIngresos);

// Formatear resultados
$cursosHTML = "";
while ($row = mysqli_fetch_assoc($resultCursos)) {
    $cursosHTML .= "<tr>
        <td>" . htmlspecialchars($row['TITULO']) . "</td>
        <td>" . $row['alumnos_inscritos'] . "</td>
        <td>$" . number_format($row['ingresos_totales'], 2) . "</td>
    </tr>";
}

$ingresosHTML = "";
$totalIngresos = 0;
while ($row = mysqli_fetch_assoc($resultIngresos)) {
    $ingresosHTML .= "<tr>
        <td>" . htmlspecialchars($row['TITULO']) . "</td>
        <td>" . $row['FECHA_INSCRIPCION'] . "</td>
        <td>" . $row['FROMAPAGO'] . "</td>
        <td>$" . number_format($row['COSTO'], 2) . "</td>
    </tr>";
    $totalIngresos += $row['COSTO'];
}

echo json_encode([
    "cursosHTML" => $cursosHTML,
    "ingresosHTML" => $ingresosHTML,
    "totalIngresos" => $totalIngresos,
]);
?>
