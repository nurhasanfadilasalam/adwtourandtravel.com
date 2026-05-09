<?php

namespace App\Filament\Customer\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Gallery;
use Filament\Forms\Form;
use App\Models\MyGallery;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Customer\Resources\MyGalleryResource\Pages;
use App\Filament\Customer\Resources\MyGalleryResource\RelationManagers;

class MyGalleryResource extends Resource
{
    protected static ?string $model = Gallery::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    public static function canAccess(): bool
    {
        return Filament::getCurrentPanel()?->getId() === 'customer';
    }


    protected static ?int $navigationSort = 4;

    protected static ?string $label = 'Galeri Saya';

    protected static ?string $navigationLabel = 'Galeri Saya';

    protected static ?string $title = 'Galeri Saya';

    protected static ?string $slug = 'my-gallery';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                RichEditor::make('deskripsi')
                    ->required()
                    ->columnSpanFull(),
                FileUpload::make('link_gambar')
                    ->label('Gambar Gallery')
                    ->image() // Ensures only image files can be uploaded
                    ->disk('public') // Store files on the 'public' disk (in `storage/app/public`)
                    ->directory('gallery-files') // Store images in the 'tour-leaders' folder inside the 'public' disk
                    ->maxSize(2048) // Max size in kilobytes (2MB)
                    ->enableOpen() // Allow users to open the image
                    ->columnSpanFull()
                    ->default(null), // Default value for the field

                // Forms\Components\TextInput::make('upload_by')
                //     ->default('Administrator')
                //     ->readOnly()
                //     ->required()
                //     ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                // Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    // Tables\Actions\ForceDeleteBulkAction::make(),
                    // Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\GaleriSaya::route('/'),
            'create' => Pages\CreateMyGallery::route('/create'),
            'edit' => Pages\EditMyGallery::route('/{record}/edit'),
        ];
    }

    /**
    * CUSTOMER ONLY SEE THEIR OWN Data
    */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Filament::auth()->id())
            ->latest()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }



}
