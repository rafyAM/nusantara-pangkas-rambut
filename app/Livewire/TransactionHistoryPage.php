<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use App\Models\Transaction;
use App\Models\CashierShift;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.kasir')]
class TransactionHistoryPage extends Component
{
    // --- Filter ---
    public ?int $selectedShiftId = null;
    public string $filterPaymentMethod = 'all';
    public string $searchQuery = '';
    public string $sortBy = 'recent'; // recent, oldest, highest, lowest

    // =============================================
    //  COMPUTED PROPERTIES
    // =============================================

    #[Computed]
    public function availableShifts()
    {
        $user = Auth::user();
        if (!$user) {
            return collect();
        }

        return CashierShift::withoutGlobalScopes()
            ->byUser($user->id)
            ->orderBy('start_at', 'desc')
            ->limit(10)
            ->get();
    }

    #[Computed]
    public function transactionList()
    {
        $user = Auth::user();
        if (!$user) {
            return collect();
        }

        $query = Transaction::with(['customer', 'employee', 'transactionItems'])
            ->where('status', 'completed');

        // Filter by shift
        if ($this->selectedShiftId) {
            $query->where('cashier_shift_id', $this->selectedShiftId);
        } else {
            // Default: current shift or latest shift
            $currentShift = $this->getCurrentShift();
            if ($currentShift) {
                $query->where('cashier_shift_id', $currentShift->id);
            } else {
                // Show latest shift's transactions
                $latestShift = CashierShift::withoutGlobalScopes()
                    ->byUser($user->id)
                    ->latest('end_at')
                    ->first();
                if ($latestShift) {
                    $query->where('cashier_shift_id', $latestShift->id);
                    $this->selectedShiftId = $latestShift->id;
                }
            }
        }

        // Filter by payment method
        if ($this->filterPaymentMethod !== 'all') {
            $query->where('payment_method', $this->filterPaymentMethod);
        }

        // Search by customer name or transaction ID
        if (!empty($this->searchQuery)) {
            $search = $this->searchQuery;
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', function ($customerQuery) use ($search) {
                    $customerQuery->where('name', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%');
                })
                ->orWhereRaw('CAST(id AS CHAR) LIKE ?', ['%' . $search . '%']);
            });
        }

        // Sort
        match ($this->sortBy) {
            'oldest' => $query->orderBy('transaction_date', 'asc'),
            'highest' => $query->orderByDesc('total_amount'),
            'lowest' => $query->orderBy('total_amount'),
            default => $query->orderBy('transaction_date', 'desc'),
        };

        return $query->get();
    }

    #[Computed]
    public function totalSales()
    {
        return $this->transactionList->sum('total_amount');
    }

    #[Computed]
    public function totalTransactions()
    {
        return count($this->transactionList);
    }

    #[Computed]
    public function paymentMethodSummary()
    {
        $methods = ['cash', 'qris', 'transfer', 'e_wallet', 'debit_card', 'credit_card'];
        $summary = [];

        foreach ($methods as $method) {
            $amount = $this->transactionList
                ->filter(fn($t) => $t->payment_method === $method)
                ->sum('total_amount');
            if ($amount > 0) {
                $summary[$method] = $amount;
            }
        }

        return $summary;
    }

    // =============================================
    //  PRIVATE HELPERS
    // =============================================

    private function getCurrentShift()
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }

        return CashierShift::withoutGlobalScopes()
            ->open()
            ->byUser($user->id)
            ->latest()
            ->first();
    }

    // =============================================
    //  RENDER
    // =============================================

    public function render()
    {
        return view('livewire.transaction-history', [
            'availableShifts' => $this->availableShifts,
            'transactionList' => $this->transactionList,
            'totalSales' => $this->totalSales,
            'totalTransactions' => $this->totalTransactions,
            'paymentMethodSummary' => $this->paymentMethodSummary,
        ]);
    }
}
