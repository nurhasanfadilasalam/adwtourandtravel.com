<?php

namespace App\Filament\Customer\Resources\MyProfileResource\Pages;


use App\Models\Customer;
use Filament\Forms\Form;
use Filament\Actions\Action;
use function Filament\authorize;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Support\Exceptions\Halt;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Illuminate\Auth\Access\AuthorizationException;
use Filament\Pages\Concerns\InteractsWithFormActions;
use App\Filament\Customer\Resources\MyProfileResource;


class FormProfile extends Page
{
    protected static string $resource = MyProfileResource::class;

    protected static string $view = 'filament.customer.resources.my-profile-resource.pages.form-profile';

    // Form handling and state management
    use InteractsWithFormActions;


    public array $data = [];

    // #[Locked]
    public ?Customer $record = null;

    // Mount method to initialize values before form is rendered
    public function mount(): void
    {
        // Menggunakan Filament::auth() untuk memastikan mengambil ID dari guard yang tepat
        $userId = \Filament\Facades\Filament::auth()->id();

        if (! $userId) {
            abort(403, 'User tidak terautentikasi.');
        }

        $this->record = Customer::firstOrCreate(
            ['user_id' => $userId],
            ['user_id' => $userId] // Data default jika baru dibuat
        );

        $this->form->fill(
            $this->record->attributesToArray()
        );
    }

    public function fillForm(): void
    {
        $data = $this->record->attributesToArray();

        $this->form->fill($data);
    }

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('filament-panels::resources/pages/edit-record.notifications.saved.title'));
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->sectionProfile(),
                $this->sectionPassport(),
                // $this->sectionTambahan(),
                $this->sectionUpload(),
                $this->sectionImagePreview()
            ])
            ->model($this->record)
            ->operation('edit')
            ->statePath('data');
    }

    protected function sectionProfile(): Component
    {
        return Section::make('Data Pribadi')
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
                    // TextInput::make('no_kk')
                    //     ->label('Nomor KK')
                    //     ->numeric(),
                    DatePicker::make('tgl_lahir')
                        ->label('Tanggal Lahir'),
                    TextInput::make('tempat_lahir')
                        ->label('Tempat Lahir'),
                    TextInput::make('nama_ayah')
                        ->label('Nama Ayah Kandung'),
                    TextInput::make('no_hp')
                        ->label('Nomor Handphone')
                        ->prefix('+62'),
                ]),
                Textarea::make('alamat')->columnSpanFull(),
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

    protected function sectionPassport(): Component
    {
        return Section::make('Data Passport')
            ->collapsible()
            ->schema([
                Grid::make(2)->schema([
                    TextInput::make('nama_passport'),
                    TextInput::make('no_passport')
                        ->default(fn() => $this->record?->no_passport),
                    TextInput::make('kota_passport')
                        ->default(fn() => $this->record?->kota_passport),
                    DatePicker::make('tgl_dikeluarkan_passport')
                        ->default(fn() => $this->record?->tgl_dikeluarkan_passport),
                    DatePicker::make('tgl_habis_passport')
                        ->default(fn() => $this->record?->tgl_habis_passport),
                ]),
            ]);
    }

    // protected function sectionTambahan(): Component
    // {
    //     return Section::make('Data Tambahan')
    //         ->collapsible()
    //         ->schema([

    //         ]);
    // }

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

    protected function sectionImagePreview(): Component
    {
        return Section::make('Preview Dokumen')
            ->collapsible()
            ->collapsed(true)
            ->schema([
                Grid::make(2)
                    ->schema([
                        Placeholder::make('upload_photo_preview')
                            ->label('Photo KTP')
                            ->extraAttributes(['class' => 'w-12 h-24'])
                            ->content(function ($get) {
                                $upload = $get('upload_ktp');

                                if (blank($upload)) {
                                    return '-';
                                }

                                // ambil path file pertama
                                $path = is_array($upload)
                                    ? collect($upload)->first()
                                    : $upload;

                                return view('components.image-preview', [
                                    'src' => $path,
                                ]);
                            })
                            // ->content( fn($get) => dd($get))
                            ->default(fn() => $this->record?->upload_ktp),

                        Placeholder::make('upload_kk_preview')
                            ->label('Photo KK')
                            ->extraAttributes(['class' => 'w-12 h-24'])
                            ->content(function ($get) {
                                $upload = $get('upload_kk');

                                if (blank($upload)) {
                                    return '-';
                                }

                                // ambil path file pertama
                                $path = is_array($upload)
                                    ? collect($upload)->first()
                                    : $upload;

                                return view('components.image-preview', [
                                    'src' => $path,
                                ]);
                            })
                            // ->content( fn($get) => dd($get))
                            ->default(fn() => $this->record?->upload_kk),

                        Placeholder::make('upload_passport_preview')
                            ->label('Photo KTP')
                            ->extraAttributes(['class' => 'w-12 h-24'])
                            ->content(function ($get) {
                                $upload = $get('upload_passport');

                                if (blank($upload)) {
                                    return '-';
                                }

                                // ambil path file pertama
                                $path = is_array($upload)
                                    ? collect($upload)->first()
                                    : $upload;

                                return view('components.image-preview', [
                                    'src' => $path,
                                ]);
                            })
                            // ->content( fn($get) => dd($get))
                            ->default(fn() => $this->record?->upload_ktp),


                        Placeholder::make('upload_photo_preview')
                            ->label('Photo KTP')
                            ->extraAttributes(['class' => 'w-12 h-24'])
                            ->content(function ($get) {
                                $upload = $get('upload_ktp');

                                if (blank($upload)) {
                                    return '-';
                                }

                                // ambil path file pertama
                                $path = is_array($upload)
                                    ? collect($upload)->first()
                                    : $upload;

                                return view('components.image-preview', [
                                    'src' => $path,
                                ]);
                            })
                            // ->content( fn($get) => dd($get))
                            ->default(fn() => $this->record?->upload_ktp),
                    ]),
            ]);
    }


    /* ================= SAVE ================= */
    public function save(): void
    {

        try {
            $data = $this->form->getState();

            $this->handleRecordUpdate($this->record, $data);
        } catch (Halt $exception) {
            return;
        }

        $this->getSavedNotification()->send();
    }

    protected function handleRecordUpdate(Customer $record, array $data): Customer
    {
        $record->fill($data);

        $keysToWatch = [
            'upload_ktp',
            'upload_kk',
            'upload_passport',
            'upload_photo',
        ];

        if ($record->isDirty($keysToWatch)) {
            $this->dispatch('customerProfileUpdated');
        }

        $record->save();

        return $record;
    }

    /**
     * @return array<Action | ActionGroup>
     */
    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
        ];
    }

    protected function getSaveFormAction(): Action
    {
        return Action::make('save')
            ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
            ->submit('save')
            ->keyBindings(['mod+s']);
    }


    public static function canView(Model $record): bool
    {
        try {
            return authorize('update', $record)->allowed();
        } catch (AuthorizationException $exception) {
            return $exception->toResponse()->allowed();
        }
    }
}
