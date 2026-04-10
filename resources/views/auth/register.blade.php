<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-white mb-2">Buat Akun Baru</h2>
        <p class="text-gray-400 text-sm">Bergabunglah untuk menikmati layanan potong rambut kelas atas.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" value="Nama Lengkap" />
            <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="John Doe" />
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-400" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <!-- Phone -->
            <div>
                <x-input-label for="phone" value="Nomor WhatsApp" />
                <x-text-input id="phone" type="text" name="phone" :value="old('phone')" required autocomplete="phone" placeholder="08123456789" />
                <x-input-error :messages="$errors->get('phone')" class="mt-2 text-red-400" />
            </div>

            <!-- Gender -->
            <div>
                <x-input-label for="gender" value="Jenis Kelamin" />
                
                <div class="relative">
                    <select id="gender" name="gender" class="w-full bg-gray-800/60 border border-gray-600 text-white rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all duration-300 shadow-inner appearance-none cursor-pointer" required>
                        <option value="" disabled selected>Pilih Salah Satu</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }} class="bg-gray-900 text-white">Laki Laki</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }} class="bg-gray-900 text-white">Perempuan</option>
                    </select>
                    <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-gray-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>

                <x-input-error :messages="$errors->get('gender')" class="mt-2 text-red-400" />
            </div>
        </div>

        <!-- Address -->
        <div>
            <x-input-label for="address" value="Alamat Lengkap" />
            <x-text-input id="address" type="text" name="address" :value="old('address')" required autocomplete="address" placeholder="Jl. Sudirman No. 123" />
            <x-input-error :messages="$errors->get('address')" class="mt-2 text-red-400" />
        </div>

        <!-- Email -->
        <div>
            <x-input-label for="email" value="Alamat Email" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="contoh@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-400" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <!-- Password -->
            <div>
                <x-input-label for="password" value="Kata Sandi" />
                <x-text-input id="password"
                                type="password"
                                name="password"
                                required autocomplete="new-password" placeholder="••••••••" />
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-400" />
            </div>

            <!-- Confirm Password -->
            <div>
                <x-input-label for="password_confirmation" value="Konfirmasi Sandi" />
                <x-text-input id="password_confirmation"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-400" />
            </div>
        </div>

        <div class="pt-4">
            <x-primary-button class="w-full">
                Daftar Pelanggan Baru
            </x-primary-button>
        </div>
        
        <div class="text-center mt-6 text-sm text-gray-400">
            Sudah punya akun? 
            <a href="{{ route('login') }}" class="text-yellow-500 hover:text-yellow-400 font-semibold hover:underline transition-colors">Masuk di sini</a>
        </div>
    </form>
</x-guest-layout>
