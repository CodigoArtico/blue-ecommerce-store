<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/16454e4b22.js" crossorigin="anonymous"></script>
    <title>403 - Acceso Denegado</title>
    <style>
        @import url('https://fonts.googleapis.com/css?family=Raleway:400,700');

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Raleway', sans-serif;
        }

        body {
            background: radial-gradient(circle, #B3E5FC, #00BCD4);
            color: #333;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            overflow: hidden;
        }

        .container {
            text-align: center;
            animation: fadeIn 1.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h1 {
            font-size: 120px;
            font-weight: 700;
            color: #007E90;
            margin-bottom: 10px;
            animation: bounce 1.5s infinite ease-in-out;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        p {
            font-size: 18px;
            margin-bottom: 30px;
            color: #005662;
            line-height: 1.6;
        }

        .buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .button {
            text-decoration: none;
            padding: 15px 35px;
            border-radius: 50px;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 16px;
            transition: all 0.3s ease;
            color: #fff;
            background: linear-gradient(90deg, #4DD0E1, #007E90);
            box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.2);
        }

        .button:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0px 12px 20px rgba(0, 0, 0, 0.3);
        }

        .back {
            background: linear-gradient(90deg, #00ACC1, #0097A7);
        }

        .back:hover {
            background: linear-gradient(90deg, #007E90, #005662);
        }

        .dashboard {
            background: linear-gradient(90deg, #4DD0E1, #00BCD4);
        }

        .dashboard:hover {
            background: linear-gradient(90deg, #0097A7, #007E90);
        }

        .floating-icon {
            position: absolute;
            font-size: 50px;
            opacity: 0.1;
            animation: float 6s infinite linear;
        }

        .icon1 {
            top: 20%;
            left: 10%;
        }

        .icon2 {
            top: 70%;
            left: 80%;
        }

        .icon3 {
            top: 40%;
            left: 50%;
        }

        @keyframes float {
            0% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-20px);
            }

            100% {
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>403</h1>
        <p>Acceso denegado. No tienes los permisos necesarios para acceder a esta página.</p>
        <div class="buttons">
            <a href="{{ url()->previous() }}" class="button back"><i class="fa-solid fa-arrow-left mr-2"></i> Volver</a>
            <a href="{{ route('dashboard') }}" class="button dashboard"><i class="fas fa-home mr-2"></i> Inicio</a>
        </div>
    </div>
    <!-- Floating icons -->
    <i class="fa-solid fa-circle icon1 floating-icon"></i>
    <i class="fa-solid fa-square icon2 floating-icon"></i>
    <i class="fa-solid fa-star icon3 floating-icon"></i>
</body>
</html>
