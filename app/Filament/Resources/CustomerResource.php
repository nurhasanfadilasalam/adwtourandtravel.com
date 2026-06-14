<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerDocumentResource\RelationManagers\DocumentsRelationManager;
use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use stdClass;


class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationGroup = "Kelola Customer";

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-heart';

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
                                    'perempuan' => 'Perempuan'
                                ])
                                ->searchable()
                                ->default('laki-laki'),
                            TextInput::make('no_ktp')
                                ->label('NIK')
                                ->numeric(),
                            // TextInput::make('no_kk')->numeric(),
                            DatePicker::make('tgl_lahir')
                                ->label('Tanggal Lahir'),
                            TextInput::make('tempat_lahir')
                                ->label('Tempat Lahir'),
                            TextInput::make('nama_ayah')
                                ->label('Nama Ayah'),
                            TextInput::make('no_hp')
                                ->label('Nomor HP')
                                ->prefix('+62'),
                            Textarea::make('alamat')
                                ->label('Alamat'),
                        ]),

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
                                   'Tidak Sekolah' => 'Tidak Sekolah',
                                    'SD' => 'SD',
                                    'SMP' => 'SMP',
                                    'SMA' => 'SMA / SMK',
                                    'D1' => 'D1',
                                    'D2' => 'D2',
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
                            TextInput::make('nama_passport')
                                ->label('Nama Passport'),
                            TextInput::make('no_passport')
                                ->label('No Passport'),
                            TextInput::make('kota_passport')
                                ->label('Kota Passport'),
                            DatePicker::make('tgl_dikeluarkan_passport')
                                ->label('Tgl Dikeluarkan Passport'),
                            DatePicker::make('tgl_habis_passport')
                                ->label('Tgl Habis Passport'),
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
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Customer')
                    ->sortable(),
                Tables\Columns\TextColumn::make('no_ktp')
                    ->label('NIK')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->sortable(),
                Tables\Columns\TextColumn::make('no_hp')
                    ->label('No HP')
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_passport')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_ayah')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('kota_passport')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tgl_passport')
                    ->label('Tanggal Passport')
                    ->columnSpan(3)
                    ->alignRight()
                    ->html(true)
                    ->getStateUsing(function ($record) {
                        // Format the 'tgl_dikeluarkan_passport' and 'tgl_habis_passport' dates
                        $data1 = Carbon::parse($record->tgl_dikeluarkan_passport)->format('Y-m-d');  // Format the issued date
                        $data2 = Carbon::parse($record->tgl_habis_passport)->translatedFormat('l, d F Y');

                        // Concatenate with <br> for line breaks between the two
                        return 'Start  : ' . $data1 . '<br>' . 'End    : ' . $data2;
                    }),
                Tables\Columns\TextColumn::make('nama_ktp')
                    ->label('Nama KTP')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_passport')
                    ->label('Nama Passport')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tgl_lahir')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tempat_lahir')
                    ->searchable(),
                Tables\Columns\TextColumn::make('provinsi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kota_kabupaten')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kewarganegaraan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status_pernikahan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenis_pendidikan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenis_pekerjaan')
                    ->searchable()
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
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),

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
            'documents' => DocumentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
            // 'index' => Pages\CustomerView::route('/'),
        ];
    }
}
