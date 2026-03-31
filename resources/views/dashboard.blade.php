<x-app-layout>
    <x-slot name="header">

    </x-slot>

    <div x-data="{ showBookingModal:false, selectedTime:'', selectedDatetime:'' }" class="min-h-screen bg-gradient-to-br from-gray-950 via-gray-900 to-gray-950 py-8 px-4">
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

                <button class="relative bg-gray-800/70 hover:bg-gray-700 transition p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-yellow-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                    </svg>
                    <div class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></div>
                </button>
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

            <!-- BRANCH & DATE PICKER -->
            <div class="mb-10">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                    <h2 class="text-lg font-semibold text-white flex items-center">
                        <span class="w-1 h-5 bg-yellow-500 rounded-full mr-2"></span>
                        Pilih Jadwal & Cabang
                    </h2>

                    <form method="GET" action="{{ route('dashboard') }}" id="branchForm" class="w-full sm:w-64">
                        <input type="hidden" name="date" value="{{ $selectedDate }}">
                        <select name="branch_id" onchange="document.getElementById('branchForm').submit()" class="w-full bg-gray-800 text-white rounded-xl border border-gray-700 px-4 py-2 focus:ring-yellow-500">
                            @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ $selectedBranchId == $branch->id ? 'selected' : '' }}>Cabang: {{ $branch->name }}</option>
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

            <!-- TIME SLOT -->
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

            <!-- HISTORY -->
            <div>
                <!-- <h2 class="text-lg font-semibold text-gray-200 mb-5 flex items-center gap-2">
                    <span class="text-gray-500"></span>
                    Riwayat Booking
                </h2> -->

                <div class="space-y-3">
                    @forelse($recentHistory as $history)
                    <div class="flex justify-between items-center p-4 bg-gray-800 rounded-xl border border-gray-700 hover:bg-gray-700 transition">
                        <div>
                            <p class="text-white font-medium">
                                {{ $history->reservation_time->translatedFormat('d M Y H:i') }}
                            </p>
                            <p class="text-sm text-gray-400">
                                Status:
                                <span class="{{ $history->status=='completed' ? 'text-green-400' : 'text-red-400' }} font-semibold">
                                    {{ ucfirst($history->status) }}
                                </span>
                            </p>
                        </div>
                        <a href="{{ route('dashboard',['date'=>now()->format('Y-m-d'), 'branch_id' => $history->branch_id]) }}" class="text-yellow-400 text-sm font-semibold hover:underline">
                            Rebook
                        </a>
                    </div>
                    @empty
                    <p class="text-gray-500">Belum ada riwayat booking.</p>
                    @endforelse
                </div>
            </div>

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
                        <div class="space-y-4">
                            <!-- Cabang -->
                            <div>
                                <label class="block text-sm text-gray-300 mb-1">Cabang</label>
                                <input type="hidden" name="branch_id" value="{{ $selectedBranchId }}">
                                <input type="text" disabled value="{{ $branches->where('id', $selectedBranchId)->first()->name ?? '' }}" class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-gray-400 cursor-not-allowed">
                            </div>
                            
                            <!-- Capster -->
                            <div>
                                <label class="block text-sm text-gray-300 mb-1">Pilih Kapster (Opsional)</label>
                                <select name="employee_id" class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-yellow-500">
                                    <option value="">Siapa saja</option>
                                    @foreach($capsters as $capster)
                                    <option value="{{ $capster->id }}">
                                        {{ $capster->name }} - {{ ucfirst($capster->position) }}
                                    </option>
                                    @endforeach
                                </select>
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
            .scrollbar-hide::-webkit-scrollbar {
                display: none;
            }

            .scrollbar-hide {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }
        </style>

        <script>
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('/sw.js').then(function(registration) {
                    console.log('Service Worker registered with scope:', registration.scope);
                    if ('Notification' in window) {
                        Notification.requestPermission().then(function(permission) {
                            if (permission === 'granted') {
                                initPush();
                            }
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
                        if (!subscription) {
                            subscribeUser();
                        } else {
                            // Sinkronkan selalu ke backend setiap kali dashboard dibuka
                            // Ini pencegahan jika kamu mereset database (migrate:fresh) tapi browser belum direset
                            sendSubscriptionToBackend(subscription);
                        }
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
                    }).then(function(subscription) {
                        sendSubscriptionToBackend(subscription);
                    }).catch(function(err) {
                        console.log('Failed to subscribe the user: ', err);
                    });
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
                const base64 = (base64String + padding)
                    .replace(/\-/g, '+')
                    .replace(/_/g, '/');

                const rawData = window.atob(base64);
                const outputArray = new Uint8Array(rawData.length);

                for (let i = 0; i < rawData.length; ++i) {
                    outputArray[i] = rawData.charCodeAt(i);
                }
                return outputArray;
            }
        </script>
    </div>
</x-app-layout>