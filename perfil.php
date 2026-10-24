<?php
include("00ConexionDB.php"); 
session_start();

if (isset($_SESSION['rol']) && $_SESSION['rol'] == 2 && isset($_SESSION['idUsuario'])) {
    $idUsuario = $_SESSION['idUsuario'];

    $query = "SELECT NOMBRE, EMAIL, FOTO FROM usuario WHERE ID_USUARIO = ?";
    if ($stmt = $conex->prepare($query)) { 
        $stmt->bind_param('i', $idUsuario);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $usuario = $result->fetch_assoc();
        } else {
            echo "No se encontraron datos del usuario.";
            exit;
        }
        $stmt->close();
    } else {
        die("Error al preparar la consulta: " . $conex->error);
    }
} else {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - BitAcademy</title>
    <link rel="stylesheet" href="perfil.css">
</head>
<body>
    <header>
        <nav class="navbar container">
            <a href="inicio.php" class="logo">BitAcademy</a>
            <ul class="nav-links">
                <li><a href="inicio.php">Inicio</a></li>
                <li><a href="cursos.php">Cursos</a></li>
                <li><a href="perfil.php" class="active">Perfil</a></li>
                <li><a href="chat_alumno.php">Chat</a></li>
                <li><a href="00Cerrarsesion.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <main class="profile-container">
        <div class="sidebar">
            <ul>
                <li><a href="Kardex.php">Kardex</a></li>
                <li><a href="Compras.php">Cursos Comprados</a></li>
                <li><a href="modificar.php">Editar Perfil</a></li>
            </ul>
        </div>

        <div class="profile-content">
            <div class="profile-header">
                <h1>Tu Perfil</h1>
                <p class="subtitle">Aquí puedes ver y gestionar tu información personal.</p>
            </div>

            <div class="profile-card">
                <div class="profile-avatar-container">
                    <?php if (!empty($usuario['FOTO'])): ?>
                        <img src="data:image/jpeg;base64,<?php echo $usuario['FOTO']; ?>" alt="Foto de perfil" class="profile-avatar">
                    <?php else: ?>
                        <img src="default-profile.png" alt="Foto predeterminada" class="profile-avatar">
                    <?php endif; ?>
                </div>
                <div class="profile-info">
                    <h2><?php echo htmlspecialchars($usuario['NOMBRE']); ?></h2>
                    <span class="badge">Estudiante</span>
                    <p class="email-text">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                        <?php echo htmlspecialchars($usuario['EMAIL']); ?>
                    </p>
                </div>
            </div>
        </div>
    </main>    

    <footer>
        <div class="container">
            <p>&copy; 2026 BitAcademy. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>