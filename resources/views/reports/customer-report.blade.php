<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Customer</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 15mm;
        }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #333; padding: 6px; }
        th { background: #f1f1f1; }
    </style>
</head>
<body>

<h3>Laporan Data Customer</h3>

{{-- @if($start || $end)
    <p>
        Periode:
        {{ $start ?? '-' }} s/d {{ $end ?? '-' }}
    </p>
@endif --}}

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Title</th>
            <th>Nama</th>
            <th>Nama Ayah</th>
            <th>ID</th>
            <th>No Identitas</th>
            <th>Nama Passpor</th>
            <th>No Passpor</th>
            <th>No HP</th>
            <th>Kewarganegaraan</th>
            <th>Tgl Dibuat</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $i => $row)
            <tr>
                <td>{{ $i + 1 }}</td>
              <td>
                    @if ($row->jenis_kelamin === 'laki-laki')
                        Mr.
                    @elseif ($row->jenis_kelamin === 'perempuan')
                        Mrs.
                    @else
                        -
                    @endif
                </td>
                <td>{{ $row->nama_ktp }}</td>
                <td>{{ $row->nama_ayah }}</td>
                <td>NIK</td>
                <td>{{ $row->no_ktp }}</td>
                <td>{{ $row->nama_passport }}</td>
                <td>{{ $row->no_passport }}</td>
                <td>{{ $row->no_hp }}</td>
                <td>{{ ucfirst($row->kewarganegaraan) }}</td>
                <td>{{ $row->created_at->format('d-m-Y') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
