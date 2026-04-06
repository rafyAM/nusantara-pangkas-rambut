<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 h-[calc(100vh-6rem)]">
    
    <!-- Kolom Kiri: Katalog (60%) -->
    <div class="lg:col-span-7 bg-white rounded-lg shadow-sm border border-gray-200 flex flex-col h-full overflow-hidden print:hidden">
        
        <!-- Header & Tabs -->
        <div class="p-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <div class="flex space-x-2">
                <button 
                    wire:click="$set('activeTab', 'service')"
                    class="px-4 py-2 text-sm font-medium rounded-md transition {{ $activeTab === 'service' ? 'bg-indigo-600 text-white shadow' : 'bg-white text-gray-600 border border-gray-300 hover:bg-gray-50' }}"
                >
                    Layanan
                </button>
                <button 
                    wire:click="$set('activeTab', 'product')"
                    class="px-4 py-2 text-sm font-medium rounded-md transition {{ $activeTab === 'product' ? 'bg-indigo-600 text-white shadow' : 'bg-white text-gray-600 border border-gray-300 hover:bg-gray-50' }}"
                >
                    Produk
                </button>

                <button 
                    wire:click="$set('activeTab', 'reservation')"
                    class="px-4 py-2 text-sm font-medium rounded-md transition {{ $activeTab === 'reservation' ? 'bg-indigo-600 text-white shadow' : 'bg-white text-gray-600 border border-gray-300 hover:bg-gray-50' }}"
                >
                    Reservation
                </button>
            </div>
            
            <div class="relative w-64">
                <input 
                    wire:model.live.debounce.300ms="search" 
                    type="text" 
                    placeholder="Cari item..." 
                    class="w-full pl-10 pr-4 py-2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                >
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Katalog Item List -->
        <div class="flex-1 overflow-y-auto p-4 bg-gray-50/50 relative">
            
            <div wire:loading wire:target="search, activeTab" class="absolute inset-0 bg-white/50 z-10 flex items-center justify-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
            </div>

            @if($activeTab === 'service')
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    @forelse($this->services as $service)
                        <div class="bg-white border border-gray-200 rounded-lg p-4 flex flex-col justify-between hover:border-indigo-500 hover:shadow-sm transition cursor-pointer"
                             wire:click="addToCart({{ $service->id }}, 'service')">
                            <div>
                                <h3 class="font-medium text-gray-900 line-clamp-2 min-h-[2.5rem]">{{ $service->name }}</h3>
                                <p class="text-indigo-600 font-bold mt-2">Rp {{ number_format($service->price, 0, ',', '.') }}</p>
                            </div>
                            <button class="mt-4 w-full bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-medium py-2 px-4 rounded transition flex items-center justify-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                Tambah
                            </button>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-8 text-gray-500">
                            Tidak ada layanan ditemukan.
                        </div>
                    @endforelse
                </div>
            @elseif($activeTab === 'reservation')
                <div class="space-y-4">
                    @forelse($reservationsData as $reservation)
                        <div wire:key="reservation-{{ $reservation->id }}" class="bg-white border border-gray-200 rounded-lg p-4 flex flex-col md:flex-row md:items-center justify-between hover:border-indigo-500 hover:shadow-sm transition">
                            <div class="flex-1 mb-4 md:mb-0">
                                <div class="flex items-center space-x-2 mb-2">
                                    <h3 class="font-bold text-gray-900 text-lg">{{ $reservation->customer?->name ?? 'Pelanggan Umum' }}</h3>
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded 
                                        {{ $reservation->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                          ($reservation->status === 'arrived' ? 'bg-teal-100 text-teal-800' :
                                          ($reservation->status === 'confirmed' ? 'bg-blue-100 text-blue-800' : 
                                          ($reservation->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'))) }}">
                                        {{ ucfirst($reservation->status) }}
                                    </span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm text-gray-600">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        {{ $reservation->reservation_time->translatedFormat('l, d F Y') }}
                                    </div>
                                    <div class="flex items-center font-medium text-indigo-600">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        {{ $reservation->reservation_time->format('H:i') }} WIB
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        Barber: <span class="font-medium ml-1 text-gray-800">{{ $reservation->employee?->name ?? 'Bebas' }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                        {{ $reservation->customer?->phone ?? '-' }}
                                    </div>
                                </div>
                                @if($reservation->services && $reservation->services->count() > 0)
                                    <div class="mt-2 text-xs text-gray-500">
                                        <strong>Layanan:</strong> {{ $reservation->services->pluck('name')->join(', ') }}
                                    </div>
                                @endif
                            </div>
                            
                            <div class="md:ml-4 flex-shrink-0 flex flex-col gap-2">
                                @if($reservation->status === 'pending')
                                <div class="flex gap-2 w-full">
                                <button wire:click="approveReservation({{ $reservation->id }})" class="flex-1 md:w-auto bg-white text-green-600 hover:bg-green-100 font-medium py-2 px-4 rounded transition flex items-center justify-center border border-green-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Hadir
                                    </button>
                                    <button wire:click="cancelReservation({{ $reservation->id }})" wire:confirm="Yakin ingin membatalkan/menghanguskan secara manual?" class="flex-1 md:w-auto bg-white text-red-600 hover:bg-red-100 font-medium py-2 px-4 rounded transition flex items-center justify-center border border-red-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        Batal/Hangus
                                    </button>
                                </div>
                                @endif
                                <button wire:click="loadReservationToCart({{ $reservation->id }})" class="w-full md:w-auto {{ $reservation->status === 'pending' ? 'bg-white text-indigo-600' : 'bg-indigo-50 text-indigo-700' }} hover:bg-indigo-100 font-medium py-2 px-4 rounded transition flex items-center justify-center border border-indigo-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    Proses Ke Kasir
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full h-64 flex flex-col items-center justify-center text-gray-500 bg-white rounded-lg border-2 border-dashed border-gray-300">
                            <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <p class="text-xl font-medium text-gray-900">Tidak ada reservasi ditemukan</p>
                            <p class="text-sm mt-1">Belum ada pelanggan yang membuat reservasi untuk hari ini ke depan.</p>
                        </div>
                    @endforelse
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    @forelse($productsData as $product)
                        @php
                            $isDisabled = $product->stock <= 0;
                        @endphp
                        <div class="bg-white border rounded-lg p-4 flex flex-col justify-between transition {{ $isDisabled ? 'opacity-50 cursor-not-allowed border-gray-200' : 'border-gray-200 hover:border-indigo-500 hover:shadow-sm cursor-pointer' }}"
                             @if(!$isDisabled) wire:click="addToCart({{ $product->id }}, 'product')" @endif>
                            <div>
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="font-medium text-gray-900 line-clamp-2 flex-1">{{ $product->name }}</h3>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $product->stock > 5 ? 'bg-green-100 text-green-800' : ($product->stock > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }} ml-2">
                                        {{ $product->stock }}
                                    </span>
                                </div>
                                <p class="text-indigo-600 font-bold">Rp {{ number_format((float) $product->price, 0, ',', '.') }}</p>
                            </div>
                            <button class="mt-4 w-full font-medium py-2 px-4 rounded transition flex items-center justify-center {{ $isDisabled ? 'bg-gray-100 text-gray-400' : 'bg-indigo-50 hover:bg-indigo-100 text-indigo-700' }}"
                                    @disabled($isDisabled)>
                                @if($isDisabled) Habis @else 
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                Tambah @endif
                            </button>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-8 text-gray-500">
                            Tidak ada produk ditemukan.
                        </div>
                    @endforelse
                </div>
                
            @endif
        </div>
    </div>

    <!-- Kolom Kanan: Cart & Checkout (40%) -->
    <div class="lg:col-span-5 flex flex-col h-full space-y-4 print:hidden">
        
        <!-- Error General -->
        @error('general')
            <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-md">
                <p class="text-sm text-red-700">{{ $message }}</p>
            </div>
        @enderror

        <!-- Customer Selection -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 relative z-40">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Informasi Pelanggan</h2>
            
            @if($selectedCustomerId)
                <div class="flex items-center justify-between p-3 bg-indigo-50 border border-indigo-100 rounded-md">
                    <div>
                        <p class="text-sm font-semibold text-indigo-900">{{ $customerName }}</p>
                        <p class="text-xs text-indigo-600">Terdaftar</p>
                    </div>
                    <button wire:click="clearCustomer" class="text-gray-400 hover:text-red-500">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                    </button>
                </div>
            @else
                <div class="relative">
                    <input 
                        wire:model.live.debounce.300ms="customerSearch" 
                        wire:model.blur="customerName"
                        type="text" 
                        placeholder="Nama atau nomor telepon pelanggan..." 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    >
                    
                    @if(count($customerSuggestions) > 0)
                        <div class="absolute z-50 mt-1 w-full bg-white rounded-md shadow-lg border border-gray-200 py-1">
                            @foreach($customerSuggestions as $suggestion)
                                <button 
                                    wire:click="selectCustomer({{ $suggestion['id'] }}, '{{ addslashes($suggestion['name']) }}')"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-900"
                                >
                                    <div class="font-medium">{{ $suggestion['name'] }}</div>
                                    <div class="text-xs text-gray-500">{{ $suggestion['phone'] ?? '-' }}</div>
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Cart Items -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 flex-1 flex flex-col overflow-hidden">
            <h2 class="text-lg font-medium text-gray-900 p-4 border-b border-gray-200">Keranjang Belanja</h2>
            
            <div class="flex-1 overflow-y-auto p-4 space-y-4">
                @forelse($cart as $key => $item)
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                        <div class="flex-1 min-w-0 pr-4">
                            <h4 class="text-sm font-medium text-gray-900 truncate">{{ $item['name'] }}</h4>
                            <p class="text-xs text-gray-500">{{ $item['type'] === 'service' ? 'Layanan' : 'Produk' }} • Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                            @error('cart.'.$key) <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <div class="flex items-center border border-gray-300 rounded-md bg-white">
                                <button wire:click="updateQuantity('{{ $key }}', {{ $item['quantity'] - 1 }})" class="px-2 py-1 text-gray-500 hover:text-gray-700 focus:outline-none">
                                    -
                                </button>
                                <span class="px-2 text-sm font-medium text-gray-900">{{ $item['quantity'] }}</span>
                                <button wire:click="updateQuantity('{{ $key }}', {{ $item['quantity'] + 1 }})" class="px-2 py-1 text-gray-500 hover:text-gray-700 focus:outline-none">
                                    +
                                </button>
                            </div>
                            
                            <div class="w-24 text-right">
                                <p class="text-sm font-bold text-gray-900">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                            </div>

                            <button wire:click="removeFromCart('{{ $key }}')" class="text-gray-400 hover:text-red-500">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="h-full flex flex-col items-center justify-center text-gray-500">
                        <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        <p class="text-sm">Keranjang masih kosong</p>
                    </div>
                @endforelse
            </div>
            
            <!-- Summary & Payment -->
            <div class="bg-gray-50 p-4 border-t border-gray-200">
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span class="font-medium">Rp {{ number_format($this->subtotal, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex justify-between items-center text-gray-600 pb-2 border-b border-gray-200">
                        <div class="flex items-center space-x-2">
                            <span>Diskon</span>
                            <select wire:model.live="discountType" class="py-1 pl-2 pr-6 text-xs border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="nominal">Rp</option>
                                <option value="percent">%</option>
                            </select>
                        </div>
                        <div class="flex items-center space-x-2">
                            <input wire:model.live.debounce.500ms="discountValue" type="number" min="0" class="w-24 py-1 px-2 text-sm text-right border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" placeholder="0">
                            @if($this->discountAmount > 0)
                                <span class="font-medium text-red-600">- Rp {{ number_format($this->discountAmount, 0, ',', '.') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex justify-between items-center pt-2">
                        <span class="text-base font-bold text-gray-900">Total Tagihan</span>
                        <span class="text-xl font-bold text-indigo-600">Rp {{ number_format($this->total, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-200">
                    <h3 class="text-sm font-medium text-gray-900 mb-3">Metode Pembayaran</h3>
                    <div class="grid grid-cols-3 gap-2">
                        <button wire:click="$set('paymentMethod', 'cash')" class="py-2 px-1 text-sm font-medium rounded-md border text-center transition {{ $paymentMethod === 'cash' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                            Cash
                        </button>
                        <button wire:click="$set('paymentMethod', 'qris')" class="py-2 px-1 text-sm font-medium rounded-md border text-center transition {{ $paymentMethod === 'qris' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                            QRIS
                        </button>
                        <button wire:click="$set('paymentMethod', 'transfer')" class="py-2 px-1 text-sm font-medium rounded-md border text-center transition {{ $paymentMethod === 'transfer' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                            Transfer
                        </button>
                    </div>

                    @if($paymentMethod === 'cash')
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Bayar Tunai</label>
                            <input wire:model.live.debounce.300ms="paymentAmount" type="number" class="w-full text-lg font-bold p-2 border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" placeholder="0">
                            @error('paymentAmount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror

                            @if($paymentAmount >= $this->total && $this->total > 0)
                                <div class="mt-2 flex justify-between items-center p-2 bg-green-50 rounded text-green-800">
                                    <span class="text-sm font-medium">Kembalian:</span>
                                    <span class="text-lg font-bold">Rp {{ number_format($this->changeAmount, 0, ',', '.') }}</span>
                                </div>
                            @endif
                        </div>
                    @endif

                    <button 
                        wire:click="processTransaction"
                        wire:loading.attr="disabled"
                        @disabled(empty($cart) || ($paymentMethod === 'cash' && $paymentAmount < $this->total))
                        class="mt-4 w-full bg-indigo-600 text-white font-bold py-3 px-4 rounded-md shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition flex justify-center items-center"
                    >
                        <span wire:loading.remove wire:target="processTransaction">Proses Transaksi</span>
                        <span wire:loading wire:target="processTransaction" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Memproses...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="transaction-modal"
         style="display: none;"
         onclick="if(event.target===this) closeTransactionModal()"
         class="fixed inset-0 z-50 print:hidden flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm"
         aria-labelledby="modal-title" role="dialog" aria-modal="true">
        
        <div id="transaction-modal-content"
             class="relative bg-white rounded-2xl shadow-2xl p-6 w-full max-w-sm text-center transform transition-all duration-300 opacity-0 scale-95 translate-y-4">
             
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            
            <h3 class="text-xl font-bold text-gray-900 mb-2">Transaksi Berhasil!</h3>
            <p class="text-sm text-gray-500 mb-6">
                Transaksi <span class="font-mono font-bold">{{ $completedInvoiceNumber }}</span> tersimpan.
            </p>

            <div class="grid grid-cols-2 gap-3">
                <button type="button" onclick="closeTransactionModal()" class="w-full inline-flex justify-center items-center rounded-lg border border-gray-300 px-4 py-2.5 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 transition shadow-sm">
                    Tutup
                </button>
                <button type="button" onclick="window.print()" class="w-full inline-flex justify-center items-center rounded-lg border border-transparent px-4 py-2.5 bg-indigo-600 text-sm font-semibold text-white hover:bg-indigo-700 transition shadow-sm">
                    Cetak Struk
                </button>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body * { visibility: hidden; }
            #print-area, #print-area * { visibility: visible; }
            #print-area { position: absolute; left: 0; top: 0; width: 100%; font-family: monospace; color: black; }
        }
    </style>
    
    @if($completedTransactionId)
        @php
            $printTx = \App\Models\Transaction::with(['items.service', 'items.product', 'customer', 'branch', 'employee'])->find($completedTransactionId);
        @endphp
        
        @if($printTx)
            <div id="print-area" class="hidden print:block print:w-[80mm] print:p-2 print:text-sm print:mx-auto">
                <div class="text-center font-bold text-xl mb-1">{{ $printTx->branch->name ?? config('app.name') }}</div>
                <div class="text-center text-xs mb-4 border-b border-dashed border-gray-400 pb-2">
                    {{ $printTx->branch->address ?? '' }}
                </div>
                
                <div class="text-xs mb-2">
                    <div class="flex justify-between"><span>No:</span> <span>{{ $printTx->invoice_number }}</span></div>
                    <div class="flex justify-between"><span>Tgl:</span> <span>{{ $printTx->transaction_date->format('d/m/Y H:i') }}</span></div>
                    <div class="flex justify-between"><span>Kasir:</span> <span>{{ $printTx->employee->name ?? auth()->user()->name }}</span></div>
                    @if($printTx->customer)
                        <div class="flex justify-between"><span>Plgn:</span> <span>{{ $printTx->customer->name }}</span></div>
                    @endif
                </div>

                <div class="border-t border-b border-dashed border-gray-400 py-2 mb-2 text-xs">
                    @foreach($printTx->items as $item)
                        <div class="mb-1">
                            <div>{{ $item->item_type === 'service' ? $item->service?->name : $item->product?->name }}</div>
                            <div class="flex justify-between">
                                <span>{{ $item->quantity }} x {{ number_format($item->price, 0, ',', '.') }}</span>
                                <span>{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="text-xs font-bold mb-4">
                    @if($printTx->notes && str_contains($printTx->notes, 'Diskon'))
                        <div class="flex justify-between font-normal text-gray-600">
                            <span>Murni:</span>
                            <span>{{ number_format($printTx->items->sum('subtotal'), 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between font-normal text-gray-600 mb-1">
                            <span>{{ $printTx->notes }}:</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-base border-t border-solid border-gray-800 pt-1">
                        <span>TOTAL:</span>
                        <span>Rp {{ number_format($printTx->total_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between font-normal mt-1">
                        <span>Bayar ({{ strtoupper($printTx->payment_method) }}):</span>
                    </div>
                </div>

                <div class="text-center text-xs mt-6 border-t border-dashed border-gray-400 pt-2">
                    <p>Terima Kasih</p>
                    <p>Silakan datang kembali</p>
                </div>
            </div>
        @endif
    @endif

    <script>
        function openTransactionModal() {
            const modal = document.getElementById('transaction-modal');
            const content = document.getElementById('transaction-modal-content');
            modal.style.display = 'flex';
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    content.classList.remove('opacity-0', 'scale-95', 'translate-y-4');
                    content.classList.add('opacity-100', 'scale-100', 'translate-y-0');
                });
            });
        }

        function closeTransactionModal() {
            const modal = document.getElementById('transaction-modal');
            const content = document.getElementById('transaction-modal-content');
            content.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
            content.classList.add('opacity-0', 'scale-95', 'translate-y-4');
            setTimeout(() => { modal.style.display = 'none'; }, 200);
        }

        window.addEventListener('transaction-completed', () => openTransactionModal());
    </script>
</div>
