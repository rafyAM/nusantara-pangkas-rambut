<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Service;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Transaksi';

    protected static ?string $navigationLabel = 'Transaksi';

    protected static ?string $modelLabel = 'Transaksi';

    protected static ?string $pluralModelLabel = 'Transaksi';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Transaksi')
                    ->schema([
                        Forms\Components\TextInput::make('invoice_number')
                            ->label('No. Invoice')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Otomatis')
                            ->hiddenOn('create'),
                        Forms\Components\Select::make('branch_id')
                            ->label('Cabang')
                            ->relationship('branch', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live(),
                        Forms\Components\Select::make('employee_id')
                            ->label('Karyawan')
                            ->relationship(
                                'employee',
                                'name',
                                fn(Builder $query, Forms\Get $get) =>
                                $query->where('is_active', true)
                                    ->when($get('branch_id'), fn($q, $branchId) => $q->where('branch_id', $branchId))
                            )
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live(),
                        Forms\Components\Select::make('customer_id')
                            ->label('Pelanggan')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('phone')
                                    ->label('No. Telepon')
                                    ->tel()
                                    ->maxLength(20),
                            ])
                            ->createOptionModalHeading('Tambah Pelanggan Baru'),
                        Forms\Components\DateTimePicker::make('transaction_date')
                            ->label('Tanggal Transaksi')
                            ->required()
                            ->default(now())
                            ->native(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Detail Layanan')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->label('')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('item_type')
                                    ->label('Jenis Item')
                                    ->options([
                                        'service' => 'Layanan',
                                        'product' => 'Produk',
                                    ])
                                    ->default('service')
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Set $set) {
                                        $set('service_id', null);
                                        $set('product_id', null);
                                        $set('employee_id', null);
                                        $set('price', 0);
                                        $set('subtotal', 0);
                                    })
                                    ->columnSpan(2),

                                Forms\Components\Select::make('service_id')
                                    ->label('Layanan')
                                    ->options(fn() => Service::where('is_active', true)->pluck('name', 'id'))
                                    ->required(fn(Forms\Get $get) => $get('item_type') === 'service')
                                    ->visible(fn(Forms\Get $get) => $get('item_type') === 'service')
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                        if ($state) {
                                            $price = Service::find($state)?->price ?? 0;
                                            $set('price', $price);
                                            $set('subtotal', $price * ($get('quantity') ?? 1));
                                        }
                                    })
                                    ->columnSpan(3),

                                Forms\Components\Select::make('product_id')
                                    ->label('Produk')
                                    ->options(fn(Forms\Get $get) => \App\Models\Product::where('is_active', true)
                                        ->where('stock', '>', 0)
                                        ->when($get('../../branch_id'), fn($q, $b) => $q->where('branch_id', $b))
                                        ->pluck('name', 'id'))
                                    ->required(fn(Forms\Get $get) => $get('item_type') === 'product')
                                    ->visible(fn(Forms\Get $get) => $get('item_type') === 'product')
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                        if ($state) {
                                            $price = \App\Models\Product::find($state)?->price ?? 0;
                                            $set('price', $price);
                                            $set('subtotal', $price * ($get('quantity') ?? 1));
                                        }
                                    })
                                    ->columnSpan(5),

                                Forms\Components\Select::make('employee_id')
                                    ->label('Barber')
                                    ->options(fn(Forms\Get $get) => \App\Models\Employee::where('is_active', true)
                                        ->when($get('../../branch_id'), fn($q, $b) => $q->where('branch_id', $b))
                                        ->pluck('name', 'id'))
                                    ->visible(fn(Forms\Get $get) => $get('item_type') === 'service')
                                    ->searchable()
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('quantity')
                                    ->label('Qty')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->minValue(1)
                                    ->live(debounce: 500)
                                    ->afterStateUpdated(function ($state, Forms\Get $get, Forms\Set $set) {
                                        $set('subtotal', floatval($get('price') ?? 0) * intval($state ?? 1));
                                    })
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('price')
                                    ->label('Harga')
                                    ->numeric()
                                    ->required()
                                    ->prefix('Rp')
                                    ->live(debounce: 500)
                                    ->afterStateUpdated(function ($state, Forms\Get $get, Forms\Set $set) {
                                        $set('subtotal', floatval($state ?? 0) * intval($get('quantity') ?? 1));
                                    })
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->disabled()
                                    ->dehydrated()
                                    ->columnSpan(2),
                            ])
                            ->columns(12)
                            ->live()
                            ->afterStateUpdated(fn(Forms\Get $get, Forms\Set $set) => self::updateTotalAmount($get, $set))
                            ->deleteAction(fn($action) => $action->after(fn(Forms\Get $get, Forms\Set $set) => self::updateTotalAmount($get, $set)))
                            ->addActionLabel('Tambah Layanan')
                            ->defaultItems(1),
                    ]),

                Forms\Components\Section::make('Pembayaran')
                    ->schema([
                        Forms\Components\Placeholder::make('total_display')
                            ->label('Total Pembayaran')
                            ->content(function (Forms\Get $get): string {
                                $items = $get('items') ?? [];
                                $total = collect($items)->sum(fn($item) => floatval($item['subtotal'] ?? 0));
                                return 'Rp ' . number_format($total, 0, ',', '.');
                            }),
                        Forms\Components\Hidden::make('total_amount')
                            ->default(0)
                            ->dehydrated(),
                        Forms\Components\Select::make('payment_method')
                            ->label('Metode Pembayaran')
                            ->options([
                                'cash' => 'Tunai',
                                'transfer' => 'Transfer Bank',
                                'ewallet' => 'E-Wallet',
                                'debit' => 'Kartu Debit',
                            ])
                            ->required()
                            ->default('cash'),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'completed' => 'Selesai',
                                'pending' => 'Menunggu',
                                'cancelled' => 'Dibatalkan',
                            ])
                            ->required()
                            ->default('completed'),
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(3),
            ]);
    }

    public static function updateTotalAmount(Forms\Get $get, Forms\Set $set): void
    {
        $items = $get('items') ?? [];
        $total = collect($items)->sum(fn($item) => floatval($item['subtotal'] ?? 0));
        $set('total_amount', $total);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('No. Invoice')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Pelanggan')
                    ->searchable()
                    ->default('Walk-in')
                    ->sortable(),
                Tables\Columns\TextColumn::make('employee.name')
                    ->label('Karyawan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Cabang')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('transaction_date')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Pembayaran')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'cash' => 'Tunai',
                        'transfer' => 'Transfer',
                        'ewallet' => 'E-Wallet',
                        'debit' => 'Debit',
                        default => $state,
                    })
                    ->color('gray'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'completed' => 'Selesai',
                        'pending' => 'Menunggu',
                        'cancelled' => 'Dibatalkan',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'completed' => 'success',
                        'pending' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('transaction_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('branch_id')
                    ->label('Cabang')
                    ->relationship('branch', 'name'),
                Tables\Filters\SelectFilter::make('employee_id')
                    ->label('Karyawan')
                    ->relationship('employee', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'completed' => 'Selesai',
                        'pending' => 'Menunggu',
                        'cancelled' => 'Dibatalkan',
                    ]),
                Tables\Filters\SelectFilter::make('payment_method')
                    ->label('Pembayaran')
                    ->options([
                        'cash' => 'Tunai',
                        'transfer' => 'Transfer Bank',
                        'ewallet' => 'E-Wallet',
                        'debit' => 'Kartu Debit',
                    ]),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->whereNull('transactions.deleted_at');
    }
}
