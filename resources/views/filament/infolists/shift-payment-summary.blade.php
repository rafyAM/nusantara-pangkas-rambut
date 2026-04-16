@php
    /** @var \App\Models\CashierShift $record */
    $methods = [
        'cash'        => 'Tunai',
        'qris'        => 'QRIS',
        'transfer'    => 'Transfer',
        'e_wallet'    => 'E-Wallet',
        'debit_card'  => 'Debit Card',
        'credit_card' => 'Credit Card',
    ];

    $summary = $record->payments()
        ->selectRaw('method, SUM(amount) as total, COUNT(DISTINCT transaction_id) as trx_count')
        ->groupBy('method')
        ->get()
        ->keyBy('method');

    $totalSales = $summary->sum('total');
    $totalTrx   = $record->transactions()->where('status', 'completed')->count();
@endphp

<div class="space-y-2">
    @forelse($methods as $key => $label)
        @if(isset($summary[$key]))
            <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                        {{ $key === 'cash' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ $label }}
                    </span>
                    <span class="text-xs text-gray-500">{{ $summary[$key]->trx_count }} transaksi</span>
                </div>
                <span class="font-semibold text-gray-900">
                    Rp {{ number_format($summary[$key]->total, 0, ',', '.') }}
                </span>
            </div>
        @endif
    @empty
    @endforelse

    @if($summary->isEmpty())
        <p class="text-sm text-gray-500 italic py-2">Belum ada pembayaran pada shift ini.</p>
    @else
        <div class="flex items-center justify-between pt-3 border-t-2 border-gray-300">
            <div>
                <span class="font-bold text-gray-900">Total Penjualan</span>
                <span class="text-xs text-gray-500 ml-2">{{ $totalTrx }} transaksi</span>
            </div>
            <span class="text-lg font-bold text-indigo-600">
                Rp {{ number_format($totalSales, 0, ',', '.') }}
            </span>
        </div>
    @endif
</div>
