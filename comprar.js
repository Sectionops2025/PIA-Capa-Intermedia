document.getElementById('compra-form').addEventListener('submit', function(event) {
    event.preventDefault(); // Evitamos el envío inmediato para validar primero

    // Obtener los valores de los campos
    const nombre = document.getElementById('nombre').value.trim();
    const email = document.getElementById('email').value.trim();
    const tarjeta = document.getElementById('tarjeta').value.trim();
    const expiracion = document.getElementById('expiracion').value.trim();
    const cvv = document.getElementById('cvv').value.trim();

    // Validar el campo de nombre (debe tener al menos 3 caracteres)
    if (nombre.length < 3) {
        alert("Por favor, ingresa el nombre completo que aparece en la tarjeta.");
        return;
    }

    // Validar el campo de correo electrónico (formato básico de email)
    const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    if (!emailPattern.test(email)) {
        alert("Por favor, ingresa un correo electrónico válido.");
        return;
    }

    // Validar el número de tarjeta (16 dígitos numéricos)
    const tarjetaPattern = /^\d{16}$/;
    if (!tarjetaPattern.test(tarjeta)) {
        alert("Por favor, ingresa un número de tarjeta válido de 16 dígitos sin espacios.");
        return;
    }

    // Validar la fecha de expiración (formato MM/AA)
    const expiracionPattern = /^(0[1-9]|1[0-2])\/\d{2}$/;
    if (!expiracionPattern.test(expiracion)) {
        alert("Por favor, ingresa una fecha de expiración válida en formato MM/AA (Ej. 12/25).");
        return;
    }

    // Validar el CVV (exactamente 3 dígitos)
    const cvvPattern = /^\d{3}$/;
    if (!cvvPattern.test(cvv)) {
        alert("Por favor, ingresa un CVV válido de 3 dígitos.");
        return;
    }

    // Si todas las validaciones pasan, ENVIAMOS EL FORMULARIO AL PHP
    // Quitamos el window.location.href para que el backend pueda guardar la compra.
    this.submit(); 
});