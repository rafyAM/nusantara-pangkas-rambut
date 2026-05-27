<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- PWA Manifest -->
        <link rel="manifest" href="/manifest.json">
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
            <!-- Bottom Navigation Bar for Mobile/PWA -->
            <div class="md:hidden fixed bottom-0 left-0 z-50 w-full h-16 bg-white border-t border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                <div class="flex h-full max-w-lg mx-auto font-medium">
                    <!-- Home -->
                    <a href="{{ route('dashboard') }}" class="flex-1 inline-flex flex-col items-center justify-center px-2 text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m0 0l-7 7m-7-7v10a1 1 0 001 1h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        <span class="text-xs">Home</span>
                    </a>

                    <!-- History -->
                    <a href="{{ route('history') }}" class="flex-1 inline-flex flex-col items-center justify-center px-2 text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <span class="text-xs">History</span>
                    </a>

                    <!-- Profile -->
                    <a href="{{ route('profile.edit') }}" class="flex-1 inline-flex flex-col items-center justify-center px-2 text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="text-xs">Profile</span>
                    </a>
                </div>
            </div>
        </div>
        @livewireScripts
    </body>
</html>