<!-- Modal de Edición -->
<div id="edit-modal" class="fixed inset-0 bg-opacity-50 hidden justify-center items-center z-50">
    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-lg mx-auto relative">
        <!-- Cerrar Modal -->
        <button id="close-edit-modal"
            class="absolute top-4 right-4 text-gray-600 hover:text-gray-800 transition duration-200 ease-in-out">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                </path>
            </svg>
        </button>

        <h3 class="text-2xl font-semibold text-gray-900 text-center mb-6">Editar Perfil</h3>

        <!-- Formulario de Edición -->
        <form id="profile-update-form" enctype="multipart/form-data">
            @csrf
            @method('patch')

            <!-- Campos del formulario -->
            <div class="space-y-4">
                <div class="space-y-2">
                    <x-input-label for="name" :value="__('Nombre')" class="text-black dark:text-gray-900" />
                    <x-text-input id="name" name="name" type="text"
                        class="mt-1 block w-full border-gray-300 rounded-lg p-3 bg-white text-black dark:bg-white dark:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        :value="$user->name" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-600 dark:text-gray-900" />
                </div>

                <div class="space-y-2">
                    <x-input-label for="email" :value="__('Correo Electrónico')" class="text-black dark:text-gray-900" />
                    <x-text-input id="email" name="email" type="email"
                        class="mt-1 block w-full border-gray-300 rounded-lg p-3 bg-white text-black dark:bg-white dark:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        :value="$user->email" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600 dark:text-gray-900" />
                </div>

                <div class="space-y-2">
                    <x-input-label for="password" :value="__('Nueva Contraseña')" class="text-black dark:text-gray-900" />
                    <x-text-input id="password" name="password" type="password"
                        class="mt-1 block w-full border-gray-300 rounded-lg p-3 bg-white text-black dark:bg-white dark:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        minlength="8" placeholder="Nueva contraseña (mínimo 8 caracteres)" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600 dark:text-gray-900" />
                </div>

                <div class="space-y-2">
                    <x-input-label for="telefono" :value="__('Teléfono')" class="text-black dark:text-gray-900" />
                    <x-text-input id="telefono" name="telefono" type="text"
                        class="mt-1 block w-full border-gray-300 rounded-lg p-3 bg-white text-black dark:bg-white dark:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        :value="$user->telefono" />
                    <x-input-error :messages="$errors->get('telefono')" class="mt-2 text-red-600 dark:text-gray-900" />
                </div>

                <div class="space-y-2">
                    <x-input-label for="direccion" :value="__('Dirección')" class="text-black dark:text-gray-900" />
                    <x-text-input id="direccion" name="direccion" type="text"
                        class="mt-1 block w-full border-gray-300 rounded-lg p-3 bg-white text-black dark:bg-white dark:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        :value="$user->direccion" />
                    <x-input-error :messages="$errors->get('direccion')" class="mt-2 text-red-600 dark:text-gray-900" />
                </div>

                <div class="space-y-2">
                    <x-input-label for="img" :value="__('Imagen de Perfil')" class="text-black dark:text-gray-900" />
                    <input type="file" id="img" name="img"
                        class="mt-1 block w-full border-gray-300 rounded-lg p-3 bg-white text-black dark:bg-white dark:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        accept="image/*">
                    <x-input-error :messages="$errors->get('img')" class="mt-2 text-red-600 dark:text-gray-900" />
                </div>

                <div class="flex justify-center gap-4 mt-6">
                    <button type="submit"
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none transition duration-200 ease-in-out">
                        <i class="fa-solid fa-floppy-disk mr-1"></i>Guardar Cambios
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Script para abrir y cerrar el modal -->
<script>
    document.getElementById('open-edit-modal').addEventListener('click', function() {
        document.getElementById('edit-modal').classList.remove('hidden');
    });

    document.getElementById('close-edit-modal').addEventListener('click', function() {
        document.getElementById('edit-modal').classList.add('hidden');
    });

    document.getElementById('profile-update-form').addEventListener('submit', function(e) {
        e.preventDefault(); // Evita que se envíe el formulario de manera tradicional.

        let formData = new FormData(this); // Crea un FormData con los datos del formulario.

        fetch("{{ route('profile.update') }}", {
                method: 'POST', // Usamos POST para enviar los datos.
                headers: {
                    'X-Requested-With': 'XMLHttpRequest', // Asegura que es una petición AJAX.
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content') // CSRF Token.
                },
                body: formData // Los datos del formulario.
            })
            .then(response => response.json()) // Procesamos la respuesta JSON del servidor.
            .then(data => {
                if (data.success) {
                    reproducirSonidoSuccess(); // Reproduce un sonido de éxito.
                    mostrarToast('success', data.message); // Muestra un mensaje de éxito.
                    setTimeout1medio5sg(() => {
                        // console.log("Se ejecutó el callback y la página se recargará.");
                    }, 1000);
                } else {
                    alert('Error: ' + data.message); // Muestra un mensaje de error si ocurre un problema.
                }
            })
            .catch(error => {
                console.error('Error en la actualización del perfil:', error);
            });
    });
</script>
