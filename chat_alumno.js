document.addEventListener("DOMContentLoaded", () => {
    const chatMessages = document.getElementById("chat-messages");
    const messageInput = document.getElementById("message-input");
    const sendMessageButton = document.getElementById("send-message");
    const userButtons = document.querySelectorAll(".user-btn");
    
    let selectedUserId = null;
    let renderedMessageCount = 0; 

    const fetchMessages = () => {
        if (!selectedUserId) return;

        fetch("00cargar_historial.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ destinatario: selectedUserId }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.error) {
                    console.error("Error al cargar mensajes:", data.error);
                    return;
                }

                if (data.length === 0 && renderedMessageCount === 0) {
                    chatMessages.innerHTML = "<p class='no-messages' style='color: var(--text-muted); text-align: center; margin-top: 20px;'>No hay mensajes todavía.</p>";
                    return;
                }

                if (data.length === renderedMessageCount) {
                    return; 
                }

                if (renderedMessageCount === 0) {
                    chatMessages.innerHTML = "";
                }

                for (let i = renderedMessageCount; i < data.length; i++) {
                    const message = data[i];
                    const messageElement = document.createElement("div");
                    
                    messageElement.className = message.REMITENTE === selectedUserId ? "message other" : "message self";

                    const name = document.createElement("span");
                    name.className = "message-name";
                    name.textContent = message.REMITENTE === selectedUserId ? message.NOMBRE_REMITENTE : "Tú";

                    const text = document.createElement("p");
                    text.textContent = message.TEXT;

                    messageElement.appendChild(name);
                    messageElement.appendChild(text);
                    chatMessages.appendChild(messageElement);
                }

                renderedMessageCount = data.length; 
                chatMessages.scrollTop = chatMessages.scrollHeight;
            })
            .catch((err) => console.error("Error al cargar historial:", err));
    };

    // Enviar mensaje al presionar Enter (Shift+Enter hace salto de línea)
    messageInput.addEventListener("keydown", (e) => {
        if (e.key === "Enter" && !e.shiftKey) {
            e.preventDefault(); 
            sendMessageButton.click();
        }
    });

    sendMessageButton.addEventListener("click", () => {
        const message = messageInput.value.trim();
        if (!message || !selectedUserId) {
            return; 
        }

        fetch("00enviar_mensaje.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ destinatario: selectedUserId, mensaje: message }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.error) {
                    alert("Error al enviar mensaje: " + data.error);
                } else {
                    messageInput.value = "";
                    fetchMessages(); 
                }
            })
            .catch((err) => console.error("Error al enviar mensaje:", err));
    });

    userButtons.forEach((button) => {
        button.addEventListener("click", () => {
            selectedUserId = button.getAttribute("data-id");
            document.getElementById("chat-title").textContent = `Chat con ${button.textContent}`;
            
            chatMessages.innerHTML = "";
            renderedMessageCount = 0;
            
            fetchMessages();
        });
    });

    setInterval(fetchMessages, 3000);
});