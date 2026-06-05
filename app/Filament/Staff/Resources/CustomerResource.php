<?php

namespace App\Filament\Staff\Resources;

use stdClass;
use Filament\Forms;
use Filament\Tables;
use App\Models\Customer;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Illuminate\Foundation\Auth\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Staff\Resources\CustomerResource\Pages;
use App\Filament\Staff\Resources\CustomerResource\RelationManagers;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CustomersExport;
use Filament\Tables\Actions\Action;


class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-s-user';

    protected static ?string $navigationGroup = "Kelola Customer";

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public function canAccessPanel(User $user): bool
    {
        return $user->hasRole('staff');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('Data Pribadi')
                    ->collapsible()
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('nama_ktp')
                                ->label('Nama Sesuai KTP')
                                ->required(),
                             Select::make('jenis_kelamin')
                                ->label('Jenis Kelamin')
                                ->options([
                                    'laki-laki' => 'Laki-Laki',
                                    'perempuan' => 'Perempuan',
                                ])
                                ->searchable()
                                ->default('laki-laki'),
                            TextInput::make('no_ktp')
                                ->label('NIK')
                                ->numeric(),
                            // TextInput::make('no_kk')->numeric(),
                            DatePicker::make('tgl_lahir'),
                            TextInput::make('tempat_lahir'),
                            TextInput::make('nama_ayah'),
                            TextInput::make('no_hp')->prefix('+62'),
                        ]),
                        Textarea::make('alamat')->columnSpanFull(),
                    ]),

                 Section::make('Data Tambahan')
                    ->collapsible()
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('provinsi')
                                ->label('Provinsi')
                                ->options([
                                    'Aceh' => 'Aceh',
                                    'Sumatera Utara' => 'Sumatera Utara',
                                    'Sumatera Barat' => 'Sumatera Barat',
                                    'Riau' => 'Riau',
                                    'Kepulauan Riau' => 'Kepulauan Riau',
                                    'Jambi' => 'Jambi',
                                    'Sumatera Selatan' => 'Sumatera Selatan',
                                    'Bangka Belitung' => 'Kepulauan Bangka Belitung',
                                    'Bengkulu' => 'Bengkulu',
                                    'Lampung' => 'Lampung',
                                    'DKI Jakarta' => 'DKI Jakarta',
                                    'Jawa Barat' => 'Jawa Barat',
                                    'Jawa Tengah' => 'Jawa Tengah',
                                    'DI Yogyakarta' => 'DI Yogyakarta',
                                    'Jawa Timur' => 'Jawa Timur',
                                    'Banten' => 'Banten',
                                    'Bali' => 'Bali',
                                    'Nusa Tenggara Barat' => 'Nusa Tenggara Barat',
                                    'Nusa Tenggara Timur' => 'Nusa Tenggara Timur',
                                    'Kalimantan Barat' => 'Kalimantan Barat',
                                    'Kalimantan Tengah' => 'Kalimantan Tengah',
                                    'Kalimantan Selatan' => 'Kalimantan Selatan',
                                    'Kalimantan Timur' => 'Kalimantan Timur',
                                    'Kalimantan Utara' => 'Kalimantan Utara',
                                    'Sulawesi Utara' => 'Sulawesi Utara',
                                    'Sulawesi Tengah' => 'Sulawesi Tengah',
                                    'Sulawesi Selatan' => 'Sulawesi Selatan',
                                    'Sulawesi Tenggara' => 'Sulawesi Tenggara',
                                    'Gorontalo' => 'Gorontalo',
                                    'Sulawesi Barat' => 'Sulawesi Barat',
                                    'Maluku' => 'Maluku',
                                    'Maluku Utara' => 'Maluku Utara',
                                    'Papua' => 'Papua',
                                    'Papua Barat' => 'Papua Barat',
                                    'Papua Selatan' => 'Papua Selatan',
                                    'Papua Tengah' => 'Papua Tengah',
                                    'Papua Pegunungan' => 'Papua Pegunungan',
                                    'Papua Barat Daya' => 'Papua Barat Daya',
                                ])
                                ->searchable()
                                ->required(),
                            TextInput::make('kota_kabupaten'),
                            Select::make('kewarganegaraan')
                                ->label('Kewarganegaraan')
                                ->options([
                                    'Indonesia' => 'Indonesia',
                                    'Malaysia' => 'Malaysia',
                                    'Singapura' => 'Singapura',
                                    'Brunei' => 'Brunei Darussalam',
                                    'Thailand' => 'Thailand',
                                    'Lainnya' => 'Lainnya',
                                ])
                                ->searchable()
                                ->default('Indonesia'),
                            // ->default(fn () => $this->record?->kewarganegaraan)
                            Select::make('status_pernikahan')
                                ->label('Status Pernikahan')
                                ->options([
                                    'Belum Menikah' => 'Belum Menikah',
                                    'Menikah' => 'Menikah',
                                    'Cerai Hidup' => 'Cerai Hidup',
                                    'Cerai Mati' => 'Cerai Mati',
                                ])
                                ->placeholder('Pilih status'),
                            Select::make('jenis_pendidikan')
                                ->label('Pendidikan Terakhir')
                                ->options([
                                    'SD' => 'SD',
                                    'SMP' => 'SMP',
                                    'SMA' => 'SMA / SMK',
                                    'D3' => 'D3',
                                    'S1' => 'S1',
                                    'S2' => 'S2',
                                    'S3' => 'S3',
                                    'Lainnya' => 'Lainnya',
                                ])
                                ->searchable(),
                            Select::make('jenis_pekerjaan')
                                ->label('Pekerjaan')
                                ->options([
                                    'Pelajar/Mahasiswa' => 'Pelajar / Mahasiswa',
                                    'Karyawan Swasta' => 'Karyawan Swasta',
                                    'PNS' => 'PNS',
                                    'Wiraswasta' => 'Wiraswasta',
                                    'Ibu Rumah Tangga' => 'Ibu Rumah Tangga',
                                    'Pensiunan' => 'Pensiunan',
                                    'Lainnya' => 'Lainnya',
                                ])
                                ->searchable(),
                        ]),
                    ]),

                Section::make('Data Passport')
                    ->collapsible()
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('nama_passport'),
                            TextInput::make('no_passport'),
                            TextInput::make('kota_passport'),
                            DatePicker::make('tgl_dikeluarkan_passport'),
                            DatePicker::make('tgl_habis_passport'),
                        ]),
                    ]),



                Section::make('Upload Dokumen')
                    ->collapsible()
                    ->schema([
                        Grid::make(4)->schema([
                            FileUpload::make('upload_ktp')
                                ->label('Image KTP')
                                ->disk('public')
                                ->directory('customer-ktp')
                                ->image()
                                ->visibility('public')
                                ->imagePreviewHeight('75')
                                ->panelAspectRatio('3:2')
                                ->panelLayout('integrated'),

                            FileUpload::make('upload_kk')
                                ->label('Image KK')
                                ->disk('public')
                                ->directory('customer-kk')
                                ->image()
                                ->visibility('public')
                                ->imagePreviewHeight('75')
                                ->panelAspectRatio('3:2')
                                ->panelLayout('integrated'),

                            FileUpload::make('upload_passport')
                                ->label('Image Passport')
                                ->disk('public')
                                ->directory('customer-passport')
                                ->image()
                                ->visibility('public')
                                ->imagePreviewHeight('75')
                                ->panelAspectRatio('3:2')
                                ->panelLayout('integrated'),

                            FileUpload::make('upload_photo')
                                ->label('Photo Fullbody')
                                ->disk('public')
                                ->directory('customer-photo')
                                ->image()
                                ->visibility('public')
                                ->imagePreviewHeight('75')
                                ->panelAspectRatio('3:2')
                                ->panelLayout('integrated'),
                        ]),
                    ]),


            ]);
    }



    protected function sectionProfile(): Component
    {
        return Section::make('Data Pribadi')
            ->collapsible()
            ->schema([
                Grid::make(2)->schema([
                    TextInput::make('nama_ktp')->required(),
                    TextInput::make('nama_passport'),
                    TextInput::make('no_ktp')->numeric(),
                    TextInput::make('no_kk')->numeric(),
                    DatePicker::make('tgl_lahir'),
                    TextInput::make('tempat_lahir'),
                    TextInput::make('nama_ayah'),
                    TextInput::make('no_hp')->prefix('+62'),
                ]),
                Textarea::make('alamat')->columnSpanFull(),
            ]);
    }

    protected function sectionPassport(): Component
    {
        return Section::make('Data Passport')
            ->collapsible()
            ->schema([
                Grid::make(2)->schema([
                    TextInput::make('no_passport'),
                    TextInput::make('kota_passport'),
                    DatePicker::make('tgl_dikeluarkan_passport'),
                    DatePicker::make('tgl_habis_passport'),
                ]),
            ]);
    }

    protected function sectionTambahan(): Component
    {
        return Section::make('Data Tambahan')
            ->collapsible()
            ->schema([
                Grid::make(2)->schema([
                    Select::make('provinsi')
                        ->label('Provinsi')
                        ->options([
                            'Aceh' => 'Aceh',
                            'Sumatera Utara' => 'Sumatera Utara',
                            'Sumatera Barat' => 'Sumatera Barat',
                            'Riau' => 'Riau',
                            'Kepulauan Riau' => 'Kepulauan Riau',
                            'Jambi' => 'Jambi',
                            'Sumatera Selatan' => 'Sumatera Selatan',
                            'Bangka Belitung' => 'Kepulauan Bangka Belitung',
                            'Bengkulu' => 'Bengkulu',
                            'Lampung' => 'Lampung',
                            'DKI Jakarta' => 'DKI Jakarta',
                            'Jawa Barat' => 'Jawa Barat',
                            'Jawa Tengah' => 'Jawa Tengah',
                            'DI Yogyakarta' => 'DI Yogyakarta',
                            'Jawa Timur' => 'Jawa Timur',
                            'Banten' => 'Banten',
                            'Bali' => 'Bali',
                            'Nusa Tenggara Barat' => 'Nusa Tenggara Barat',
                            'Nusa Tenggara Timur' => 'Nusa Tenggara Timur',
                            'Kalimantan Barat' => 'Kalimantan Barat',
                            'Kalimantan Tengah' => 'Kalimantan Tengah',
                            'Kalimantan Selatan' => 'Kalimantan Selatan',
                            'Kalimantan Timur' => 'Kalimantan Timur',
                            'Kalimantan Utara' => 'Kalimantan Utara',
                            'Sulawesi Utara' => 'Sulawesi Utara',
                            'Sulawesi Tengah' => 'Sulawesi Tengah',
                            'Sulawesi Selatan' => 'Sulawesi Selatan',
                            'Sulawesi Tenggara' => 'Sulawesi Tenggara',
                            'Gorontalo' => 'Gorontalo',
                            'Sulawesi Barat' => 'Sulawesi Barat',
                            'Maluku' => 'Maluku',
                            'Maluku Utara' => 'Maluku Utara',
                            'Papua' => 'Papua',
                            'Papua Barat' => 'Papua Barat',
                            'Papua Selatan' => 'Papua Selatan',
                            'Papua Tengah' => 'Papua Tengah',
                            'Papua Pegunungan' => 'Papua Pegunungan',
                            'Papua Barat Daya' => 'Papua Barat Daya',
                        ])
                        ->searchable()
                        ->required(),
                    TextInput::make('kota_kabupaten'),
                    Select::make('kewarganegaraan')
                        ->label('Kewarganegaraan')
                        ->options([
                            'Indonesia' => 'Indonesia',
                            'Malaysia' => 'Malaysia',
                            'Singapura' => 'Singapura',
                            'Brunei' => 'Brunei Darussalam',
                            'Thailand' => 'Thailand',
                            'Lainnya' => 'Lainnya',
                        ])
                        ->searchable()
                        ->default('Indonesia'),
                    // ->default(fn () => $this->record?->kewarganegaraan)
                    Select::make('status_pernikahan')
                        ->label('Status Pernikahan')
                        ->options([
                            'Belum Menikah' => 'Belum Menikah',
                            'Menikah' => 'Menikah',
                            'Cerai Hidup' => 'Cerai Hidup',
                            'Cerai Mati' => 'Cerai Mati',
                        ])
                        ->placeholder('Pilih status'),
                    Select::make('jenis_pendidikan')
                        ->label('Pendidikan Terakhir')
                        ->options([
                            'SD' => 'SD',
                            'SMP' => 'SMP',
                            'SMA' => 'SMA / SMK',
                            'D3' => 'D3',
                            'S1' => 'S1',
                            'S2' => 'S2',
                            'S3' => 'S3',
                            'Lainnya' => 'Lainnya',
                        ])
                        ->searchable(),
                    Select::make('jenis_pekerjaan')
                        ->label('Pekerjaan')
                        ->options([
                            'Pelajar/Mahasiswa' => 'Pelajar / Mahasiswa',
                            'Karyawan Swasta' => 'Karyawan Swasta',
                            'PNS' => 'PNS',
                            'Wiraswasta' => 'Wiraswasta',
                            'Ibu Rumah Tangga' => 'Ibu Rumah Tangga',
                            'Pensiunan' => 'Pensiunan',
                            'Lainnya' => 'Lainnya',
                        ])
                        ->searchable(),
                ]),
            ]);
    }

    protected function sectionUpload(): Component
    {
        return Section::make('Upload Dokumen')
            ->collapsible()
            ->schema([
                Grid::make(4)->schema([
                    FileUpload::make('upload_ktp')
                        ->label('Image KTP')
                        ->disk('public')
                        ->directory('customer-ktp')
                        ->image()
                        ->visibility('public')
                        ->imagePreviewHeight('75')
                        ->panelAspectRatio('3:2')
                        ->panelLayout('integrated'),

                    FileUpload::make('upload_kk')
                        ->label('Image KK')
                        ->disk('public')
                        ->directory('customer-kk')
                        ->image()
                        ->visibility('public')
                        ->imagePreviewHeight('75')
                        ->panelAspectRatio('3:2')
                        ->panelLayout('integrated'),

                    FileUpload::make('upload_passport')
                        ->label('Image Passport')
                        ->disk('public')
                        ->directory('customer-passport')
                        ->image()
                        ->visibility('public')
                        ->imagePreviewHeight('75')
                        ->panelAspectRatio('3:2')
                        ->panelLayout('integrated'),

                    FileUpload::make('upload_photo')
                        ->label('Photo Fullbody')
                        ->disk('public')
                        ->directory('customer-photo')
                        ->image()
                        ->visibility('public')
                        ->imagePreviewHeight('75')
                        ->panelAspectRatio('3:2')
                        ->panelLayout('integrated'),
                ]),
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

                Tables\Columns\TextColumn::make('nama_ktp')
                    ->label('Nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('nama_passport')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('no_ktp')
                    ->label('NIK')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('no_kk')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('no_hp')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_passport')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_ayah')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kota_passport')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tgl_dikeluarkan_passport')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_habis_passport')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_lahir')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tempat_lahir')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('provinsi')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('kota_kabupaten')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('kewarganegaraan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status_pernikahan')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('jenis_pendidikan')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('jenis_pekerjaan')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('upload_ktp')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('upload_kk')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('upload_passport')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('upload_photo')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Action::make('export')
                //     ->action(function ($livewire) {
                //         return Excel::download(
                //             new CustomersExport($livewire->getFilteredTableQuery()),
                //             'customer.xlsx'
                //         );
                //     })

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),

                Tables\Actions\BulkAction::make('export')
                    ->label('Export Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function ($livewire) {
                        return Excel::download(
                            new CustomersExport($livewire->getFilteredTableQuery()),
                            'customer.xlsx'
                        );
                    }),

                /* =========================
                 * EXPORT PDF (FIXED)
                 * ========================= */


                Tables\Actions\BulkAction::make('export_pdf')
                    ->label('Export PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function ($records) {
                        return response()->streamDownload(function () use ($records) {
                            echo Pdf::loadView('reports.customer-report', [
                                'records' => $records,
                            ])->stream();
                        }, 'customers-report.pdf');
                    }),

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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
