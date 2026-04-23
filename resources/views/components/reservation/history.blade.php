@props(['recentHistory', 'selectedBranchId'])

<div>
    <div class="space-y-3">
        @forelse($recentHistory as $history)
        <div class="flex justify-between items-center p-4 bg-gray-800 rounded-xl border border-gray-700 hover:bg-gray-700 transition">
            <div>
                <p class="text-white font-medium">
                    {{ $history->reservation_time->translatedFormat('d M Y H:i') }}
                </p>
                <p class="text-sm text-gray-400">
                    Status:
                    <span class="{{ $history->status == 'completed' ? 'text-green-400' : 'text-red-400' }} font-semibold">
                        {{ ucfirst($history->status) }}
                    </span>
                </p>
            </div>
            <a href="{{ route('dashboard', ['date' => now()->format('Y-m-d'), 'branch_id' => $history->branch_id]) }}"
               class="text-yellow-400 text-sm font-semibold hover:underline">
                Rebook
            </a>
        </div>
        @empty
        <p class="text-gray-500">Belum ada riwayat booking.</p>
        @endforelse
    </div>
</div>
