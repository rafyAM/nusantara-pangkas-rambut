<?php

namespace App\Filament\Widgets;

use App\Models\Branch;
use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class BranchPerformanceChart extends ChartWidget
{
    protected static ?string $heading = 'Performa Cabang (Pendapatan)';
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 1;

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'aspectRatio' => 2,
        ];
    }

    protected function getData(): array
    {
        $data = Transaction::select('branch_id', DB::raw('SUM(total_amount) as total'))
            ->groupBy('branch_id')
            ->with('branch')
            ->get();

        $labels = $data->map(fn($item) => $item->branch->name ?? 'Unknown')->toArray();
        $values = $data->map(fn($item) => $item->total)->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan',
                    'data' => $values,
                    'backgroundColor' => '#36A2EB',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
