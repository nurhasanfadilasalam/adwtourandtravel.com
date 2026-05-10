<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Customer;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class CustomersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function collection()
    {
        return Customer::with('user')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Title',
            'Nama',
            'Nama Ayah',
            'ID',
            'No Identitas',
            'Nama Passpor',
            'No Passpor',
            'Tgl DiKeluarkan Passpor',
            'Kota Passpor',
            'Masa Berlaku Passpor',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Umur',
            'Alamat',
            'Provinsi',
            'Kota/Kabupaten',
            'No HP',
            'Kewarganegaraan',
            'Status Pernikahan',
            'Pendidikan',
            'Tanggal Dibuat',
        ];
    }

    public function map($row): array
    {
        static $no = 1;

        if ($row->jenis_kelamin === 'laki-laki') {
            $title = 'Mr.';
        } elseif ($row->jenis_kelamin === 'perempuan') {
            $title = 'Mrs.';
        } else {
            $title = '-';
        }

        $umur = $row->tgl_lahir
                ? Carbon::parse($row->tgl_lahir)->age
                : '-';

        $formatTglLahir = $row->tgl_lahir
                ? Carbon::parse($row->tgl_lahir)->translatedFormat('Y m d')
                : '-';

        return [
            $no++,
            $title,
            $row->nama_ktp,
            $row->nama_ayah,
            'NIK',
            $row->no_ktp,
            $row->nama_passport,
            $row->no_passport,
            $row->tgl_dikeluarkan_passport,
            $row->kota_passport,
            $row->tgl_habis_passport,
            $row->tempat_lahir,
            $row->tgl_lahir,
            $umur,
            $row->alamat,
            $row->provinsi,
            $row->kota_kabupaten,
            $row->no_hp,
            $row->kewarganegaraan,
            $row->status_pernikahan,
            $row->jenis_pendidikan,
            $row->created_at->format('d-m-Y'),
        ];
    }

       public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => '16A34A'],
                ],
            ],
            'use' => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => 'thin',
                    ],
                ],
            ],
        ];
    }
}
