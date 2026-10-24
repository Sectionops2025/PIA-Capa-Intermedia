<?php
include("00ConexionDB.php");
session_start();

if (!isset($_SESSION['idUsuario']) || $_SESSION['rol'] != 1) {
    header("Location: inicio.php");
    exit;
}

$query = "SELECT ID_USUARIO, NOMBRE, EMAIL FROM usuario WHERE INHABILITADO = 1";
$result = $conex->query($query);

$usuariosDeshabilitados = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $usuariosDeshabilitados[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios Deshabilitados - BitAcademy</title>
    <!-- Utilizando el archivo CSS unificado para la barra lateral y tablas -->
    <link rel="stylesheet" href="reportesusuario.css"> 
</head>
<body>
    <header>
        <nav class="navbar">
            <a href="inicio.php" class="logo">BitAcademy</a>
            <ul class="nav-links">
                <li><a href="inicio.php">Inicio</a></li>
                <li><a href="cursos.php">Cursos</a></li>
                <li><a href="Admin.php">Perfil</a></li>
            </ul>
        </nav>
    </header>

    <div class="sidebar">
        <ul>
            <li><a href="UsuariosDes.php">Usuarios Deshabilitados</a></li>
            <li><a href="Categorias.php">Categorías</a></li>
            <li><a href="ReporteAdmin.php">Reportes de Usuario</a></li>
        </ul>
    </div>

    <main class="profile-content">
        <h1>Usuarios Deshabilitados</h1>
        <table id="usuarios-deshabilitados-table">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Correo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($usuariosDeshabilitados)): ?>
                    <?php foreach ($usuariosDeshabilitados as $usuario): ?>
                        <tr>
                            <td style="color: var(--text-main); font-weight: 600;"><?= htmlspecialchars($usuario['NOMBRE']) ?></td>
                            <td><?= htmlspecialchars($usuario['EMAIL']) ?></td>
                            <td>
                                <button 
                                    class="habilitar-btn" 
                                    style="background-color: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 600;"
                                    data-id="<?= htmlspecialchars($usuario['ID_USUARIO']) ?>" 
                                    onclick="habilitarUsuario(this)">
                                    Habilitar
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" style="text-align: center;">No hay usuarios deshabilitados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>

    <footer>
        <p>&copy; 2026 BitAcademy. Todos los derechos reservados.</p>
    </footer>

    <script src="deshab.js"></script>
</body>
</html>