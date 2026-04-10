<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Nusantara Pangkas Rambut') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body { font-family: 'Outfit', sans-serif; }
            .bg-auth {
                background-color: #0a0a0a;
                background-image: radial-gradient(circle at center, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0.9) 100%), url('{{ asset("images/hero-bg.png") }}');
                background-size: cover;
                background-position: center;
                background-attachment: fixed;
            }
            .glass-panel {
                background: rgba(17, 24, 39, 0.4);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.1);
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            }
            /* Override autofill styling for dark theme */
            input:-webkit-autofill,
            input:-webkit-autofill:hover, 
            input:-webkit-autofill:focus, 
            input:-webkit-autofill:active{
                -webkit-box-shadow: 0 0 0 30px #1f2937 inset !important;
                -webkit-text-fill-color: white !important;
            }
        </style>
    </head>
    <body class="font-sans text-gray-200 antialiased selection:bg-yellow-500 selection:text-black">
        <div class="min-h-screen flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8 bg-auth relative overflow-hidden">
            <!-- Overlay -->
            <div class="absolute inset-0 bg-gradient-to-b from-transparent via-gray-900/40 to-gray-950/90 z-0 pointer-events-none"></div>

            <div class="relative z-10 w-full sm:max-w-md animate-[fadeIn_0.5s_ease-out]">
                <!-- Logo area -->
                <div class="flex flex-col items-center justify-center mb-10">
                    <a href="/" class="flex flex-col items-center gap-4 group cursor-pointer outline-none">
                        <div class="w-20 h-20 rounded-full bg-gradient-to-br from-yellow-400 to-yellow-600 flex items-center justify-center shadow-[0_0_20px_rgba(234,179,8,0.3)] group-hover:scale-110 group-hover:shadow-[0_0_30px_rgba(234,179,8,0.6)] transition-all duration-500 ease-out">
                            <svg class="w-10 h-10 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </div>
                        <h1 class="text-3xl font-extrabold tracking-widest text-white group-hover:text-yellow-400 transition-colors duration-300 drop-shadow-md">NUSANTARA</h1>
                        <p class="text-yellow-500/80 text-xs tracking-widest font-semibold uppercase">Premium Barbershop</p>
                    </a>
                </div>

                <div class="w-full px-8 py-10 glass-panel sm:rounded-[2rem] border-t border-l border-white/10 border-b-black/50 border-r-black/50">
                    {{ $slot }}
                </div>
                
                <div class="text-center mt-8 text-gray-500 text-xs font-medium">
                    &copy; {{ date('Y') }} Nusantara Barbershop.
                </div>
            </div>
        </div>
    </body>
</html>
