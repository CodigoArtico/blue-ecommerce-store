<!-- Footer Pegado Abajo -->
<footer class="sticky-footer bg-gradient-to-r from-[#00bcd4] to-[#000000] border-t border-gray-300 shadow-md mt-auto">
    <div class="container mx-auto py-5 px-6 lg:px-10">

        <!-- AnimaciÃ³n de carga -->
        <div class="flex flex-col items-center text-white">
            <div class="mb-3">
                <i class="fa-solid fa-spinner fa-spin text-2xl text-[#ffffff]"></i>
            </div>
            <p class="text-center text-sm md:text-base font-medium max-w-lg">
                🚀 Pronto encontrarás más productos y mejores experiencias de compra.  
                <br>¡Estamos trabajando para ti!
                <br>
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/cf/Flag_of_Peru.svg/1280px-Flag_of_Peru.svg.png"
                                alt="Bandera de PerÃº" class="inline-block h-4">
            </p>
        </div>

        <!-- Redes sociales -->
        <div class="flex justify-center space-x-6 mt-6">
            <!-- TikTok -->
            <a title="tiktok" href="https://www.tiktok.com/@cdigo.rtico" target="_blank" class="text-2xl text-[#000000] hover:text-[#292929] transition-all">
                <i class="fab fa-tiktok"></i>
            </a>
            <!-- Instagram -->
            <a title="instagram" href="{{ config('services.social.instagram', env('INSTAGRAM_URL', 'https://www.instagram.com/codigo_artico/')) }}" target="_blank" class="text-2xl text-[#d61c5d] hover:text-[#000000] transition-all">
                <i class="fab fa-instagram"></i>
            </a>
            <!-- Facebook -->
            <a title="Facebook" href="https://www.facebook.com" target="_blank" class="text-2xl text-[#1877F2] hover:text-[#0e5aab] transition-all">
                <i class="fab fa-facebook"></i>
            </a>
            <!-- WhatsApp -->
            <a title="whatsapp" href="https://wa.me/{{ $pageWeb->telefono }}" target="_blank" class="text-2xl text-[#25d366] hover:text-[#000000] transition-all">
                <i class="fab fa-whatsapp"></i>
            </a>
        </div>

        <!-- LÃ­nea divisoria -->
        <div class="mt-4 border-t border-gray-400 w-4/5 mx-auto"></div>

        <!-- Derechos reservados -->
        <p class="text-center text-xs text-white mt-2 hover:text-gray-900 transition-all">
            Derechos reservados. &copy; {{ date('Y') }}  
            <strong class="text-black">{{ $pageWeb->nombre }}</strong>
        </p>
    </div>
</footer>
