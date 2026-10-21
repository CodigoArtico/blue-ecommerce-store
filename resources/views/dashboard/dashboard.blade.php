<x-app-layout>
    @section('title', 'Inicio')
    @push('styles')
    @endpush
    
    <div class="py-2">
        <div class="mx-auto max-w-screen-2xl sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-2 text-gray-900">
                    <!-- Billboard Component -->
                    @include('dashboard.partials.billboard', ['externalImages' => $externalImages, 'localImages' => $localImages])

                </div>
                @include('dashboard.partials.products')
            </div>
        </div>
    </div>

    @push('scripts')
    @endpush
</x-app-layout>
