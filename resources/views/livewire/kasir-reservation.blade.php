<div class="h-full w-full flex flex-col overflow-hidden">
    {{-- Header --}}
    <div class="p-6 border-b border-gray-200 bg-white">
        <div class="flex flex-col lg:flex-row items-start lg:items-center gap-4">
            <button @click="showSidebar = !showSidebar" class="p-3 bg-white border border-gray-200 rounded-full text-gray-600 hover:bg-gray-50 transition shadow-sm flex-shrink-0">
                <svg x-show="!showSidebar" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                <svg x-show="showSidebar" style="display: none;" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                <svg class="w-8 h-8 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Reservasi
            </h1>
        </div>
    </div>

    {{-- <!-- Mobile search -->
    <div class="md:hidden mb-6 relative">
        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama pelanggan..." 
            class="block w-full pl-12 pr-4 py-3 border border-gray-200 rounded-full leading-5 bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 sm:text-sm shadow-sm transition">
    </div> --}}

    <div class="flex-1 overflow-y-auto  p-4">
        @if(count($reservationsData) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($reservationsData as $reservation)
                    <div wire:key="reservation-{{ $reservation->id }}" class="bg-white border border-gray-100 rounded-3xl p-6 shadow-sm flex flex-col hover:shadow-xl hover:shadow-orange-500/5 hover:-translate-y-1 hover:border-orange-200 transition-all">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-2xl bg-orange-50 flex items-center justify-center text-orange-600 border border-orange-100">
                                    <span class="font-black text-lg">{{ $reservation->queue_number }}</span>
                                </div>
                                <div>
                                    <span class="font-bold text-gray-900 block truncate max-w-[120px]">{{ $reservation->customer?->name ?? 'Pelanggan' }}</span>
                                    <span class="text-xs font-medium text-gray-500">{{ $reservation->reservation_time->format('H:i') }} WIB</span>
                                </div>
                            </div>
                            @if($reservation->status === 'pending')
                                <span class="text-xs bg-yellow-100 text-yellow-700 px-3 py-1.5 rounded-lg font-bold border border-yellow-200 uppercase tracking-wider">Pending</span>
                            @elseif($reservation->status === 'arrived')
                                <span class="text-xs bg-green-100 text-green-700 px-3 py-1.5 rounded-lg font-bold border border-green-200 uppercase tracking-wider">Arrived</span>
                            @endif
                        </div>
                        
                        <div class="text-sm text-gray-600 mb-6 bg-gray-50 p-4 rounded-2xl border border-gray-100 flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="font-medium text-gray-900">{{ $reservation->services->count() }} Layanan</span>
                                <span class="text-gray-300">&bull;</span>
                                <span class="font-medium text-gray-500 truncate">{{ $reservation->employee?->name ?? 'Kapster Bebas' }}</span>
                            </div>
                            <p class="text-xs text-gray-500 leading-relaxed">{{ $reservation->services->pluck('name')->join(', ') }}</p>
                        </div>

                        <div class="flex flex-col gap-2 mt-auto">
                            @if($reservation->status === 'pending')
                                <div class="grid grid-cols-2 gap-2">
                                    <button wire:click="cancelReservation({{ $reservation->id }})" class="w-full text-sm bg-white hover:bg-red-50 text-gray-600 hover:text-red-700 py-2.5 rounded-xl font-bold transition border-2 border-gray-100 hover:border-red-200">
                                        Batal
                                    </button>
                                    <button wire:click="approveReservation({{ $reservation->id }})" class="w-full text-sm bg-green-50 hover:bg-green-100 text-green-700 py-2.5 rounded-xl font-bold transition border border-green-200">
                                        Hadir
                                    </button>
                                </div>
                            @elseif($reservation->status === 'arrived')
                                <button wire:click="processToKasir({{ $reservation->id }})" class="w-full text-sm bg-[#E55B13] hover:bg-[#d44c0a] text-white py-3 rounded-xl font-bold transition shadow-lg shadow-[#E55B13]/30">
                                    Proses ke Kasir
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="h-full flex flex-col items-center justify-center text-gray-400 opacity-80 pt-20">
                <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mb-6 border border-gray-100 shadow-sm">
                    <svg class="w-10 h-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <p class="font-bold text-gray-500 text-lg">Belum ada reservasi</p>
                <p class="text-sm mt-2 text-gray-400">Reservasi yang masuk akan muncul di sini</p>
            </div>
        @endif
    </div>
</div>
