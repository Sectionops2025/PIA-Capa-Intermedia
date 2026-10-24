<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compra Cancelada - OmIso</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f1115; /* Fondo base oscuro */
            color: #f8fafc;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .cancel-container {
            text-align: center;
            padding: 40px 30px;
            background-color: #1a1d24; /* Superficie oscura */
            border: 1px solid rgba(239, 68, 68, 0.2); /* Borde rojo sutil */
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(239, 68, 68, 0.1);
            max-width: 500px;
            animation: fadeIn 0.5s ease-out;
        }
        .cancel-container h1 {
            font-size: 2.2em;
            margin-bottom: 10px;
            color: #f8fafc;
        }
        .cancel-container p {
            font-size: 1.1em;
            color: #94a3b8;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .cancel-container a {
            display: inline-block;
            text-decoration: none;
            font-size: 1.05em;
            font-weight: 600;
            color: #ffffff;
            background-color: #ef4444; /* Rojo moderno */
            padding: 12px 24px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .cancel-container a:hover {
            background-color: #dc2626;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
        }
        .cancel-icon {
            font-size: 3.5em;
            margin-bottom: 20px;
            text-shadow: 0 0 20px rgba(239, 68, 68, 0.5);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="cancel-container">
        <div class="cancel-icon">❌</div>
        <h1>Compra Cancelada</h1>
        <p>Lamentamos que no hayas completado tu compra.<br> Si cambias de opinión, puedes intentarlo nuevamente cuando estés listo.</p>
        <a href="inicio.php">Volver al inicio</a>
    </div>
</body>
</html>