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

    #[Computed]
    public function products()
    {
        /** @var \App\Models\User|null $user */
        $user = \Illuminate\Support\Facades\Auth::user();
        if (!$user) {
            return collect();
        }

        $branchId = $user->employee?->branch_id ?? $user->branches()->first()?->id;

        return Product::where('is_active', true)
            ->where('branch_id', $branchId)
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
            $this->addError('general', 'Akses cabang tidak ditemukan untuk akun ini.');
            return;
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
                'employee_id' => $employee?->id, // Kasir
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

            // Set variables untuk cetak
            $this->completedTransactionId = $transaction->id;
            $this->completedInvoiceNumber = $transaction->fresh()->invoice_number;

            // Reset UI
            $this->cart = [];
            $this->clearCustomer();
            $this->paymentAmount = 0;
            $this->discountValue = 0;

            // Peringatkan Alpine/Browser untuk membuka modal
            $this->dispatch('transaction-completed');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('general', 'Gagal memproses transaksi: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.pos-kasir');
    }
}
