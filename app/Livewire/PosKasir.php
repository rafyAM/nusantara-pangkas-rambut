<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use App\Models\Service;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\CashierShift;
use App\Models\CashMovement;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

#[Layout('layouts.kasir')]
class PosKasir extends Component
{
    // --- Cart & Katalog ---
    public array $cart = [];
    public string $search = '';
    public string $activeTab = 'service';

    // --- Customer ---
    public string $customerName = '';
    public string $customerSearch = '';
    public string $customerPhone = '';
    public array $customerSuggestions = [];
    public ?int $selectedCustomerId = null;
    public bool $showCustomerModal = false;

    // --- Reservasi ---
    public ?int $processedReservationId = null;

    // --- Diskon & Pembayaran ---
    public string $discountType = 'nominal';
    public float $discountValue = 0;
    public string $paymentMethod = 'cash';
    public ?float $paymentAmount = null;
    public bool $showPaymentModal = false;

    // --- Transaksi Selesai ---
    public bool $isProcessing = false;
    public ?int $completedTransactionId = null;
    public string $completedInvoiceNumber = '';

    // --- Shift Kasir ---
    public float $openingCash = 0;
    public float $actualCash = 0;
    public string $closingNotes = '';
    public bool $showCloseShiftModal = false;

    // --- Cash Movement ---
    public bool $showCashMovementModal = false;
    public string $cashMovementType = 'in';
    public float $cashMovementAmount = 0;
    public string $cashMovementReason = '';

    // --- Handover Shift ---
    public ?array $previousShiftInfo = null;

    // =============================================
    //  LIFECYCLE & COMPUTED PROPERTIES
    // =============================================

    public function mount()
    {
        $this->loadPreviousShiftHandover();
    }

    protected function loadPreviousShiftHandover()
    {
        $cookie = request()->cookie('previous_shift_info');
        if (!$cookie) {
            return;
        }

        $handoverInfo = json_decode($cookie, true);
        if (!is_array($handoverInfo)) {
            return;
        }

        $this->previousShiftInfo = $handoverInfo;
        if (isset($handoverInfo['actual_cash'])) {
            $this->openingCash = (float) $handoverInfo['actual_cash'];
        }

        // remove cookie so it doesn't reappear
        Cookie::queue(Cookie::forget('previous_shift_info'));
    }

    public function getActiveShift()
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }
        // withoutGlobalScopes: query sudah di-scope by user_id sehingga aman
        return CashierShift::withoutGlobalScopes()
            ->open()
            ->byUser($user->id)
            ->latest()
            ->first();
    }

    #[Computed]
    public function services()
    {
        return Service::where('is_active', true)
            ->where('name', 'like', '%' . $this->search . '%')
            ->get();
    }

    #[Computed]
    public function subtotal()
    {
        return collect($this->cart)->sum('subtotal');
    }

    #[Computed]
    public function discountAmount()
    {
        if ($this->discountType === 'percent') {
            return $this->subtotal() * ($this->discountValue / 100);
        }
        return $this->discountValue;
    }

    #[Computed]
    public function total()
    {
        return max(0, $this->subtotal() - $this->discountAmount());
    }

    #[Computed]
    public function changeAmount()
    {
        if ($this->paymentMethod !== 'cash') {
            return 0;
        }

        $paymentAmount = $this->paymentAmount ?? 0;

        return max(0, $paymentAmount - $this->total());
    }

    // =============================================
    //  PRIVATE HELPERS
    // =============================================

    private function getReservationsList()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user) {
            return collect();
        }

        $branchId = $user->employee?->branch_id ?? $user->branches()->first()?->id;

        $query = \App\Models\Reservation::with(['customer', 'employee', 'services'])
            ->whereIn('status', ['pending', 'arrived'])
            ->orderBy('reservation_time', 'asc');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if (!empty($this->search)) {
            $query->whereHas('customer', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        return $query->get();
    }

    private function getProductsList()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user) {
            return collect();
        }

        $branchId = $user->employee?->branch_id ?? $user->branches()->first()?->id;

        $query = Product::where('is_active', true)
            ->where('name', 'like', '%' . $this->search . '%');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->get();
    }

    /**
     * Hitung ringkasan shift untuk ditampilkan di modal closing.
     */
    public function getShiftSummary(): array
    {
        $shift = $this->getActiveShift();
        if (!$shift) {
            return [];
        }

        $paymentSummary = Payment::where('cashier_shift_id', $shift->id)
            ->selectRaw('method, SUM(amount) as total, COUNT(DISTINCT transaction_id) as trx_count')
            ->groupBy('method')
            ->pluck('total', 'method')
            ->toArray();

        $transactionCount = Transaction::withoutGlobalScopes()
            ->where('cashier_shift_id', $shift->id)
            ->where('status', 'completed')
            ->count();

        $totalSales = Payment::where('cashier_shift_id', $shift->id)->sum('amount');
        $cashIn     = $shift->cashMovements()->where('type', 'in')->sum('amount');
        $cashOut    = $shift->cashMovements()->where('type', 'out')->sum('amount');

        $methods = ['cash', 'qris', 'transfer', 'e_wallet', 'debit_card', 'credit_card'];
        $perMethod = [];
        foreach ($methods as $m) {
            $perMethod[$m] = (float) ($paymentSummary[$m] ?? 0);
        }

        return [
            'shift'             => $shift,
            'per_method'        => $perMethod,
            'total_sales'       => (float) $totalSales,
            'transaction_count' => $transactionCount,
            'cash_in'           => (float) $cashIn,
            'cash_out'          => (float) $cashOut,
            'expected_cash'     => $shift->calculateExpectedCash(),
        ];
    }

    // =============================================
    //  SHIFT MANAGEMENT
    // =============================================

    public function openShift()
    {
        if ($this->openingCash < 0) {
            $this->addError('openingCash', 'Modal awal tidak boleh negatif.');
            return;
        }

        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user) {
            return;
        }

        // Tentukan branch_id dari konteks user
        $branchId = $user->employee?->branch_id
            ?? $user->branches()->first()?->id;

        if (!$branchId && $user->hasRole('super_admin')) {
            $branchId = \App\Models\Branch::first()?->id;
        }

        if (!$branchId) {
            $this->addError('openingCash', 'Akun ini belum terhubung ke cabang manapun.');
            return;
        }

        // Cek tidak boleh punya lebih dari 1 shift open
        $existingOpen = CashierShift::withoutGlobalScopes()
            ->open()
            ->byUser($user->id)
            ->exists();

        if ($existingOpen) {
            $this->addError('openingCash', 'Anda sudah memiliki shift yang masih aktif.');
            return;
        }

        CashierShift::create([
            'user_id'      => $user->id,
            'branch_id'    => $branchId,
            'start_at'     => now(),
            'opening_cash' => $this->openingCash,
            'status'       => 'open',
        ]);

        $this->openingCash = 0;

        $this->dispatch('shift-opened');
    }

    #[On('openCloseShiftFromLayout')]
    public function openCloseShiftModal()
    {
        $this->showCloseShiftModal = true;
        $this->actualCash = 0;
        $this->closingNotes = '';
    }

    public function closeShift()
    {
        $shift = $this->getActiveShift();
        if (!$shift) {
            return;
        }

        if ($this->actualCash < 0) {
            $this->addError('actualCash', 'Kas aktual tidak boleh negatif.');
            return;
        }

        $shift->close($this->actualCash, !empty($this->closingNotes) ? $this->closingNotes : null);

        // $this->dispatch('shift-closed');

        $this->showCloseShiftModal = false;
        $this->actualCash = 0;
        $this->closingNotes = '';

        Auth::logout();
        if (request()->hasSession()) {
            request()->session()->invalidate();
            request()->session()->regenerateToken();
        }

        return redirect('/admin/login');
    }

    public function changeShift()
    {
        $shift = $this->getActiveShift();
        if (!$shift) {
            return;
        }

        if ($this->actualCash < 0) {
            $this->addError('actualCash', 'Kas aktual tidak boleh negatif.');
            return;
        }

        $shift->close($this->actualCash, !empty($this->closingNotes) ? $this->closingNotes : null);

        $handover = [
            'user'        => $shift->user->name ?? Auth::user()?->name ?? 'Kasir',
            'end_at'      => $shift->end_at?->timezone(config('app.timezone'))->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i'),
            'actual_cash' => (float) $this->actualCash,
        ];

        // create a short-lived cookie (5 minutes) to carry handover info across logout
        Cookie::queue('previous_shift_info', json_encode($handover), 5);

        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/admin/login');
    }

    // =============================================
    //  CASH MOVEMENT
    // =============================================

    #[On('openCashMovementFromLayout')]
    public function openCashMovementModal()
    {
        $this->showCashMovementModal = true;
        $this->cashMovementType = 'in';
        $this->cashMovementAmount = 0;
        $this->cashMovementReason = '';
    }

    public function saveCashMovement()
    {
        $shift = $this->getActiveShift();
        if (!$shift) {
            $this->addError('cashMovement', 'Tidak ada shift aktif.');
            return;
        }

        if ($this->cashMovementAmount <= 0) {
            $this->addError('cashMovement', 'Jumlah harus lebih dari 0.');
            return;
        }

        if (empty($this->cashMovementReason)) {
            $this->addError('cashMovement', 'Alasan wajib diisi.');
            return;
        }

        if ($shift->status !== 'open') {
            $this->addError('cashMovement', 'Shift tidak aktif, tidak dapat menambah cash movement.');
            return;
        }

        try {
            if (empty($shift->branch_id) && Auth::user() && Auth::user()->branches()->exists()) {
                $firstBranchId = Auth::user()->branches()->first()->id;
                $shift->branch_id = $firstBranchId;
                $shift->save();
            }

            CashMovement::withoutEvents(function () use ($shift) {
                CashMovement::create([
                    'cashier_shift_id' => $shift->id,
                    'user_id'          => Auth::id(),
                    'type'             => $this->cashMovementType,
                    'amount'           => $this->cashMovementAmount,
                    'reason'           => $this->cashMovementReason,
                ]);
            });
        } catch (\RuntimeException $e) {
            $this->addError('cashMovement', $e->getMessage());
            return;
        }

        $this->showCashMovementModal = false;
        $this->cashMovementAmount = 0;
        $this->cashMovementReason = '';
    }

    //  CUSTOMER

    public function updatedCustomerSearch()
    {
        if (strlen($this->customerSearch) < 2) {
            $this->customerSuggestions = [];
            return;
        }

        $this->customerSuggestions = Customer::where('name', 'like', '%' . $this->customerSearch . '%')
            ->orWhere('phone', 'like', '%' . $this->customerSearch . '%')
            ->limit(5)
            ->get()
            ->toArray();
    }

    public function selectCustomer($id, $name)
    {
        $this->selectedCustomerId = $id;
        $this->customerName = $name;
        $this->customerSearch = '';
        $this->customerSuggestions = [];
        $this->showCustomerModal = false;
    }

    public function clearCustomer()
    {
        $this->selectedCustomerId = null;
        $this->customerName = '';
        $this->customerSearch = '';
        $this->customerPhone = '';
        $this->customerSuggestions = [];
        $this->showCustomerModal = false;
    }

    // =============================================
    //  RESERVASI
    // =============================================

    public function approveReservation($id)
    {
        $reservation = \App\Models\Reservation::find($id);
        if ($reservation && $reservation->status === 'pending') {
            $reservation->update(['status' => 'arrived']);
        }
    }

    public function cancelReservation($id)
    {
        $reservation = \App\Models\Reservation::with('customer')->find($id);
        if ($reservation && $reservation->status === 'pending') {
            $reservation->update(['status' => 'cancelled']);
            if ($reservation->customer) {
                $reservation->customer->notify(new \App\Notifications\ReservationCancelled($reservation));
            }
        }
    }

    public function loadReservationToCart($reservationId)
    {
        $reservation = \App\Models\Reservation::with(['customer', 'services'])->find($reservationId);
        if (!$reservation) {
            return;
        }

        $this->processedReservationId = $reservation->id;
        $this->cart = [];

        if ($reservation->customer) {
            $this->selectCustomer($reservation->customer->id, $reservation->customer->name);
        }

        if ($reservation->services) {
            foreach ($reservation->services as $service) {
                $this->addToCart($service->id, 'service');
            }
        }

        $this->activeTab = 'service';
    }

    //  CART

    public function addToCart($itemId, $itemType)
    {
        if ($itemType === 'service') {
            $item = Service::find($itemId);
            if (!$item || !$item->is_active) {
                return;
            }
        } else {
            $item = Product::find($itemId);
            if (!$item || !$item->is_active || $item->stock <= 0) {
                return;
            }
        }

        $cartKey = $itemType . '_' . $itemId;

        if (isset($this->cart[$cartKey])) {
            $newQty = $this->cart[$cartKey]['quantity'] + 1;

            if ($itemType === 'product' && $newQty > $item->stock) {
                $this->addError("cart.{$cartKey}", 'Stok tidak mencukupi');
                return;
            }

            $this->cart[$cartKey]['quantity'] = $newQty;
            $this->cart[$cartKey]['subtotal'] = $newQty * $item->price;
        } else {
            $this->cart[$cartKey] = [
                'id'       => $itemId,
                'type'     => $itemType,
                'name'     => $item->name,
                'price'    => $item->price,
                'quantity' => 1,
                'subtotal' => $item->price,
            ];
        }
    }

    public function removeFromCart($cartKey)
    {
        unset($this->cart[$cartKey]);
    }

    public function updateQuantity($cartKey, $qty)
    {
        $qty = (int) $qty;

        if ($qty <= 0) {
            $this->removeFromCart($cartKey);
            return;
        }

        if (!isset($this->cart[$cartKey])) {
            return;
        }

        if ($this->cart[$cartKey]['type'] === 'product') {
            $product = Product::find($this->cart[$cartKey]['id']);
            if ($product && $qty > $product->stock) {
                $this->addError("cart.{$cartKey}", 'Stok tidak mencukupi');
                return;
            }
        }

        $this->cart[$cartKey]['quantity'] = $qty;
        $this->cart[$cartKey]['subtotal'] = $qty * $this->cart[$cartKey]['price'];
    }

    public function openPaymentModal()
    {
        if (empty($this->cart)) {
            return;
        }

        $this->resetErrorBag('paymentAmount');
        $this->showPaymentModal = true;
    }

    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
    }

    //  PROCESS TRANSACTION

    public function processTransaction()
    {
        // Prevent double processing
        if ($this->isProcessing) {
            return;
        }

        if (empty($this->cart)) {
            return;
        }

        // Validasi shift aktif
        $shift = $this->getActiveShift();
        if (!$shift) {
            $this->addError('general', 'Tidak ada shift aktif. Silakan buka shift terlebih dahulu.');
            return;
        }

        if ($this->paymentMethod === 'cash' && $this->paymentAmount < $this->total()) {
            $this->addError('paymentAmount', 'Uang pembayaran kurang');
            return;
        }

        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user) {
            $this->addError('general', 'User tidak terautentikasi.');
            return;
        }

        $employee = $user->employee;

        $branchId = $employee?->branch_id ?? $user->branches()->first()?->id;

        if (!$branchId) {
            if ($user->hasRole('super_admin')) {
                $branchId = \App\Models\Branch::first()?->id;
            }

            if (!$branchId) {
                $this->addError('general', 'Akses cabang tidak ditemukan untuk akun ini.');
                return;
            }
        }

        $employeeId = $employee?->id;
        if (!$employeeId) {
            $fallbackEmployee = \App\Models\Employee::where('branch_id', $branchId)->first();
            $employeeId = $fallbackEmployee?->id;

            if (!$employeeId) {
                $this->addError('general', 'Tidak dapat memproses! Cabang ini belum memiliki satupun Data Karyawan/Kapster terdaftar.');
                return;
            }
        }

        // Validasi ulang harga & stok dari DB (mencegah manipulasi state Livewire)
        foreach ($this->cart as $cartKey => $cartItem) {
            if ($cartItem['type'] === 'service') {
                $dbItem = Service::find($cartItem['id']);
                if (!$dbItem || !$dbItem->is_active) {
                    $this->addError('general', "Layanan '{$cartItem['name']}' tidak tersedia.");
                    return;
                }
                // Sinkronkan harga dari DB
                $this->cart[$cartKey]['price'] = (float) $dbItem->price;
                $this->cart[$cartKey]['subtotal'] = (float) $dbItem->price * $cartItem['quantity'];
            } else {
                $dbItem = Product::find($cartItem['id']);
                if (!$dbItem || !$dbItem->is_active) {
                    $this->addError('general', "Produk '{$cartItem['name']}' tidak tersedia.");
                    return;
                }
                if ($dbItem->stock < $cartItem['quantity']) {
                    $this->addError('general', "Stok '{$cartItem['name']}' tidak mencukupi (tersisa {$dbItem->stock}).");
                    return;
                }
                $this->cart[$cartKey]['price'] = (float) $dbItem->price;
                $this->cart[$cartKey]['subtotal'] = (float) $dbItem->price * $cartItem['quantity'];
            }
        }

        DB::beginTransaction();

        try {
            $this->isProcessing = true;
            if (!empty($this->customerName) && !$this->selectedCustomerId) {
                $customer = Customer::create([
                    'name'  => $this->customerName,
                    'phone' => !empty($this->customerPhone) ? $this->customerPhone : null,
                ]);
                $this->selectedCustomerId = $customer->id;
            }

            // 2. Buat Transaksi Induk (dengan link ke shift)
            $transaction = Transaction::create([
                'branch_id'        => $branchId,
                'cashier_shift_id' => $shift->id,
                'customer_id'      => $this->selectedCustomerId,
                'employee_id'      => $employeeId,
                'transaction_date' => now(),
                'total_amount'     => $this->total(),
                'discount_type'    => $this->discountValue > 0 ? $this->discountType : null,
                'discount_value'   => $this->discountValue,
                'discount_amount'  => $this->discountAmount(),
                'payment_method'   => $this->paymentMethod,
                'status'           => 'completed',
            ]);

            // 3. Simpan Item & Kurangi Stok
            foreach ($this->cart as $cartItem) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'item_type'      => $cartItem['type'],
                    'service_id'     => $cartItem['type'] === 'service' ? $cartItem['id'] : null,
                    'product_id'     => $cartItem['type'] === 'product' ? $cartItem['id'] : null,
                    'employee_id'    => null,
                    'quantity'       => $cartItem['quantity'],
                    'price'          => $cartItem['price'],
                    'subtotal'       => $cartItem['subtotal'],
                ]);

                if ($cartItem['type'] === 'product') {
                    Product::where('id', $cartItem['id'])->decrement('stock', $cartItem['quantity']);
                }
            }

            // 4. Buat Payment record (link ke shift)
            Payment::create([
                'transaction_id'   => $transaction->id,
                'cashier_shift_id' => $shift->id,
                'method'           => $this->paymentMethod,
                'amount'           => $this->total(),
            ]);

            DB::commit();

            // Tambahkan loyalty points ke customer (1 poin per Rp 1.000 transaksi)
            if ($this->selectedCustomerId) {
                $earnedPoints = (int) floor($this->total() / 1000);
                if ($earnedPoints > 0) {
                    Customer::find($this->selectedCustomerId)?->addLoyaltyPoints($earnedPoints);
                }
            }

            // Otomatis ubah status Reservasinya
            if ($this->processedReservationId) {
                $reservation = \App\Models\Reservation::find($this->processedReservationId);
                if ($reservation) {
                    $reservation->update(['status' => 'completed']);

                    $serviceIds = collect($this->cart)
                        ->filter(fn($item) => $item['type'] === 'service')
                        ->pluck('id')
                        ->toArray();
                    $reservation->services()->sync($serviceIds);
                }
            }

            // Set variables untuk cetak
            $this->completedTransactionId = $transaction->id;
            $this->completedInvoiceNumber = $transaction->fresh()->invoice_number;

            // Reset UI
            $this->cart = [];
            $this->clearCustomer();
            $this->paymentAmount = null;
            $this->discountValue = 0;
            $this->discountType = 'nominal';
            $this->processedReservationId = null;
            $this->showPaymentModal = false;

            $this->dispatch('transaction-completed');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('general', 'Gagal memproses transaksi: ' . $e->getMessage());
        } finally {
            $this->isProcessing = false;
        }
    }

    //  RENDER

    public function render()
    {
        return view('livewire.pos-kasir', [
            'reservationsData' => $this->getReservationsList(),
            'productsData'     => $this->getProductsList(),
            'shiftSummary'     => $this->showCloseShiftModal ? $this->getShiftSummary() : [],
        ]);
    }
}
