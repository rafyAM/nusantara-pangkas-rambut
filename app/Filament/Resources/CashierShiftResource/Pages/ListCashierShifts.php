<?php

namespace App\Filament\Resources\CashierShiftResource\Pages;

use App\Exports\CashierShiftsExport;
use App\Filament\Resources\CashierShiftResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListCashierShifts extends ListRecords
{
    protected static string $resource = CashierShiftResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export')
                ->label('Export Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->form([
                    Forms\Components\DatePicker::make('start_date')
                        ->label('Dari Tanggal')
                        ->default(now()->startOfMonth()),
                    Forms\Components\DatePicker::make('end_date')
                        ->label('Sampai Tanggal')
                        ->default(now()),
                ])
                ->action(function (array $data) {
                    $filename = 'shift-kasir-' . now()->format('Ymd-His') . '.xlsx';
                    return Excel::download(
                        new CashierShiftsExport(
                            startDate: $data['start_date'] ?? null,
                            endDate: $data['end_date'] ?? null,
                        ),
                        $filename
                    );
                }),
        ];
    }
}