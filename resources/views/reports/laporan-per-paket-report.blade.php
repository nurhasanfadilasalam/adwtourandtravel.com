<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Paket Umroh</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 15mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
        }
        th {
            background: #eee;
        }
    </style>
</head>
<body>

    <h2>Laporan : <strong>{{ $paket->nama_paket }}</strong></h2>
    {{-- <p><strong>Paket:</strong> {{ $paket->nama_paket }}</p> --}}
    <p><strong>Periode:</strong> {{ $periode }}</p>
    <p><strong>Total Customer:</strong> {{ $customers->count() }} Orang</p>
    <p>
        <strong>Total Uang Masuk:</strong>
        Rp {{ number_format($grandTotalBayar, 0, ',', '.') }}
    </p>

    <br>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Customer</th>
                <th>Jenis Kelamin</th>
                <th>Agen</th>
                <th>Harga Paket</th>
                <th>Sudah Dibayar</th>
                <th>Sisa Tagihan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($customers as $booking)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $booking->customer->nama_ktp }}</td>
                <td>{{ $booking->customer->jenis_kelamin }}</td>
                <td>{{ $booking->creator?->name ?? '-' }}</td>
                <td>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($booking->total_bayar, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($booking->total_tagihan, 0, ',', '.') }}</td>
            </tr>
            @endforeach

            @php
                $totalHargaPaket = $customers->sum('total_price');
                $totalSudahDibayar = $customers->sum('total_bayar');
                $totalSisaTagihan = $customers->sum('total_tagihan');
            @endphp

            <tr>
                <td colspan="4" class="bold" style="text-align: left;">TOTAL </td>
                <td class="bold text-right">
                    Rp {{ number_format($totalHargaPaket, 0, ',', '.') }}
                </td>
                <td class="bold text-right">
                    Rp {{ number_format($totalSudahDibayar, 0, ',', '.') }}
                </td>
                <td class="bold text-right">
                    Rp {{ number_format($totalSisaTagihan, 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

</body>
</html>
