<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalRevenue = Transaction::sum('total_amount');
        $totalTransactions = Transaction::count();
        $avgTransaction = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        return [
            Stat::make('Total Pendapatan', 'Rp ' . Number::format($totalRevenue, locale: 'id'))
                ->description('Total omzet dari seluruh cabang')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]), 

            Stat::make('Total Transaksi', Number::format($totalTransactions, locale: 'id'))
                ->description('Jumlah layanan selesai')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('primary'),

            Stat::make('Rata-rata Transaksi', 'Rp ' . Number::format($avgTransaction, locale: 'id'))
                ->description('Rata-rata pengeluaran per pelanggan')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('warning'),
        ];
    }
}
