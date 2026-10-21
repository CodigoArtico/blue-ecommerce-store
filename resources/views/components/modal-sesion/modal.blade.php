<script>
    function togglePopup() {
        var popup = document.getElementById('popup');
        if (popup.classList.contains('hidden')) {
            popup.classList.remove('hidden');
        } else {
            popup.classList.add('hidden');
        }
    }
</script>
<!-- Ventana emergente -->
<div id="popup" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-96 p-6 relative">
        <!-- Botón de cerrar -->
        <button type="button" class="absolute top-2 right-2 text-white text-lg font-semibold hover:text-red-600 
            bg-black rounded-full w-6 h-6 flex items-center justify-center"
            onclick="togglePopup()">X</button>

        <div class="text-center">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">¡Necesitas estar autenticado!</h2>
            <p class="text-gray-600 mb-4">Por favor, inicia sesión o crea una
                cuenta.</p>

            <div class="flex justify-center gap-4">
                <a href="{{ route('login') }}"
                    class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-400 transition"><i class="fa-solid fa-user-lock fa-flip"></i> Iniciar
                    sesión</a>
                <a href="{{ route('register') }}"
                    class=" bg-gray-900 text-white py-2 px-4 rounded-lg hover:bg-gray-700 transition"><i class="fa-solid fa-user-plus fa-flip"></i> Crear
                    cuenta</a>
            </div>
        </div>
    </div>
</div>
