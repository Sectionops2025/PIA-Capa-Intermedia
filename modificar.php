<?php
include("00ConexionDB.php");
session_start();
?>

<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar perfil - BitAcademy</title>
    <!-- Utiliza login.css para mantener la consistencia de la tarjeta flotante -->
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <!-- Sección del Formulario -->
            <div class="login-form-container">
                <form method="post" enctype="multipart/form-data" id="editForm" name="nameForm">
                    <h2>Configuración</h2>
                    <p class="subtitle">Modifica la información de tu cuenta</p>

                    <label for="foto-perfil">Foto de Perfil:</label>
                    <div class="custom-file" style="margin-bottom: 20px;">
                        <input type="file" name="foto-perfil" id="foto-perfil" value="<?php echo $_SESSION['pfp']; ?>" class="custom-file-input" style="color: var(--text-muted);">
                    </div>

                    <label for="name">Nombre completo:</label>
                    <input type="text" id="name" name="name" value="<?php echo $_SESSION['nom']; ?>" placeholder="Ingresa tu nombre completo" required>

                    <label for="genero">Género:</label>
                    <select id="genero" name="genero" class="form-control" style="width: 100%; padding: 13px 16px; margin-bottom: 22px; border-radius: 8px; border: 1px solid var(--border-color); background-color: var(--bg-base); color: var(--text-main);" required>
                        <option value="" disabled>Selecciona tu género</option>
                        <option value="masculino" <?php echo ($_SESSION['gen'] == 'masculino') ? 'selected' : ''; ?>>Masculino</option>
                        <option value="femenino" <?php echo ($_SESSION['gen'] == 'femenino') ? 'selected' : ''; ?>>Femenino</option>
                        <option value="otro" <?php echo ($_SESSION['gen'] == 'otro') ? 'selected' : ''; ?>>Otro</option>
                    </select>

                    <label for="fecha-nacimiento">Fecha de Nacimiento:</label>
                    <input type="date" id="fecha-nacimiento" name="fecha-nacimiento" value="<?php echo $_SESSION['naci']; ?>" class="form-control" style="width: 100%; padding: 13px 16px; margin-bottom: 22px; border-radius: 8px; border: 1px solid var(--border-color); background-color: var(--bg-base); color: var(--text-main);" required>

                    <label for="password">Nueva Contraseña:</label>
                    <input type="password" id="password" name="password" value="<?php echo $_SESSION['contra']; ?>" placeholder="Ingresa tu contraseña" required>

                    <label for="confirm-password">Confirmar contraseña:</label>
                    <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirma tu contraseña" required>

                    <button type="submit" id="btnedit" name="btnedit">Guardar Cambios</button>

                    <p id="error-message" class="error-message"></p>
                </form>
            </div>
            
            <!-- Logo lateral -->
            <div class="login-logo">
                <img src="logo.png" alt="Logotipo BitAcademy">
            </div>
        </div>
    </div>

    <?php include("00ConexionEditUser.php"); ?>
    <script src="editarPerfil.js"></script>
</body>
</html>