<x-app-layout>
    <x-slot name="header"></x-slot>

    <div x-data="{
        showBookingModal: false,
        selectedTime: '',
        selectedDatetime: '',
        bookedCapsters: {{ Js::from($slotBookedCapsters) }},
        allCapsters: {{ Js::from($capsters->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'position' => $c->position])) }},
        get availableCapsters() {
            if (!this.selectedDatetime) return this.allCapsters;
            const booked = this.bookedCapsters[this.selectedDatetime] || [];
            return this.allCapsters.filter(c => !booked.includes(c.id));
        }
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
                @if($customer->loyalty_points > 0)
                <div class="flex items-center gap-2 bg-yellow-500/10 border border-yellow-500/30 px-4 py-2 rounded-2xl">
                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <span class="text-yellow-400 font-semibold text-sm">{{ number_format($customer->loyalty_points) }} poin</span>
                </div>
                @endif
            </div>

            <x-reservation.alerts />

            <x-reservation.branch-date-picker
                :branches="$branches"
                :selectedBranchId="$selectedBranchId"
                :selectedDate="$selectedDate"
                :availableDays="$availableDays"
            />

            <x-reservation.time-slots :slots="$slots" />

            <x-reservation.upcoming-bookings :upcomingReservations="$upcomingReservations" />

            <x-reservation.history :recentHistory="$recentHistory" :selectedBranchId="$selectedBranchId" />

        </div>

        <x-reservation.booking-modal
            :selectedDate="$selectedDate"
            :selectedBranchId="$selectedBranchId"
            :branches="$branches"
        />

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
