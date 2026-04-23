<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Exports\TransactionsExport;
use App\Filament\Resources\TransactionResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

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
                    Forms\Components\Select::make('branch_id')
                        ->label('Cabang')
                        ->relationship('branch', 'name')
                        ->placeholder('Semua Cabang')
                        ->searchable()
                        ->preload(),
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'completed' => 'Selesai',
                            'pending'   => 'Menunggu',
                            'cancelled' => 'Dibatalkan',
                        ])
                        ->placeholder('Semua Status'),
                ])
                ->action(function (array $data) {
                    $filename = 'transaksi-' . now()->format('Ymd-His') . '.xlsx';
                    return Excel::download(
                        new TransactionsExport(
                            startDate: $data['start_date'] ?? null,
                            endDate: $data['end_date'] ?? null,
                            branchId: $data['branch_id'] ?? null,
                            status: $data['status'] ?? null,
                        ),
                        $filename
                    );
                }),
        ];
    }
}
