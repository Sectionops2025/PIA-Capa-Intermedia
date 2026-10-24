// Variables para manejar los usuarios y el chat
let activeUser = null;

// Función para cargar los usuarios desde la base de datos
function cargarUsuarios() {
    fetch("obtener_usuarios.php")
        .then(response => response.json())
        .then(usuarios => {
            const userList = document.getElementById("user-list");
            userList.innerHTML = ""; // Limpiar la lista antes de agregar nuevos usuarios

            usuarios.forEach(usuario => {
                const userButton = document.createElement("li");
                userButton.innerHTML = `<button class="user-btn" data-username="${usuario}">${usuario}</button>`;
                userList.appendChild(userButton);
            });

            // Agregar el evento click para cada botón de usuario recién agregado
            document.querySelectorAll(".user-btn").forEach(button => {
                button.addEventListener("click", function() {
                    activeUser = this.getAttribute("data-username");
                    document.getElementById("chat-username").textContent = activeUser;
                    document.getElementById("messages").innerHTML = ''; // Limpiar los mensajes previos
                });
            });
        })
        .catch(error => console.error("Error al cargar los usuarios:", error));
}

// Llamar a cargarUsuarios cuando se carga la página
document.addEventListener("DOMContentLoaded", cargarUsuarios);

// Función para minimizar el chat sin desaparecer
document.getElementById("chat-header").addEventListener("click", function() {
    const chatBody = document.getElementById("chat-body");
    const toggleBtn = document.getElementById("chat-toggle");

    if (chatBody.classList.contains("minimized")) {
        chatBody.classList.remove("minimized");
        toggleBtn.textContent = "-";
    } else {
        chatBody.classList.add("minimized");
        toggleBtn.textContent = "+";
    }
});

// Función para minimizar la lista de usuarios sin desaparecer
document.getElementById("users-header").addEventListener("click", function() {
    const chatUsers = document.getElementById("chat-users");
    const toggleBtn = document.getElementById("users-toggle");

    if (chatUsers.classList.contains("minimized")) {
        chatUsers.classList.remove("minimized");
        toggleBtn.textContent = "-";
    } else {
        chatUsers.classList.add("minimized");
        toggleBtn.textContent = "+";
    }
});

// Función para enviar mensajes
document.getElementById("chat-form").addEventListener("submit", function(event) {
    event.preventDefault();

    const inputField = document.getElementById("chat-input");
    const messagesContainer = document.getElementById("messages");

    if (inputField.value.trim() !== "" && activeUser !== null) {
        const userMessage = document.createElement("div");
        userMessage.classList.add("message");
        userMessage.innerHTML = `<span><strong>Tú:</strong> ${inputField.value}</span>`;
        messagesContainer.appendChild(userMessage);
        inputField.value = "";
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
});
