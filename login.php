<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - BitAcademy</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <!-- Formulario -->
            <div class="login-form-container">
                <form method="post" enctype="multipart/form-data" name="loginForm" id="loginForm">
                    <h2>Bienvenido de vuelta</h2>
                    <p class="subtitle">Accede a tus cursos y gestiona tu aprendizaje</p>

                    <label for="email">Correo electrónico</label>
                    <input type="email" id="email" name="email" placeholder="tucorreo@ejemplo.com" required>

                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" placeholder="Ingresa tu contraseña" required>

                    <button type="submit" id="btnini" name="btnini">Iniciar Sesión</button>

                    <p id="error-message" class="error-message"></p>

                    <p class="extra-options">¿No tienes cuenta? <a href="registro.php">Regístrate</a></p>
                </form>
            </div>

            <!-- Logo lateral con halo neón -->
            <div class="login-logo">
                <img src="logo.png" alt="Logotipo BitAcademy">
            </div>
        </div>
    </div>

    <script src="login.js"></script>
</body>
</html>