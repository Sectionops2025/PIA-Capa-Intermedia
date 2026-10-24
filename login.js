document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('loginForm');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const errorMessage = document.getElementById('error-message');

    form.addEventListener('submit', async (event) => {
        event.preventDefault(); 

        const email = emailInput.value.trim();
        const password = passwordInput.value.trim();

        if (email === '' || password === '') {
            errorMessage.textContent = 'Por favor, completa todos los campos.';
            errorMessage.style.display = 'block';
        } else {
            errorMessage.style.display = 'none';

            
            const formData = new FormData(form);

            try {
                
                const response = await fetch('00ConexionIniSes.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.text();
                console.log(result);

                
                if (response.ok && result.includes('exitoso')) {
                    
                    alert('Bienvenido');
                    window.location.href = 'inicio.php'; 
                } else {
                    
                    errorMessage.textContent = 'Usuario o contraseña incorrectos. Intenta nuevamente.';
                    errorMessage.style.display = 'block';
                }
            } catch (error) {
                console.error('Error:', error);
                errorMessage.textContent = 'Error de conexión. Intenta nuevamente.';
                errorMessage.style.display = 'block';
            }
        }
    });
});
