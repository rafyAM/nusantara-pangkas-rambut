<x-app-layout>
    <x-slot name="header">

    </x-slot>

    <div class="min-h-screen bg-gradient-to-br from-gray-950 via-gray-900 to-gray-950 py-8 px-4">
        <div class="max-w-7xl mx-auto">

            <!-- HISTORY CARD -->
            <div class="mb-8 p-6 rounded-3xl bg-white/5 backdrop-blur-lg border border-white/10 shadow-xl flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-white">
                        Riwayat Pemesanan
                    </h1>
                    <p class="text-gray-400 mt-1">
                        Daftar 15 pesanan terakhir Anda.
                    </p>
                </div>
            </div>

            <!-- HISTORY LIST -->
            <div>
                <div class="space-y-4">
                    @forelse($history as $item)
                    <div class="flex justify-between items-center p-5 bg-gray-800 rounded-2xl border border-gray-700 hover:bg-gray-750 hover:border-gray-600 transition shadow-md">
                        <div>
                            <p class="text-lg text-white font-medium mb-1">
                                {{ $item->reservation_time->translatedFormat('l, d F Y - H:i') }} WIB
                            </p>
                            <p class="text-sm text-gray-400 mb-2">
                                Cabang: <span class="font-semibold text-gray-300">{{ $item->branch->name ?? 'Cabang Utama' }}</span> | 
                                Kapster: <span class="font-semibold text-gray-300">{{ $item->employee->name ?? 'Siapa saja' }}</span>
                            </p>
                            <p class="text-sm text-gray-400">
                                Status:
                                <span class="{{ $item->status=='completed' ? 'text-green-500 bg-green-500/10' : 'text-red-500 bg-red-500/10' }} font-bold px-2 py-1 rounded-lg text-xs ml-1 border {{ $item->status=='completed' ? 'border-green-500/20' : 'border-red-500/20' }}">
                                    {{ strtoupper($item->status) }}
                                </span>
                            </p>
                        </div>
                        <a href="{{ route('dashboard',['date'=>now()->format('Y-m-d'), 'branch_id' => $item->branch_id]) }}" class="bg-yellow-500 text-black px-4 py-2 rounded-xl text-sm font-bold shadow hover:bg-yellow-400 hover:scale-[1.05] transition">
                            Rebook
                        </a>
                    </div>
                    @empty
                    <div class="p-8 text-center bg-gray-800/50 border border-gray-700 rounded-2xl">
                        <p class="text-gray-400">Belum ada riwayat pemesanan.</p>
                        <a href="{{ route('dashboard') }}" class="inline-block mt-4 text-yellow-500 font-semibold hover:underline">Mulai Pesan Sekarang</a>
                    </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
