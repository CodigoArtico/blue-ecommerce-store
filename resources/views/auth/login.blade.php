<x-guest-layout>
    @section('title', 'Inicar Sesion')

    <style>
        @import url('https://fonts.googleapis.com/css?family=Raleway:400,700');

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Raleway, sans-serif;
        }

        body {
            background: linear-gradient(90deg, #B3E5FC, #00BCD4);
        }

        .container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .screen {
            background: linear-gradient(90deg, #4DD0E1, #00ACC1);
            position: relative;
            height: 600px;
            width: 360px;
            box-shadow: 0px 0px 24px #4FC3F7;
        }

        .screen__content {
            z-index: 1;
            position: relative;
            height: 100%;
        }

        .screen__background {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 0;
            clip-path: inset(0 0 0 0);
        }

        .screen__background__shape {
            transform: rotate(45deg);
            position: absolute;
        }

        .screen__background__shape1 {
            height: 520px;
            width: 520px;
            background: #FFF;
            top: -50px;
            right: 120px;
            border-radius: 0 72px 0 0;
        }

        .screen__background__shape2 {
            height: 220px;
            width: 220px;
            background: #00BCD4;
            top: -172px;
            right: 0;
            border-radius: 32px;
        }

        .screen__background__shape3 {
            height: 540px;
            width: 190px;
            background: linear-gradient(270deg, #4DD0E1, #00ACC1);
            top: -24px;
            right: 0;
            border-radius: 32px;
        }

        .screen__background__shape4 {
            height: 400px;
            width: 200px;
            background: #80DEEA;
            top: 420px;
            right: 50px;
            border-radius: 60px;
        }

        .login {
            width: 320px;
            padding: 30px;
            padding-top: 106px;
        }

        .login__field {
            padding: 20px 0px;
            position: relative;
        }

        .login__icon {
            position: absolute;
            top: 30px;
            color: #4DD0E1;
        }

        .login__input {
           /* text-transform: lowercase;  Convierte el texto a minúsculas al mostrarlo */
            border: none;
            border-bottom: 2px solid #B2EBF2;
            background: none;
            padding: 10px;
            padding-left: 24px;
            font-weight: 700;
            width: 75%;
            transition: .2s;
            color: #000000;
        }

        .login__input:active,
        .login__input:focus,
        .login__input:hover {
            outline: none;
            border-bottom-color: #00BCD4;
        }

        .login__submit {
            background: #fff;
            font-size: 14px;
            margin-top: 30px;
            padding: 16px 20px;
            border-radius: 26px;
            border: 1px solid #B2EBF2;
            text-transform: uppercase;
            font-weight: 700;
            display: flex;
            align-items: center;
            width: 100%;
            color: #00ACC1;
            box-shadow: 0px 2px 2px #4FC3F7;
            cursor: pointer;
            transition: .2s;
        }

        .login__submit:active,
        .login__submit:focus,
        .login__submit:hover {
            border-color: #00BCD4;
            outline: none;
        }

        .button__icon {
            font-size: 24px;
            margin-left: auto;
            color: #4DD0E1;
        }

        .via {
            margin-bottom: 10px;
            /* Reduce el espacio debajo de "Log in Via" */
        }


        .social-login {
            position: absolute;
            height: 90px;
            width: 160px;
            text-align: center;
            bottom: 0px;
            right: 0px;
            color: #fff;
            top: 475px;
        }

        .social-icons {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .social-login__icon {
            padding: 5px 10px;
            color: #fff;
            text-decoration: none;
            text-shadow: 0px 0px 8px #4DD0E1;
        }

        .social-login__icon:hover {
            transform: scale(1.5);
        }

        .link {
            color: #000000;
            /* Color de los enlaces */
            text-decoration: underline;
            /* Subrayado para que parezca un link */
            transition: color 0.2s;
            /* Efecto de transición */
        }

        .link:hover {
            color: #494747;
            /* Color al pasar el ratón */
        }
    </style>

    <div class="container">
        <div class="screen">
            <div class="screen__content">
                <form method="POST" action="{{ route('login') }}" class="login">
                    @csrf
                    <div class="login__field">
                        <i class="login__icon fas fa-user"></i>
                        <x-text-input id="email" class="login__input" type="email" name="email"
                            :value="old('email')" placeholder="User name / Email" required autofocus 
                            oninput="this.value" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    
                    <div class="login__field">
                        <i class="login__icon fas fa-lock"></i>
                        <x-text-input id="password" class="login__input" type="password" name="password" required
                            placeholder="Password" autocomplete="current-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                    <button type="submit" class="button login__submit">
                        <span class="button__text">Iniciar Sesion</span>
                        <i class="button__icon fas fa-chevron-right"></i>
                    </button>
                </form>
                <div class="text-center text-gray-900 font-medium">
                    <strong><a href="{{ route('dashboard') }}" class="link">Iniciar sin Cuenta</a> |
                        <a href="{{ route('register') }}" class="link">¿Crear una Cuenta?</a></strong>
                </div>
                <div class="social-login">
                    <strong class="via">Log in Via</strong>
                    <div class="social-icons">
                        <a href="#" class="social-login__icon fab fa-instagram"></a>
                        <a href="#" class="social-login__icon fab fa-facebook"></a>
                        <a href="#" class="social-login__icon fab fa-twitter"></a>
                    </div>
                </div>
            </div>
            <div class="screen__background">
                <span class="screen__background__shape screen__background__shape4"></span>
                <span class="screen__background__shape screen__background__shape3"></span>
                <span class="screen__background__shape screen__background__shape2"></span>
                <span class="screen__background__shape screen__background__shape1"></span>
            </div>
        </div>
    </div>
</x-guest-layout>
