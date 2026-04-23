<?php

namespace App\Filament\Resources\BranchScheduleResource\Pages;

use App\Filament\Resources\BranchScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBranchSchedule extends EditRecord
{
    protected static string $resource = BranchScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
