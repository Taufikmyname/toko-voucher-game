<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Banner Section -->
            @if($banners->isNotEmpty())
            <div class="mb-8 relative">
                <div class="swiper-container h-48 md:h-64 rounded-lg overflow-hidden">
                    <div class="swiper-wrapper">
                        @foreach($banners as $banner)
                            <div class="swiper-slide">
                                <a href="{{ $banner->link_url ?? '#' }}">
                                    <img src="{{ asset('storage/' . $banner->image_path) }}" alt="{{ $banner->title }}" class="w-full h-full object-cover">
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
                <!-- Swiper Navigation -->
                <div class="swiper-button-next text-white"></div>
                <div class="swiper-button-prev text-white"></div>
                <!-- Swiper Pagination -->
                <div class="swiper-pagination"></div>
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-semibold mb-6">Pilih Game Favoritmu</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @forelse($games as $game)
                            <a href="{{ route('topup.form', $game->slug) }}" class="block group">
                                <div class="border rounded-lg overflow-hidden transition-all duration-300 group-hover:shadow-lg group-hover:scale-105">
                                    <img src="{{ asset('storage/' . $game->thumbnail) }}" alt="{{ $game->name }}" class="w-full h-48 object-cover">
                                    <p class="text-center font-semibold p-2">{{ $game->name }}</p>
                                </div>
                            </a>
                        @empty
                            <p class="col-span-full text-center">Belum ada game yang tersedia.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var swiper = new Swiper('.swiper-container', {
                loop: true,
                autoplay: {
                    delay: 4000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
            });
        });
    </script>
    @endpush
</x-app-layout>