<?php

namespace App\Filament\Resources\CashierShiftResource\Pages;

use App\Filament\Resources\CashierShiftResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCashierShifts extends ListRecords
{
    protected static string $resource = CashierShiftResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Kosongkan karena kita tidak ingin ada tombol "New Shift" di halaman admin
        ];
    }
}