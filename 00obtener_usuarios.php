<?php
include("00ConexionDB.php");

header("Content-Type: application/json");

$query = "SELECT NOMBRE FROM USUARIO WHERE BAJA = 0"; // Ajusta el nombre del campo y la condición si es necesario
$result = $conex->query($query);

$usuarios = [];
if ($result && $result->num_rows > 0) {
    while ($usuario = $result->fetch_assoc()) {
        $usuarios[] = $usuario['NOMBRE'];
    }
}

echo json_encode($usuarios);
