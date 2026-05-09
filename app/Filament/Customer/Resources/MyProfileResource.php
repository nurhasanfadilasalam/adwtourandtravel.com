<?php

namespace App\Filament\Customer\Resources;

use Filament\Forms;
use Filament\Tables;
use Customer\MyProfile;
use App\Models\Customer;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Customer\Clusters\MyAccount;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Customer\Resources\MyProfileResource\Pages;
use App\Filament\Customer\Resources\MyProfileResource\RelationManagers;


class MyProfileResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 2;

    // public static function getLabel(): string
    // {
    //     return 'Profile Saya';
    // }

    protected static ?string $slug = 'profile-saya';

    // protected static ?string $cluster = MyAccount::class;

    protected static ?string $navigationLabel = 'Profile Saya';

    /**
     * NO CREATE / EDIT FROM HERE
     */
    public static function canCreate(): bool
    {
        return true;
    }

    public static function canAccess(): bool
    {
        return Filament::getCurrentPanel()?->getId() === 'customer';
    }

    public static function canEdit($record): bool
    {
        return true;
    }

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
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\FormProfile::route('/'),
            'create' => Pages\CreateMyProfile::route('/create'),
            'edit' => Pages\EditMyProfile::route('/{record}/edit'),
        ];
    }

    /**
    * CUSTOMER ONLY SEE THEIR OWN BOOKINGS
    */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Filament::auth()->id())
            ->latest();
    }
}
