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
    <header class="bg-white shadow relative z-50 print:hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo / Title -->
                <div class="flex-shrink-0 flex items-center font-bold text-xl text-indigo-600">
                    {{ config('app.name', 'Nusantara Pangkas Rambut') }}
                </div>

                <!-- Shift Status & User Menu -->
                <div class="flex items-center space-x-4 text-sm font-medium">
                    @php
                        $currentShift = \App\Models\CashierShift::where('user_id', auth()->id())->where('status', 'open')->latest()->first();
                    @endphp

                    @if($currentShift)
                        <div class="flex items-center space-x-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                <span class="w-2 h-2 mr-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                Shift Aktif
                            </span>
                            <span class="text-gray-400 text-xs">sejak {{ $currentShift->start_at->timezone(config('app.timezone'))->format('H:i') }}</span>
                            <button onclick="Livewire.dispatch('openCashMovementFromLayout')"
                                class="px-3 py-1 text-xs font-medium rounded-md border border-gray-300 text-gray-600 hover:bg-gray-50 transition">
                                Cash In/Out
                            </button>
                            <button onclick="Livewire.dispatch('openCloseShiftFromLayout')"
                                class="px-3 py-1 text-xs font-medium rounded-md border border-red-300 text-red-600 hover:bg-red-50 transition">
                                Tutup Shift
                            </button>
                        </div>
                        <span class="text-gray-300">|</span>
                    @endif

                    <span class="text-gray-600">Halo, {{ auth()->user()->name ?? 'Kasir' }}</span>
                    <form method="POST" action="{{ route('filament.admin.auth.logout') }}">
                        @csrf
                        <button type="submit" class="text-red-500 hover:text-red-700 underline">Logout</button>
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