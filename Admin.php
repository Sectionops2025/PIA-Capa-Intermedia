<?php
include("00ConexionDB.php"); // Incluye la conexión a la base de datos
session_start();

// Verificar si el usuario tiene rol de administrador (ROL = 1) y está autenticado
if (isset($_SESSION['rol']) && $_SESSION['rol'] == 1 && isset($_SESSION['idUsuario'])) {
    $idUsuario = $_SESSION['idUsuario'];

    // Consultar la base de datos para obtener los datos del usuario
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
    // Redirigir al login si no hay sesión o no es administrador
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - Panel de Administración</title>
    <link rel="stylesheet" href="Admin.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="container">
                <a href="inicio.php" class="logo">OmIso</a>
                <ul class="nav-links">
                    <li><a href="inicio.php">Inicio</a></li>
                    <li><a href="cursos.php">Cursos</a></li>
                    <li><a href="Admin.php" class="active">Perfil</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main class="profile-container">
        <!-- Sidebar -->
      <div class="sidebar">
    <ul>
        <li><a href="UsuariosDes.php">Usuarios Deshabilitados</a></li>
        <li><a href="Categorias.php">Categorías</a></li>
        <li><a href="ReporteAdmin.php">Reportes de Usuario</a></li>
        <li><a href="reportes.php">Reportes Generales</a></li>
        <li><a href="modificar.php">Editar Perfil</a></li>
    </ul>
</div>

        <!-- Contenido del perfil -->
        <div class="profile-content">
            <div class="profile-header">
                <h1>Tu Perfil</h1>
                <p class="subtitle">Gestiona Usuarios y Categorías desde este panel.</p>
            </div>
            
            <div class="profile-card">
                <div class="profile-avatar-container">
                    <?php if (!empty($usuario['FOTO'])): ?>
                        <img src="data:image/jpeg;base64,<?php echo htmlspecialchars($usuario['FOTO']); ?>" 
                             alt="Foto de perfil" class="profile-avatar">
                    <?php else: ?>
                        <img src="default-profile.png" 
                             alt="Foto predeterminada" class="profile-avatar">
                    <?php endif; ?>
                </div>
                <div class="profile-info">
                    <h2><?php echo htmlspecialchars($usuario['NOMBRE']); ?></h2>
                    <span class="badge">Administrador</span>
                    <p class="email-text">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
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