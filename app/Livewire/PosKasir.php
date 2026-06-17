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
use App\Models\BusinessDayReport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

#[Layout('layouts.kasir')]
class PosKasir extends Component
{
    // --- Cart & Katalog ---
    public array $cart = [];
    public ?int $cartKapsterId = null;
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
    public string $cashMovementCategory = 'operasional';

    // --- Handover Shift ---
    public ?array $previousShiftInfo = null;

    // --- Antrian Walk-in ---
    public ?int $printQueueReservationId = null;

    // --- Tutup Usaha (Business Day) ---
    public ?int $printBusinessDayReportId = null;

    // =============================================
    //  LIFECYCLE & COMPUTED PROPERTIES
    // =============================================

    public function mount()
    {
        $this->loadPreviousShiftHandover();
        
        $reservationId = request()->query('reservation_id');
        if ($reservationId) {
            $this->loadReservationToCart($reservationId);
        }
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
    public function activeQueues()
    {
        $user = Auth::user();
        if (!$user) {
            return collect();
        }

        $branchId = $user->employee?->branch_id ?? $user->branches()->first()?->id;

        $query = \App\Models\Reservation::with(['customer', 'services', 'employee'])
            ->activeQueue()
            ->orderBy('reservation_time', 'asc');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->get();
    }

    #[Computed]
    public function kapsters()
    {
        $user = Auth::user();
        if (!$user) {
            return collect();
        }

        $branchId = $user->employee?->branch_id ?? $user->branches()->first()?->id;

        $query = \App\Models\Employee::where('is_active', true);
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->orderBy('name')->get();
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
    //  TUTUP USAHA (BUSINESS DAY)
    // =============================================

    /**
     * Bangun ringkasan laporan hari operasional untuk cabang user saat ini.
     * Mengembalikan null jika belum ada shift hari ini.
     */
    #[Computed]
    public function businessDayReport(): ?array
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user) {
            return null;
        }

        // Prioritas branch_id: dari shift aktif user (paling akurat untuk konteks tutup usaha),
        // baru fallback ke employee/branches user atau super_admin default.
        $activeShift = $this->getActiveShift();
        $branchId = $activeShift?->branch_id
            ?? $user->employee?->branch_id
            ?? $user->branches()->first()?->id;
        if (!$branchId && $user->hasRole('super_admin')) {
            $branchId = \App\Models\Branch::first()?->id;
        }
        if (!$branchId) {
            return null;
        }

        $today = today();

        // Shift yang relevan untuk tutup usaha hari ini = shift yang punya transaksi/cash movement
        // bertanggal hari ini, ATAU shift yang sedang aktif sekarang.
        // Ini mencegah shift stale dari hari-hari sebelumnya (yang lupa ditutup) ikut terhitung.
        $shiftIdsFromTrx = Transaction::withoutGlobalScopes()
            ->where('branch_id', $branchId)
            ->whereDate('transaction_date', $today)
            ->where('status', 'completed')
            ->whereNotNull('cashier_shift_id')
            ->pluck('cashier_shift_id')
            ->unique();

        $shiftIdsFromCm = CashMovement::whereHas('cashierShift', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->whereDate('created_at', $today)
            ->pluck('cashier_shift_id')
            ->unique();

        $shiftIds = $shiftIdsFromTrx->merge($shiftIdsFromCm)->unique();

        if ($activeShift && $activeShift->branch_id == $branchId) {
            $shiftIds = $shiftIds->push($activeShift->id)->unique();
        }

        if ($shiftIds->isEmpty()) {
            return null;
        }

        $shifts = CashierShift::withoutGlobalScopes()
            ->whereIn('id', $shiftIds)
            ->with('user')
            ->orderBy('start_at')
            ->get();

        if ($shifts->isEmpty()) {
            return null;
        }

        $shiftIds = $shifts->pluck('id')->all();

        // Transaksi: scoped ke shift + tanggal hari ini (untuk aman jika shift aktif punya trx kemarin)
        $transactions = Transaction::withoutGlobalScopes()
            ->whereIn('cashier_shift_id', $shiftIds)
            ->whereDate('transaction_date', $today)
            ->where('status', 'completed')
            ->with(['items.service', 'items.product', 'items.employee', 'payments'])
            ->get();

        // Cash movement hari ini saja
        $cashMovements = CashMovement::whereIn('cashier_shift_id', $shiftIds)
            ->whereDate('created_at', $today)
            ->get();

        // Breakdown per shift
        $shiftsBreakdown = [];
        foreach ($shifts as $idx => $shift) {
            $shiftTrx = $transactions->where('cashier_shift_id', $shift->id);
            $itemsByService = [];
            $cashTotal = 0;
            $nonCashTotal = 0;

            foreach ($shiftTrx as $trx) {
                foreach ($trx->payments as $p) {
                    if ($p->method === 'cash') {
                        $cashTotal += (float) $p->amount;
                    } else {
                        $nonCashTotal += (float) $p->amount;
                    }
                }
                foreach ($trx->items as $item) {
                    if ($item->item_type !== 'service' || !$item->service) {
                        continue;
                    }
                    $svc = $item->service;
                    $sid = $svc->id;
                    if (!isset($itemsByService[$sid])) {
                        $itemsByService[$sid] = [
                            'service_id'     => $sid,
                            'service_name'   => $svc->name,
                            'qty'            => 0,
                            'total_harga'    => 0,
                            'komisi_kapster' => 0,
                            'komisi_owner'   => 0,
                        ];
                    }
                    $itemsByService[$sid]['qty']            += (int) $item->quantity;
                    $itemsByService[$sid]['total_harga']    += (float) $item->subtotal;
                    $itemsByService[$sid]['komisi_kapster'] += (float) $item->subtotal * ((float) $svc->commission_kapster_pct) / 100;
                    $itemsByService[$sid]['komisi_owner']   += (float) $item->subtotal * ((float) $svc->commission_owner_pct) / 100;
                }
            }

            $shiftsBreakdown[] = [
                'id'             => $shift->id,
                'shift_number'   => $idx + 1,
                'kasir_name'     => $shift->user->name ?? '—',
                'shift_start'    => $shift->start_at?->format('H:i'),
                'shift_end'      => $shift->end_at?->format('H:i'),
                'status'         => $shift->status,
                'opening_cash'   => (float) $shift->opening_cash,
                'order_count'    => $shiftTrx->count(),
                'cash_total'     => $cashTotal,
                'non_cash_total' => $nonCashTotal,
                'items'          => array_values($itemsByService),
            ];
        }

        // Komisi per kapster
        $komisiPerKapster = [];
        foreach ($transactions as $trx) {
            foreach ($trx->items as $item) {
                if ($item->item_type !== 'service' || !$item->service || !$item->employee) {
                    continue;
                }
                $key = $item->employee_id;
                if (!isset($komisiPerKapster[$key])) {
                    $komisiPerKapster[$key] = [
                        'employee_id' => $item->employee_id,
                        'name'        => $item->employee->name,
                        'amount'      => 0,
                        'jobs'        => 0,
                    ];
                }
                $komisiPerKapster[$key]['amount'] += (float) $item->subtotal * ((float) $item->service->commission_kapster_pct) / 100;
                $komisiPerKapster[$key]['jobs']   += (int) $item->quantity;
            }
        }

        // Totals
        $totalGross   = (float) $transactions->sum('total_amount');
        $totalCash    = 0;
        $totalNonCash = 0;
        foreach ($transactions as $trx) {
            foreach ($trx->payments as $p) {
                if ($p->method === 'cash') {
                    $totalCash += (float) $p->amount;
                } else {
                    $totalNonCash += (float) $p->amount;
                }
            }
        }
        $totalKomisiKapster = (float) array_sum(array_column($komisiPerKapster, 'amount'));

        // Fee kasir: grouped by kasir unik (bukan per-shift, supaya 1 kasir yang shift 2x tetap dihitung 1).
        // - 1 kasir & 1 shift penuh    → full_day @100k
        // - >1 kasir ATAU >1 shift     → masing-masing kasir half_day @50k
        $shiftCount      = $shifts->count();
        $uniqueKasirs    = $shifts->groupBy('user_id');
        $kasirCount      = $uniqueKasirs->count();
        $isFullDay       = ($kasirCount === 1 && $shiftCount === 1);
        $tipeHari        = $isFullDay ? 'full_day' : 'half_day';
        $feePerKasir     = $isFullDay ? 100000 : 50000;
        $feeKasirPerKasir = $uniqueKasirs->values()->map(function ($shiftsOfKasir, $i) use ($isFullDay, $feePerKasir) {
            $first = $shiftsOfKasir->first();
            return [
                'shift_id'     => $first->id,
                'shift_number' => $i + 1,
                'kasir_name'   => $first->user->name ?? '—',
                'tipe'         => $isFullDay ? 'full_day' : 'half_day',
                'fee_amount'   => (float) $feePerKasir,
            ];
        })->all();
        $totalFeeKasir = (float) array_sum(array_column($feeKasirPerKasir, 'fee_amount'));

        $totalOwnerNet = $totalGross - $totalKomisiKapster - $totalFeeKasir;

        // Cash movements
        $cashIn = (float) $cashMovements->where('type', 'in')->sum('amount');
        $cashOutCollection = $cashMovements->where('type', 'out');
        $cashOutByCategory = $cashOutCollection
            ->groupBy(fn($m) => $m->category ?: CashMovement::CATEGORY_OTHER)
            ->map(function ($group, $cat) {
                return [
                    'category' => $cat,
                    'label'    => CashMovement::CATEGORIES[$cat] ?? ucfirst(str_replace('_', ' ', $cat)),
                    'amount'   => (float) $group->sum('amount'),
                    'items'    => $group->map(fn($x) => [
                        'reason' => $x->reason,
                        'amount' => (float) $x->amount,
                    ])->values()->all(),
                ];
            })->values()->all();
        $totalCashOut             = (float) $cashOutCollection->sum('amount');
        $totalCashOutOperasional  = (float) $cashOutCollection->where('category', CashMovement::CATEGORY_OPERASIONAL)->sum('amount');

        // Rekonsiliasi
        $modalAwalHari    = (float) ($shifts->first()->opening_cash ?? 0);
        $expectedKasAkhir = $modalAwalHari + $totalCash + $cashIn - $totalCashOut;
        $selisih          = $this->actualCash > 0
            ? ((float) $this->actualCash - $expectedKasAkhir)
            : null;

        return [
            'business_date'              => $today->toDateString(),
            'branch_id'                  => $branchId,
            'shift_count'                => $shiftCount,
            'kasir_count'                => $kasirCount,
            'tipe_hari'                  => $tipeHari,
            'shifts'                     => $shiftsBreakdown,
            'total_orders'               => $transactions->count(),
            'total_gross'                => $totalGross,
            'total_cash'                 => $totalCash,
            'total_non_cash'             => $totalNonCash,
            'komisi_per_kapster'         => array_values($komisiPerKapster),
            'total_komisi_kapster'       => $totalKomisiKapster,
            'fee_kasir_per_kasir'        => $feeKasirPerKasir,
            'total_fee_kasir'            => $totalFeeKasir,
            'total_owner_net'            => $totalOwnerNet,
            'cash_in'                    => $cashIn,
            'cash_out_by_category'       => $cashOutByCategory,
            'total_cash_out'             => $totalCashOut,
            'total_cash_out_operasional' => $totalCashOutOperasional,
            'modal_awal_hari'            => $modalAwalHari,
            'expected_kas_akhir'         => $expectedKasAkhir,
            'actual_kas_akhir'           => (float) $this->actualCash,
            'selisih_kas'                => $selisih,
            'opened_at'                  => $shifts->first()->start_at?->toDateTimeString(),
        ];
    }

    /**
     * Tutup hari operasional: close shift saat ini + simpan BusinessDayReport.
     * Dipanggil dari modal Close Order. Mengeluarkan user setelah selesai (sama seperti closeShift).
     */
    public function tutupUsaha()
    {
        $shift = $this->getActiveShift();
        if (!$shift) {
            $this->addError('actualCash', 'Tidak ada shift aktif.');
            return;
        }

        if ($this->actualCash <= 0) {
            $this->addError('actualCash', 'Isi kas aktual di laci.');
            return;
        }

        // Cek tidak ada shift lain di cabang yang masih open
        $otherOpen = CashierShift::withoutGlobalScopes()
            ->where('branch_id', $shift->branch_id)
            ->where('status', 'open')
            ->where('id', '!=', $shift->id)
            ->exists();
        if ($otherOpen) {
            $this->addError('actualCash', 'Masih ada shift lain di cabang ini yang aktif. Tutup dulu sebelum Tutup Usaha.');
            return;
        }

        $report = $this->businessDayReport;
        if (!$report) {
            $this->addError('actualCash', 'Belum ada data shift hari ini.');
            return;
        }

        $expected = (float) $report['expected_kas_akhir'];
        $selisih  = (float) $this->actualCash - $expected;

        DB::beginTransaction();
        try {
            // 1) Tutup shift saat ini
            $shift->close($this->actualCash, !empty($this->closingNotes) ? $this->closingNotes : null);

            // 2) Simpan / update laporan hari operasional
            $bdr = BusinessDayReport::withoutGlobalScopes()->updateOrCreate(
                [
                    'branch_id'     => $report['branch_id'],
                    'business_date' => $report['business_date'],
                ],
                [
                    'opened_at'                  => $report['opened_at'],
                    'closed_at'                  => now(),
                    'total_orders'               => $report['total_orders'],
                    'total_gross'                => $report['total_gross'],
                    'total_cash'                 => $report['total_cash'],
                    'total_non_cash'             => $report['total_non_cash'],
                    'total_komisi_kapster'       => $report['total_komisi_kapster'],
                    'total_fee_kasir'            => $report['total_fee_kasir'],
                    'total_owner_net'            => $report['total_owner_net'],
                    'total_cash_out_operasional' => $report['total_cash_out_operasional'],
                    'modal_awal_hari'            => $report['modal_awal_hari'],
                    'expected_kas_akhir'         => $expected,
                    'actual_kas_akhir'           => (float) $this->actualCash,
                    'selisih_kas'                => $selisih,
                    'status'                     => BusinessDayReport::STATUS_CLOSED,
                    'closed_by'                  => Auth::id(),
                    'snapshot'                   => $report,
                    'notes'                      => $this->closingNotes ?: null,
                ]
            );

            DB::commit();

            // Pastikan tidak ada print-area lain yang tumpang tindih dengan laporan tutup usaha.
            $this->completedTransactionId   = null;
            $this->completedInvoiceNumber   = '';
            $this->printQueueReservationId  = null;
            $this->showCloseShiftModal      = false;

            $this->printBusinessDayReportId = $bdr->id;
            $this->dispatch('business-day-closed');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('actualCash', 'Gagal menutup usaha: ' . $e->getMessage());
        }
    }

    #[On('clearPrintBusinessDay')]
    public function clearPrintBusinessDay()
    {
        $this->printBusinessDayReportId = null;
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
        $this->cashMovementCategory = CashMovement::CATEGORY_OPERASIONAL;
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
                    'category'         => $this->cashMovementType === 'out'
                        ? ($this->cashMovementCategory ?: CashMovement::CATEGORY_OPERASIONAL)
                        : null,
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
    //  ANTRIAN WALK-IN
    // =============================================

    public function createQueueFromCart()
    {
        if (empty($this->cart)) {
            $this->addError('general', 'Tambahkan layanan ke cart sebelum membuat antrian.');
            return;
        }

        // Ambil service IDs dari cart (produk diabaikan — antrian = layanan)
        $serviceIds = collect($this->cart)
            ->filter(fn($item) => ($item['type'] ?? null) === 'service')
            ->pluck('id')
            ->unique()
            ->values()
            ->all();

        if (empty($serviceIds)) {
            $this->addError('general', 'Antrian harus berisi minimal 1 layanan.');
            return;
        }

        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user) {
            $this->addError('general', 'User tidak terautentikasi.');
            return;
        }

        $branchId = $user->employee?->branch_id ?? $user->branches()->first()?->id;
        if (!$branchId && $user->hasRole('super_admin')) {
            $branchId = \App\Models\Branch::first()?->id;
        }
        if (!$branchId) {
            $this->addError('general', 'Cabang tidak ditemukan untuk akun ini.');
            return;
        }

        // Resolve customer: existing > new (kalau ada nama + phone) > guest_name
        $customerId = $this->selectedCustomerId;
        $guestName  = null;

        if (!$customerId && !empty(trim($this->customerName))) {
            if (!empty(trim($this->customerPhone))) {
                $customer = Customer::firstOrCreate(
                    ['phone' => trim($this->customerPhone)],
                    ['name'  => trim($this->customerName)]
                );
                $customerId = $customer->id;
            } else {
                $guestName = trim($this->customerName);
            }
        }

        DB::beginTransaction();
        try {
            $reservation = \App\Models\Reservation::create([
                'customer_id'      => $customerId,
                'employee_id'      => $this->cartKapsterId ?: null,
                'branch_id'        => $branchId,
                'source'           => \App\Models\Reservation::SOURCE_WALK_IN,
                'guest_name'       => $guestName,
                'reservation_time' => now(),
                'status'           => 'arrived',
            ]);

            $reservation->services()->sync($serviceIds);

            DB::commit();

            // Reset cart setelah antrian dibuat
            $this->cart = [];
            $this->cartKapsterId = null;
            $this->clearCustomer();

            // Cegah tumpang tindih dengan print-area lain.
            $this->completedTransactionId   = null;
            $this->completedInvoiceNumber   = '';
            $this->printBusinessDayReportId = null;

            $this->printQueueReservationId = $reservation->id;
            $this->dispatch('queue-created');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('general', 'Gagal membuat antrian: ' . $e->getMessage());
        }
    }

    #[On('clearPrintQueue')]
    public function clearPrintQueue()
    {
        $this->printQueueReservationId = null;
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
        $this->cartKapsterId = $reservation->employee_id ?: null;

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

    public function hasServiceInCart(): bool
    {
        foreach ($this->cart as $item) {
            if (($item['type'] ?? null) === 'service') {
                return true;
            }
        }
        return false;
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

        // Validasi kapster: jika ada layanan di cart, kapster wajib dipilih
        if ($this->hasServiceInCart() && empty($this->cartKapsterId)) {
            $this->addError('cartKapsterId', 'Pilih kapster untuk transaksi ini.');
            $this->addError('general', 'Pilih kapster untuk transaksi ini.');
            return;
        }

        // Tentukan employee_id untuk transactions:
        // - Jika ada service di cart, pakai kapster yang dipilih.
        // - Jika murni produk, fallback ke employee user / pertama di cabang (kompatibilitas FK NOT NULL).
        if ($this->cartKapsterId) {
            $employeeId = (int) $this->cartKapsterId;
        } else {
            $employeeId = $employee?->id
                ?? \App\Models\Employee::where('branch_id', $branchId)->value('id');

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
                    'employee_id'    => $cartItem['type'] === 'service' ? $this->cartKapsterId : null,
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
            $this->cartKapsterId = null;
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
