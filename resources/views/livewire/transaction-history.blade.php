<div class="h-full w-full flex flex-col overflow-hidden">
    {{-- Header --}}
    <div class="p-6 border-b border-gray-200 bg-white">
        <div class="flex flex-col lg:flex-row items-start lg:items-center gap-4">
            <button @click="showSidebar = !showSidebar" class="p-3 bg-white border border-gray-200 rounded-full text-gray-600 hover:bg-gray-50 transition shadow-sm flex-shrink-0">
                <svg x-show="!showSidebar" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                <svg x-show="showSidebar" style="display: none;" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                    <svg class="w-8 h-8 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Transaction History
                </h1>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="flex-1 overflow-y-auto">
        <div class="p-6">
            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                    <p class="text-sm font-medium text-gray-600 mb-2">Total Transaksi</p>
                    <p class="text-4xl font-bold text-gray-900">{{ $totalTransactions }}</p>
                </div>
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                    <p class="text-sm font-medium text-gray-600 mb-2">Total Penjualan</p>
                    <p class="text-4xl font-bold text-orange-600">Rp {{ number_format($totalSales, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                    <p class="text-sm font-medium text-gray-600 mb-2">Rata-rata Transaksi</p>
                    <p class="text-4xl font-bold text-gray-900">
                        @if($totalTransactions > 0)
                            Rp {{ number_format($totalSales / $totalTransactions, 0, ',', '.') }}
                        @else
                            Rp 0
                        @endif
                    </p>
                </div>
            </div>

            {{-- Filters & Search --}}
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm mb-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    {{-- Shift Filter --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Shift</label>
                        <select wire:model.live="selectedShiftId" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-orange-500 focus:border-orange-500 transition">
                            <option value="">-- Pilih Shift --</option>
                            @foreach($availableShifts as $shift)
                                <option value="{{ $shift->id }}">
                                    {{ $shift->start_at->format('d/m/Y H:i') }} - 
                                    @if($shift->end_at)
                                        {{ $shift->end_at->format('H:i') }}
                                    @else
                                        Sedang Berjalan
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Payment Method Filter --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Metode Pembayaran</label>
                        <select wire:model.live="filterPaymentMethod" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-orange-500 focus:border-orange-500 transition">
                            <option value="all">Semua Metode</option>
                            <option value="cash">Tunai</option>
                            <option value="qris">QRIS</option>
                        </select>
                    </div>

                    {{-- Sort --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Urutkan</label>
                        <select wire:model.live="sortBy" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-orange-500 focus:border-orange-500 transition">
                            <option value="recent">Recent</option>
                            <option value="oldest">Oldest</option>
                            <option value="highest">Highest</option>
                            <option value="lowest">Lowest</option>
                        </select>
                    </div>

                    {{-- Search --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Cari</label>
                        <div class="relative">
                            <input wire:model.live.debounce.300ms="searchQuery" type="text" placeholder="Nama pelanggan / ID..." 
                                class="w-full px-4 py-3 pl-10 border border-gray-200 rounded-xl focus:ring-orange-500 focus:border-orange-500 transition placeholder-gray-400">
                            <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Payment Method Summary --}}
            @if(count($paymentMethodSummary) > 0)
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm mb-8">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Ringkasan Metode Pembayaran</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    @foreach($paymentMethodSummary as $method => $amount)
                        <div class="p-4 bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl border border-orange-200">
                            <p class="text-xs font-bold text-orange-700 uppercase mb-2 capitalize">{{ str_replace('_', ' ', $method) }}</p>
                            <p class="text-lg font-bold text-orange-600">Rp {{ number_format($amount, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Transactions Table --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Pelanggan</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Waktu</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Items</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Metode</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($transactionList as $transaction)
                                <tr class="hover:bg-orange-50 transition">
                                    <td class="px-6 py-4">
                                        <span class="inline-block bg-orange-100 text-orange-700 px-3 py-1 rounded-md font-bold text-sm">
                                            #{{ str_pad($transaction->id, 4, '0', STR_PAD_LEFT) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div>
                                            <p class="font-bold text-gray-900">{{ $transaction->customer?->name ?? 'Pelanggan Umum' }}</p>
                                            @if($transaction->customer?->phone)
                                                <p class="text-xs text-gray-500">{{ $transaction->customer->phone }}</p>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm">
                                            <p class="font-medium text-gray-900">{{ $transaction->transaction_date->format('d/m/Y') }}</p>
                                            <p class="text-xs text-gray-500">{{ $transaction->transaction_date->format('H:i') }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-block bg-blue-100 text-blue-700 px-3 py-1 rounded-md font-bold text-sm">
                                            {{ $transaction->items->count() }} item
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-block bg-green-100 text-green-700 px-3 py-1 rounded-md text-xs font-bold capitalize">
                                            {{ str_replace('_', ' ', $transaction->payment_method) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <p class="font-bold text-lg text-gray-900">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</p>
                                        @if($transaction->discount_amount > 0)
                                            <p class="text-xs text-red-600">Diskon: Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</p>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <p class="text-lg font-bold text-gray-500 mb-1">Tidak ada transaksi</p>
                                            <p class="text-sm text-gray-400">Coba ubah filter atau pilih shift yang berbeda</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
