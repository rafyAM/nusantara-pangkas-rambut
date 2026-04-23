@props(['slots'])

<div class="mb-12">
    <h2 class="text-lg font-semibold text-white mb-5 flex items-center gap-2">
        <span class="w-1 h-6 bg-yellow-500 rounded"></span>
        Pilih Waktu
    </h2>

    <div class="grid grid-cols-3 md:grid-cols-6 gap-3">
        @foreach($slots as $slot)
        <button
            @if($slot['available'])
            @click="showBookingModal=true;selectedTime='{{ $slot['time'] }}';selectedDatetime='{{ $slot['datetime'] }}'"
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
