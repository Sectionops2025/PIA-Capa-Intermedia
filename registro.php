<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - BitAcademy</title>
    <link rel="stylesheet" href="registro.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            
            <div class="login-form-container">
                <form method="post" enctype="multipart/form-data" id="registerForm" name="registerFrom">
                    <h2>Crear Cuenta</h2>
                    <p>Únete a nuestra plataforma y comienza a aprender.</p>

                    <label for="foto-perfil">Foto de Perfil:</label>
                    <div class="custom-file">
                        <input type="file" id="foto-perfil" name="foto-perfil" class="custom-file-input" required>
                    </div>

                    <label for="name">Nombre completo:</label>
                    <input type="text" id="name" name="name" placeholder="Ej. Ana Pérez" required>

                    <label for="email">Correo electrónico:</label>
                    <input type="email" id="email" name="email" placeholder="tucorreo@ejemplo.com" required>

                    <label for="genero">Género:</label>
                    <select id="genero" name="genero" required>
                        <option value="" disabled selected>Selecciona una opción</option>
                        <option value="1">Masculino</option>
                        <option value="2">Femenino</option>
                        <option value="3">Otro</option>
                    </select>

                    <label for="fecha-nacimiento">Fecha de Nacimiento:</label>
                    <input type="date" id="fecha-nacimiento" name="fecha-nacimiento" required>

                    <label for="cuenta">Tipo de Cuenta:</label>
                    <select id="cuenta" name="cuenta" required>
                        <option value="" disabled selected>Selecciona tu rol</option>
                        <option value="3">Instructor</option>
                        <option value="2">Estudiante</option>
                    </select>

                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" placeholder="Crea una contraseña segura" required>

                    <label for="confirm-password">Confirmar contraseña:</label>
                    <input type="password" id="confirm-password" name="confirm-password" placeholder="Repite tu contraseña" required>

                    <button type="submit" id="btnregistro" name="btnregistro">Registrarse</button>

                    <p id="error-message" class="error-message"></p>

                    <p class="extra-options">¿Ya tienes una cuenta? <a href="login.php">Inicia sesión</a></p>
                </form>

                <?php include("00ConexionNewUser.php"); ?>
            </div>
            
            <div class="login-logo">
                <img src="logo.png" alt="Logotipo BitAcademy">
            </div>
        </div>
    </div>

    <script src="registro.js"></script>
</body>
</html>