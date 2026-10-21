<style>
    @keyframes slide-left {
        from {
            transform: translateX(0);
        }
        to {
            transform: translateX(-50%);
        }
    }

    .animate-slide-left {
        animation: slide-left 45s linear infinite;
    }

    .image-container {
    position: relative;
    display: flex;
    align-items: center;
    overflow: hidden;
}

.image-container::before,
.image-container::after {
    content: "";
    position: absolute;
    top: 0;
    width: 10%; /* Ajusta el ancho del desvanecimiento */
    height: 100%;
    z-index: 2;
    pointer-events: none;
}

.image-container::before {
    left: 0;
    background: linear-gradient(to right, rgba(255, 255, 255, 1), rgba(255, 255, 255, 0));
}

.image-container::after {
    right: 0;
    background: linear-gradient(to left, rgba(255, 255, 255, 1), rgba(255, 255, 255, 0));
}

.image-item {
    width: 20rem;
    height: 100%;
    background-size: cover;
    background-position: center;
    flex-shrink: 0;
    -webkit-mask-image: linear-gradient(to right, transparent, black 20%, black 80%, transparent);
    mask-image: linear-gradient(to right, transparent, black 20%, black 80%, transparent);
}

</style>

@props(['externalImages', 'localImages'])

@php
    // Unir imágenes externas y locales (sin necesidad de agregar la URL base nuevamente)
    $allImages = array_merge($externalImages, $localImages);

    // Duplicar las URLs para lograr un efecto continuo
    $doubledImageUrls = array_merge($allImages, $allImages);

    // Calcular el número total de imágenes duplicadas
    $imageCount = count($doubledImageUrls);
@endphp

<div class="p-4 sm:p-2 lg:p-2 rounded-xl overflow-hidden">
    <div class="relative overflow-hidden rounded-xl h-64 md:h-96 bg-gray-200 image-container">
        <div class="flex w-full h-full overflow-hidden">
            <div class="flex animate-slide-left" style="width: calc({{ $imageCount }} * 20rem);">
                @foreach ($doubledImageUrls as $imageUrl)
                    <div class="image-item" style="background-image: url('{{ $imageUrl }}');"></div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    // Convierte los arrays de imágenes en formato JSON
    // const externalImages = @json($externalImages);
    // const localImages = @json($localImages);

    // Imprimir todas las URLs en la consola
    // console.log('External Images:', externalImages);
    // console.log('Local Images:', localImages);
</script>
