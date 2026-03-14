<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-yellow-500 leading-tight">
            {{ __('') }}
        </h2>
    </x-slot>
    
    <!-- Content for Mobile/PWA View -->
    <div class="pb-24 pt-6 px-4 max-w-md mx-auto sm:max-w-7xl sm:px-6 lg:px-8">
        
        <!-- Welcome Section -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-100">Halo, {{ Auth::guard('customer')->user()->name }}!</h1>
            <p class="text-gray-400 mt-1">Siap tampil rapi hari ini?</p>
        </div>

        <!-- Push Notification Prompt (Hidden initially, shown by JS later) -->
        <div id="push-prompt" class="hidden relative overflow-hidden bg-gradient-to-r from-yellow-600 to-yellow-400 rounded-2xl p-5 mb-8 shadow-lg shadow-yellow-500/20">
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-gray-900 text-lg">Aktifkan Notifikasi</h3>
                    <p class="text-gray-900 text-sm opacity-90 leading-tight mt-1">Dapatkan pengingat jadwal cukurmu agar tidak hangus.</p>
                </div>
                <button class="bg-gray-900 text-yellow-400 font-bold px-4 py-2 rounded-xl text-sm whitespace-nowrap ml-4 hover:bg-gray-800 transition">
                    Aktifkan
                </button>
            </div>
            <!-- Decorative circle -->
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-white opacity-20 rounded-full blur-xl"></div>
        </div>

        <!-- Call to Action (Huge Button) -->
        <div class="mb-10">
            <a href="#" class="group relative flex items-center justify-center w-full bg-gray-800 hover:bg-gray-700 border border-gray-700 transition-all duration-300 rounded-3xl p-6 shadow-xl overflow-hidden">
                <!-- Glossy / Gradient Effect -->
                <div class="absolute inset-0 bg-gradient-to-br from-yellow-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="relative z-10 flex flex-col items-center">
                    <div class="bg-yellow-500 text-gray-900 rounded-full p-4 mb-3 shadow-lg shadow-yellow-500/30 group-hover:scale-110 transition-transform duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-white tracking-wide">Booking Sekarang</span>
                </div>
            </a>
        </div>

        <!-- Upcoming Reservation Widget -->
        <div class="mb-10">
            <h2 class="text-lg font-semibold text-gray-300 mb-4 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2 text-yellow-500">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                Jadwal Mendatang
            </h2>
            
            @forelse($upcomingReservations as $reservation)
                <div class="bg-gray-800 border border-gray-700 rounded-2xl p-5 shadow-lg mb-4 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-3">
                        <span class="inline-flex items-center rounded-md bg-yellow-400/10 px-2 py-1 text-xs font-medium text-yellow-500 ring-1 ring-inset ring-yellow-400/20">
                            {{ ucfirst($reservation->status) }}
                        </span>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="flex flex-col items-center justify-center bg-gray-900 rounded-xl w-16 h-16 border border-gray-700 shrink-0">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $reservation->reservation_time->translatedFormat('M') }}</span>
                            <span class="text-2xl font-black text-yellow-500 leading-none mt-1">{{ $reservation->reservation_time->format('d') }}</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white leading-tight">
                                {{ $reservation->reservation_time->format('H:i') }} WIB
                            </h3>
                            <p class="text-sm text-gray-400 mt-1">
                                {{ $reservation->branch->name ?? 'Cabang Utama' }}
                            </p>
                            <p class="text-sm text-gray-300 font-medium mt-1">
                                Kapster: {{ $reservation->employee ? $reservation->employee->user->name : 'Siapa Saja' }}
                            </p>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-700 flex justify-between items-center">
                        <div class="text-sm text-gray-400">
                            Toleransi telat 15 menit
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-gray-800/50 border border-gray-700/50 border-dashed rounded-2xl p-8 text-center">
                    <div class="bg-gray-900 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-gray-600">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                        </svg>
                    </div>
                    <p class="text-gray-400 font-medium">Belum ada jadwal cukur.</p>
                </div>
            @endforelse
        </div>

        <!-- Recent History -->
        <div>
            <h2 class="text-lg font-semibold text-gray-300 mb-4 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2 text-gray-500">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                Riwayat Terakhir
            </h2>
            
            <div class="space-y-3">
                @forelse($recentHistory as $history)
                    <div class="bg-gray-800 rounded-xl p-4 flex items-center justify-between shadow-md">
                        <div>
                            <h4 class="font-bold text-gray-200">{{ $history->reservation_time->translatedFormat('d F Y') }}</h4>
                            <p class="text-xs text-gray-500 mt-1">Status: 
                                <span class="{{ $history->status == 'completed' ? 'text-green-500' : 'text-red-500' }}">{{ ucfirst($history->status) }}</span>
                            </p>
                        </div>
                        <a href="#" class="text-yellow-500 text-sm font-semibold py-2 px-3 rounded-lg bg-gray-900 hover:bg-black transition">Rebook</a>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Belum ada riwayat cukur.</p>
                @endforelse
            </div>
        </div>

    </div>
</x-app-layout>
