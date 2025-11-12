<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso al Sistema</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        /* ------------------------------------------- */
        /* ESTILOS DE FONDO Y CENTRADO DE PANTALLA */
        /* ------------------------------------------- */
        body {
            /* 1. Fondo que cubre toda la pantalla */
            background-image: url('{{ asset('website/images/img_4_colored.jpg') }}');
            background-repeat: no-repeat;
            background-size: cover;
            background-attachment: fixed;
            background-position: center center;
            background-color: #343a40;
            /* Color de respaldo */

            /* ¡NUEVA LÍNEA! Capa de superposición con color y opacidad */
            /* Puedes ajustar el color (aquí es negro) y el valor de opacidad (0.5 es 50%) */
            background-blend-mode: overlay;
            /* Combina la imagen con el color de fondo */
            background-color: rgba(0, 0, 0, 0.7);
            /* Capa negra semi-transparente */

            /* 2. Centrado usando Flexbox */
            display: flex;
            align-items: center;
            /* Centrado vertical */
            justify-content: center;
            /* Centrado horizontal */
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        /* ------------------------------------------- */
        /* ESTILOS DEL CARD DE LOGIN */
        /* ------------------------------------------- */
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            /* Fondo casi blanco semitransparente */
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        .login-header {
            margin-bottom: 25px;
            color: #333;
        }

        /* ------------------------------------------- */
        /* ESTILOS DE CAMPOS Y BOTÓN */
        /* ------------------------------------------- */
        .input-group {
            display: flex;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            overflow: hidden;
        }

        .input-group-icon {
            background-color: #eee;
            color: #555;
            padding: 10px 15px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .input-group input {
            flex-grow: 1;
            border: none;
            padding: 10px;
            outline: none;
            font-size: 16px;
        }

        .login-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .remember-me {
            font-size: 14px;
            display: flex;
            align-items: center;
        }

        .btn-login {
            background-color: #007bff;
            /* Color primario azul */
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
            width: 100%;
            /* Botón de ancho completo */
        }

        .btn-login:hover {
            background-color: #0056b3;
        }

        .footer-link {
            display: block;
            margin-top: 20px;
            font-size: 14px;
            color: #007bff;
            text-decoration: none;
        }

        /* Ajuste para el botón de ancho completo si no es necesario recordar */
        .btn-container {
            width: 40%;
        }

        /* Si el botón es el único elemento en la fila */
        .full-width-button {
            width: 100%;
            margin-left: 0;
        }
    </style>
</head>

<body>

    <div class="login-card">

        <h2 class="login-header">Acceso al Sistema</h2>

        <form action="/login" method="POST">
            @csrf

            <div class="input-group">
                <span class="input-group-icon"><i class="fas fa-envelope"></i></span>
                <input type="email" name="email" placeholder="Correo Electrónico" required>
            </div>

            <div class="input-group">
                <span class="input-group-icon"><i class="fas fa-lock"></i></span>
                <input type="password" name="password" placeholder="Contraseña" required>
            </div>

            <div class="login-actions">
                <div class="remember-me">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember" style="margin-left: 5px;">Recordarme</label>
                </div>

                <div class="btn-container">
                    <button type="submit" class="btn-login full-width-button">
                        <i class="fas fa-sign-in-alt"></i> Ingresar
                    </button>
                </div>
            </div>

        </form>

        <a href="https://premium43.web-hosting.com:2096" target="_blank" class="footer-link">
            Ir al correo institucional
        </a>

    </div>

</body>

</html>
