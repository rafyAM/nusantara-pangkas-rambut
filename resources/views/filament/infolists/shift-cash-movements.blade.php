@php
    /** @var \App\Models\CashierShift $record */
    $movements = $record->cashMovements()->orderBy('created_at')->get();
    $totalIn   = $movements->where('type', 'in')->sum('amount');
    $totalOut  = $movements->where('type', 'out')->sum('amount');
@endphp

@if($movements->isEmpty())
    <p class="text-sm text-gray-500 italic py-2">Tidak ada cash in/out pada shift ini.</p>
@else
    <div class="space-y-2 mb-4">
        @foreach($movements as $movement)
            <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold
                        {{ $movement->type === 'in' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $movement->type === 'in' ? '▲ Cash In' : '▼ Cash Out' }}
                    </span>
                    <div>
                        <p class="text-sm text-gray-700">{{ $movement->reason }}</p>
                        <p class="text-xs text-gray-400">{{ $movement->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                <span class="font-semibold {{ $movement->type === 'in' ? 'text-green-700' : 'text-red-700' }}">
                    {{ $movement->type === 'in' ? '+' : '-' }} Rp {{ number_format($movement->amount, 0, ',', '.') }}
                </span>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-2 gap-4 pt-2 border-t-2 border-gray-200">
        <div class="text-center p-3 bg-green-50 rounded-lg">
            <p class="text-xs text-green-600 mb-1">Total Cash In</p>
            <p class="font-bold text-green-800">+ Rp {{ number_format($totalIn, 0, ',', '.') }}</p>
        </div>
        <div class="text-center p-3 bg-red-50 rounded-lg">
            <p class="text-xs text-red-600 mb-1">Total Cash Out</p>
            <p class="font-bold text-red-800">- Rp {{ number_format($totalOut, 0, ',', '.') }}</p>
        </div>
    </div>
@endif
