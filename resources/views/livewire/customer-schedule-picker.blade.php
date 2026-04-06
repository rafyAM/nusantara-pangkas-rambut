<div class="relative w-full">
    <!-- DATE PICKER -->
    <div class="mb-10">
        <h2 class="text-lg font-semibold text-white mb-4 flex items-center">
            <span class="w-1 h-5 bg-yellow-500 rounded-full"></span>
            Pilih Jadwal
        </h2>

        <div class="relative">
            <!-- Scroll fade hints -->
            <div class="absolute left-0 top-0 bottom-3 w-8 bg-gradient-to-r from-zinc-900 to-transparent z-0 pointer-events-none"></div>
            <div class="absolute right-0 top-0 bottom-3 w-8 bg-gradient-to-l from-zinc-900 to-transparent z-0 pointer-events-none"></div>

            <div class="flex gap-4 overflow-x-auto pb-3 px-2 scrollbar-hide scroll-smooth snap-x snap-mandatory relative z-10">
                @foreach($availableDays as $day)
                    <button type="button" wire:key="day-{{ $day['date'] }}" wire:click="selectDate('{{ $day['date'] }}')"
                    class="group relative min-w-[76px] h-[84px] px-2 rounded-2xl flex flex-col items-center justify-center gap-2 flex-shrink-0 snap-start transition-all duration-200
                    {{ $selectedDate === $day['date']
                        ? 'bg-yellow-500 text-gray-900 shadow-md shadow-yellow-500/30 scale-[1.05]'
                        : 'bg-white/5 text-gray-400 border border-white/10 hover:bg-white/10 hover:text-white hover:scale-[1.03]' }}">

                        <span class="text-[11px] font-medium tracking-widest uppercase
                            {{ $selectedDate === $day['date'] ? 'text-yellow-900/70' : 'text-gray-500 group-hover:text-gray-300' }}">
                            {{ $day['dayName'] }}
                        </span>

                        <span class="text-xl font-bold leading-none">
                            {{ $day['shortDate'] }}
                        </span>

                        @if($selectedDate === $day['date'])
                            <span class="absolute bottom-2.5 w-4 h-[2px] rounded-full bg-yellow-900/40"></span>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <!-- TIME SLOT -->
    <div class="mb-12">
        <h2 class="text-lg font-semibold text-white mb-5 flex items-center gap-2">
            <span class="w-1 h-6 bg-yellow-500 rounded"></span>
            Pilih Waktu
        </h2>

        <div class="grid grid-cols-3 md:grid-cols-6 gap-3">
            @foreach($slots as $slot)
                <button type="button" wire:key="slot-{{ $slot['time'] }}"
                    @if($slot['available'])
                        @click="showBookingModal=true;selectedTime='{{ $slot['time'] }}';selectedDatetime='{{ $slot['datetime'] }}';selectedDateForModal='{{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y') }}'"
                        class="py-3 rounded-xl bg-gray-800 text-white border border-gray-700 hover:bg-yellow-500 hover:text-black transition font-semibold shadow-md"
                    @else
                        disabled
                        class="py-3 rounded-xl bg-gray-900 text-gray-600 border border-gray-800 cursor-not-allowed"
                    @endif>
                    {{ $slot['time'] }}
                </button>
            @endforeach
        </div>
    </div>
</div>
