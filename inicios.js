document.addEventListener('DOMContentLoaded', () => {
    const buttons = document.querySelectorAll('.course-card button');

    buttons.forEach(button => {
        button.addEventListener('click', (event) => {
            const courseName = event.target.closest('.course-card').querySelector('h3').textContent;
            alert(`Has seleccionado el curso: ${courseName}`);
            
            // Añadir la clase de animación para hacer el efecto de clic
            button.classList.add('animate');

            // Remover la clase de animación después de un tiempo corto para que pueda reutilizarse
            setTimeout(() => {
                button.classList.remove('animate');
            }, 100); // 100 ms para que coincida con la duración de la animación
        });
    });
});

/* Animación para el botón al hacer clic */
@keyframes buttonClick {
    from {
        transform: scale(1);
    }
    to {
        transform: scale(0.9);
    }
}

/* Aplica la animación cuando se agrega la clase 'animate' */
button.animate {
    animation: buttonClick 0.1s ease-in-out;
}
