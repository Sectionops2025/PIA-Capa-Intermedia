document.addEventListener('DOMContentLoaded', () => {
    const editForm = document.getElementById('editForm');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm-password');
    const nameInput = document.getElementById('name');
    const generoInput = document.getElementById('genero');
    const fechaNacimientoInput = document.getElementById('fecha-nacimiento');
    const fileInput = document.getElementById("foto-perfil");
    const errorMessage = document.getElementById('error-message');

    // Expresión regular para validar la contraseña
    const passwordRegex = /^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

    // Validaciones de formulario
    editForm.addEventListener('submit', async (event) => {
        event.preventDefault(); // Evita el envío por defecto del formulario

        const password = passwordInput.value.trim();
        const confirmPassword = confirmPasswordInput.value.trim();
        const name = nameInput.value.trim();
        const genero = generoInput.value.trim();
        const fechaNacimiento = fechaNacimientoInput.value.trim();

        // Validar si algún campo está vacío
        if (!name || !genero || !fechaNacimiento || !password || !confirmPassword) {
            errorMessage.textContent = 'Por favor, completa todos los campos.';
            errorMessage.style.display = 'block';
            return;
        }

        // Validar si las contraseñas coinciden
        if (password !== confirmPassword) {
            errorMessage.textContent = 'Las contraseñas no coinciden.';
            errorMessage.style.display = 'block';
            return;
        }

        // Validar si la contraseña cumple con los requisitos
        if (!passwordRegex.test(password)) {
            errorMessage.textContent = 'La contraseña debe tener al menos 8 caracteres, una mayúscula, un número y un carácter especial.';
            errorMessage.style.display = 'block';
            return;
        }

        // Si todo está bien, ocultar el mensaje de error
        errorMessage.style.display = 'none';

        // Crear objeto FormData para enviar los datos del formulario, incluida la imagen
        const formData = new FormData(editForm);

        try {
            // Enviar los datos al servidor usando fetch
            const response = await fetch('00ConexionEditUser.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.text();
            console.log(result);

            if (response.ok) {
                alert('¡Perfil actualizado correctamente!');
                window.location.href = 'inicio.php'; // Redirigir al inicio después de la actualización
            } else {
                errorMessage.textContent = 'Hubo un error al actualizar el perfil. Intenta nuevamente.';
                errorMessage.style.display = 'block';
            }
        } catch (error) {
            console.error('Error:', error);
            errorMessage.textContent = 'Error de conexión. Intenta nuevamente.';
            errorMessage.style.display = 'block';
        }
    });

    // Mostrar vista previa de la foto de perfil seleccionada
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

    // Establecer la fecha máxima en el campo de fecha de nacimiento
    const today = new Date().toISOString().split('T')[0];
    fechaNacimientoInput.setAttribute('max', today);
});
