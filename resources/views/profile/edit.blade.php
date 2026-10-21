<x-app-layout>
    @section('title', 'Perfil')

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Perfil del Usuario -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl mx-auto">
                    <a href="{{ config('services.telegram.url', env('TELEGRAM_URL', 'https://t.me/codigoartico')) }}?text=Hola%2C+me+interesa+adquirir+la+plataforma+web+ecommerce."
                        class="bg-green-400 text-white hover:bg-green-600 text-sm py-2 px-4 rounded transition duration-300 flex items-center justify-center">
                        <i class="fa-brands fa-whatsapp mr-2"></i> Quiero esta tienda
                    </a>
                    <section class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow-lg mt-2">
                        <header class="text-center mb-6">
                            <h2 class="text-3xl font-semibold text-gray-900">
                                Información del Perfil
                            </h2>
                            <p class="mt-2 text-sm text-gray-600">
                                Visualiza y actualiza tu información personal.
                            </p>
                        </header>

                        <!-- Información del Perfil -->
                        <div class="mt-6 flex items-center justify-center space-x-8">
                            <!-- Imagen de Perfil -->
                            <div class="w-32 h-32 rounded-full overflow-hidden bg-gray-300">
                                <img src="{{ $user->img ? asset('storage/img_user/' . $user->img) : asset('images/user.png') }}"
                                    alt="Imagen de Perfil" class="w-full h-full object-cover">
                            </div>
                            <!-- Información del Usuario -->
                            <div class="space-y-4 text-gray-900">
                                <h3 class="text-xl font-semibold text-gray-900"><i
                                        class="fa-solid fa-circle-user mr-1"></i>{{ $user->name }}</h3>
                                <p class="text-sm text-gray-900"><i
                                        class="fa-solid fa-envelope-circle-check mr-1"></i>{{ $user->email }}</p>
                                <div class="flex items-center space-x-3 text-gray-900">
                                    <i class="fas fa-phone-alt"></i>
                                    <p class="text-sm">{{ $user->telefono ?? 'No disponible' }}</p>
                                </div>
                                <div class="flex items-center space-x-3 text-gray-900">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <p class="text-sm">{{ $user->direccion ?? 'No disponible' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-center gap-6">
                            <!-- Botón Editar Perfil -->
                            <button id="open-edit-modal"
                                class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                                <i class="fa-regular fa-pen-to-square mr-1"></i>Editar
                            </button>

                            <!-- Botón Cerrar Sesión -->
                            <form method="POST" action="{{ route('logout') }}" class="w-auto">
                                @csrf
                                <button type="submit"
                                    class="flex items-center px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100">
                                    <i class="fa-solid fa-sign-out-alt mr-2"></i> Cerrar sesión
                                </button>
                            </form>
                        </div>

                    </section>
                </div>
            </div>
        </div>
    </div>
    {{-- INCLUIR EL MODAL Y JS PARA FUNCION EDITAR  --}}
    @include('profile.partials.modal-edit-form')
</x-app-layout>
