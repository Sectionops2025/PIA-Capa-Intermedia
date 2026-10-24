document.addEventListener('DOMContentLoaded', () => {
    const registerForm = document.getElementById('registerForm');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm-password');
    const nameInput = document.getElementById('name');
    const errorMessage = document.getElementById('error-message');

    // Expresión regular para validar la contraseña
    const passwordRegex = /^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*#?&]{8,}$/;

    registerForm.addEventListener('submit', async (event) => {
        event.preventDefault(); // Evita que el formulario se envíe inmediatamente

        const name = nameInput.value.trim();
        const email = emailInput.value.trim();
        const password = passwordInput.value.trim();
        const confirmPassword = confirmPasswordInput.value.trim();

        // Validar si algún campo está vacío
        if (name === '' || email === '' || password === '' || confirmPassword === '') {
            errorMessage.textContent = 'Por favor, completa todos los campos.';
            errorMessage.style.display = 'block';
        }
        // Validar si las contraseñas coinciden
        else if (password !== confirmPassword) {
            errorMessage.textContent = 'Las contraseñas no coinciden.';
            errorMessage.style.display = 'block';
        }
        // Validar si la contraseña cumple con los requisitos
        else if (!passwordRegex.test(password)) {
            errorMessage.textContent = 'La contraseña debe tener al menos 8 caracteres, una mayúscula, un número y un carácter especial.';
            errorMessage.style.display = 'block';
        } 
        // Si todo está bien, se oculta el mensaje de error
        else {
            errorMessage.style.display = 'none';

            // Crear un objeto con los datos del formulario
            const formData =  new FormData(registerForm);


            try {
                // Enviar los datos al servidor usando fetch
                const response = await fetch('00ConexionNewUser.php', {
                    method: 'POST',
                    body: formData 
                });

                const result = await response.text();
                console.log(result);

                if (response.ok) {
                    alert('Registro exitoso');
                    window.location.href = 'login.php'; 
                } else {
                    errorMessage.textContent = 'Hubo un error al registrar. Intenta nuevamente.';
                    errorMessage.style.display = 'block';
                }
            } catch (error) {
                console.error('Error:', error);
                errorMessage.textContent = 'Error de conexión. Intenta nuevamente.';
                errorMessage.style.display = 'block';
            }
        }
    });

    const fileInput = document.getElementById("foto-perfil");
    fileInput.addEventListener("change", function () {
        const file = fileInput.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const previewImage = document.querySelector(".custom-file img") || document.createElement("img");
                previewImage.src = e.target.result;
                previewImage.style.width = "100px";
                previewImage.style.height = "100px";
                previewImage.style.borderRadius = "50%";

                if (!previewImage.parentElement) {
                    document.querySelector(".custom-file").appendChild(previewImage);
                }
            };
            reader.readAsDataURL(file);
        }
    });

    // Establecer la fecha máxima en el campo de fecha (para evitar seleccionar fechas futuras)
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('fecha-nacimiento').setAttribute('max', today);

    
});
