<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Kasir POS') }} - Kasir</title>

    <!-- Tailwind CSS (via Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="bg-gray-50 text-gray-800 font-sans antialiased overflow-hidden">
    <div class="flex h-screen overflow-hidden" x-data="{ showSidebar: false }">
        
        <!-- Mobile/Desktop Overlay -->
        <div x-show="showSidebar" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40" @click="showSidebar = false" x-transition.opacity style="display: none;"></div>

        <!-- Sidebar -->
        <aside x-show="showSidebar" 
            x-transition:enter="transition-transform duration-300 ease-in-out"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition-transform duration-300 ease-in-out"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="fixed inset-y-0 left-0 z-50 w-64 md:w-80 lg:w-96 bg-white shadow-2xl flex flex-col print:hidden h-full border-r border-gray-100 flex-shrink-0">
            <!-- Logo -->
            <div class="h-16 flex items-center justify-between px-6 border-b border-gray-100">
                <div class="flex items-center gap-2 font-bold text-base text-gray-900 overflow-hidden">
                    <div class="w-8 h-8 bg-orange-500 text-white rounded-md flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <span class="whitespace-nowrap truncate">Nusantara Pangkas Rambut</span>
                </div>
            </div>

            <!-- Cashier Profile -->
            <div class="p-4">
                <div class="flex items-center gap-3 p-3 rounded-lg border border-gray-100 bg-gray-50 shadow-sm">
                    <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 font-bold overflow-hidden">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Kasir') }}&color=f97316&background=ffedd5" alt="Avatar" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-900 truncate">{{ auth()->user()->name ?? 'Kasir' }}</p>
                        @php
                            $currentShift = \App\Models\CashierShift::where('user_id', auth()->id())->where('status', 'open')->latest()->first();
                        @endphp
                        @if($currentShift)
                            <p class="text-xs text-gray-500 truncate">{{ $currentShift->start_at->timezone(config('app.timezone'))->format('H:i') }} - Aktif</p>
                        @else
                            <p class="text-xs text-gray-500 truncate">Belum buka shift</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-2 space-y-1 overflow-y-auto">
                <a href="{{ route('kasir.pos') }}" 
                    class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition 
                    {{ request()->routeIs('kasir.pos') 
                            ? 'bg-orange-500 text-white shadow-md shadow-orange-500/30' 
                            : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' 
                    }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('kasir.pos') ? 'text-white' : 'text-gray-400' }}" 
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    <span>Cashier</span>
                </a>
                {{-- <a href="{{ route('kasir.report') }}" 
                    class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition 
                    {{ request()->routeIs('kasir.report') 
                            ? 'bg-orange-500 text-white shadow-md shadow-orange-500/30' 
                            : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' 
                    }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('kasir.report') ? 'text-white' : 'text-gray-400' }}" 
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    <span>Report</span>
                </a> --}}
                <a href="{{ route('kasir.transaction-history') }}" 
                    class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition 
                        {{ request()->routeIs('kasir.transaction-history') 
                                ? 'bg-orange-500 text-white shadow-md shadow-orange-500/30' 
                                : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' 
                        }}">                    
                        <svg class="w-5 h-5 {{ request()->routeIs('kasir.transaction-history') ? 'text-white' : 'text-gray-400' }}"  fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    <span>History</span>
                </a>
                <a href="{{ route('kasir.reservations') }}" 
                    class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition 
                        {{ request()->routeIs('kasir.reservations') 
                                ? 'bg-orange-500 text-white shadow-md shadow-orange-500/30' 
                                : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' 
                        }}">                    
                        <svg class="w-5 h-5 {{ request()->routeIs('kasir.reservations') ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    <span>Reservasi</span>
                </a>
                {{-- <a href="{{ route('kasir.supply') }}" 
                    class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition 
                    {{ request()->routeIs('kasir.supply') 
                            ? 'bg-orange-500 text-white shadow-md shadow-orange-500/30' 
                            : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' 
                    }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('kasir.supply') ? 'text-white' : 'text-gray-400' }}" 
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    <span>Supply</span>
                </a> --}}
            </nav>

            <!-- Logout -->
            <div class="p-4 border-t border-gray-100">
                <form method="POST" action="{{ route('filament.admin.auth.logout') }}" class="w-full">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 px-4 py-3 w-full rounded-xl text-orange-500 hover:bg-orange-50 font-medium transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Log out
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col h-full overflow-hidden min-h-0 bg-gray-50">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts

    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').then(function(registration) {
                if ('Notification' in window) {
                    Notification.requestPermission().then(function(permission) {
                        if (permission === 'granted') initPushKasir();
                    });
                }
            }).catch(function(err) {
                console.log('Service worker registration failed:', err);
            });
        }

        function initPushKasir() {
            if (!('PushManager' in window)) return;
            navigator.serviceWorker.ready.then(function(registration) {
                registration.pushManager.getSubscription().then(function(subscription) {
                    if (!subscription) subscribeKasir();
                    else sendSubscriptionToKasirBackend(subscription);
                });
            });
        }

        function subscribeKasir() {
            navigator.serviceWorker.ready.then(function(registration) {
                const vapidPublicKey = "{{ config('webpush.vapid.public_key') }}";
                if (!vapidPublicKey) return;
                const applicationServerKey = urlB64ToUint8Array(vapidPublicKey);
                registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: applicationServerKey
                }).then(sendSubscriptionToKasirBackend)
                  .catch(function(err) { console.log('Failed to subscribe kasir:', err); });
            });
        }

        function sendSubscriptionToKasirBackend(subscription) {
            fetch('{{ route("kasir.push.subscribe") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
</body>

</html>