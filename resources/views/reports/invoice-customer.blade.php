<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #000;
        }

        .container {
            width: 100%;
        }

        /* HEADER */
        .header {
            width: 100%;
            margin-bottom: 20px;
        }

        .header-left {
            float: left;
            width: 60%;
        }

        .header-right {
            float: right;
            width: 40%;
            text-align: right;
        }

        .logo {
            width: 70px;
            margin-bottom: 5px;
        }

        .company-name {
            font-weight: bold;
            text-transform: uppercase;
        }

        .invoice-box {
            border: 2px solid #000;
            display: inline-block;
            padding: 6px 12px;
            text-align: center;
        }

        .invoice-box .title {
            background: #000;
            color: #fff;
            padding: 4px;
            font-weight: bold;
        }

        .clear {
            clear: both;
        }

        /* CUSTOMER */
        .customer {
            margin-bottom: 15px;
        }

        /* TABLE */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        table th,
        table td {
            border: 1px solid #000;
            padding: 6px;
        }

        table th {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        /* FOOTER */
        .rekening {
            margin-top: 15px;
        }

        .footer {
            margin-top: 30px;
            width: 100%;
        }

        .footer-left {
            float: left;
            width: 60%;
        }

        .footer-right {
            float: right;
            width: 40%;
            text-align: center;
        }

        .qr {
            width: 90px;
            margin-top: 5px;
        }
    </style>
</head>
<body>

<div class="container">

    {{-- HEADER --}}
    <div class="header">
        <div class="header-left">
            {{-- LOGO --}}
            <img src="{{ public_path('frontend/images/icon_adw.png') }}" class="logo">

            <div class="company-name">PT AET DUNIA WISATA</div>
            <div>
                Jl. Imam Munandar No. 350, Tangkerang Labuai<br>
                Kec. Bukit Raya, Kota Pekanbaru<br>
                Riau, 28281
            </div>
        </div>

        <div class="header-right">
            <div class="invoice-box">
                <div class="title">INVOICE</div>
                <div>No. {{ str_pad($booking->id, 3, '0', STR_PAD_LEFT) }}</div>
            </div>
        </div>

        <div class="clear"></div>
    </div>

    {{-- CUSTOMER --}}
    <div class="customer">
        <strong>Kepada Yth.</strong><br>
        {{ $booking->customer->user->name ?? '-' }}
    </div>

    {{-- TABLE --}}
    <table>
        <thead>
        <tr>
            <th>No</th>
            <th>Deskripsi</th>
            <th>Qty</th>
            <th>Harga</th>
            <th>Sub Total</th>
            <th>Keterangan</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td class="text-center">1</td>
            <td>{{ $booking->paketUmroh->nama_paket ?? '-' }}</td>
            <td class="text-center">1</td>
            <td class="text-right">Rp {{ number_format($booking->total_price,0,',','.') }}</td>
            <td class="text-right">Rp {{ number_format($booking->total_price,0,',','.') }}</td>
            <td>
                Jamaah atas nama {{ $booking->customer->user->name }}<br>
                (NIK: {{ $booking->customer->no_ktp ?? '-' }})
            </td>
        </tr>

        <tr>
            <td colspan="4" class="bold text-right">TOTAL</td>
            <td class="bold text-right">Rp {{ number_format($booking->total_price,0,',','.') }}</td>
            <td></td>
        </tr>

        @foreach($payments as $index => $payment)
        <tr>
            <td colspan="4">
                Payment {{ $index+1 }} {{ $booking->paketUmroh->nama_paket }}
                ({{ $payment->tanggal_bayar->format('d/m/Y') }})
            </td>
            <td class="text-right">
                Rp {{ number_format($payment->jumlah_bayar,0,',','.') }}
            </td>
            <td>
                {{ ucfirst($payment->metode_pembayaran) }}
            </td>
        </tr>
        @endforeach

        <tr>
            <td colspan="4" class="bold">SISA PEMBAYARAN</td>
            <td class="bold text-right">
                Rp {{ number_format($booking->sisa_tagihan,0,',','.') }}
            </td>
            <td></td>
        </tr>
        </tbody>
    </table>

    {{-- REKENING --}}
    <div class="rekening">
        <strong>Pembayaran dapat ditransfer ke nomor rekening berikut:</strong><br>
        <strong>Mandiri</strong> : 108051234555 a.n PT. Aet Dunia Wisata<br>
        <strong>BSI</strong> : 7142147453 a.n PT. Aet Dunia Wisata
    </div>

    <br>

    <strong>Catatan:</strong><br>
    Pelunasan paling lambat H-30 keberangkatan

    {{-- FOOTER --}}
    <div class="footer">
        <div class="footer-left"></div>

        <div class="footer-right">
            Pekanbaru, {{ now()->translatedFormat('d F Y') }}<br><br>

            {{-- QR --}}
            <img src="{{ public_path('frontend/images/qr_code_adw.png') }}" class="qr"><br>

            <strong>( H. Abdul Azis )</strong><br>
            Direktur
        </div>

        <div class="clear"></div>
    </div>

</div>

</body>
</html>
