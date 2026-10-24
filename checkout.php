<?php
session_start();
require __DIR__ . "/vendor/autoload.php";
require '00ConexionDB.php'; // Conexión a la base de datos

// Capturar el ID del curso desde la URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Curso no válido.";
    exit;
}

$idCurso = intval($_GET['id']); // ID del curso pasado por la URL

// Obtener los datos del curso, incluyendo el costo
$queryCurso = "SELECT TITULO, COSTO FROM curso WHERE ID_CURSO = ?";
$stmt = $conex->prepare($queryCurso);
$stmt->bind_param("i", $idCurso);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $curso = $result->fetch_assoc();
    $tituloCurso = htmlspecialchars($curso['TITULO']);
    $costoCurso = intval($curso['COSTO'] * 100); // Convertir a centavos
} else {
    echo "Curso no encontrado.";
    exit;
}

// Configurar clave secreta de Stripe
$stripe_secret_key = "sk_test_51QQCLpCTE3bhJvbytvmnv6PN6kHTzIi4PKPWT4frj1drKBoCewMsOXQFaFtHzmVOML14OggJND2pb2XVlds51UDH00zC2hmHKj";
\Stripe\Stripe::setApiKey($stripe_secret_key);

// Crear una sesión de pago en Stripe
$checkout_session = \Stripe\Checkout\Session::create([
    "payment_method_types" => ["card"],
    "mode" => "payment",
    "success_url" => "http://localhost:3000/success.php?id={$idCurso}",
    "cancel_url" => "http://localhost:3000/cancel.php",
    "line_items" => [[
        "quantity" => 1,
        "price_data" => [
            "currency" => "mxn",
            "unit_amount" => $costoCurso, // Precio en centavos
            "product_data" => [
                "name" => $tituloCurso
            ]
        ]
    ]]
]);

// Redirigir al usuario a la URL de Stripe
http_response_code(303);
header("Location: " . $checkout_session->url);
?>
