<?php
include("00ConexionDB.php");
session_start();

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['idUsuario'])) {
    die("Error: Usuario no autenticado.");
}

// Validar que se pase el parámetro ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: ID del curso no válido.");
}

$idCurso = intval($_GET['id']);

// Consulta para obtener la información del certificado desde la tabla `kardex`
$queryCertificado = "
    SELECT 
    u_alumno.NOMBRE AS nombre_alumno, -- Nombre del alumno
    u_instructor.NOMBRE AS nombre_instructor, -- Nombre del instructor
    c.TITULO AS nombre_curso, -- Nombre del curso
    nv.FECHA_COMPLETADO AS fecha_termino -- Fecha en que el curso fue completado
    FROM 
        niveles_completados nv
    INNER JOIN 
        usuario u_alumno ON nv.ID_USUARIO = u_alumno.ID_USUARIO -- Relación entre niveles_completados y usuario (alumno)
    INNER JOIN 
        curso c ON nv.ID_CURSO = c.ID_CURSO -- Relación entre niveles_completados y curso
    INNER JOIN 
        usuario u_instructor ON c.ID_INSTRUCTOR = u_instructor.ID_USUARIO -- Relación entre curso e instructor
    WHERE 
        nv.ID_CURSO = ? AND nv.ID_USUARIO = ?; -- Filtrar por curso y usuario específicos

";

$stmtCertificado = $conex->prepare($queryCertificado);
$stmtCertificado->bind_param("ii", $idCurso, $_SESSION['idUsuario']);
$stmtCertificado->execute();
$resultCertificado = $stmtCertificado->get_result();

if ($resultCertificado->num_rows === 0) {
    die("Error: No tienes el curso completado o no existe el curso.");
}

$row = $resultCertificado->fetch_assoc();

// Datos para el certificado
$nombreAlumno = htmlspecialchars($row['nombre_alumno']);
$nombreInstructor = htmlspecialchars($row['nombre_instructor']);
$nombreCurso = htmlspecialchars($row['nombre_curso']);
$fechaFinalizacion = htmlspecialchars($row['fecha_termino']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificado de Finalización</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="certificado.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script> <!-- jsPDF -->
</head>
<body>
    <div class="certificate" id="certificado">
        <div class="certificatewatermark">
            <div class="header">
                <h3>BitAcademy Otorga el presente</h3>
                <h1>Certificado de Finalización</h1>
                <p>Por haber concluido de manera</p>
                <p>satisfactoria el curso de:</p>
            </div>

            <div class="content">
                <h2><?= $nombreCurso ?></h2>
                <p>Impartido por el instructor:</p>
                <p><strong><?= $nombreInstructor ?></strong></p>
            </div>

            <div class="footer">
                <h3>Al alumno:</h3>
                <p><strong><?= $nombreAlumno ?></strong></p>
                <p>Fecha de Finalización: <strong><?= $fechaFinalizacion ?></strong></p>
            </div>
        </div>
        
    </div>
    <div class="save-pdf-container">
        <button onclick="guardarComoPDF() "id="savePDF" class="btn-pdf">Descargar Certificado como PDF</button>
    </div>
    

    <script>
        async function guardarComoPDF() {
            const { jsPDF } = window.jspdf;

            // Crear una instancia de jsPDF
            const pdf = new jsPDF();

            // Seleccionar el elemento que quieres convertir a PDF
            const certificado = document.getElementById('certificado');

            // Usar html2canvas para renderizar el elemento en una imagen
            const canvas = await html2canvas(certificado, { scale: 2 }); // Mejor calidad

            // Convertir el canvas a imagen base64
            const imgData = canvas.toDataURL('image/png');

            // Agregar la imagen al PDF
            pdf.addImage(imgData, 'PNG', 10, 10, 190, 140); // Ajusta el tamaño según sea necesario

            // Descargar el archivo PDF
            pdf.save('certificado.pdf');
        }
    </script>
    

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script> <!-- html2canvas -->
</body>
</html>

