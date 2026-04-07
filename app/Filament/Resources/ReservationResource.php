<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservationResource\Pages;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\Reservation;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Operasional';

    protected static ?string $navigationLabel = 'Reservasi';

    protected static ?string $modelLabel = 'Reservasi';

    protected static ?string $pluralModelLabel = 'Reservasi';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Reservasi')
                    ->schema([
                        Forms\Components\Select::make('branch_id')
                            ->label('Cabang')
                            ->options(function () {
                                $user = Auth::user();
                                if ($user->hasRole('super_admin')) {
                                    return Branch::pluck('name', 'id')->toArray();
                                }
                                return $user->branches()->pluck('name', 'branches.id')->toArray();
                            })
                            ->default(function () {
                                $user = Auth::user();
                                return $user->hasRole('super_admin') ? null : $user->branches()->first()?->id;
                            })
                            ->disabled(fn () => ! Auth::user()->hasRole('super_admin'))
                            ->dehydrated()
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn (Forms\Set $set) => $set('employee_id', null)),

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

                        Forms\Components\Select::make('employee_id')
                            ->label('Barber / Karyawan')
                            ->options(fn (Forms\Get $get) => Employee::where('is_active', true)
                                ->when(
                                    $get('branch_id'),
                                    fn ($q, $branchId) => $q->where('branch_id', $branchId)
                                )
                                ->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->live(),

                        Forms\Components\DateTimePicker::make('reservation_time')
                            ->label('Waktu Reservasi')
                            ->required()
                            ->native(false)
                            ->default(now()),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending'   => 'Menunggu',
                                'arrived'   => 'Sudah Datang',
                                'completed' => 'Selesai',
                                'cancelled' => 'Dibatalkan',
                            ])
                            ->required()
                            ->default('pending'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Layanan & Catatan')
                    ->schema([
                        Forms\Components\Select::make('services')
                            ->label('Pilih Layanan')
                            ->multiple()
                            ->relationship('services', 'name', fn ($query) => $query->where('is_active', true))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->placeholder('Catatan tambahan (opsional)...')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Pelanggan')
                    ->default('Walk-in')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Cabang')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('employee.name')
                    ->label('Barber')
                    ->searchable()
                    ->sortable()
                    ->default('-'),

                Tables\Columns\TextColumn::make('services.name')
                    ->label('Layanan')
                    ->badge()
                    ->separator(', ')
                    ->searchable(),

                Tables\Columns\TextColumn::make('reservation_time')
                    ->label('Waktu Reservasi')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending'   => 'Menunggu',
                        'arrived'   => 'Sudah Datang',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                        default     => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending'   => 'warning',
                        'arrived'   => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    }),

                Tables\Columns\TextColumn::make('notes')
                    ->label('Catatan')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('reservation_time', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending'   => 'Menunggu',
                        'arrived'   => 'Sudah Datang',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                    ]),

                Tables\Filters\SelectFilter::make('branch_id')
                    ->label('Cabang')
                    ->relationship('branch', 'name'),

                Tables\Filters\SelectFilter::make('employee_id')
                    ->label('Barber')
                    ->relationship('employee', 'name')
                    ->searchable()
                    ->preload(),

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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListReservations::route('/'),
            'create' => Pages\CreateReservation::route('/create'),
            'edit'   => Pages\EditReservation::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->whereNull('reservations.deleted_at');
    }
}
