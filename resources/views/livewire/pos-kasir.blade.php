<div class="h-full w-full flex flex-col overflow-hidden">
    {{-- OPEN OVERLAY MODAL AWAL --}}
    @if(!$this->getActiveShift())
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-gray-900/70 backdrop-blur-sm">
            <div class="bg-white rounded-3xl shadow-2xl p-8 w-full max-w-md text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-2xl bg-orange-100 mb-6">
                    <svg class="h-8 w-8 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Buka Shift Kasir</h3>
                <p class="text-sm text-gray-500 mb-8">Masukkan modal awal kas untuk memulai shift Anda.</p>

                @error('openingCash')
                    <div class="mb-4 text-sm text-red-600 bg-red-50 p-3 rounded-xl font-medium">{{ $message }}</div>
                @enderror

                @if($previousShiftInfo)
                    <div class="mb-6 bg-orange-50 border border-orange-100 rounded-xl p-4 text-left">
                        <div class="flex items-start">
                            <svg class="h-5 w-5 text-orange-500 mt-0.5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="text-sm text-orange-900 flex-1">
                                <p class="font-bold mb-2">Handover Shift Sebelumnya:</p>
                                <p class="flex justify-between mb-1"><span>Kasir:</span> <span class="font-medium">{{ $previousShiftInfo['user'] }}</span></p>
                                <p class="flex justify-between mb-1"><span>Ditutup:</span> <span class="font-medium">{{ $previousShiftInfo['end_at'] }}</span></p>
                                <div class="w-full h-px bg-orange-200 my-2"></div>
                                <p class="flex justify-between font-bold text-base"><span>Saldo Akhir:</span> <span>Rp {{ number_format($previousShiftInfo['actual_cash'], 0, ',', '.') }}</span></p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="mb-8" x-data="inputRupiah(@entangle('openingCash').live)"> 
                    <label class="block text-sm font-bold text-gray-700 mb-2 text-left">Modal Awal (Rp)</label>
                    <input type="text" x-model="nilaiTampil" autofocus inputmode="numeric"
                        class="w-full text-2xl font-bold text-center p-4 border-gray-200 rounded-xl focus:ring-orange-500 focus:border-orange-500 bg-gray-50">
                </div>

                <button wire:click="openShift" wire:loading.attr="disabled"
                    class="w-full bg-orange-600 text-white font-bold py-4 px-4 rounded-xl shadow-lg shadow-orange-600/30 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 disabled:opacity-50 transition">
                    <span wire:loading.remove wire:target="openShift">Mulai Shift</span>
                    <span wire:loading wire:target="openShift">Memproses...</span>
                </button>
            </div>
        </div>
    @endif

    {{-- MOBILE CART TOGGLE BUTTON --}}
    <div class="fixed bottom-4 right-4 z-30 lg:hidden" x-data="{ show: false }">
        <button @click="$dispatch('toggle-cart')" class="relative flex items-center justify-center w-14 h-14 bg-indigo-600 text-white rounded-full shadow-lg hover:bg-indigo-700 active:scale-95 transition-all">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            @if(count($cart) > 0)
            <span class="absolute -top-1 -right-1 flex items-center justify-center w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full">{{ count($cart) }}</span>
            @endif
        </button>
    </div>

    {{-- MAIN POS LAYOUT (Fills Main Content Area) --}}
    <div class="flex flex-1 h-full w-full overflow-hidden print:hidden relative min-h-0" x-data="{ showMobileCart: false }">

        {{-- LEFT SIDE: Catalog & Order List --}}
        <div class="flex-1 flex flex-col p-4 lg:p-8 overflow-hidden bg-[#F8F9FA] min-h-0">
            
            {{-- Top Header --}}
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between mb-4 gap-4">
                <div class="flex items-center gap-3 w-full lg:w-auto">
                    <button @click="showSidebar = !showSidebar" class="p-3 bg-white border border-gray-200 rounded-full text-gray-600 hover:bg-gray-50 transition shadow-sm flex-shrink-0">
                        <svg x-show="!showSidebar" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        <svg x-show="showSidebar" style="display: none;" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                    <div class="relative flex-1 lg:w-96">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search menu..." 
                            class="block w-full pl-12 pr-4 py-3 border border-gray-200 rounded-full leading-5 bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 sm:text-sm transition shadow-sm">
                    </div>
                </div>
                <div class="flex gap-2 w-full lg:w-auto overflow-x-auto hide-scrollbar pb-2 lg:pb-0">
                    <button onclick="Livewire.dispatch('openCashMovementFromLayout')" class="bg-white border border-gray-200 text-gray-700 px-4 lg:px-6 py-2.5 lg:py-3 rounded-full font-bold hover:bg-gray-50 transition shadow-sm flex items-center gap-2 whitespace-nowrap text-sm lg:text-base">
                        <span>Cash Movement</span>
                    </button>
                    <button onclick="Livewire.dispatch('openCloseShiftFromLayout')" class="bg-red-600 text-white px-4 lg:px-6 py-2.5 lg:py-3 rounded-full font-bold hover:bg-red-700 transition shadow-md shadow-red-600/20 flex items-center gap-2 whitespace-nowrap text-sm lg:text-base">
                        <span>Close Order</span>
                    </button>
                </div>
            </div>



            {{-- Menu Section --}}
            <div class="flex flex-col flex-1 overflow-hidden min-h-0">
                {{-- Antrian Aktif --}}
                @if($this->activeQueues->isNotEmpty())
                    <div class="mb-5">
                        <h2 class="text-lg font-bold text-gray-900 flex items-baseline gap-2 mb-3">
                            Antrian Aktif
                            <span class="text-xs font-medium text-gray-500">({{ $this->activeQueues->count() }})</span>
                        </h2>

                        <div class="flex gap-3 overflow-x-auto pb-2 hide-scrollbar">
                            @foreach($this->activeQueues as $queue)
                                <button type="button"
                                    wire:click="loadReservationToCart({{ $queue->id }})"
                                    class="flex-shrink-0 w-44 bg-white border-2 border-gray-100 hover:border-orange-400 hover:shadow-md hover:shadow-orange-500/10 rounded-2xl p-3 text-left transition group">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-orange-600 bg-orange-50 px-2 py-0.5 rounded">{{ $queue->queue_number }}</span>
                                        <span class="text-[10px] font-bold text-gray-400">{{ $queue->reservation_time->format('H:i') }}</span>
                                    </div>
                                    <p class="font-bold text-sm text-gray-900 truncate">{{ $queue->display_name }}</p>
                                    <p class="text-xs text-gray-500 truncate mt-0.5">
                                        {{ $queue->services->pluck('name')->join(', ') ?: '—' }}
                                    </p>
                                    @if($queue->employee)
                                        <p class="text-[10px] font-semibold text-gray-400 mt-1">Kapster: {{ $queue->employee->name }}</p>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <h1 class="text-3xl font-bold text-gray-900 mb-4 flex items-baseline gap-2"> Service <span class="text-sm font-medium text-gray-500">({{ $this->services->count() + count($productsData) }} Items)</span></h1>
                
                {{-- Categories Tabs --}}
                <div class="flex gap-4 mb-6 overflow-x-auto">
                    <button wire:click="$set('activeTab', 'service')" class="flex items-center gap-1 px-6 py-3 rounded-full whitespace-nowrap transition border-2 {{ $activeTab === 'service' ? 'border-orange-500 text-orange-600 bg-white shadow-sm' : 'border-transparent text-gray-500 bg-white hover:bg-gray-50 border-gray-100' }}">
                        <span class="font-bold">Layanan</span>
                        <span class="text-xs font-medium {{ $activeTab === 'service' ? 'text-orange-500' : 'text-gray-400' }}">({{ $this->services->count() }})</span>
                    </button>

                    <button wire:click="$set('activeTab', 'product')" class="flex items-center gap-1 px-6 py-3 rounded-full whitespace-nowrap transition border-2 {{ $activeTab === 'product' ? 'border-orange-500 text-orange-600 bg-white shadow-sm' : 'border-transparent text-gray-500 bg-white hover:bg-gray-50 border-gray-100' }}">
                        <span class="font-bold">Produk</span>
                        <span class="text-xs font-medium {{ $activeTab === 'product' ? 'text-orange-500' : 'text-gray-400' }}">({{ count($productsData) }})</span>
                    </button>
                </div>

                {{-- Items Grid --}}
                <div class="flex-1 overflow-y-auto pb-6 relative pr-2">
                    <div wire:loading wire:target="search, activeTab" class="absolute inset-0 bg-[#F8F9FA]/80 z-10 flex items-center justify-center rounded-xl">
                        <div class="animate-spin rounded-full h-10 w-10 border-4 border-orange-200 border-t-orange-600"></div>
                    </div>

                    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-5 mt-2">
                        @if($activeTab === 'service')
                            @foreach($this->services as $service)
                                <div class="bg-white border border-gray-100 rounded-3xl p-4 flex flex-col hover:shadow-xl hover:shadow-orange-500/5 hover:-translate-y-1 hover:border-orange-200 transition-all cursor-pointer group" wire:click="addToCart({{ $service->id }}, 'service')">
                                    <div class="aspect-square bg-gray-50 rounded-2xl mb-4 flex items-center justify-center overflow-hidden relative border border-gray-50">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($service->name) }}&background=fff7ed&color=f97316&font-size=0.33&size=256" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    </div>
                                    <h2 class="font-medium text-gray-900 mb-3 line-clamp-2 leading-tight text-lg">{{ $service->name }}</h2>
                                    <div class="mt-auto flex flex-col items-start gap-1">
                                        <span class="text-[10px] font-bold tracking-wider uppercase bg-orange-50 text-orange-600 px-2 py-1 rounded-md">Service</span>
                                        <span class="font-extrabold text-gray-900 text-xl">Rp {{ number_format($service->price, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            @foreach($productsData as $product)
                                @php $isDisabled = $product->stock <= 0; @endphp
                                <div class="bg-white border border-gray-100 rounded-3xl p-4 flex flex-col transition-all {{ $isDisabled ? 'opacity-50 grayscale cursor-not-allowed' : 'hover:shadow-xl hover:shadow-orange-500/5 hover:-translate-y-1 hover:border-orange-200 cursor-pointer group' }}" @if(!$isDisabled) wire:click="addToCart({{ $product->id }}, 'product')" @endif>
                                    <div class="aspect-square bg-gray-50 rounded-2xl mb-4 flex items-center justify-center overflow-hidden relative border border-gray-50">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($product->name) }}&background=f0fdf4&color=16a34a&font-size=0.33&size=256" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        @if($isDisabled)
                                            <div class="absolute inset-0 bg-white/60 flex items-center justify-center backdrop-blur-[1px]">
                                                <span class="bg-red-600 text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-sm">Habis</span>
                                            </div>
                                        @endif
                                    </div>
                                    <h3 class="font-bold text-gray-900 mb-3 line-clamp-2 leading-tight">{{ $product->name }}</h3>
                                    <div class="mt-auto flex items-center justify-between w-full">
                                        <span class="text-[10px] font-bold tracking-wider uppercase {{ $isDisabled ? 'bg-gray-100 text-gray-500' : 'bg-green-50 text-green-600' }} px-2 py-1 rounded-md">Stock: {{ $product->stock }}</span>
                                        <span class="font-extrabold text-gray-900 text-sm">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Mobile Floating Cart Button --}}
        <button @click="showMobileCart = true" class="lg:hidden fixed bottom-6 right-6 bg-orange-600 text-white p-4 rounded-full shadow-2xl shadow-orange-600/40 z-40 hover:bg-orange-700 active:scale-95 transition-transform flex items-center justify-center">
            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            @if(count($cart) > 0)
                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold w-6 h-6 flex items-center justify-center rounded-full border-2 border-white">{{ count($cart) }}</span>
            @endif
        </button>

        {{-- Mobile Overlay for Cart --}}
        <div x-show="showMobileCart" class="fixed inset-0 bg-gray-900/50 z-40 lg:hidden" @click="showMobileCart = false" x-transition.opacity style="display: none;"></div>

        {{-- RIGHT SIDE: Cart Sidebar --}}
        <div class="fixed inset-y-0 right-0 z-50 w-full sm:w-[420px] lg:w-[420px] lg:relative lg:z-30 bg-white shadow-2xl lg:shadow-[-10px_0_30px_-15px_rgba(0,0,0,0.1)] flex flex-col border-l border-gray-100 flex-shrink-0 transition-transform duration-300 transform lg:translate-x-0"
            :class="showMobileCart ? 'translate-x-0' : 'translate-x-full'">
            
            {{-- Customer Section --}}
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between bg-white rounded-2xl">
                    <div class="flex items-center gap-4">
                        <button @click="showMobileCart = false" class="lg:hidden w-10 h-10 rounded-full border border-gray-200 flex items-center justify-center text-gray-400 hover:bg-gray-50 transition mr-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        @if($selectedCustomerId)
                            <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center overflow-hidden shadow-sm">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($customerName) }}&color=f97316&background=ffedd5&size=128" alt="Avatar" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 text-base leading-tight">{{ $customerName }}</h3>
                                <p class="text-[11px] font-bold text-orange-500 mt-0.5 tracking-wider uppercase">#{{ str_pad($selectedCustomerId, 4, '0', STR_PAD_LEFT) }}</p>
                            </div>
                        @else
                            <div class="w-12 h-12 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 border border-gray-100">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 text-base leading-tight">Pelanggan</h3>
                                <p class="text-[11px] font-bold text-gray-400 mt-0.5 uppercase tracking-wider">Pilih pelanggan</p>
                            </div>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <button wire:click="$set('showCustomerModal', true)" class="w-10 h-10 rounded-full border border-gray-200 flex items-center justify-center text-gray-400 hover:text-orange-500 hover:bg-orange-50 hover:border-orange-200 transition" title="Input pelanggan">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        </button>
                        @if($selectedCustomerId || !empty($customerName))
                            <button wire:click="clearCustomer" class="w-10 h-10 rounded-full border border-gray-200 flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 hover:border-red-200 transition" title="Hapus pelanggan">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Cart List --}}
            <div class="flex-1 overflow-y-auto p-6 space-y-5 relative bg-white-200">
                @error('general')
                    <div class="bg-red-50 border border-red-200 p-4 rounded-xl mb-4 text-sm text-red-700 flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <span class="font-medium">{{ $message }}</span>
                    </div>
                @enderror

                @forelse($cart as $key => $item)
                    <div class="flex items-center gap-4 bg-white group px-4 py-4 border border-gray-200 rounded-xl">
                        <div class="w-16 h-16 rounded-2xl bg-gray-50 border border-gray-100 flex-shrink-0 flex items-center justify-center overflow-hidden">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($item['name']) }}&background=f3f4f6&color=9ca3af&font-size=0.4&size=128" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-gray-900 text-[15px] truncate mb-1">{{ $item['name'] }}</h4>
                            <p class="text-[13px] font-medium text-gray-500 mb-3">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                            @error('cart.'.$key) <p class="text-xs text-red-500 mt-1 mb-2 font-medium">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex flex-col items-end gap-3">
                            <div class="flex items-center bg-gray-50 rounded-xl border border-gray-200 overflow-hidden">
                                <button wire:click="updateQuantity('{{ $key }}', {{ $item['quantity'] - 1 }})" class="w-8 h-8 flex items-center justify-center bg-gray-50 hover:bg-gray-200 text-gray-600 transition"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg></button>
                                <span class="w-8 text-center text-sm font-bold text-gray-900 bg-white h-full flex items-center justify-center border-x border-gray-200">{{ $item['quantity'] }}</span>
                                <button wire:click="updateQuantity('{{ $key }}', {{ $item['quantity'] + 1 }})" class="w-8 h-8 flex items-center justify-center bg-gray-50 hover:bg-gray-200 text-gray-600 transition"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg></button>
                            </div>
                            <div class="flex items-center gap-2">
                                <p class="font-extrabold text-gray-900 text-[15px]">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                                <button wire:click="removeFromCart('{{ $key }}')" class="text-gray-300 hover:text-red-500 transition"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="h-full flex flex-col items-center justify-center text-gray-400 opacity-80 pt-10">
                        <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-gray-100">
                            <svg class="w-10 h-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        </div>
                        <p class="font-bold text-gray-500">Keranjang masih kosong</p>
                        <p class="text-sm mt-1">Pilih menu untuk menambahkan</p>
                    </div>
                @endforelse
            </div>

            {{-- Summary & Checkout --}}
            <div class="p-6 bg-white border-t border-gray-100 relative before:absolute before:inset-x-0 before:-top-6 before:h-6 before:bg-gradient-to-t before:from-white before:to-transparent before:pointer-events-none">
                {{-- Pilih Kapster (1 transaksi = 1 kapster) --}}
                @if($this->hasServiceInCart())
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Kapster</label>
                        <select wire:model.live="cartKapsterId"
                            class="w-full text-sm font-semibold border rounded-xl px-3 py-2.5 bg-white focus:ring-orange-500 focus:border-orange-500 transition
                                {{ $errors->has('cartKapsterId') ? 'border-red-300 text-red-600' : 'border-gray-200 text-gray-800' }}">
                            <option value="">— Pilih kapster —</option>
                            @foreach($this->kapsters as $kapster)
                                <option value="{{ $kapster->id }}">{{ $kapster->name }}</option>
                            @endforeach
                        </select>
                        @error('cartKapsterId') <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>
                @endif

                <div class="space-y-3 mb-6">
                    <div class="flex justify-between text-gray-500 text-sm font-medium">
                        <span>Sub Total</span>
                        <span class="font-bold text-gray-900">Rp {{ number_format($this->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-gray-500 text-sm font-medium">
                        <div class="flex items-center gap-2">
                            <span>Tax (0%)</span>
                        </div>
                        <span class="font-bold text-gray-900">Rp 0</span>
                    </div>
                    <div class="w-16 h-1 bg-gray-100 rounded-full mx-auto my-4"></div>

                    <div class="flex justify-between items-end">
                        <span class="font-bold text-gray-900 text-lg">Total</span>
                        <span class="font-black text-gray-900 text-2xl">Rp {{ number_format($this->total, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-5 gap-3">
                    <button wire:click="createQueueFromCart" wire:loading.attr="disabled"
                        @disabled(empty($cart) || $isProcessing)
                        class="sm:col-span-2 w-full border-2 border-orange-500 bg-white text-orange-600 font-bold py-4 px-4 rounded-2xl hover:bg-orange-50 focus:outline-none focus:ring-4 focus:ring-orange-500/20 disabled:opacity-50 disabled:cursor-not-allowed transition-all flex justify-center items-center gap-2 text-base transform active:scale-[0.98]">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Antrian
                    </button>
                    <button wire:click="openPaymentModal" wire:loading.attr="disabled"
                        @disabled(empty($cart) || $isProcessing)
                        class="sm:col-span-3 w-full bg-[#E55B13] text-white font-bold py-4 px-6 rounded-2xl shadow-xl shadow-[#E55B13]/30 hover:bg-[#d44c0a] focus:outline-none focus:ring-4 focus:ring-orange-500/30 disabled:opacity-50 disabled:shadow-none disabled:cursor-not-allowed transition-all flex justify-center items-center text-lg transform active:scale-[0.98]">
                        Bayar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- PAYMENT MODAL --}}
    @if($showPaymentModal)
        <div class="fixed inset-0 z-[56] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm print:hidden"
            onclick="if(event.target===this) @this.closePaymentModal()">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-black text-gray-900">Checkout Pembayaran</h3>
                        <p class="text-sm text-gray-500">Pilih metode bayar lalu konfirmasi transaksi.</p>
                    </div>
                    <button wire:click="closePaymentModal" class="w-10 h-10 rounded-full border border-gray-200 text-gray-500 hover:bg-gray-50 transition">
                        <svg class="w-4 h-4 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6 space-y-5 max-h-[70vh] overflow-y-auto">
                    @error('general')
                        <div class="bg-red-50 border border-red-200 p-3 rounded-xl text-sm text-red-700">{{ $message }}</div>
                    @enderror

                    <div class="bg-gray-50 rounded-2xl border border-gray-100 p-4">
                        <h4 class="font-bold text-gray-900 mb-3">Rincian Keranjang</h4>
                        <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                            @foreach($cart as $item)
                                <div class="flex items-start justify-between text-sm">
                                    <div class="min-w-0 pr-3">
                                        <p class="font-semibold text-gray-900 truncate">{{ $item['name'] }}</p>
                                        <p class="text-xs text-gray-500">{{ $item['quantity'] }} x Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                                    </div>
                                    <p class="font-bold text-gray-900 whitespace-nowrap">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-3 pt-3 border-t border-gray-200 flex justify-between items-center">
                            <span class="font-bold text-gray-700">Total</span>
                            <span class="font-black text-xl text-gray-900">Rp {{ number_format($this->total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-bold text-gray-900 mb-3 text-sm">Payment Method</h4>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach(['cash' => ['name' => 'Cash', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', 'color' => 'green'], 'qris' => ['name' => 'Qris', 'icon' => 'M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z', 'color' => 'blue']] as $method => $data)
                                <button wire:click="$set('paymentMethod', '{{ $method }}')" class="flex flex-col items-center justify-center p-3 rounded-2xl border-2 transition-all {{ $paymentMethod === $method ? 'border-orange-500 bg-orange-50/50 shadow-sm shadow-orange-500/10' : 'border-gray-100 bg-gray-50 hover:bg-gray-100' }}">
                                    <svg class="w-6 h-6 mb-2 text-{{ $data['color'] }}-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $data['icon'] }}"></path></svg>
                                    <span class="text-[11px] font-bold text-gray-700 uppercase tracking-wide">{{ $data['name'] }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    @if($paymentMethod === 'cash')
                        <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100" x-data="inputRupiah(@entangle('paymentAmount').live)">
                            <label class="block text-[11px] font-bold text-gray-500 mb-2 uppercase tracking-wider">Jumlah Uang Tunai</label>
                            <input type="text" x-model="nilaiTampil" inputmode="numeric" class="w-full text-xl font-black p-3 border border-gray-200 rounded-xl focus:ring-orange-500 focus:border-orange-500 text-right bg-white shadow-sm transition" placeholder="Rp 0">
                            @error('paymentAmount') <p class="mt-2 text-xs font-bold text-red-600 bg-red-50 p-2 rounded-lg">{{ $message }}</p> @enderror

                            @if(!empty($paymentAmount) && (float)$paymentAmount > 0 && $this->total > 0)
                                @if((float)$paymentAmount > $this->total)
                                    <div class="flex justify-between items-center mt-3 pt-3 border-t border-gray-200">
                                        <span class="font-bold text-gray-500 text-sm">Kembalian:</span>
                                        <span class="font-black text-green-600 text-lg">Rp {{ number_format((float)$paymentAmount - $this->total, 0, ',', '.') }}</span>
                                    </div>
                                @elseif((float)$paymentAmount == $this->total)
                                    <div class="flex justify-between items-center mt-3 pt-3 border-t border-gray-200">
                                        <span class="font-bold text-gray-500 text-sm">Status:</span>
                                        <span class="font-black text-blue-600 text-lg">Pembayaran Pas ✓</span>
                                    </div>
                                @else
                                    <div class="flex justify-between items-center mt-3 pt-3 border-t border-gray-200">
                                        <span class="font-bold text-gray-500 text-sm">Kekurangan:</span>
                                        <span class="font-black text-red-600 text-lg">Rp {{ number_format($this->total - (float)$paymentAmount, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endif
                </div>

                <div class="p-6 bg-gray-50 border-t border-gray-100 grid grid-cols-2 gap-3">
                    <button wire:click="closePaymentModal" class="w-full rounded-xl border-2 border-gray-200 px-6 py-4 bg-white text-base font-bold text-gray-700 hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button wire:click="processTransaction" wire:loading.attr="disabled" wire:target="processTransaction" @disabled($paymentMethod === 'cash' && $paymentAmount < $this->total) class="w-full rounded-xl px-6 py-4 bg-[#E55B13] text-base font-bold text-white hover:bg-[#d44c0a] transition shadow-lg shadow-[#E55B13]/20 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="processTransaction">Konfirmasi Bayar</span>
                        <span wire:loading wire:target="processTransaction">Memproses...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- TRANSACTION SUCCESS MODAL --}}
    @if($showCustomerModal)
        <div class="fixed inset-0 z-[55] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm print:hidden"
            onclick="if(event.target===this) @this.set('showCustomerModal', false)">
            <div class="bg-white rounded-3xl shadow-2xl p-6 w-full max-w-md">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-xl font-black text-gray-900">Input Pelanggan</h3>
                    <button wire:click="$set('showCustomerModal', false)" class="w-9 h-9 rounded-full border border-gray-200 text-gray-500 hover:bg-gray-50 transition">
                        <svg class="w-4 h-4 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="relative">
                    <input wire:model.live.debounce.300ms="customerSearch" wire:model.blur="customerName" type="text" placeholder="Ketik nama atau no telp..."
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 text-sm font-medium transition placeholder-gray-400">

                    @if(count($customerSuggestions) > 0)
                        <div class="mt-2 w-full bg-white rounded-xl shadow-xl border border-gray-100 py-2 max-h-60 overflow-y-auto">
                            @foreach($customerSuggestions as $suggestion)
                                <button wire:click="selectCustomer({{ $suggestion['id'] }}, '{{ addslashes($suggestion['name']) }}')" class="w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition flex justify-between items-center group">
                                    <div class="font-bold">{{ $suggestion['name'] }}</div>
                                    <div class="text-xs text-gray-400 group-hover:text-orange-400 font-mono">{{ $suggestion['phone'] ?? '-' }}</div>
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                @if(!empty($customerSearch) && count($customerSuggestions) === 0)
                    <input wire:model.live="customerPhone" type="text" placeholder="No. telepon (opsional)" class="mt-3 w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 text-sm font-medium transition placeholder-gray-400">
                    <p class="mt-2 text-xs text-gray-500">Nama ini akan disimpan sebagai pelanggan baru saat transaksi diproses.</p>
                @endif

                <div class="mt-6 grid grid-cols-2 gap-3">
                    <button wire:click="$set('showCustomerModal', false)" class="w-full rounded-xl border-2 border-gray-200 px-5 py-3 bg-white text-sm font-bold text-gray-700 hover:bg-gray-50 transition">
                        Tutup
                    </button>
                    <button wire:click="$set('showCustomerModal', false)" class="w-full rounded-xl px-5 py-3 bg-orange-600 text-sm font-bold text-white hover:bg-orange-700 transition">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    @endif

    <div id="transaction-modal" style="display: none;" onclick="if(event.target===this) closeTransactionModal()"
        class="fixed inset-0 z-50 print:hidden flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">

        <div id="transaction-modal-content"
            class="relative bg-white rounded-3xl shadow-2xl p-8 w-full max-w-sm text-center transform transition-all duration-300 opacity-0 scale-95 translate-y-4">

            <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 mb-5 shadow-inner">
                <svg class="h-10 w-10 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <h3 class="text-2xl font-black text-gray-900 mb-2">Order Success!</h3>
            <p class="text-sm text-gray-500 mb-8 font-medium">
                Invoice <span class="font-mono font-bold text-orange-600 bg-orange-50 px-2 py-0.5 rounded">{{ $completedInvoiceNumber }}</span> has been saved.
            </p>

            <div class="flex flex-col gap-3">
                <button type="button" onclick="window.print()" class="w-full inline-flex justify-center items-center rounded-xl border border-transparent px-6 py-4 bg-[#E55B13] text-base font-bold text-white hover:bg-[#d44c0a] transition shadow-lg shadow-orange-500/20">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Print Receipt
                </button>
                <button type="button" onclick="closeTransactionModal()" class="w-full inline-flex justify-center items-center rounded-xl border-2 border-gray-200 px-6 py-4 bg-white text-base font-bold text-gray-700 hover:bg-gray-50 hover:border-gray-300 transition">
                    New Order
                </button>
            </div>
        </div>
    </div>

    {{-- CASH MOVEMENT MODAL --}}
    @if($showCashMovementModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm print:hidden"
            onclick="if(event.target===this) @this.set('showCashMovementModal', false)">
            <div class="bg-white rounded-3xl shadow-2xl p-8 w-full max-w-md">
                <h3 class="text-2xl font-black text-gray-900 mb-6">Cash Movement</h3>

                @error('cashMovement')
                    <div class="mb-5 text-sm text-red-600 bg-red-50 p-3 rounded-xl font-medium">{{ $message }}</div>
                @enderror

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-3">Tipe Transaksi</label>
                        <div class="grid grid-cols-2 gap-3">
                            <button wire:click="$set('cashMovementType', 'in')"
                                class="py-3 text-sm font-bold rounded-xl border-2 text-center transition {{ $cashMovementType === 'in' ? 'bg-green-50 border-green-500 text-green-700 shadow-sm' : 'border-gray-100 bg-gray-50 text-gray-500 hover:border-gray-200' }}">
                                Cash In
                            </button>
                            <button wire:click="$set('cashMovementType', 'out')"
                                class="py-3 text-sm font-bold rounded-xl border-2 text-center transition {{ $cashMovementType === 'out' ? 'bg-red-50 border-red-500 text-red-700 shadow-sm' : 'border-gray-100 bg-gray-50 text-gray-500 hover:border-gray-200' }}">
                                Cash Out
                            </button>
                        </div>
                    </div>

                    <div x-data="inputRupiah(@entangle('cashMovementAmount').live)">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Jumlah (Rp)</label>
                        <input type="text" x-model="nilaiTampil" inputmode="numeric"
                            class="w-full text-xl font-black p-4 border border-gray-200 rounded-xl focus:ring-orange-500 focus:border-orange-500 bg-gray-50 transition"
                            placeholder="Rp 0">
                    </div>

                    @if($cashMovementType === 'out')
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Kategori</label>
                            <select wire:model="cashMovementCategory"
                                class="w-full p-4 border border-gray-200 rounded-xl focus:ring-orange-500 focus:border-orange-500 bg-gray-50 transition font-medium">
                                @foreach(\App\Models\CashMovement::CATEGORIES as $catKey => $catLabel)
                                    <option value="{{ $catKey }}">{{ $catLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Alasan</label>
                        <input wire:model="cashMovementReason" type="text"
                            class="w-full p-4 border border-gray-200 rounded-xl focus:ring-orange-500 focus:border-orange-500 bg-gray-50 transition font-medium"
                            placeholder="Contoh: setor tambahan, beli plastik">
                    </div>
                </div>

                <div class="mt-8 grid grid-cols-2 gap-3">
                    <button wire:click="$set('showCashMovementModal', false)"
                        class="w-full rounded-xl border-2 border-gray-200 px-6 py-4 bg-white text-base font-bold text-gray-700 hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button wire:click="saveCashMovement"
                        class="w-full rounded-xl px-6 py-4 text-base font-bold text-white transition shadow-lg
                        {{ $cashMovementType === 'in' ? 'bg-green-600 hover:bg-green-700 shadow-green-600/30' : 'bg-red-600 hover:bg-red-700 shadow-red-600/30' }}">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- CLOSE SHIFT MODAL --}}
    @if($showCloseShiftModal && !empty($shiftSummary))
        @php
            $ss = $shiftSummary;
            $shift = $ss['shift'];
            $diff = $actualCash - $ss['expected_cash'];
        @endphp
        <div class="fixed inset-0 z-[60] flex items-start justify-center p-4 bg-gray-900/70 backdrop-blur-sm print:hidden overflow-y-auto">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg my-8 overflow-hidden">
                {{-- Header --}}
                <div class="p-8 border-b border-gray-100 bg-[#E55B13] text-center text-white">
                    <h3 class="text-2xl font-black mb-1">Laporan Shift Kasir</h3>
                    <p class="text-orange-100 font-medium">{{ $shift->user->name ?? auth()->user()->name }}</p>
                </div>

                <div class="p-8 space-y-6 text-sm">
                    {{-- Info Shift --}}
                    <div class="grid grid-cols-2 gap-4 text-gray-600 bg-gray-50 p-4 rounded-2xl">
                        <div>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">Mulai</span>
                            <p class="font-bold text-gray-900">{{ $shift->start_at->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">Durasi</span>
                            <p class="font-bold text-gray-900">{{ $shift->start_at->diffForHumans(now(), true) }}</p>
                        </div>
                    </div>

                    {{-- Modal Awal --}}
                    <div class="flex justify-between items-center p-4 bg-orange-50 rounded-2xl border border-orange-100">
                        <span class="font-bold text-orange-900">Modal Awal</span>
                        <span class="font-black text-orange-700 text-lg">Rp {{ number_format($shift->opening_cash, 0, ',', '.') }}</span>
                    </div>

                    {{-- Penjualan --}}
                    <div>
                        <h4 class="font-bold text-gray-900 mb-3 text-base">Penjualan</h4>
                        <div class="space-y-2">
                            @foreach(['cash' => 'Tunai', 'qris' => 'QRIS', 'transfer' => 'Transfer', 'e_wallet' => 'E-Wallet', 'debit_card' => 'Debit Card', 'credit_card' => 'Credit Card'] as $method => $label)
                                @if(($ss['per_method'][$method] ?? 0) > 0)
                                    <div class="flex justify-between text-gray-600 font-medium">
                                        <span>{{ $label }}</span>
                                        <span>Rp {{ number_format($ss['per_method'][$method], 0, ',', '.') }}</span>
                                    </div>
                                @endif
                            @endforeach
                            <div class="flex justify-between font-black text-gray-900 pt-3 mt-2 border-t border-gray-100 text-base">
                                <span>Total Penjualan</span>
                                <span>Rp {{ number_format($ss['total_sales'], 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-gray-400 text-xs font-bold mt-1">
                                <span>Jumlah Transaksi</span>
                                <span>{{ $ss['transaction_count'] }} trx</span>
                            </div>
                        </div>
                    </div>

                    {{-- Cash In/Out --}}
                    <div>
                        <h4 class="font-bold text-gray-900 mb-3 text-base">Cash Movement</h4>
                        <div class="space-y-2 bg-gray-50 p-4 rounded-2xl border border-gray-100">
                            <div class="flex justify-between text-green-600 font-bold">
                                <span>Cash In</span>
                                <span>+ Rp {{ number_format($ss['cash_in'], 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-red-600 font-bold">
                                <span>Cash Out</span>
                                <span>- Rp {{ number_format($ss['cash_out'], 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Rekonsiliasi --}}
                    <div class="p-5 rounded-2xl border-2 {{ $actualCash == 0 ? 'border-gray-200 bg-white' : ($diff == 0 ? 'border-green-500 bg-green-50' : ($diff > 0 ? 'border-yellow-400 bg-yellow-50' : 'border-red-500 bg-red-50')) }} transition-colors">
                        <h4 class="font-black text-gray-900 mb-4 text-base">Rekonsiliasi Kas</h4>
                        <div class="space-y-4">
                            <div class="flex justify-between text-gray-700 items-center">
                                <span class="font-bold">Expected Cash</span>
                                <span class="font-black text-lg bg-white px-3 py-1 rounded-lg border border-gray-200">Rp {{ number_format($ss['expected_cash'], 0, ',', '.') }}</span>
                            </div>
                            <div x-data="inputRupiah(@entangle('actualCash').live)">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kas Aktual (hitung fisik)</label>
                                <input type="text" x-model="nilaiTampil"
                                    class="w-full text-lg font-bold p-2 border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Rp 0">
                                @error('actualCash') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            @if($actualCash > 0)
                                <div class="flex justify-between items-center pt-3 border-t border-gray-200/50">
                                    <span class="font-bold">Selisih</span>
                                    <span class="text-xl font-black {{ $diff == 0 ? 'text-green-600' : ($diff > 0 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ $diff >= 0 ? '+' : '' }}Rp {{ number_format($diff, 0, ',', '.') }}
                                        <span class="text-xs ml-1 font-bold">{{ $diff == 0 ? '(PAS)' : ($diff > 0 ? '(LEBIH)' : '(KURANG)') }}</span>
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- LAPORAN HARI OPERASIONAL --}}
                    @php $bdr = $this->businessDayReport; @endphp
                    @if($bdr)
                        <div>
                            <h4 class="font-black text-gray-900 mb-3 text-base flex items-center gap-2">
                                Laporan Hari Operasional
                                <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded {{ $bdr['tipe_hari'] === 'full_day' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $bdr['tipe_hari'] === 'full_day' ? 'Full Day' : 'Half Day' }} · {{ ($bdr['kasir_count'] ?? $bdr['shift_count']) }} kasir
                                </span>
                            </h4>

                            {{-- Bagi Hasil ringkas --}}
                            <div class="space-y-2 mb-3">
                                <div class="bg-orange-50 border border-orange-100 rounded-xl p-3 flex justify-between items-center">
                                    <div>
                                        <p class="text-[11px] font-black uppercase text-orange-600">Komisi Kapster</p>
                                        <p class="text-[10px] text-orange-700/70 font-bold">{{ collect($bdr['komisi_per_kapster'])->pluck('name')->join(', ') ?: '—' }}</p>
                                    </div>
                                    <p class="font-black text-orange-700">Rp {{ number_format($bdr['total_komisi_kapster'], 0, ',', '.') }}</p>
                                </div>
                                <div class="bg-blue-50 border border-blue-100 rounded-xl p-3 flex justify-between items-center">
                                    <div>
                                        <p class="text-[11px] font-black uppercase text-blue-700">Fee Kasir</p>
                                        <p class="text-[10px] text-blue-700/70 font-bold">{{ collect($bdr['fee_kasir_per_kasir'])->pluck('kasir_name')->join(', ') }}</p>
                                    </div>
                                    <p class="font-black text-blue-700">Rp {{ number_format($bdr['total_fee_kasir'], 0, ',', '.') }}</p>
                                </div>
                                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-3 flex justify-between items-center">
                                    <p class="text-[11px] font-black uppercase text-emerald-700">Owner (Mama) Net</p>
                                    <p class="font-black text-emerald-700 text-lg">Rp {{ number_format($bdr['total_owner_net'], 0, ',', '.') }}</p>
                                </div>
                            </div>

                            @if(!empty($bdr['cash_out_by_category']))
                                <div class="bg-gray-50 border border-gray-100 rounded-xl p-3 mb-3">
                                    <p class="text-[11px] font-black uppercase text-gray-500 mb-2">Pengeluaran</p>
                                    @foreach($bdr['cash_out_by_category'] as $cat)
                                        <div class="flex justify-between text-xs font-semibold text-gray-700">
                                            <span>{{ $cat['label'] }}</span>
                                            <span>Rp {{ number_format($cat['amount'], 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <p class="text-[11px] text-gray-400 italic">Detail per shift × layanan akan tercetak di nota.</p>
                        </div>
                    @endif

                    {{-- Notes --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Catatan (opsional)</label>
                        <textarea wire:model="closingNotes" rows="2"
                            class="w-full p-4 border border-gray-200 rounded-xl focus:ring-orange-500 focus:border-orange-500 text-sm font-medium bg-gray-50 transition"
                            placeholder="Alasan selisih kas, kejadian khusus, dll"></textarea>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="p-6 bg-gray-50 border-t border-gray-100 flex flex-col gap-3">
                    <button wire:click="tutupUsaha" wire:confirm="Tutup hari operasional dan cetak laporan? Aksi ini juga menutup shift saat ini."
                        class="w-full rounded-xl px-6 py-4 bg-gray-900 text-base font-black text-white hover:bg-black transition shadow-lg shadow-gray-900/30 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Tutup Usaha & Cetak
                    </button>
                    <div class="grid grid-cols-3 gap-2">
                        <button wire:click="$set('showCloseShiftModal', false)"
                            class="w-full rounded-xl border-2 border-gray-200 px-3 py-3 bg-white text-xs font-bold text-gray-700 hover:bg-gray-50 transition">
                            Batal
                        </button>
                        <button wire:click="closeShift" wire:confirm="Tutup shift ini saja (tanpa simpan laporan hari)? Anda akan logout."
                            class="w-full rounded-xl border-2 border-red-300 px-3 py-3 bg-red-50 text-xs font-bold text-red-600 hover:bg-red-100 transition">
                            Tutup Shift
                        </button>
                        <button wire:click="changeShift" wire:confirm="Yakin ingin ganti shift? Shift ini akan ditutup dan Anda akan di-logout."
                            class="w-full rounded-xl border-2 border-orange-300 px-3 py-3 bg-orange-50 text-xs font-bold text-orange-600 hover:bg-orange-100 transition">
                            Ganti Shift
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif


    {{-- ============================================= --}}
    {{-- PRINT AREA: RECEIPT --}}
    {{-- ============================================= --}}
    <style>
        @media print {
            @page { margin: 4mm; }
            body * { visibility: hidden; }
            #print-area, #print-area *,
            #print-queue-area, #print-queue-area *,
            #print-bdr-area, #print-bdr-area * { visibility: visible; }
            #print-area, #print-queue-area, #print-bdr-area {
                position: absolute;
                left: 0;
                top: 0;
                font-family: 'Courier New', monospace;
                color: black;
                box-sizing: border-box;
            }
            #print-area      { width: 80mm; font-size: 11px; line-height: 1.3; padding: 2mm; }
            #print-queue-area { width: 58mm; font-size: 11px; line-height: 1.35; padding: 2mm; }
            #print-bdr-area  { width: 58mm; font-size: 10px; line-height: 1.3; padding: 2mm; }
        }
        
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
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
        @if($printTx->discount_amount > 0)
        <div class="flex justify-between font-normal text-gray-600">
            <span>Subtotal:</span>
            <span>{{ number_format($printTx->items->sum('subtotal'), 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between font-normal text-gray-600 mb-1">
            <span>Diskon {{ $printTx->discount_type === 'percent' ? '(' . number_format($printTx->discount_value, 0) . '%)' : '' }}:</span>
            <span>- {{ number_format($printTx->discount_amount, 0, ',', '.') }}</span>
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

{{-- PRINT AREA: NOTA ANTRIAN 58mm --}}
@if($printQueueReservationId)
@php
$printQueue = \App\Models\Reservation::with(['customer', 'services', 'employee', 'branch'])
    ->withoutGlobalScopes()
    ->find($printQueueReservationId);
@endphp
@if($printQueue)
<div id="print-queue-area" class="hidden print:block print:w-[58mm] print:p-2 print:text-sm print:mx-auto">
    <div class="text-center font-bold text-xl mb-1">{{ $printQueue->branch->name ?? config('app.name') }}</div>
    <div class="text-center text-xs mb-3 border-b border-dashed border-gray-400 pb-2">
        {{ $printQueue->branch->address ?? '' }}
    </div>

    <div class="text-center mb-3">
        <div class="text-xs">NOMOR ANTRIAN</div>
        <div class="text-4xl font-black tracking-widest mt-1">{{ $printQueue->queue_number }}</div>
    </div>

    <div class="text-xs border-t border-b border-dashed border-gray-400 py-2 mb-2">
        <div class="flex justify-between"><span>Tgl:</span><span>{{ $printQueue->reservation_time->format('d/m/Y H:i') }}</span></div>
        <div class="flex justify-between"><span>Nama:</span><span>{{ $printQueue->display_name }}</span></div>
        @if($printQueue->employee)
        <div class="flex justify-between"><span>Kapster:</span><span>{{ $printQueue->employee->name }}</span></div>
        @endif
    </div>

    <div class="text-xs mb-3">
        <div class="font-bold mb-1">Layanan:</div>
        @foreach($printQueue->services as $svc)
            <div class="flex justify-between">
                <span>- {{ $svc->name }}</span>
                <span>{{ number_format($svc->price, 0, ',', '.') }}</span>
            </div>
        @endforeach
    </div>

    @if($printQueue->notes)
    <div class="text-xs italic border-t border-dashed border-gray-400 pt-2 mb-2">
        Catatan: {{ $printQueue->notes }}
    </div>
    @endif

    <div class="text-center text-xs mt-4 border-t border-dashed border-gray-400 pt-2">
        <p>Mohon menunggu giliran</p>
        <p>Terima kasih</p>
    </div>
</div>
@endif
@endif

{{-- PRINT AREA: LAPORAN TUTUP USAHA 58mm --}}
@if($printBusinessDayReportId)
@php
$printBdr = \App\Models\BusinessDayReport::withoutGlobalScopes()
    ->with(['branch','closedBy'])
    ->find($printBusinessDayReportId);
$snap = $printBdr?->snapshot ?? [];
@endphp
@if($printBdr && !empty($snap))
<div id="print-bdr-area" class="hidden print:block print:w-[58mm] print:p-2 print:text-xs print:mx-auto">
    <div class="text-center font-bold text-base mb-1">{{ $printBdr->branch->name ?? config('app.name') }}</div>
    <div class="text-center text-[10px] mb-2 border-b border-dashed border-gray-400 pb-2">
        LAPORAN TUTUP USAHA<br>
        {{ \Carbon\Carbon::parse($snap['business_date'])->format('d/m/Y') }}
        · {{ strtoupper($snap['tipe_hari']) }}
    </div>

    {{-- Transaksi per shift --}}
    @foreach($snap['shifts'] as $sh)
        <div class="mb-2 pb-1 border-b border-dashed border-gray-400 text-[10px]">
            <div class="font-bold">Shift {{ $sh['shift_number'] }} · {{ $sh['kasir_name'] }}</div>
            <div class="text-[9px]">{{ $sh['shift_start'] }}–{{ $sh['shift_end'] ?? 'now' }}</div>
            @foreach($sh['items'] as $item)
                <div class="flex justify-between">
                    <span>{{ $item['service_name'] }} x{{ $item['qty'] }}</span>
                    <span>{{ number_format($item['total_harga'], 0, ',', '.') }}</span>
                </div>
            @endforeach
            <div class="flex justify-between font-bold mt-1">
                <span>Tunai/Non:</span>
                <span>{{ number_format($sh['cash_total'], 0, ',', '.') }} / {{ number_format($sh['non_cash_total'], 0, ',', '.') }}</span>
            </div>
        </div>
    @endforeach

    <div class="text-[10px] mb-2 pb-1 border-b border-dashed border-gray-400">
        <div class="flex justify-between font-bold"><span>TOTAL ORDER</span><span>{{ $snap['total_orders'] }}</span></div>
        <div class="flex justify-between"><span>Tunai</span><span>{{ number_format($snap['total_cash'], 0, ',', '.') }}</span></div>
        <div class="flex justify-between"><span>Non Tunai</span><span>{{ number_format($snap['total_non_cash'], 0, ',', '.') }}</span></div>
        <div class="flex justify-between font-bold"><span>TOTAL</span><span>{{ number_format($snap['total_gross'], 0, ',', '.') }}</span></div>
    </div>

    {{-- Bagi Hasil --}}
    <div class="text-[10px] mb-2 pb-1 border-b border-dashed border-gray-400">
        <div class="font-bold mb-1">BAGI HASIL</div>
        @foreach($snap['komisi_per_kapster'] as $kap)
            <div class="flex justify-between">
                <span>Kapster {{ $kap['name'] }}</span>
                <span>{{ number_format($kap['amount'], 0, ',', '.') }}</span>
            </div>
        @endforeach
        <div class="flex justify-between font-bold">
            <span>Total Kapster</span>
            <span>{{ number_format($snap['total_komisi_kapster'], 0, ',', '.') }}</span>
        </div>
        @foreach($snap['fee_kasir_per_kasir'] as $fee)
            <div class="flex justify-between">
                <span>Kasir {{ $fee['kasir_name'] }} ({{ $fee['tipe'] === 'full_day' ? 'FD' : 'HD' }})</span>
                <span>{{ number_format($fee['fee_amount'], 0, ',', '.') }}</span>
            </div>
        @endforeach
        <div class="flex justify-between font-bold">
            <span>Total Kasir</span>
            <span>{{ number_format($snap['total_fee_kasir'], 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between font-bold mt-1 pt-1 border-t border-gray-400">
            <span>OWNER NET</span>
            <span>{{ number_format($snap['total_owner_net'], 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- Rekonsiliasi Kas --}}
    <div class="text-[10px] mb-2">
        <div class="font-bold mb-1">REKONSILIASI KAS</div>
        <div class="flex justify-between"><span>Modal awal</span><span>{{ number_format($snap['modal_awal_hari'], 0, ',', '.') }}</span></div>
        <div class="flex justify-between"><span>+ Tunai</span><span>{{ number_format($snap['total_cash'], 0, ',', '.') }}</span></div>
        @if($snap['cash_in'] > 0)
            <div class="flex justify-between"><span>+ Cash In</span><span>{{ number_format($snap['cash_in'], 0, ',', '.') }}</span></div>
        @endif
        @foreach($snap['cash_out_by_category'] as $cat)
            <div class="flex justify-between"><span>- {{ $cat['label'] }}</span><span>{{ number_format($cat['amount'], 0, ',', '.') }}</span></div>
        @endforeach
        <div class="flex justify-between font-bold"><span>Expected</span><span>{{ number_format($snap['expected_kas_akhir'], 0, ',', '.') }}</span></div>
        <div class="flex justify-between"><span>Actual</span><span>{{ number_format($printBdr->actual_kas_akhir, 0, ',', '.') }}</span></div>
        <div class="flex justify-between font-bold border-t border-gray-400 pt-1">
            <span>SELISIH</span>
            <span>{{ ($printBdr->selisih_kas ?? 0) >= 0 ? '+' : '' }}{{ number_format($printBdr->selisih_kas, 0, ',', '.') }}</span>
        </div>
    </div>

    @if($printBdr->notes)
    <div class="text-[10px] italic border-t border-dashed border-gray-400 pt-1 mb-2">
        Catatan: {{ $printBdr->notes }}
    </div>
    @endif

    <div class="text-center text-[9px] mt-3 pt-2 border-t border-dashed border-gray-400">
        Dicetak: {{ now()->format('d/m/Y H:i') }}<br>
        oleh {{ $printBdr->closedBy->name ?? auth()->user()->name }}
    </div>
</div>
@endif
@endif

    {{-- JAVASCRIPT --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('inputRupiah', (entangleData) => ({
                nilaiAsli: entangleData,

                get nilaiTampil() {
                    if (!this.nilaiAsli) return '';
                    return 'Rp ' + parseInt(this.nilaiAsli).toLocaleString('id-ID');
                },

                set nilaiTampil(val) {
                    const angka = val.toString().replace(/[^0-9]/g, '');
                    this.nilaiAsli = angka ? parseInt(angka, 10) : 0;
                }
            }));
        });

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
        setTimeout(() => {
            modal.style.display = 'none';
        }, 200);
    }

    window.addEventListener('transaction-completed', () => openTransactionModal());

    window.addEventListener('queue-created', () => {
        // Beri waktu Livewire render print-queue-area, baru cetak
        setTimeout(() => {
            window.print();
            // Reset state setelah dialog cetak ditutup (delay supaya tidak bentrok dengan print)
            setTimeout(() => {
                if (window.Livewire) {
                    Livewire.dispatch('clearPrintQueue');
                }
            }, 1000);
        }, 200);
    });

    window.addEventListener('business-day-closed', () => {
        setTimeout(() => {
            window.print();
            setTimeout(() => {
                if (window.Livewire) {
                    Livewire.dispatch('clearPrintBusinessDay');
                }
            }, 1000);
        }, 300);
    });
    window.addEventListener('shift-closed', () => {
        window.location.reload();
    });
    window.addEventListener('shift-opened', () => {
        window.location.reload();
    });
</script>
</div>