<?php

namespace App\Exports;

use App\Models\CashierShift;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CashierShiftsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    public function __construct(
        protected ?string $startDate = null,
        protected ?string $endDate = null,
    ) {}

    public function query(): Builder
    {
        return CashierShift::with(['user', 'branch'])
            ->when($this->startDate, fn ($q) => $q->whereDate('start_at', '>=', $this->startDate))
            ->when($this->endDate, fn ($q) => $q->whereDate('start_at', '<=', $this->endDate))
            ->orderBy('start_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'Cabang',
            'Kasir',
            'Mulai',
            'Selesai',
            'Modal Awal (Rp)',
            'Expected (Rp)',
            'Aktual (Rp)',
            'Selisih (Rp)',
            'Status',
        ];
    }

    public function map($row): array
    {
        return [
            $row->branch?->name ?? '-',
            $row->user?->name ?? '-',
            $row->start_at?->timezone(config('app.timezone'))->format('d/m/Y H:i'),
            $row->end_at?->timezone(config('app.timezone'))->format('d/m/Y H:i') ?? '-',
            (float) $row->opening_cash,
            (float) ($row->expected_cash ?? 0),
            (float) ($row->actual_cash ?? 0),
            (float) ($row->difference ?? 0),
            $row->status === 'open' ? 'Aktif' : 'Ditutup',
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
        return 'Shift Kasir';
    }
}
