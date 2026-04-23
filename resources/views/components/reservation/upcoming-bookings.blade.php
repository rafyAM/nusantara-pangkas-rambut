@props(['upcomingReservations'])

@if($upcomingReservations->isNotEmpty())
<div class="mb-12">
    <h2 class="text-lg font-semibold text-gray-500 mb-5 flex items-center gap-2">
        <span class="w-1 h-6 bg-yellow-500 rounded"></span>
        Booking Mendatang
    </h2>

    <div class="grid md:grid-cols-2 gap-4">
        @foreach($upcomingReservations as $reservation)
        <div class="p-5 rounded-2xl bg-white/5 backdrop-blur border border-white/10 hover:scale-[1.02] transition shadow-lg">
            <div class="flex items-start gap-4">
                <div class="w-16 h-16 bg-yellow-500 text-black rounded-xl flex flex-col items-center justify-center font-bold">
                    <span class="text-xs text-gray-500">{{ $reservation->reservation_time->translatedFormat('M') }}</span>
                    <span class="text-xl text-gray-500">{{ $reservation->reservation_time->format('d') }}</span>
                </div>

                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <h3 class="text-white font-semibold text-lg">
                            {{ $reservation->reservation_time->format('H:i') }} WIB
                        </h3>
                        @if($reservation->queue_number)
                        <span class="text-xs bg-yellow-500/20 text-yellow-400 border border-yellow-500/30 px-2 py-0.5 rounded-full font-mono">
                            {{ $reservation->queue_number }}
                        </span>
                        @endif
                    </div>
                    <p class="text-gray-500 text-sm mt-1">
                        {{ $reservation->branch->name ?? 'Cabang Utama' }}
                    </p>
                    <p class="text-gray-500 text-sm mt-1">
                        Kapster: {{ $reservation->employee?->name ?? 'Siapa saja' }}
                    </p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
