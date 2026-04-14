<?php

namespace App\Filament\Resources\CashierShiftResource\Pages;

use App\Filament\Resources\CashierShiftResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCashierShift extends ViewRecord
{
    protected static string $resource = CashierShiftResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Kosongkan karena kita tidak ingin ada tombol "Edit" di halaman admin
        ];
    }
}