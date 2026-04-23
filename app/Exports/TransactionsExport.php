<?php

namespace App\Exports;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    public function __construct(
        protected ?string $startDate = null,
        protected ?string $endDate = null,
        protected ?int $branchId = null,
        protected ?string $status = null,
    ) {}

    public function query(): Builder
    {
        return Transaction::withoutGlobalScopes()
            ->with(['customer', 'employee', 'branch'])
            ->when($this->startDate, fn ($q) => $q->whereDate('transaction_date', '>=', $this->startDate))
            ->when($this->endDate, fn ($q) => $q->whereDate('transaction_date', '<=', $this->endDate))
            ->when($this->branchId, fn ($q) => $q->where('branch_id', $this->branchId))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->orderBy('transaction_date', 'desc');
    }

    public function headings(): array
    {
        return [
            'No. Invoice',
            'Tanggal',
            'Pelanggan',
            'Karyawan',
            'Cabang',
            'Total (Rp)',
            'Diskon (Rp)',
            'Metode Pembayaran',
            'Status',
        ];
    }

    public function map($row): array
    {
        return [
            $row->invoice_number,
            $row->transaction_date?->format('d/m/Y H:i'),
            $row->customer?->name ?? 'Walk-in',
            $row->employee?->name ?? '-',
            $row->branch?->name ?? '-',
            (float) $row->total_amount,
            (float) $row->discount_amount,
            match ($row->payment_method) {
                'cash'        => 'Tunai',
                'qris'        => 'QRIS',
                'transfer'    => 'Transfer Bank',
                'e_wallet'    => 'E-Wallet',
                'debit_card'  => 'Kartu Debit',
                'credit_card' => 'Kartu Kredit',
                default       => $row->payment_method,
            },
            match ($row->status) {
                'completed' => 'Selesai',
                'pending'   => 'Menunggu',
                'cancelled' => 'Dibatalkan',
                default     => $row->status,
            },
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Transaksi';
    }
}
