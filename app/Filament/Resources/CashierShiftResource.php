<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CashierShiftResource\Pages;
use App\Models\CashierShift;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CashierShiftResource extends Resource
{
    protected static ?string $model = CashierShift::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Transaksi';

    protected static ?string $navigationLabel = 'Shift Kasir';

    protected static ?string $modelLabel = 'Shift Kasir';

    protected static ?string $pluralModelLabel = 'Shift Kasir';

    protected static ?int $navigationSort = 2;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Kasir')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Cabang')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('start_at')
                    ->label('Mulai')
                    ->dateTime('d/m/Y H:i')
                    ->timezone(config('app.timezone'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_at')
                    ->label('Selesai')
                    ->dateTime('d/m/Y H:i')
                    ->timezone(config('app.timezone'))
                    ->placeholder('-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('duration')
                    ->label('Durasi')
                    ->getStateUsing(function (CashierShift $record): string {
                        $end = $record->end_at ?? now();
                        return $record->start_at->diffForHumans($end, true);
                    }),

                Tables\Columns\TextColumn::make('opening_cash')
                    ->label('Modal Awal')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('expected_cash')
                    ->label('Expected')
                    ->money('IDR')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('actual_cash')
                    ->label('Aktual')
                    ->money('IDR')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('difference')
                    ->label('Selisih')
                    ->money('IDR')
                    ->placeholder('-')
                    ->color(fn (CashierShift $record): string => match (true) {
                        $record->difference === null    => 'gray',
                        (float) $record->difference > 0 => 'warning',
                        (float) $record->difference < 0 => 'danger',
                        default                         => 'success',
                    }),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'closed',
                        'warning' => 'open',
                    ])
                    ->formatStateUsing(fn (string $state): string => $state === 'open' ? 'Aktif' : 'Ditutup'),
            ])
            ->defaultSort('start_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('branch_id')
                    ->label('Cabang')
                    ->relationship('branch', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Kasir')
                    ->options(fn () => User::whereHas('roles', fn ($q) => $q->whereIn('name', ['cashier', 'admin', 'super_admin']))
                        ->pluck('name', 'id'))
                    ->searchable(),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'open'   => 'Aktif',
                        'closed' => 'Ditutup',
                    ]),

                Tables\Filters\Filter::make('start_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('until')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn (Builder $q, $date) => $q->whereDate('start_at', '>=', $date))
                            ->when($data['until'], fn (Builder $q, $date) => $q->whereDate('start_at', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Shift')
                    ->columns(3)
                    ->schema([
                        Infolists\Components\TextEntry::make('branch.name')
                            ->label('Cabang')
                            ->badge()
                            ->color('primary'),
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Kasir'),
                        Infolists\Components\TextEntry::make('start_at')
                            ->label('Mulai')
                            ->dateTime('d/m/Y H:i')
                            ->timezone(config('app.timezone')),
                        Infolists\Components\TextEntry::make('end_at')
                            ->label('Selesai')
                            ->dateTime('d/m/Y H:i')
                            ->timezone(config('app.timezone'))
                            ->placeholder('-'),
                        Infolists\Components\TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => $state === 'open' ? 'warning' : 'success')
                            ->formatStateUsing(fn (string $state): string => $state === 'open' ? 'Aktif' : 'Ditutup'),
                        Infolists\Components\TextEntry::make('duration')
                            ->label('Durasi')
                            ->getStateUsing(function (CashierShift $record): string {
                                $end = $record->end_at ?? now();
                                return $record->start_at->diffForHumans($end, true);
                            }),
                    ]),

                Infolists\Components\Section::make('Rekonsiliasi Kas')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('opening_cash')
                            ->label('Modal Awal')
                            ->money('IDR'),
                        Infolists\Components\TextEntry::make('expected_cash')
                            ->label('Expected Cash')
                            ->money('IDR')
                            ->placeholder('-'),
                        Infolists\Components\TextEntry::make('actual_cash')
                            ->label('Kas Aktual')
                            ->money('IDR')
                            ->placeholder('-'),
                        Infolists\Components\TextEntry::make('difference')
                            ->label('Selisih')
                            ->money('IDR')
                            ->placeholder('-')
                            ->color(fn (CashierShift $record): string => match (true) {
                                $record->difference === null    => 'gray',
                                (float) $record->difference > 0 => 'warning',
                                (float) $record->difference < 0 => 'danger',
                                default                         => 'success',
                            })
                            ->suffixAction(null),
                        Infolists\Components\TextEntry::make('notes')
                            ->label('Catatan')
                            ->columnSpanFull()
                            ->placeholder('Tidak ada catatan'),
                    ]),

                Infolists\Components\Section::make('Ringkasan Pembayaran')
                    ->schema([
                        Infolists\Components\ViewEntry::make('payment_summary')
                            ->view('filament.infolists.shift-payment-summary'),
                    ]),

                Infolists\Components\Section::make('Transaksi')
                    ->schema([
                        Infolists\Components\ViewEntry::make('transactions_list')
                            ->view('filament.infolists.shift-transactions'),
                    ]),

                Infolists\Components\Section::make('Cash Movements')
                    ->schema([
                        Infolists\Components\ViewEntry::make('cash_movements_list')
                            ->view('filament.infolists.shift-cash-movements'),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCashierShifts::route('/'),
            'view'  => Pages\ViewCashierShift::route('/{record}'),
        ];
    }
}
