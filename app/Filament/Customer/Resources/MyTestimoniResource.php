<?php

namespace App\Filament\Customer\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Testimoni;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Customer\Resources\MyTestimoniResource\Pages;


class MyTestimoniResource extends Resource
{
    protected static ?string $model = Testimoni::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?int $navigationSort = 5;

    protected static ?string $label = 'Testimoni Saya';

    protected static ?string $navigationLabel = 'Testimoni Saya';

    protected static ?string $title = 'Testimoni Saya';

    protected static ?string $slug = 'testimoni-saya';

    public static function canAccess(): bool
    {
        return Filament::getCurrentPanel()?->getId() === 'customer';
    }

    /**
    * NO CREATE / EDIT FROM HERE
    */
    public static function canCreate(): bool
    {
        return true;
    }

    public static function canEdit($record): bool
    {
        return true;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('content')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('star_count')
                    ->label("Rating (Stars)")
                    ->options([
                    '5' => '5 Stars',
                    '4' => '4 Stars',
                    '3' => '3 Stars',
                    '2' => '2 Stars',
                    '1' => '1 Star',
                    ])
                    ->default('5')
                    ->columnSpanFull()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('content')
                    ->wrap()
                    ->sortable(),
                 Tables\Columns\TextColumn::make('star_count')
                    ->label("Rating (Stars)")
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListMyTestimonis::route('/'),
            'create' => Pages\CreateMyTestimoni::route('/create'),
            'edit' => Pages\EditMyTestimoni::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $userID = Filament::auth()->user()?->id;

        return parent::getEloquentQuery()
            ->when(
                $userID,
                fn($query) =>
                $query->where('user_id', $userID)
            );
    }
}
