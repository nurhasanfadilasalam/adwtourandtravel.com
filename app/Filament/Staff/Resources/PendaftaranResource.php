<?php

namespace App\Filament\Staff\Resources;

use stdClass;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Pendaftaran;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Spatie\Permission\Traits\HasRoles;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Password;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Staff\Resources\PendaftaranResource\Pages;
use App\Filament\Staff\Resources\PendaftaranResource\RelationManagers;

class PendaftaranResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = "Kelola Customer";

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    // public static function shouldRegisterNavigation(): bool
    // {
    //     return auth()->user()?->hasRole('staff') ?? false;
    // }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()
            ::where('role', 'customer')
            ->count();
    }


    public function canAccessPanel(User $user): bool
    {
        return $user->hasRole('staff');
    }

    public static function getLabel(): string
    {
        return 'Pendaftaran';
    }

    public static function getSlug(): string
    {
        return 'pendaftaran';
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Registrasi Sistem')
                    ->collapsible()
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama sesuai KTP')
                                    ->required()
                                    ->columnSpanFull()
                                    ->maxLength(255),



                                TextInput::make('phone')
                                    ->label('No. HP / WhatsApp')
                                    ->tel()
                                    ->required()
                                    ->maxLength(20),

                                TextInput::make('username')
                                    ->label('Username (silahkan masukkan NIK)')
                                    ->unique(table: User::class, ignorable: fn($record) => $record)
                                    ->required()
                                    ->numeric()
                                    ->maxLength(255),

                                // TextInput::make('email')
                                //     ->label('Email')
                                //     ->email()
                                //     ->unique(
                                //         table: User::class,
                                //         ignorable: fn($record) => $record
                                //     )
                                //     ->maxLength(255),
                            ]),

                        Card::make([
                            TextInput::make('password')
                                ->label('Password')
                                ->password()
                                ->rule(Password::default())
                                ->required(fn(string $context) => $context === 'create')
                                ->dehydrated(fn($state) => filled($state))
                                ->maxLength(255)
                                ->confirmed(),

                            TextInput::make('password_confirmation')
                                ->label('Konfirmasi Password')
                                ->password()
                                ->required(fn(string $context) => $context === 'create')
                                ->dehydrated(false),
                        ])
                    ]),

                /**
                 * Hidden field
                 * Role selalu customer
                 */
                Hidden::make('role')
                    ->default('customer'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('no')
                    ->alignCenter()
                    ->state(
                        static function (HasTable $livewire, stdClass $rowLoop): string {
                            return (string) (
                                $rowLoop->iteration +
                                ($livewire->getTableRecordsPerPage() * (
                                    $livewire->getTablePage() - 1
                                ))
                            );
                        }
                    ),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Customer')
                    ->searchable()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('email')
                //     ->label('Email')
                //     ->searchable()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('username')
                    ->label('Username')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('No HP.')
                    ->searchable()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('role')
                //     ->label(__('filament.users.role'))
                //     ->badge()
                //     ->sortable(),
                // ->formatStateUsing(function (string $state): string {
                //     $role = UserRoles::from($state);

                //     return $role->getLabel() ?? $state;
                // }),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),       // soft delete
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * 📌 Query hanya customer
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->where('role', 'customer');
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
            'index' => Pages\ListPendaftarans::route('/'),
            'create' => Pages\CreatePendaftaran::route('/create'),
            'edit' => Pages\EditPendaftaran::route('/{record}/edit'),
        ];
    }
}
