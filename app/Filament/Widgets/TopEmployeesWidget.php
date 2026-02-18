<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class TopEmployeesWidget extends BaseWidget
{
    protected static ?int $sort = 5;
    protected static ?string $heading = 'Top Performa Pegawai (Berdasarkan Transaksi)';
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Employee::query()
                    ->withCount('transactions')
                    ->orderByDesc('transactions_count')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Pegawai'),
                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Cabang'),
                Tables\Columns\TextColumn::make('transactions_count')
                    ->label('Total Transaksi')
                    ->sortable(),
            ]);
    }
}
