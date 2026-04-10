<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-white mb-2">Selamat Datang Kembali</h2>
        <p class="text-gray-400 text-sm">Masuk untuk mengelola jadwal cukur Anda.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Alamat Email" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="contoh@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-400" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex justify-between items-center mb-2">
                <x-input-label for="password" value="Kata Sandi" class="!mb-0" />
                @if (Route::has('password.request'))
                    <a class="text-xs text-yellow-500 hover:text-yellow-400 hover:underline transition-colors" href="{{ route('password.request') }}">
                        Lupa sandi?
                    </a>
                @endif
            </div>

            <x-text-input id="password"
                            type="password"
                            name="password"
                            required autocomplete="current-password" placeholder="••••••••" />

            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-400" />
        </div>

        <!-- Remember Me -->
        <div class="block">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <input id="remember_me" type="checkbox" class="rounded bg-gray-800/60 border-gray-600 text-yellow-500 shadow-sm focus:ring-yellow-500 focus:ring-offset-gray-900 transition-all cursor-pointer" name="remember">
                <span class="ms-3 text-sm text-gray-400 group-hover:text-gray-300 transition-colors">Ingat saya di perangkat ini</span>
            </label>
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full">
                Masuk Sekarang
            </x-primary-button>
        </div>
        
        <div class="text-center mt-6 text-sm text-gray-400">
            Belum punya akun? 
            <a href="{{ route('register') }}" class="text-yellow-500 hover:text-yellow-400 font-semibold hover:underline transition-colors">Daftar di sini</a>
        </div>
    </form>
</x-guest-layout>
