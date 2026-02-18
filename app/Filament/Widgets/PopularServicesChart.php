<?php

namespace App\Filament\Widgets;

use App\Models\TransactionItem;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PopularServicesChart extends ChartWidget
{
    protected static ?string $heading = 'Layanan Populer';
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = 1;

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'right',
                ],
            ],
            'maintainAspectRatio' => false,
            'aspectRatio' => 2,
        ];
    }

    protected function getData(): array
    {
        $data = TransactionItem::select('service_id', DB::raw('COUNT(*) as total'))
            ->groupBy('service_id')
            ->with('service')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $labels = $data->map(fn($item) => $item->service->name ?? 'Unknown')->toArray();
        $values = $data->map(fn($item) => $item->total)->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pesanan',
                    'data' => $values,
                    'backgroundColor' => [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF'
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
