<x-guest-layout>
    @section('title', 'Crear Cuenta')

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
            height: 650px;
            width: 400px;
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

        .register {
            width: 320px;
            padding: 30px;
            padding-top: 90px;
        }

        .register__field {
            padding: 20px 0px;
            position: relative;
        }

        .register__icon {
            position: absolute;
            top: 30px;
            color: #4DD0E1;
        }

        .register__input {
            text-transform: lowercase; /* Convierte el texto a minúsculas al mostrarlo */
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

        .register__input:active,
        .register__input:focus,
        .register__input:hover {
            outline: none;
            border-bottom-color: #00BCD4;
        }

        .register__submit {
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

        .register__submit:active,
        .register__submit:focus,
        .register__submit:hover {
            border-color: #00BCD4;
            outline: none;
        }

        .button__icon {
            font-size: 24px;
            margin-left: auto;
            color: #4DD0E1;
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
    
    <div class="container">
        <div class="screen">
            <div class="screen__content">
                <form method="POST" action="{{ route('register') }}" class="register">
                    @csrf

                    <!-- Nombre -->
                    <div class="register__field">
                        <i class="register__icon fas fa-user"></i>
                        <x-text-input id="name" class="register__input" type="text" name="name" :value="old('name')" placeholder="Nombre" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Correo Electrónico -->
                    <div class="register__field">
                        <i class="register__icon fas fa-envelope"></i>
                        <x-text-input id="email" class="register__input" type="email" name="email" :value="old('email')" placeholder="Correo Electrónico" required
                        oninput="this.value = this.value.toLowerCase()"  />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Contraseña -->
                    <div class="register__field">
                        <i class="register__icon fas fa-lock"></i>
                        <x-text-input id="password" class="register__input" type="password" name="password" required
                            minlength="8" placeholder="Contraseña" autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs" />
                    </div>

                    <!-- Confirmar Contraseña -->
                    <div class="register__field">
                        <i class="register__icon fas fa-lock"></i>
                        <x-text-input id="password_confirmation" class="register__input" type="password"
                            name="password_confirmation" required minlength="8" placeholder="Confirmar Contraseña"
                            autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-xs" />
                    </div>

                    <button type="submit" class="button register__submit">
                        <span class="button__text">Registrar</span>
                        <i class="button__icon fas fa-chevron-right"></i>
                    </button>

                    <div class="text-center mt-4">
                        <strong><a class="link" href="{{ route('login') }}">¿Ya estás registrado?</a></strong>
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
