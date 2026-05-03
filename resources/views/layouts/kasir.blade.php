<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Kasir POS') }} - Kasir</title>

    <!-- Tailwind CSS (via Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="bg-gray-100 text-gray-800 font-sans antialiased">
    <!-- Navbar -->
    <header class="bg-white shadow relative z-50 print:hidden" x-data="{ mobileOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">

                <!-- Logo / Title -->
                <div class="flex-shrink-0 flex items-center font-bold text-lg sm:text-xl text-indigo-600">
                    {{ config('app.name', 'Nusantara Pangkas Rambut') }}
                </div>

                @php
                    $currentShift = \App\Models\CashierShift::where('user_id', auth()->id())->where('status', 'open')->latest()->first();
                @endphp

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-3 text-sm font-medium">
                    @if($currentShift)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                            <span class="w-2 h-2 mr-1.5 rounded-full bg-green-500 animate-pulse"></span>
                            Shift Aktif
                        </span>
                        <span class="text-gray-400 text-xs hidden lg:inline">sejak {{ $currentShift->start_at->timezone(config('app.timezone'))->format('H:i') }}</span>
                        <button onclick="Livewire.dispatch('openCashMovementFromLayout')"
                            class="px-3 py-1 text-xs font-medium rounded-md border border-gray-300 text-gray-600 hover:bg-gray-50 transition">
                            Cash In/Out
                        </button>
                        <button onclick="Livewire.dispatch('openCloseShiftFromLayout')"
                            class="px-3 py-1 text-xs font-medium rounded-md border border-red-300 text-red-600 hover:bg-red-50 transition">
                            Tutup Shift
                        </button>
                        <span class="text-gray-300">|</span>
                    @endif
                    <span class="text-gray-600">Halo, {{ auth()->user()->name ?? 'Kasir' }}</span>
                    <form method="POST" action="{{ route('filament.admin.auth.logout') }}">
                        @csrf
                        <button type="submit" class="text-red-500 hover:text-red-700 underline">Logout</button>
                    </form>
                </div>

                <!-- Mobile: Shift badge + Hamburger -->
                <div class="flex items-center gap-3 md:hidden">
                    @if($currentShift)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                            <span class="w-1.5 h-1.5 mr-1 rounded-full bg-green-500 animate-pulse"></span>
                            Aktif
                        </span>
                    @endif
                    <button @click="mobileOpen = !mobileOpen" class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 transition focus:outline-none" aria-label="Toggle menu">
                        <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg x-show="mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Dropdown Menu -->
        <div x-show="mobileOpen"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="md:hidden bg-white border-t border-gray-100 shadow-lg"
             style="display:none">
            <div class="px-4 py-3 space-y-3">

                <!-- User greeting -->
                <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
                    <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700">{{ auth()->user()->name ?? 'Kasir' }}</span>
                </div>

                @if($currentShift)
                    <!-- Shift info -->
                    <div class="flex items-center justify-between py-1">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                <span class="w-1.5 h-1.5 mr-1 rounded-full bg-green-500 animate-pulse"></span>
                                Shift Aktif
                            </span>
                            <span class="text-xs text-gray-400">sejak {{ $currentShift->start_at->timezone(config('app.timezone'))->format('H:i') }}</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="grid grid-cols-2 gap-2">
                        <button onclick="Livewire.dispatch('openCashMovementFromLayout'); mobileOpen = false"
                            class="flex items-center justify-center gap-1.5 py-2 px-3 text-xs font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Cash In/Out
                        </button>
                        <button onclick="Livewire.dispatch('openCloseShiftFromLayout')"
                            class="flex items-center justify-center gap-1.5 py-2 px-3 text-xs font-medium rounded-lg border border-red-300 text-red-600 hover:bg-red-50 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Tutup Shift
                        </button>
                    </div>
                @endif

                <!-- Logout -->
                <div class="pt-2 border-t border-gray-100">
                    <form method="POST" action="{{ route('filament.admin.auth.logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 py-2.5 px-4 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="py-6">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
            {{ $slot }}
        </div>
    </main>

    @livewireScripts
</body>

</html>