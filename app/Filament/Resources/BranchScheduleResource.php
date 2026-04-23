<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BranchScheduleResource\Pages;
use App\Models\BranchSchedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BranchScheduleResource extends Resource
{
    protected static ?string $model = BranchSchedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Manajemen Cabang';

    protected static ?string $navigationLabel = 'Jam Operasional';

    protected static ?string $modelLabel = 'Jam Operasional';

    protected static ?string $pluralModelLabel = 'Jam Operasional';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('branch_id')
                            ->label('Cabang')
                            ->relationship('branch', 'name')
                            ->required()
                            ->preload(),
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
                            ->label('Hari Libur')
                            ->helperText('Aktifkan jika cabang tutup pada hari ini'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Cabang')
                    ->sortable()
                    ->searchable(),
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
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success'),
            ])
            ->defaultSort('branch_id')
            ->filters([
                Tables\Filters\SelectFilter::make('branch_id')
                    ->label('Cabang')
                    ->relationship('branch', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBranchSchedules::route('/'),
            'create' => Pages\CreateBranchSchedule::route('/create'),
            'edit'   => Pages\EditBranchSchedule::route('/{record}/edit'),
        ];
    }
}
