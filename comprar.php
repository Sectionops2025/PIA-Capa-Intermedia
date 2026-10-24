<?php
session_start();
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
    $costoCurso = number_format($curso['COSTO'], 2);
} else {
    echo "Curso no encontrado.";
    exit;
}

// Procesar el formulario si es enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['nombre'], $_POST['email'], $_POST['tarjeta'], $_POST['expiracion'], $_POST['cvv'], $_POST['formaPago'], $_SESSION['idUsuario'])) {
        $idUsuario = $_SESSION['idUsuario']; // ID del usuario autenticado
        $formaPago = $_POST['formaPago']; // Forma de pago seleccionada
        $fechaHoy = date('Y-m-d');

        // Insertar en la tabla usuario_curso
        $queryInsert = "INSERT INTO usuario_curso (ID_USUARIO, ID_CURSO, FECHA_INSCRIPCION, FROMAPAGO, CALIFICACION) VALUES (?, ?, ?, ?, NULL)";
        $stmt = $conex->prepare($queryInsert);
        $stmt->bind_param("iiss", $idUsuario, $idCurso, $fechaHoy, $formaPago);

        if ($stmt->execute()) {
            echo "<p style='color: #28a745; text-align: center;'>¡Compra realizada con éxito! Ahora estás inscrito en el curso.</p>";
            header("Location: CursoCom.php?id={$idCurso}");
        } else {
            echo "<p style='color: #ef4444; text-align: center;'>Error al procesar la compra. Por favor, intenta nuevamente.</p>";
        }
    } else {
        echo "<p style='color: #f59e0b; text-align: center;'>Por favor, completa todos los campos del formulario.</p>";
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Completar Pago - BitAcademy</title>
    <link rel="stylesheet" href="comprar.css">
</head>
<body>
    <div class="compra-container">
        <h1>Detalles del Pago</h1>
        
        <div class="curso-info">
            <p><strong><?php echo $tituloCurso; ?></strong></p>
            <p class="precio-highlight">Total: $<?php echo $costoCurso; ?> MXN</p>
        </div>

        <form id="compra-form" method="POST" action="">
            <div class="input-group">
                <label for="nombre">Nombre en la tarjeta:</label>
                <input type="text" id="nombre" name="nombre" placeholder="Ej. Juan Pérez" required>
            </div>

            <div class="input-group">
                <label for="email">Correo electrónico:</label>
                <input type="email" id="email" name="email" placeholder="tucorreo@ejemplo.com" required>
            </div>

            <div class="input-group">
                <label for="tarjeta">Número de tarjeta:</label>
                <input type="text" id="tarjeta" name="tarjeta" placeholder="0000 0000 0000 0000" maxlength="16" required>
            </div>

            <div class="row">
                <div class="input-group">
                    <label for="expiracion">Expiración:</label>
                    <input type="text" id="expiracion" name="expiracion" placeholder="MM/AA" maxlength="5" required>
                </div>

                <div class="input-group">
                    <label for="cvv">CVV:</label>
                    <input type="text" id="cvv" name="cvv" placeholder="123" maxlength="3" required>
                </div>
            </div>

            <div class="input-group">
                <label for="formaPago">Método de Pago:</label>
                <select id="formaPago" name="formaPago" required>
                    <option value="TARJETA CRÉDITO">Tarjeta de Crédito</option>
                    <option value="TARJETA DÉBITO">Tarjeta de Débito</option>
                </select>
            </div>

            <button type="submit" class="btn-comprar">Procesar pago seguro</button>
        </form>
    </div>

    <script src="comprar.js"></script>
</body>
</html>