<x-guest-layout>
    <style>
        @import url('https://fonts.googleapis.com/css?family=Raleway:400,700');

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Raleway, sans-serif;
        }

        body {
            background: linear-gradient(90deg, #C7C5F4, #776BCC);
        }

        .container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .screen {
            background: linear-gradient(90deg, #5D54A4, #7C78B8);
            position: relative;
            height: 500px;
            width: 400px;
            box-shadow: 0px 0px 24px #5C5696;
            padding: 30px;
        }

        .screen__content {
            z-index: 1;
            position: relative;
            height: 100%;
        }

        .login__field {
            padding: 20px 0;
            position: relative;
        }

        .login__icon {
            position: absolute;
            top: 30px;
            left: 0;
            /* Asegura que el ícono esté alineado a la izquierda */
            color: #ffffff;
        }

        .login__input {
            border: none;
            border-bottom: 2px solid #D1D1D4;
            background: none;
            padding: 10px;
            padding-left: 40px;
            /* Espacio para el ícono */
            font-weight: 700;
            width: 100%;
            transition: .2s;
        }

        .login__input::placeholder {
            color: rgb(218, 211, 211);
            /* Cambia el color del placeholder a blanco */
            opacity: 1;
            /* Asegura que el color sea completamente opaco */
        }

        .login__input:active,
        .login__input:focus,
        .login__input:hover {
            outline: none;
            border-bottom-color: #6A679E;
        }

        .login__submit {
            background: #fff;
            font-size: 14px;
            margin-top: 30px;
            padding: 16px 20px;
            border-radius: 26px;
            border: 1px solid #D4D3E8;
            text-transform: uppercase;
            font-weight: 700;
            display: flex;
            align-items: center;
            width: 100%;
            color: #4C489D;
            box-shadow: 0px 2px 2px #5C5696;
            cursor: pointer;
            transition: .2s;
        }

        .login__submit:active,
        .login__submit:focus,
        .login__submit:hover {
            border-color: #6A679E;
            outline: none;
        }

        .link {
            color: #000000;
            text-decoration: underline;
            transition: color 0.2s;
        }

        .link:hover {
            color: #494747;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <div class="container">
        <div class="screen">
            <div class="screen__content">
                <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <!-- Email Address -->
                    <div class="login__field">
                        <i class="login__icon fas fa-envelope"></i>
                        <x-text-input id="email" class="login__input" type="email" name="email"
                            :value="old('email')" placeholder="Correo Electrónico" required autofocus />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <x-primary-button class="login__submit">
                            {{ __('Email Password Reset Link') }}
                        </x-primary-button>
                    </div>

                    <div class="text-center mt-4">
                        <a class="link" href="{{ route('login') }}">Volver a Iniciar Sesión</a>
                    </div>
                </form>
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
