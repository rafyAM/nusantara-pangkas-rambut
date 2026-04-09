<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nusantara Pangkas Rambut - Premium Barbershop</title>

    <!-- Tailwind CSS (via Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Outfit', sans-serif; }
        .bg-hero {
            background-color: #0a0a0a;
            background-image: radial-gradient(circle at center, rgba(0,0,0,0) 0%, rgba(0,0,0,0.8) 100%), url('{{ asset("images/hero-bg.png") }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        .text-glow {
            text-shadow: 0 0 20px rgba(234, 179, 8, 0.4);
        }
        .glass-panel {
            background: rgba(17, 24, 39, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="bg-gray-950 text-gray-200 antialiased selection:bg-yellow-500 selection:text-black min-h-screen flex flex-col">

    <!-- Navbar -->
    <nav class="fixed w-full z-50 transition-all duration-300 backdrop-blur-md bg-black/50 border-b border-white/5">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-yellow-400 to-yellow-600 flex items-center justify-center shadow-[0_0_15px_rgba(234,179,8,0.3)]">
                    <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </div>
                <span class="text-xl font-bold text-white tracking-wider">NUSANTARA</span>
            </div>
            
            <div class="hidden md:flex items-center gap-8 text-sm font-medium">
                <a href="#services" class="hover:text-yellow-400 transition-colors">Layanan</a>
                <a href="#about" class="hover:text-yellow-400 transition-colors">Tentang Kami</a>
                <!-- <a href="#contact" class="hover:text-yellow-400 transition-colors">Hubungi</a> -->
            </div>

            <div class="flex items-center gap-4">
                @if (Route::has('login'))
                    @auth('customer')
                        <a href="{{ url('/dashboard') }}" class="px-5 py-2 rounded-full border border-yellow-500/50 text-yellow-400 hover:bg-yellow-500 hover:text-black transition-all font-semibold text-sm">
                            Masuk Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-300 hover:text-white transition hidden sm:block">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-6 py-2.5 rounded-full bg-yellow-500 text-black font-semibold hover:bg-yellow-400 hover:shadow-[0_0_20px_rgba(234,179,8,0.4)] transition-all text-sm shrink-0">
                                Reservasi Sekarang
                            </a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- w-full stretch for Main Body -->
    <main class="flex-grow">
        <!-- Hero Section -->
        <section class="relative h-screen flex items-center justify-center bg-hero overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-b from-gray-950/80 via-gray-900/60 to-gray-950 z-0"></div>
            
            <div class="relative z-10 max-w-4xl mx-auto px-6 text-center pt-20 animate-[fadeIn_1s_ease-out]">
                <span class="inline-block py-1 px-3 rounded-full border border-yellow-500/30 bg-yellow-500/10 text-yellow-400 text-xs font-semibold tracking-widest uppercase mb-6 drop-shadow-lg backdrop-blur-md">Premium Grooming Experience</span>
                
                <h1 class="text-5xl md:text-7xl lg:text-8xl font-extrabold text-white mb-6 leading-tight select-none">
                    Gaya Rambut Terbaik <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 via-yellow-500 to-yellow-600 text-glow">Untuk Pria Sejati.</span>
                </h1>
                
                <p class="text-lg md:text-xl text-gray-300 mb-10 max-w-2xl mx-auto leading-relaxed">
                    Tinggalkan cara lama. Reservasi antrean tanpa ribet secara online, temukan kapster terfavoritmu, dan nikmati standar pelayanan potong rambut tertinggi di kota ini.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <a href="{{ route('register') }}" class="px-8 py-4 rounded-full bg-yellow-500 text-black font-bold text-lg hover:bg-yellow-400 hover:scale-105 hover:shadow-[0_0_30px_rgba(234,179,8,0.5)] transition-all duration-300 w-full sm:w-auto">
                        Buat Jadwal Cukur
                    </a>
                    <a href="#services" class="px-8 py-4 rounded-full glass-panel text-white font-semibold text-lg hover:bg-white/10 hover:border-gray-400 transition-all duration-300 border border-gray-600 w-full sm:w-auto">
                        Lihat Layanan
                    </a>
                </div>
            </div>

            <!-- Scroll Indicator -->
            <div class="absolute bottom-10 left-1/2 -translate-x-1/2 animate-bounce cursor-pointer z-10 hidden md:block opacity-70 hover:opacity-100 transition-opacity">
                <a href="#services">
                    <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                </a>
            </div>
        </section>

        <!-- Features Section -->
        <section id="services" class="py-24 bg-gray-950 relative">
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[80%] h-[1px] bg-gradient-to-r from-transparent via-yellow-900/50 to-transparent"></div>
            
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">Mendefinisikan Ulang <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-yellow-600">Kenyamanan</span></h2>
                    <p class="text-gray-400 max-w-xl mx-auto text-lg leading-relaxed">Kami mengedepankan efisiensi waktu Anda tanpa kompromi pada kualitas potongan rambut berstandar premium.</p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="glass-panel p-10 rounded-3xl hover:bg-white/[0.05] hover:-translate-y-2 transition-all duration-300 group shadow-lg shadow-black/50">
                        <div class="w-16 h-16 bg-yellow-500/10 rounded-2xl flex items-center justify-center mb-8 group-hover:bg-yellow-500/20 group-hover:scale-110 transition-all duration-300 text-yellow-500">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-4">Reservasi Real-time</h3>
                        <p class="text-gray-400 leading-relaxed">
                            Pilih jadwal dan slot waktu presisi tinggi. Tidak perlu lagi menunggu berjam-jam membaca majalah lama di bangku antrean ganti.
                        </p>
                    </div>

                    <!-- Feature 2 (Center Highlight) -->
                    <div class="bg-gradient-to-br from-yellow-900/30 to-gray-900/80 p-10 rounded-3xl border border-yellow-500/30 hover:border-yellow-400 relative overflow-hidden group hover:-translate-y-2 transition-all duration-300 shadow-[0_0_40px_rgba(234,179,8,0.08)]">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-30 group-hover:scale-110 transition-all duration-500">
                            <svg class="w-32 h-32 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                        </div>
                        <div class="relative z-10">
                            <div class="w-16 h-16 bg-gradient-to-br from-yellow-400 to-yellow-500 rounded-2xl flex items-center justify-center mb-8 text-black shadow-lg shadow-yellow-500/40 group-hover:scale-110 transition-all duration-300">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                            <h3 class="text-2xl font-bold text-white mb-4">Kapster Berpengalaman</h3>
                            <p class="text-gray-300 leading-relaxed font-light">
                                Potongan Anda dieksekusi oleh ahlinya. Kenali keahlian masing-masing spesialis dan booking seniman rambut terfavorit Anda lewat aplikasi.
                            </p>
                        </div>
                    </div>

                    <!-- Feature 3 -->
                    <div class="glass-panel p-10 rounded-3xl hover:bg-white/[0.05] hover:-translate-y-2 transition-all duration-300 group shadow-lg shadow-black/50">
                        <div class="w-16 h-16 bg-yellow-500/10 rounded-2xl flex items-center justify-center mb-8 group-hover:bg-yellow-500/20 group-hover:scale-110 transition-all duration-300 text-yellow-500">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-4">Tersebar Luas</h3>
                        <p class="text-gray-400 leading-relaxed">
                            Kami selalu berada lebih dekat dari yang Anda kira. Cek ketersediaan cabang Nusantara terdekat dan saksikan performa kami secara langsung.
                        </p>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- CTA Section -->
        <section class="py-20 bg-yellow-500 relative overflow-hidden">
            <div class="max-w-5xl mx-auto px-6 text-center relative z-10">
                <h2 class="text-4xl md:text-5xl font-extrabold text-black mb-6">Mulai Transformasi Anda Hari Ini.</h2>
                <p class="text-black/80 text-xl md:text-2xl mb-10 max-w-2xl mx-auto font-medium">Buat akun untuk mengelola riwayat pangkas dan perbarui ketampanan secara instan.</p>
                <div class="flex gap-4 justify-center">
                    <a href="{{ route('register') }}" class="px-8 py-4 rounded-full bg-black text-yellow-500 font-bold text-lg hover:bg-gray-900 hover:scale-105 transition-all shadow-xl">
                        Daftar Sebagai Pelanggan
                    </a>
                </div>
            </div>
            
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10 mix-blend-overlay"></div>
        </section>
    </main>

    <!-- Footer -->
    <footer id="contact" class="border-t border-white/10 bg-black pt-20 pb-8 text-center text-gray-500 text-sm">
        <div class="max-w-4xl mx-auto px-6 flex flex-col items-center">
            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-gray-800 to-gray-900 flex items-center justify-center mb-6 border border-gray-700 shadow-lg cursor-pointer hover:scale-110 hover:border-yellow-500 transition-all duration-300">
                <svg class="w-8 h-8 text-yellow-500/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14 5l7 7m0 0l-7-7m7-7H3"></path></svg>
            </div>
            <h4 class="text-2xl font-bold text-white tracking-[0.2em] mb-3">NUSANTARA</h4>
            <p class="mb-10 text-gray-400 font-light tracking-wide text-md">Level Up Your Haircut Game.</p>
            
            <div class="w-24 h-[1px] bg-gray-800 mb-8 w-full"></div>
            <p class="font-light tracking-wider">&copy; {{ date('Y') }} Nusantara Barbershop Inc. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
