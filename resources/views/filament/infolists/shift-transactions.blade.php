@php
    /** @var \App\Models\CashierShift $record */
    $transactions = $record->transactions()
        ->with(['customer', 'employee'])
        ->where('status', 'completed')
        ->orderByDesc('transaction_date')
        ->get();
@endphp

@if($transactions->isEmpty())
    <p class="text-sm text-gray-500 italic py-2">Belum ada transaksi pada shift ini.</p>
@else
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="border-b border-gray-200 text-xs text-gray-500 uppercase">
                    <th class="pb-2 pr-4">Invoice</th>
                    <th class="pb-2 pr-4">Waktu</th>
                    <th class="pb-2 pr-4">Pelanggan</th>
                    <th class="pb-2 pr-4">Kasir</th>
                    <th class="pb-2 pr-4">Metode</th>
                    <th class="pb-2 text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($transactions as $trx)
                    <tr class="hover:bg-gray-50">
                        <td class="py-2 pr-4 font-mono text-xs text-indigo-600">{{ $trx->invoice_number }}</td>
                        <td class="py-2 pr-4 text-gray-600 text-xs">{{ $trx->transaction_date->timezone(config('app.timezone'))->format('H:i') }}</td>
                        <td class="py-2 pr-4 text-gray-700">{{ $trx->customer?->name ?? 'Umum' }}</td>
                        <td class="py-2 pr-4 text-gray-700">{{ $trx->employee?->name ?? '-' }}</td>
                        <td class="py-2 pr-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                {{ $trx->payment_method === 'cash' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ strtoupper($trx->payment_method) }}
                            </span>
                        </td>
                        <td class="py-2 text-right font-semibold text-gray-900">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="border-t-2 border-gray-300">
                    <td colspan="5" class="pt-2 font-bold text-gray-900">Total ({{ $transactions->count() }} transaksi)</td>
                    <td class="pt-2 text-right font-bold text-indigo-600">
                        Rp {{ number_format($transactions->sum('total_amount'), 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
@endif
