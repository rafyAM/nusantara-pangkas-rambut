<?php

namespace App\Filament\Resources\BranchResource\RelationManagers;

use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ServicesRelationManager extends RelationManager
{
    protected static string $relationship = 'services';

    protected static ?string $title = 'Harga Layanan Cabang';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('service_id')
                    ->label('Layanan')
                    ->options(Service::where('is_active', true)->pluck('name', 'id'))
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('price_override')
                    ->label('Harga Override (Rp)')
                    ->numeric()
                    ->prefix('Rp')
                    ->helperText('Kosongkan untuk pakai harga default layanan')
                    ->nullable(),
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif di Cabang Ini')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Layanan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga Default')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('pivot.price_override')
                    ->label('Harga Cabang')
                    ->money('IDR')
                    ->placeholder('Pakai default'),
                Tables\Columns\IconColumn::make('pivot.is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label('Tambah Layanan')
                    ->preloadRecordSelect()
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Forms\Components\TextInput::make('price_override')
                            ->label('Harga Override (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->nullable(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make()->label('Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make(),
            ]);
    }
}
