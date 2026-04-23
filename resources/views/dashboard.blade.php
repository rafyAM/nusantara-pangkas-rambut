<x-app-layout>
    <x-slot name="header"></x-slot>

    <div x-data="{
        showBookingModal: false,
        selectedTime: '',
        selectedDatetime: '',
        selectedDatetime: '',
    }" class="min-h-screen bg-gradient-to-br from-gray-950 via-gray-900 to-gray-950 py-8 px-4">

        <div class="max-w-7xl mx-auto">

            <!-- WELCOME CARD -->
            <div class="mb-8 p-6 rounded-3xl bg-white/5 backdrop-blur-lg border border-white/10 shadow-xl flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-white">
                        Halo, {{ Auth::guard('customer')->user()->name }} 👋
                    </h1>
                    <p class="text-gray-400 mt-1">
                        Siap tampil lebih fresh hari ini?
                    </p>
                </div>
                @php $customer = Auth::guard('customer')->user(); @endphp
                <div class="flex items-center gap-3">
                    @if($customer->loyalty_points > 0)
                    <div class="flex items-center gap-2 bg-yellow-500/10 border border-yellow-500/30 px-4 py-2 rounded-2xl">
                        <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <span class="text-yellow-400 font-bold text-sm">{{ $customer->loyalty_points }} Poin</span>
                    </div>
                    @endif

                    <button class="relative bg-gray-800/70 hover:bg-gray-700 transition p-3 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-yellow-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                        <div class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></div>
                    </button>
                </div>
            </div>

            @if(session('success'))
            <div class="mb-4 bg-green-500/10 border border-green-500/50 text-green-400 px-4 py-3 rounded-xl relative">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
            @endif

            @if($errors->any())
            <div class="mb-4 bg-red-500/10 border border-red-500/50 text-red-400 px-4 py-3 rounded-xl relative">
                <ul class="list-disc pl-5 text-sm">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @php
                $isKapsterSelected = !empty($selectedEmployeeId);
            @endphp

            <!-- STEPPER PROGRESS UI -->
            <div class="mb-10 w-full max-w-3xl mx-auto px-4 sm:px-10">
                <div class="flex items-start justify-between relative">
                    <!-- Connecting Line Base -->
                    <div class="absolute left-8 right-8 top-5 h-[3px] bg-gray-800 z-0 rounded-full"></div>
                    
                    <!-- Active Line (Progress) -->
                    <div class="absolute left-8 top-5 h-[3px] bg-yellow-500 z-0 rounded-full transition-all duration-700 ease-in-out" 
                         x-bind:style="showBookingModal ? 'width: calc(100% - 4rem)' : '{{ $isKapsterSelected ? 'calc(66.66% - 2.5rem)' : (!empty($selectedDate) ? 'calc(33.33% - 1.5rem)' : '0%') }}'"></div>

                    <!-- Step 1: Cabang -->
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold shadow-[0_0_15px_rgba(234,179,8,0.4)] transition-all duration-300 {{ !empty($selectedBranchId) ? 'bg-yellow-500 text-black' : 'bg-gray-950 border-[3px] border-yellow-500 text-yellow-500' }}">
                            @if(!empty($selectedBranchId))
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            @else
                                <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                            @endif
                        </div>
                        <p class="mt-3 text-sm font-bold {{ !empty($selectedBranchId) ? 'text-white' : 'text-yellow-500' }}">Cabang</p>
                        <p class="text-xs font-medium tracking-wide {{ !empty($selectedBranchId) ? 'text-yellow-500' : 'text-yellow-400/80 animate-pulse' }}">{{ !empty($selectedBranchId) ? 'Selesai' : 'Sedang Pilih' }}</p>
                    </div>

                    <!-- Step 2: Tanggal -->
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold border-[3px] transition-all duration-300
                            {{ !empty($selectedDate) 
                                ? 'bg-yellow-500 border-yellow-500 text-black shadow-[0_0_15px_rgba(234,179,8,0.4)]' 
                                : (!empty($selectedBranchId) ? 'bg-gray-950 border-yellow-500 shadow-[0_0_15px_rgba(234,179,8,0.4)]' : 'bg-gray-950 border-gray-800 text-gray-600') }}">
                            @if(!empty($selectedDate))
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            @elseif(!empty($selectedBranchId))
                                <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                            @endif
                        </div>
                        <p class="mt-3 text-sm font-bold {{ !empty($selectedDate) ? 'text-white' : (!empty($selectedBranchId) ? 'text-yellow-500' : 'text-gray-600') }}">Tanggal</p>
                        <p class="text-xs font-medium tracking-wide {{ !empty($selectedDate) ? 'text-yellow-500' : (!empty($selectedBranchId) ? 'text-yellow-400/80 animate-pulse' : 'text-gray-600') }}">{{ !empty($selectedDate) ? 'Selesai' : (!empty($selectedBranchId) ? 'Sedang Pilih' : 'Menunggu') }}</p>
                    </div>

                    <!-- Step 3: Kapster -->
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold border-[3px] transition-all duration-300
                            {{ $isKapsterSelected 
                                ? 'bg-yellow-500 border-yellow-500 text-black shadow-[0_0_15px_rgba(234,179,8,0.4)]' 
                                : (!empty($selectedDate) ? 'bg-gray-950 border-yellow-500 shadow-[0_0_15px_rgba(234,179,8,0.4)]' : 'bg-gray-950 border-gray-800 text-gray-600') }}">
                            @if($isKapsterSelected)
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            @elseif(!empty($selectedDate))
                                <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                            @endif
                        </div>
                        <p class="mt-3 text-sm font-bold {{ $isKapsterSelected ? 'text-white' : (!empty($selectedDate) ? 'text-yellow-500' : 'text-gray-600') }}">Kapster</p>
                        <p class="text-xs font-medium tracking-wide {{ $isKapsterSelected ? 'text-yellow-500' : (!empty($selectedDate) ? 'text-yellow-400/80 animate-pulse' : 'text-gray-600') }}">{{ $isKapsterSelected ? 'Selesai' : (!empty($selectedDate) ? 'Sedang Pilih' : 'Menunggu') }}</p>
                    </div>

                    <!-- Step 4: Waktu -->
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold border-[3px] transition-all duration-300"
                             x-bind:class="{
                                'bg-yellow-500 border-yellow-500 text-black shadow-[0_0_15px_rgba(234,179,8,0.4)]': showBookingModal,
                                'bg-gray-950 border-yellow-500 shadow-[0_0_15px_rgba(234,179,8,0.4)]': !showBookingModal && {{ $isKapsterSelected ? 'true' : 'false' }},
                                'bg-gray-950 border-gray-800 text-gray-600': !{{ $isKapsterSelected ? 'true' : 'false' }}
                             }">
                             <template x-if="showBookingModal">
                                 <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                             </template>
                             <template x-if="!showBookingModal && {{ $isKapsterSelected ? 'true' : 'false' }}">
                                 <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                             </template>
                        </div>
                        <p class="mt-3 text-sm font-bold transition-colors duration-300" 
                           x-bind:class="showBookingModal ? 'text-white' : ({{ $isKapsterSelected ? 'true' : 'false' }} ? 'text-yellow-500' : 'text-gray-600')">Waktu</p>
                        <p class="text-xs font-medium tracking-wide transition-colors duration-300"
                           x-bind:class="showBookingModal ? 'text-yellow-500' : ({{ $isKapsterSelected ? 'true' : 'false' }} ? 'text-yellow-400/80 animate-pulse' : 'text-gray-700')">
                           <span x-text="showBookingModal ? 'Selesai' : ({{ $isKapsterSelected ? 'true' : 'false' }} ? 'Sedang Pilih' : 'Menunggu')"></span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- BRANCH & DATE PICKER -->
            <div class="mb-10">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                    <h2 class="text-lg font-semibold text-white flex items-center">
                        <span class="w-1 h-5 bg-yellow-500 rounded-full mr-2"></span>
                        Pilih Cabang
                    </h2>

                    <form method="GET" action="{{ route('dashboard') }}" id="branchForm" class="w-full sm:w-64">
                        <input type="hidden" name="date" value="{{ $selectedDate }}">
                        <select name="branch_id" onchange="document.getElementById('branchForm').submit()"
                            class="w-full bg-gray-800 text-white rounded-xl border border-gray-700 
                                px-3 py-2 text-sm md:px-4 md:py-2.5 md:text-base 
                                hover:border-gray-500 hover:bg-gray-750 
                                focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 
                                transition-all duration-200 ease-in-out cursor-pointer shadow-sm appearance-none">
                            <option value="" disabled {{ empty($selectedBranchId) ? 'selected' : '' }}>Pilih </option>
                            @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ $selectedBranchId == $branch->id ? 'selected' : '' }}>
                                Cabang: {{ $branch->name }}
                            </option>
                            @endforeach
                        </select>
                    </form>
                </div>

                @if(!empty($selectedBranchId))
                <div class="relative animate-[fadeIn_0.5s_ease-out]">
                    <div class="flex items-center gap-2 mb-3 mt-6">
                        <span class="w-1 h-5 bg-yellow-500 rounded-full"></span>
                        <h2 class="text-md font-semibold text-white">Pilih Tanggal</h2>
                    </div>

                    <div class="absolute left-0 top-10 bottom-3 w-8 bg-gradient-to-r from-zinc-900 to-transparent z-10 pointer-events-none"></div>
                    <div class="absolute right-0 top-10 bottom-3 w-8 bg-gradient-to-l from-zinc-900 to-transparent z-10 pointer-events-none"></div>

                    <div class="flex gap-2.5 overflow-x-auto pb-1 px-8 scrollbar-hide scroll-smooth snap-x snap-mandatory">
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
                @else
                <div class="mt-6 p-6 text-center bg-gray-800/40 border border-dashed border-gray-700/60 rounded-2xl opacity-80 backdrop-blur-sm transition-all duration-300">
                    <div class="w-12 h-12 bg-gray-800/80 text-yellow-500/50 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <h3 class="text-gray-300 font-semibold text-base mb-1">Lokasi Belum Ditentukan</h3>
                    <p class="text-xs text-gray-500">Pilih salah satu cabang pangkas rambut di atas untuk melanjutkan.</p>
                </div>
                @endif
            </div>

            <!-- KAPSTER PICKER -->
            @if(!empty($selectedDate))
            <div class="mb-10 animate-[fadeIn_0.5s_ease-out]">
                <div class="flex items-center gap-2 mb-4">
                    <span class="w-1 h-5 bg-yellow-500 rounded-full"></span>
                    <h2 class="text-lg font-semibold text-white">Pilih Kapster</h2>
                </div>
                <div class="relative">
                    <div class="absolute left-0 top-0 bottom-0 w-8 bg-gradient-to-r from-zinc-900 to-transparent z-10 pointer-events-none"></div>
                    <div class="absolute right-0 top-0 bottom-0 w-8 bg-gradient-to-l from-zinc-900 to-transparent z-10 pointer-events-none"></div>

                    <div class="flex gap-4 overflow-x-auto pb-4 px-8 scrollbar-hide scroll-smooth snap-x snap-mandatory">                      
                        @foreach($capsters as $capster)
                        <a href="{{ route('dashboard', ['date' => $selectedDate, 'branch_id' => $selectedBranchId, 'employee_id' => $capster->id]) }}"
                            class="group relative min-w-[120px] rounded-2xl p-3 flex flex-col items-center justify-center gap-3 flex-shrink-0 snap-center transition-all duration-200 border
                            {{ $selectedEmployeeId == $capster->id
                                ? 'bg-yellow-500 text-black border-yellow-400 shadow-lg shadow-yellow-500/20 scale-[1.05]'
                                : 'bg-white/5 text-gray-300 border-white/10 hover:bg-white/10 hover:text-white hover:scale-[1.03]' }}">
                            
                            <div class="w-12 h-12 rounded-full flexitems-center justify-center font-bold text-xl mb-1
                                {{ $selectedEmployeeId == $capster->id ? 'bg-black/20 text-black' : 'bg-white/10 text-white' }} flex items-center justify-center truncate px-2">
                                {{ strtoupper(substr($capster->name, 0, 1)) }}
                            </div>
                            <span class="text-sm font-semibold truncate w-full text-center">{{ $capster->name }}</span>
                        </a>
                        @endforeach
                        
                        @if($capsters->isEmpty())
                        <div class="text-gray-500 text-sm italic w-full text-center py-4">Belum ada kapster yang aktif di cabang ini.</div>
                        @endif
                    </div>
                </div>
            </div>
            @else
            <div class="mb-10 p-8 text-center bg-gray-800/40 border border-dashed border-gray-700/60 rounded-2xl opacity-80 backdrop-blur-sm transition-all duration-300 hover:opacity-100 group">
                <div class="w-14 h-14 bg-gray-800/80 text-yellow-500/50 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-yellow-500/10 group-hover:text-yellow-500 transition-colors duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <h3 class="text-gray-300 font-semibold text-lg mb-2">Pilih Tanggal Dulu</h3>
                <p class="text-sm text-gray-500 max-w-md mx-auto">Tentukan Tanggal kedatangan di atas untuk melihat siapa saja Kapster profesional kami yang bersedia melayani harimu.</p>
            </div>
            @endif

            <!-- TIME SLOT -->
            <div class="mb-12">
                <h2 class="text-lg font-semibold text-white mb-5 flex items-center gap-2">
                    <span class="w-1 h-6 bg-yellow-500 rounded"></span>
                    Pilih Waktu
                </h2>

                @if($selectedEmployeeId)
                <div class="grid grid-cols-3 md:grid-cols-6 gap-3">
                    @forelse($slots as $slot)
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
                    @empty
                    <div class="col-span-full py-4 text-center text-gray-400 bg-gray-800 rounded-xl border border-gray-700">Tidak ada slot tersedia.</div>
                    @endforelse
                </div>
                @else
                <div class="p-6 text-center bg-yellow-500/10 border border-yellow-500/20 text-yellow-500 rounded-2xl">
                    Silakan pilih <strong class="text-yellow-400">Kapster</strong> terlebih dahulu untuk melihat jadwal ketersediaan waktu potong rambut.
                </div>
                @endif
            </div>

            <!-- UPCOMING BOOKING -->
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
                                <span class="text-xs text-gray-500  ">{{ $reservation->reservation_time->translatedFormat('M') }}</span>
                                <span class="text-xl text-gray-500">{{ $reservation->reservation_time->format('d') }}</span>
                            </div>

                            <div>
                                <h3 class="text-white font-semibold text-lg">
                                    {{ $reservation->reservation_time->format('H:i') }} WIB
                                </h3>
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



        </div>

        <!-- BOOKING MODAL -->
        <div x-show="showBookingModal" style="display:none" class="fixed inset-0 z-[60] flex items-center justify-center px-4">

            <!-- Overlay -->
            <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="showBookingModal=false"></div>

            <!-- Modal -->
            <div x-show="showBookingModal" x-transition class="relative w-full max-w-lg bg-gray-900 rounded-3xl shadow-2xl border border-gray-700">

                <form action="{{ route('reservations.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="reservation_time" x-model="selectedDatetime">

                    <!-- Header -->
                    <div class="flex justify-between items-center px-6 py-4 border-b border-gray-700">
                        <h3 class="text-lg font-bold text-white">Konfirmasi Booking</h3>
                        <button type="button" @click="showBookingModal=false" class="text-gray-400 hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="p-6 space-y-6">

                        <!-- Selected Time -->
                        <div class="bg-gray-800 rounded-2xl p-4 border border-gray-700 flex items-center gap-4">
                            <div class="bg-yellow-500 text-white font-bold text-xl px-4 py-2 rounded-xl" x-text="selectedTime"></div>
                            <div>
                                <p class="text-sm text-gray-400">Jadwal Dipilih</p>
                                <p class="text-white font-semibold">
                                    {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y') }}
                                </p>
                            </div>
                        </div>

                        <!-- Form -->
                        <div class="space-y-5">
                            <!-- Cabang -->
                            <div>
                                <label class="block text-sm text-gray-300 mb-1">Cabang</label>
                                <input type="hidden" name="branch_id" value="{{ $selectedBranchId }}">
                                <input type="text" disabled value="{{ $branches->where('id', $selectedBranchId)->first()->name ?? '' }}" class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-gray-400 cursor-not-allowed">
                            </div>

                            <!-- Capster -->
                            <div>
                                <label class="block text-sm text-gray-300 mb-1">Kapster Terpilih</label>
                                <input type="hidden" name="employee_id" value="{{ $selectedEmployeeId }}">
                                <input type="text" disabled value="{{ $capsters->where('id', $selectedEmployeeId)->first()->name ?? '' }}" class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-yellow-500 font-semibold cursor-not-allowed">
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="flex gap-3 px-6 py-4 border-t border-gray-700">
                        <button type="button" @click="showBookingModal=false" class="w-1/2 py-3 rounded-xl bg-gray-700 text-white hover:bg-gray-600 transition">
                            Batal
                        </button>
                        <button type="submit" class="w-1/2 py-3 rounded-xl bg-yellow-500 text-white font-semibold hover:bg-yellow-400 transition">
                            Konfirmasi
                        </button>
                    </div>

                </form>
            </div>
        </div>

        <style>
            .scrollbar-hide::-webkit-scrollbar { display: none; }
            .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        </style>

        <script>
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('/sw.js').then(function(registration) {
                    if ('Notification' in window) {
                        Notification.requestPermission().then(function(permission) {
                            if (permission === 'granted') initPush();
                        });
                    }
                }).catch(function(err) {
                    console.log('Service worker registration failed:', err);
                });
            }

            function initPush() {
                if (!('PushManager' in window)) return;
                navigator.serviceWorker.ready.then(function(registration) {
                    registration.pushManager.getSubscription().then(function(subscription) {
                        if (!subscription) subscribeUser();
                        else sendSubscriptionToBackend(subscription);
                    });
                });
            }

            function subscribeUser() {
                navigator.serviceWorker.ready.then(function(registration) {
                    const vapidPublicKey = "{{ config('webpush.vapid.public_key') }}";
                    if (!vapidPublicKey) return;
                    const applicationServerKey = urlB64ToUint8Array(vapidPublicKey);
                    registration.pushManager.subscribe({
                        userVisibleOnly: true,
                        applicationServerKey: applicationServerKey
                    }).then(sendSubscriptionToBackend)
                      .catch(function(err) { console.log('Failed to subscribe:', err); });
                });
            }

            function sendSubscriptionToBackend(subscription) {
                fetch('{{ route("push.subscribe") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(subscription)
                });
            }

            function urlB64ToUint8Array(base64String) {
                if (!base64String) return new Uint8Array(0);
                const padding = '='.repeat((4 - base64String.length % 4) % 4);
                const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
                const rawData = window.atob(base64);
                const outputArray = new Uint8Array(rawData.length);
                for (let i = 0; i < rawData.length; ++i) outputArray[i] = rawData.charCodeAt(i);
                return outputArray;
            }
        </script>
    </div>
</x-app-layout>
