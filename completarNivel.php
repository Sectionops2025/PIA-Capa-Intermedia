<?php
include("00ConexionDB.php");
session_start();

// Leer los datos enviados en la petición
$data = json_decode(file_get_contents("php://input"), true);

// Verificar si el usuario está autenticado
if (!isset($_SESSION['idUsuario'])) {
    echo json_encode(["success" => false, "error" => "Usuario no autenticado"]);
    exit;
}

// Verificar que los datos necesarios estén presentes
if (!isset($data['idNivel']) || empty($data['idNivel']) || !isset($data['idCurso']) || empty($data['idCurso'])) {
    echo json_encode(["success" => false, "error" => "Datos incompletos"]);
    exit;
}

// Variables
$idNivel = intval($data['idNivel']);
$idCurso = intval($data['idCurso']);
$idUsuario = intval($_SESSION['idUsuario']);
$fechaCompletado = date("Y-m-d H:i:s");

// Comprobar si el nivel ya está registrado
$sqlVerificar = $conex->prepare("SELECT * FROM niveles_completados WHERE ID_NIV = ? AND ID_CURSO = ? AND ID_USUARIO = ?");
$sqlVerificar->bind_param("iii", $idNivel, $idCurso, $idUsuario);
$sqlVerificar->execute();
$resultVerificar = $sqlVerificar->get_result();

if ($resultVerificar->num_rows > 0) {
    echo json_encode(["success" => true, "message" => "Nivel ya completado"]);
    $sqlVerificar->close();
    exit;
}

// Insertar el nivel completado en la base de datos
$sqlInsert = $conex->prepare("INSERT INTO niveles_completados (ID_NIV, ID_CURSO, ID_USUARIO, FECHA_COMPLETADO) VALUES (?, ?, ?, ?)");
$sqlInsert->bind_param("iiis", $idNivel, $idCurso, $idUsuario, $fechaCompletado);

if ($sqlInsert->execute()) {
    echo json_encode(["success" => true, "message" => "Nivel registrado con éxito"]);
} else {
    echo json_encode(["success" => false, "error" => "Error al registrar nivel"]);
}

$sqlInsert->close();
?>
