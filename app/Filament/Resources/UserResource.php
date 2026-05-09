<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Password;
use Filament\Forms\Components\DateTimePicker;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;


class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-s-user';

    protected static ?string $navigationGroup = "Kelola User";

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('username')
                        ->label('Username')
                        ->required()
                        ->unique(table: User::class, ignorable: fn($record) => $record)
                        ->maxLength(255),
                    // TextInput::make('email')
                    //     ->email()
                    //     ->unique(
                    //         table: User::class,
                    //         ignorable: fn($record) => $record
                    //     )
                    //     ->maxLength(255),
                    TextInput::make('phone')
                        ->tel()
                        ->maxLength(255)
                        ->default(null),
                    Select::make('role')
                        ->label("Role User")
                        ->options([
                            'customer' => 'customer',
                            'staff' => 'staff',
                        ])
                        ->default('customer')
                        ->required(),
                    // DateTimePicker::make('email_verified_at'),
                ])->columns(2),
                Section::make('Keamanan Akun')
                    ->schema([
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
                    ]),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('username')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('email')
                //     ->searchable(),

                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('role'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

}
