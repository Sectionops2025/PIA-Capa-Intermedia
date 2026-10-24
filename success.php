<?php
session_start();
require '00ConexionDB.php'; 

if (!isset($_SESSION['idUsuario'])) {
    echo "Usuario no autenticado.";
    exit;
}

$idUsuario = $_SESSION['idUsuario']; 

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Curso no válido.";
    exit;
}

$idCurso = intval($_GET['id']); 

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

$fechaHoy = date('Y-m-d');
$formaPago = "STRIPE/TARJETA"; 

$queryInsert = "INSERT INTO usuario_curso (ID_USUARIO, ID_CURSO, FECHA_INSCRIPCION, FORMAPAGO, CALIFICACION) VALUES (?, ?, ?, ?, NULL)";
$stmt = $conex->prepare($queryInsert);
$stmt->bind_param("iiss", $idUsuario, $idCurso, $fechaHoy, $formaPago);

if ($stmt->execute()) {
    // Éxito en la inserción
} else {
    echo "<p>Error al procesar la compra. Por favor, intenta nuevamente.</p>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compra Exitosa - BitAcademy</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f1115;
            color: #f8fafc;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .success-container {
            text-align: center;
            padding: 40px;
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 16px;
            background-color: #1a1d24;
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.5);
            max-width: 500px;
            width: 90%;
        }
        .success-container h1 {
            font-size: 2.2em;
            margin-bottom: 15px;
            color: #f8fafc;
        }
        .success-container p {
            font-size: 1.1em;
            margin-bottom: 30px;
            color: #94a3b8;
        }
        .success-container a {
            display: inline-block;
            text-decoration: none;
            font-size: 1.05em;
            font-weight: 600;
            color: #ffffff;
            background-color: #8b5cf6;
            padding: 12px 25px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .success-container a:hover {
            background-color: #7c3aed;
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.4);
            transform: translateY(-2px);
        }
        .success-icon {
            font-size: 4em;
            color: #10b981;
            margin-bottom: 20px;
            text-shadow: 0 0 20px rgba(16, 185, 129, 0.4);
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">✔️</div>
        <h1>¡Compra Exitosa!</h1>
        <p>Ahora estás inscrito en el curso <br><strong style="color: #f8fafc;"><?php echo $tituloCurso; ?></strong>.</p>
        <p>¡Comienza tu aprendizaje ahora mismo!</p>
        <a href="CursoCom.php?id=<?php echo $idCurso; ?>">Ir al curso</a>
    </div>
</body>
</html>