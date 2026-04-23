<?php

namespace App\Filament\Resources\BranchScheduleResource\Pages;

use App\Filament\Resources\BranchScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBranchSchedules extends ListRecords
{
    protected static string $resource = BranchScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
