{{--
    Top Navigation — serupa referensi D-ASSA (struktur & class disamakan 1:1).

    Palet primary/secondary didefinisikan di resources/css/app.css (@theme)
    dengan basis primary = #007774 agar selaras tema SI-RT.

    Pemakaian:
        @include('layouts.navigation')

    Membutuhkan komponen:
        x-nav-link, x-responsive-nav-link, x-dropdown, x-dropdown-link
    Membutuhkan Alpine.js (sudah dimuat di layouts/app.blade.php).
--}}
<nav x-data="{ open: false }" class="bg-gradient-to-r from-primary to-primary-700 border-b border-primary-800">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-white font-bold text-xl">
                        <img src="{{ site_logo_url() }}" alt="Logo" class="h-8 w-auto object-contain bg-white rounded-md px-1 py-0.5">
                        <span>{{ setting('site_short_name', 'SI-RT') }}</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-4 sm:-my-px sm:ms-10 sm:flex">
                    @if(auth()->user()->hasPermission('view_dashboard'))
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
                            class="text-white hover:text-secondary-200 border-secondary">
                            Beranda
                        </x-nav-link>
                    @endif
                    @if(auth()->user()->hasPermission('view_warga'))
                        <x-nav-link :href="route('warga.index')" :active="request()->routeIs('warga.*')"
                            class="text-white hover:text-secondary-200 border-secondary">
                            Data Warga
                        </x-nav-link>
                    @endif
                    @if(auth()->user()->hasPermission('view_kk'))
                        <x-nav-link :href="route('kartu-keluarga.index')" :active="request()->routeIs('kartu-keluarga.*')"
                            class="text-white hover:text-secondary-200 border-secondary">
                            Kartu Keluarga
                        </x-nav-link>
                    @endif
                    @if(auth()->user()->hasPermission('view_keuangan'))
                        <x-nav-link :href="route('keuangan.pemasukan.index')" :active="request()->routeIs('keuangan.*')"
                            class="text-white hover:text-secondary-200 border-secondary">
                            Keuangan
                        </x-nav-link>
                    @endif
                    @if(auth()->user()->hasPermission('view_kas'))
                        <x-nav-link :href="route('kas-warga.index')" :active="request()->routeIs('kas-warga.*')"
                            class="text-white hover:text-secondary-200 border-secondary">
                            Kas Warga
                        </x-nav-link>
                    @endif
                    @if(auth()->user()->hasPermission('view_inventaris'))
                        <x-nav-link :href="route('inventaris.index')" :active="request()->routeIs('inventaris.*', 'peminjaman-inventaris.*')"
                            class="text-white hover:text-secondary-200 border-secondary">
                            Inventaris
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white hover:text-secondary-200 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.index')">Profil</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                Keluar
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-secondary-200 hover:bg-primary-700 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @if(auth()->user()->hasPermission('view_dashboard'))
                <x-responsive-nav-link :href="route('dashboard')"
                    :active="request()->routeIs('dashboard')">Beranda</x-responsive-nav-link>
            @endif
            @if(auth()->user()->hasPermission('view_warga'))
                <x-responsive-nav-link :href="route('warga.index')"
                    :active="request()->routeIs('warga.*')">Data Warga</x-responsive-nav-link>
            @endif
            @if(auth()->user()->hasPermission('view_kk'))
                <x-responsive-nav-link :href="route('kartu-keluarga.index')"
                    :active="request()->routeIs('kartu-keluarga.*')">Kartu Keluarga</x-responsive-nav-link>
            @endif
            @if(auth()->user()->hasPermission('view_keuangan'))
                <x-responsive-nav-link :href="route('keuangan.pemasukan.index')"
                    :active="request()->routeIs('keuangan.*')">Keuangan</x-responsive-nav-link>
            @endif
            @if(auth()->user()->hasPermission('view_kas'))
                <x-responsive-nav-link :href="route('kas-warga.index')"
                    :active="request()->routeIs('kas-warga.*')">Kas Warga</x-responsive-nav-link>
            @endif
            @if(auth()->user()->hasPermission('view_inventaris'))
                <x-responsive-nav-link :href="route('inventaris.index')"
                    :active="request()->routeIs('inventaris.*', 'peminjaman-inventaris.*')">Inventaris</x-responsive-nav-link>
            @endif
        </div>
        <div class="pt-4 pb-1 border-t border-primary-600">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-primary-200">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.index')">Profil</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">Keluar</x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
