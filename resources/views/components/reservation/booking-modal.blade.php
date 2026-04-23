@props(['selectedDate', 'selectedBranchId', 'branches'])

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
                        <input type="text" disabled value="{{ $branches->where('id', $selectedBranchId)->first()->name ?? '' }}"
                            class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-gray-400 cursor-not-allowed">
                    </div>

                    <!-- Capster -->
                    <div>
                        <label class="block text-sm text-gray-300 mb-1">Pilih Kapster</label>
                        <select name="employee_id" class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-yellow-500">
                            <option value="">Siapa saja (tersedia)</option>
                            <template x-for="capster in availableCapsters" :key="capster.id">
                                <option :value="capster.id" x-text="capster.name + ' - ' + capster.position.charAt(0).toUpperCase() + capster.position.slice(1)"></option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex gap-3 px-6 py-4 border-t border-gray-700">
                <button type="button" @click="showBookingModal=false"
                    class="w-1/2 py-3 rounded-xl bg-gray-700 text-white hover:bg-gray-600 transition">
                    Batal
                </button>
                <button type="submit"
                    class="w-1/2 py-3 rounded-xl bg-yellow-500 text-white font-semibold hover:bg-yellow-400 transition">
                    Konfirmasi
                </button>
            </div>

        </form>
    </div>
</div>
