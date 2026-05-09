<?php

namespace App\Filament\Resources\PelaporanResource\Pages;

use App\Models\Customer;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Resources\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\DatePicker;
use App\Filament\Resources\PelaporanResource;
use Filament\Forms\Components\Actions\Action;

class LaporanPerPaketView extends Page
{
    protected static string $resource = PelaporanResource::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box-arrow-down';

    protected static string $view = 'filament.resources.pelaporan-resource.pages.laporan-per-paket-view';


    protected static ?string $navigationLabel = 'Laporan Customer';

    protected static ?string $navigationGroup = 'Report';

    public ?string $start_date = null;

    public ?string $end_date = null;

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Filter Laporan')
                ->schema([
                    DatePicker::make('start_date')
                        ->label('Tanggal Mulai'),

                    DatePicker::make('end_date')
                        ->label('Tanggal Akhir'),
                ])
                ->columns(2),

            Action::make('exportPdf')
                ->label('Export PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->action('generatePdf'),
        ]);
    }

    public function generatePdf()
    {
        $query = Customer::query()->with('user');

        if ($this->start_date) {
            $query->whereDate('created_at', '>=', $this->start_date);
        }

        if ($this->end_date) {
            $query->whereDate('created_at', '<=', $this->end_date);
        }

        $records = $query->get();

        return response()->streamDownload(function () use ($records) {
            echo Pdf::loadView('filament.resources.pages.customer-report', [
                'records' => $records,
                'start' => $this->start_date,
                'end' => $this->end_date,
            ])->stream();
        }, 'laporan-customer.pdf');
    }

}
