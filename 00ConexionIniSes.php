<?php
include("00ConexionDB.php");
session_start();

header("Content-Type: application/json"); // Definir el tipo de contenido como JSON

$response = []; // Crear un arreglo para almacenar la respuesta

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['email']) && !empty($_POST['password'])) {
        $mail = trim($_POST['email']);
        $pssw = trim($_POST['password']);

        // Llamar al procedimiento almacenado para verificar las credenciales
        $sql = $conex->query("CALL IniciarSesion('$mail', '$pssw')");

        // Verificar si la consulta SQL fue exitosa
        if ($sql && $sql->num_rows > 0 && $validacion = $sql->fetch_object()) {
           
            if (isset($validacion->ROL)) {
                // Guardar los datos en la sesión, incluyendo el ID del usuario
                $_SESSION['idUsuario'] = $validacion->ID_USUARIO; // Agrega el ID del usuario a la sesión
                $_SESSION['rol'] = $validacion->ROL;
                $_SESSION['contra'] = $validacion->CONTRASEÑA;
                $_SESSION['pfp'] = $validacion->FOTO;
                $_SESSION['nom'] = $validacion->NOMBRE;
                $_SESSION['naci'] = $validacion->FNACI;
                $_SESSION['Correo'] = $validacion->EMAIL;
                $_SESSION['gen'] = $validacion->GENERO;

                // Respuesta de éxito
                $response["message"] = "Inicio de sesión exitoso";
                $response["data"] = [
                    "id" => $validacion->ID_USUARIO,  // Puedes incluir el ID en la respuesta también
                    "rol" => $validacion->ROL,
                    "nombre" => $validacion->NOMBRE,
                    "email" => $validacion->EMAIL,
                    "foto" => $validacion->FOTO
                ];
            } else {
                $response["message"] = "Usuario o contraseña incorrectos";
            }
        } else {
            $response["message"] = "Usuario o contraseña incorrectos";
        }
    } else {
        $response["message"] = "Por favor, ingresa todos los campos";
    }
} else {
    $response["message"] = "Método de solicitud no permitido";
}

// Enviar la respuesta en formato JSON
echo json_encode($response);
?>
