<?php

namespace App\Filament\Resources\PaketUmrohResource\Pages;

use App\Filament\Resources\PaketUmrohResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditPaketUmroh extends EditRecord
{
    protected static string $resource = PaketUmrohResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // protected function afterSave(): void
    // {
    //     Notification::make()
    //         ->title('Tanggal paket diperbarui')
    //         ->body('Semua jadwal keberangkatan terkait telah disesuaikan.')
    //         ->success()
    //         ->send();
    // }
}
