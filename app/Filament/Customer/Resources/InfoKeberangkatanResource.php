<?php

namespace App\Filament\Customer\Resources;


use Carbon\Carbon;
use Filament\Tables;
use App\Models\Booking;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Customer\Resources\InfoKeberangkatanResource\Pages;
use App\Filament\Customer\Resources\InfoKeberangkatanResource\RelationManagers;


class InfoKeberangkatanResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    public static function canAccess(): bool
    {
        return Filament::getCurrentPanel()?->getId() === 'customer';
    }

    protected static ?int $navigationSort = 6;

    protected static ?string $label = 'Info Keberangkatan';

    protected static ?string $navigationLabel = 'Info Keberangkatan';

    protected static ?string $title = 'Info Keberangkatan';

    protected static ?string $slug = 'info-keberangkatan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Belum ada info keberangkatan')
            ->emptyStateDescription('Silakan Lengkapi Data Profile & Lakukan Booking Paket Umroh Terlebih Dahulu.')
            ->emptyStateIcon('heroicon-o-calendar-days')
            ->emptyStateActions([
                // Tables\Actions\Action::make('booking')
                //     ->label('Booking Paket Umroh')
                //     ->url(route('filament.customer.resources.bookings.index'))
                //     ->icon('heroicon-o-plus'),
            ])
            ->columns([
                TextColumn::make('jadwalKeberangkatan.tanggal_keberangkatan')
                    ->icon('heroicon-o-lock-closed')
                    ->label('Jadwal Berangkat')
                    ->badge()
                    ->color(
                        fn($state) =>
                        str_contains($state, '-') ? 'success' : 'danger'
                    )
                    ->formatStateUsing(function ($state) {
                        return Carbon::parse($state)
                            ->locale('id')
                            ->translatedFormat('l, d M Y');
                    }),

                Tables\Columns\TextColumn::make('paket_info')
                    ->label('Paket Info')
                    ->columnSpan(3)
                    ->html(true)
                    ->badge()
                    ->color(fn ($state) =>
                            str_contains($state, '-') ? 'success' : 'success'
                    )
                    // ->getStateUsing(fn (Booking $record) => sprintf(
                    //     '<strong>Kode Booking:</strong> %s<br><strong>Paket:</strong> %s<br><strong>Biaya Umroh:</strong> %s',
                    //     e($record->booking_code),
                    //     e($record->paketUmroh?->nama_paket ?? '-'),
                    //     e($record->paketUmroh?->harga_paket ?? '-')
                    // )),

                    ->getStateUsing(fn (Booking $record) => sprintf(
                        '<strong>Kode Booking:</strong> %s<br><strong>Paket:</strong> %s',
                        e($record->booking_code),
                        e($record->paketUmroh?->nama_paket ?? '-')
                    )),

                // TextColumn::make('booking_code')
                //     ->label('Kode Booking')
                //     ->searchable(),

                // TextColumn::make('paketUmroh.nama_paket')
                //     ->label('Paket Umroh'),

                BadgeColumn::make('status')
                    ->colors([
                        'success' => 'confirmed',
                        'warning' => 'pending',
                        'danger' => 'canceled',
                    ]),

                TextColumn::make('total_price')
                    ->money('IDR'),


            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Lihat Jadwal'),
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
            'index' => Pages\ListInfoKeberangkatans::route('/'),
            'create' => Pages\CreateInfoKeberangkatan::route('/create'),
            'edit' => Pages\EditInfoKeberangkatan::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $customerId = Filament::auth()->user()?->customer?->id;

        return parent::getEloquentQuery()
            ->when(
                $customerId,
                fn($query) =>
                $query->where('customer_id', $customerId)
            )
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
