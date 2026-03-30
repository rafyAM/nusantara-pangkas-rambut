<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use App\Models\Service;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.kasir')]
class PosKasir extends Component
{
    public array $cart = [];
    public string $search = '';
    public string $activeTab = 'service';

    public string $customerName = '';
    public string $customerSearch = '';
    public array $customerSuggestions = [];
    public ?int $selectedCustomerId = null;

    // Track reservasi agar otomatis statusnya menjadi completed saat dibayar
    public ?int $processedReservationId = null;

    public string $discountType = 'nominal';
    public float $discountValue = 0;

    public string $paymentMethod = 'cash';
    public float $paymentAmount = 0;

    public ?int $completedTransactionId = null;
    public string $completedInvoiceNumber = '';
    // --- Computed Properties ---

    #[Computed]
    public function services()
    {
        return Service::where('is_active', true)
            ->where('name', 'like', '%' . $this->search . '%')
            ->get();
    }

    private function getReservationsList()
    {
        /** @var \App\Models\User|null $user */
        $user = \Illuminate\Support\Facades\Auth::user();
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
            $query->whereHas('customer', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        return $query->get();
    }

    private function getProductsList()
    {
        /** @var \App\Models\User|null $user */
        $user = \Illuminate\Support\Facades\Auth::user();
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
        if ($this->paymentMethod !== 'cash')
            return 0;
        return max(0, $this->paymentAmount - $this->total());
    }

    // --- Methods ---

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
    }

    public function clearCustomer()
    {
        $this->selectedCustomerId = null;
        $this->customerName = '';
        $this->customerSearch = '';
    }

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

        // Tandai reservasi ini di memori agar bisa di-finish (completed) jika checkout
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

    public function addToCart($itemId, $itemType)
    {
        if ($itemType === 'service') {
            $item = Service::find($itemId);
            if (!$item || !$item->is_active)
                return;
        } else {
            $item = Product::find($itemId);
            if (!$item || !$item->is_active || $item->stock <= 0)
                return;
        }

        $cartKey = $itemType . '_' . $itemId;

        if (isset($this->cart[$cartKey])) {
            $newQty = $this->cart[$cartKey]['quantity'] + 1;

            // Cek stok produk saat increment
            if ($itemType === 'product' && $newQty > $item->stock) {
                $this->addError("cart.{$cartKey}", 'Stok tidak mencukupi');
                return;
            }

            $this->cart[$cartKey]['quantity'] = $newQty;
            $this->cart[$cartKey]['subtotal'] = $newQty * $item->price;
        } else {
            $this->cart[$cartKey] = [
                'id' => $itemId,
                'type' => $itemType,
                'name' => $item->name,
                'price' => $item->price,
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

        if (!isset($this->cart[$cartKey]))
            return;

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

    public function processTransaction()
    {
        if (empty($this->cart))
            return;

        if ($this->paymentMethod === 'cash' && $this->paymentAmount < $this->total()) {
            $this->addError('paymentAmount', 'Uang pembayaran kurang');
            return;
        }

        /** @var \App\Models\User|null $user */
        $user = \Illuminate\Support\Facades\Auth::user();
        if (!$user) {
            $this->addError('general', 'User tidak terautentikasi.');
            return;
        }

        $employee = $user->employee;

        // Dapatkan branch_id: prioritas dari relasi employee -> branches pivot
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

        // Pastikan Data Karyawan (employee_id) ada agar tidak melanggar Schema Database yang Not Null
        $employeeId = $employee?->id;
        if (!$employeeId) {
            // Jika akun kasir belum ditautkan ke profil karyawan, pinjam profil karyawan pertama di cabang ini
            $fallbackEmployee = \App\Models\Employee::where('branch_id', $branchId)->first();
            $employeeId = $fallbackEmployee?->id;
            
            if (!$employeeId) {
                $this->addError('general', 'Tidak dapat memproses! Cabang ini belum memiliki satupun Data Karyawan/Kapster terdaftar.');
                return;
            }
        }

        DB::beginTransaction();

        try {
            // 1. Tangani Customer (jika input manual dan belum ada di sistem)
            if (!empty($this->customerName) && !$this->selectedCustomerId) {
                $customer = Customer::create([
                    'name' => $this->customerName,
                    'phone' => $this->customerSearch // Use search input as phone if manual
                ]);
                $this->selectedCustomerId = $customer->id;
            }

            // 2. Buat Transaksi Induk
            $transaction = Transaction::create([
                // invoice_number auto generated via model booted
                'branch_id' => $branchId,
                'customer_id' => $this->selectedCustomerId,
                'employee_id' => $employeeId, // Kasir or Fallback Employee
                'transaction_date' => now(),
                'total_amount' => $this->total(),
                'payment_method' => $this->paymentMethod,
                'status' => 'completed',
                'notes' => $this->discountAmount() > 0 ? "Diskon: Rp" . number_format($this->discountAmount()) : null,
            ]);

            // 3. Simpan Item & Kurangi Stok
            foreach ($this->cart as $cartItem) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'item_type' => $cartItem['type'],
                    'service_id' => $cartItem['type'] === 'service' ? $cartItem['id'] : null,
                    'product_id' => $cartItem['type'] === 'product' ? $cartItem['id'] : null,
                    'employee_id' => null, // Tidak ada tracking barber di POS kasir per requirement
                    'quantity' => $cartItem['quantity'],
                    'price' => $cartItem['price'],
                    'subtotal' => $cartItem['subtotal'],
                ]);

                // Deduction Stok Produk
                if ($cartItem['type'] === 'product') {
                    Product::where('id', $cartItem['id'])->decrement('stock', $cartItem['quantity']);
                }
            }

            DB::commit();

            // Otomatis ubah status Reservasinya jadi "Completed" (Selesai/Lunas)
            if ($this->processedReservationId) {
                \App\Models\Reservation::where('id', $this->processedReservationId)->update(['status' => 'completed']);
            }

            // Set variables untuk cetak
            $this->completedTransactionId = $transaction->id;
            $this->completedInvoiceNumber = $transaction->fresh()->invoice_number;

            // Reset UI
            $this->cart = [];
            $this->clearCustomer();
            $this->paymentAmount = 0;
            $this->discountValue = 0;
            $this->processedReservationId = null;

            // Peringatkan Alpine/Browser untuk membuka modal
            $this->dispatch('transaction-completed');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('general', 'Gagal memproses transaksi: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.pos-kasir', [
            'reservationsData' => $this->getReservationsList(),
            'productsData' => $this->getProductsList()
        ]);
    }
}
