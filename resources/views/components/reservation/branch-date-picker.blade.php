@props(['branches', 'selectedBranchId', 'selectedDate', 'availableDays'])

<div class="mb-10">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
        <h2 class="text-lg font-semibold text-white flex items-center">
            <span class="w-1 h-5 bg-yellow-500 rounded-full mr-2"></span>
            Pilih Jadwal & Cabang
        </h2>

        <form method="GET" action="{{ route('dashboard') }}" id="branchForm" class="w-full sm:w-64">
            <input type="hidden" name="date" value="{{ $selectedDate }}">
            <select name="branch_id" onchange="document.getElementById('branchForm').submit()"
                class="w-full bg-gray-800 text-white rounded-xl border border-gray-700
                    px-3 py-2 text-sm md:px-4 md:py-2.5 md:text-base
                    hover:border-gray-500 hover:bg-gray-750
                    focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500
                    transition-all duration-200 ease-in-out cursor-pointer shadow-sm appearance-none">
                @foreach($branches as $branch)
                <option value="{{ $branch->id }}" {{ $selectedBranchId == $branch->id ? 'selected' : '' }}>
                    Cabang: {{ $branch->name }}
                </option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="relative">
        <div class="absolute left-0 top-0 bottom-3 w-8 bg-gradient-to-r from-zinc-900 to-transparent z-10 pointer-events-none"></div>
        <div class="absolute right-0 top-0 bottom-3 w-8 bg-gradient-to-l from-zinc-900 to-transparent z-10 pointer-events-none"></div>

        <div class="flex gap-2.5 overflow-x-auto pb-4 px-8 scrollbar-hide scroll-smooth snap-x snap-mandatory">
            @foreach($availableDays as $day)
            <a href="{{ route('dashboard', ['date' => $day['date'], 'branch_id' => $selectedBranchId]) }}"
                class="group relative min-w-[76px] h-[84px] rounded-2xl flex flex-col items-center justify-center gap-2 px-2 flex-shrink-0 snap-center transition-all duration-200
            {{ $selectedDate === $day['date']
                ? 'bg-yellow-500 text-white shadow-md shadow-yellow-500/30 scale-[1.05]'
                : 'bg-white/5 text-white border border-white/10 hover:bg-white/10 hover:text-white hover:scale-[1.03]' }}">

                <span class="text-[11px] font-medium tracking-widest uppercase
                    {{ $selectedDate === $day['date'] ? 'text-white/70' : 'text-white group-hover:text-gray-300' }}">
                    {{ $day['dayName'] }}
                </span>

                <span class="text-xl font-bold leading-none">
                    {{ $day['shortDate'] }}
                </span>

                @if($selectedDate === $day['date'])
                <span class="absolute bottom-3 w-4 h-[5px] rounded-full bg-yellow-900/40"></span>
                @endif
            </a>
            @endforeach
        </div>
    </div>
</div>
