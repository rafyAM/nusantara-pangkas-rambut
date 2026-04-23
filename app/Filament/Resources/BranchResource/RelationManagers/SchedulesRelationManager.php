<?php

namespace App\Filament\Resources\BranchResource\RelationManagers;

use App\Models\BranchSchedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SchedulesRelationManager extends RelationManager
{
    protected static string $relationship = 'schedules';

    protected static ?string $title = 'Jam Operasional';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('day_of_week')
                    ->label('Hari')
                    ->options(BranchSchedule::$dayNames)
                    ->required(),
                Forms\Components\TimePicker::make('open_time')
                    ->label('Jam Buka')
                    ->seconds(false)
                    ->required(),
                Forms\Components\TimePicker::make('close_time')
                    ->label('Jam Tutup')
                    ->seconds(false)
                    ->required(),
                Forms\Components\Toggle::make('is_closed')
                    ->label('Hari Libur'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('day_of_week')
                    ->label('Hari')
                    ->formatStateUsing(fn (int $state): string => BranchSchedule::$dayNames[$state] ?? '-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('open_time')
                    ->label('Jam Buka'),
                Tables\Columns\TextColumn::make('close_time')
                    ->label('Jam Tutup'),
                Tables\Columns\IconColumn::make('is_closed')
                    ->label('Libur')
                    ->boolean()
                    ->trueColor('danger')
                    ->falseColor('success'),
            ])
            ->defaultSort('day_of_week')
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Tambah Jadwal'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
