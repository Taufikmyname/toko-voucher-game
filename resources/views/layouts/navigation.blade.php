<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('images/VoucherWaveLogo.png') }}" class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                        {{ __('Beranda') }}
                    </x-nav-link>
                    @auth
                        <x-nav-link :href="route('inbox.index')" :active="request()->routeIs('inbox.index')" class="relative">
                            {{ __('Kotak Masuk') }}
                            @if($unreadMessages > 0)
                                <span class="absolute top-3 -right-1 inline-flex items-center justify-center w-4 h-4 text-xs font-bold text-white bg-red-500 border-2 border-white rounded-full"></span>
                            @endif
                        </x-nav-link>
                        <x-nav-link :href="route('my.transactions')" :active="request()->routeIs('my.transactions')">
                            {{ __('Transaksi Saya') }}
                        </x-nav-link>
                    @endauth
                </div>
            </div>

            <!-- Settings Dropdown / Login & Register Links -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            @if(Auth::user()->is_admin)
                                <div class="border-t border-gray-200"></div>
                                <div class="block px-4 py-2 text-xs text-gray-400">
                                    {{ __('Admin Panel') }}
                                </div>
                                <x-dropdown-link :href="route('admin.dashboard')">
                                    {{ __('Dashboard') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.banners.index')">
                                    {{ __('Manajemen Banner') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.games.index')">
                                    {{ __('Manajemen Game') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.products.index')">
                                    {{ __('Manajemen Produk') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.vouchers.index')">
                                    {{ __('Manajemen Stok Voucher') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.transactions.index')">
                                    {{ __('Manajemen Transaksi') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.users.index')">
                                    {{ __('Manajemen Pengguna') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.promo-notifications.create')">
                                    {{ __('Kirim Notifikasi Promo') }}
                                </x-dropdown-link>
                            @endif

                            <div class="border-t border-gray-200"></div>
                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-gray-700 underline">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="ms-4 text-sm text-gray-700 underline">Register</a>
                    @endif
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                {{ __('Beranda') }}
            </x-responsive-nav-link>
             @auth
                <x-responsive-nav-link :href="route('inbox.index')" :active="request()->routeIs('inbox.index')" class="relative">
                    {{ __('Kotak Masuk') }}
                    @if($unreadMessages > 0)
                        <span class="absolute top-1 right-4 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full">{{ $unreadMessages }}</span>
                    @endif
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('my.transactions')" :active="request()->routeIs('my.transactions')">
                    {{ __('Transaksi Saya') }}
                </x-responsive-nav-link>
            @endauth
        </div>

        <!-- Responsive Settings Options -->
        @auth
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>

            @if(Auth::user()->is_admin)
                <div class="pt-4 pb-1 border-t border-gray-200">
                    <div class="px-4">
                        <div class="font-medium text-base text-gray-800">Admin Panel</div>
                    </div>
                    <div class="mt-3 space-y-1">
                        <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            {{ __('Dashboard') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.banners.index')" :active="request()->routeIs('admin.banners.*')">
                            {{ __('Manajemen Banner') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.games.index')" :active="request()->routeIs('admin.games.*')">
                            {{ __('Manajemen Game') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.products.index')" :active="request()->routeIs('admin.products.*')">
                            {{ __('Manajemen Produk') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.vouchers.index')" :active="request()->routeIs('admin.vouchers.*')">
                            {{ __('Manajemen Stok Voucher') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.transactions.index')" :active="request()->routeIs('admin.transactions.*')">
                            {{ __('Manajemen Transaksi') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                            {{ __('Manajemen Pengguna') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.promo-notifications.create')" :active="request()->routeIs('admin.promo-notifications.*')">
                            {{ __('Kirim Notifikasi Promo') }}
                        </x-responsive-nav-link>
                    </div>
                </div>
            @endif
        @else
            <div class="py-1 border-t border-gray-200">
                <x-responsive-nav-link :href="route('login')">
                    {{ __('Log in') }}
                </x-responsive-nav-link>
                @if (Route::has('register'))
                    <x-responsive-nav-link :href="route('register')">
                        {{ __('Register') }}
                    </x-responsive-nav-link>
                @endif
            </div>
        @endauth
    </div>
</nav>
