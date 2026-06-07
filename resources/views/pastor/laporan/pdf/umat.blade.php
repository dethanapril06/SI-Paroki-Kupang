<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Demografi & Statistik Umat</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 1.2cm 1cm 1.5cm 1cm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #000;
            font-size: 10px;
            line-height: 1.4;
        }
        .header {
            position: relative;
            text-align: center;
            border-bottom: 2px solid #000;
            min-height: 72px;
            padding: 0 90px 10px;
            box-sizing: border-box;
            margin-bottom: 15px;
        }
        .header-logo {
            position: absolute;
            top: 0;
            max-width: 72px;
            max-height: 72px;
        }
        .header-logo-left {
            left: 0;
        }
        .header-logo-right {
            right: 0;
        }
        .header-text {
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        .header h3 {
            margin: 3px 0 0;
            font-size: 12px;
            font-weight: normal;
        }
        .header p {
            margin: 2px 0 0;
            font-size: 10px;
        }
        .title-container {
            text-align: center;
            margin-bottom: 15px;
        }
        .title-container h4 {
            margin: 0;
            font-size: 12px;
            text-transform: uppercase;
            font-weight: bold;
        }
        .filter-info {
            font-size: 9px;
            margin-top: 3px;
        }
        
        /* Statistik Polosan */
        .stats-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .stats-table td {
            border: 1px solid #000;
            background-color: #f2f2f2;
            padding: 6px;
            text-align: center;
            font-size: 9px;
            width: 25%;
        }
        .stats-table td strong {
            font-size: 14px;
            display: block;
            margin-bottom: 2px;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .main-table th, .main-table td {
            border: 1px solid #000;
            padding: 5px 6px;
            font-size: 9px;
        }
        .main-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
        }
        .text-left {
            text-align: left !important;
        }
        .text-center {
            text-align: center !important;
        }
        .footer {
            width: 100%;
            margin-top: 20px;
        }
        .footer-table {
            width: 100%;
            text-align: center;
        }
        .footer-table td {
            width: 50%;
            vertical-align: top;
            font-size: 9px;
        }
        .signature-area {
            height: 60px;
        }
    </style>
</head>
<body>

    <div class="header">
        <img src="{{ public_path('images/Kiri.jpeg') }}" class="header-logo header-logo-left" alt="Logo kiri">
        <img src="{{ public_path('images/Kanan.jpeg') }}" class="header-logo header-logo-right" alt="Logo kanan">
        <div class="header-text">
            <h1>PAROKI ST. MARIA ASSUMPTA KUPANG</h1>
            <h3>KEUSKUPAN AGUNG KUPANG</h3>
            <p>Jl. Perintis Kemerdekaan, Kota Kupang, NTT | Telp: (0380) 821956</p>
        </div>
    </div>

    <div class="title-container">
        <h4>Laporan Demografi & Statistik Umat Paroki</h4>
        <div class="filter-info">
            KUB: <strong>{{ strtoupper($filters['kub']) }}</strong> | 
            Jenis Kelamin: <strong>{{ strtoupper($filters['jenis_kelamin']) }}</strong> | 
            Status: <strong>{{ strtoupper($filters['status_almarhum']) }}</strong>
        </div>
    </div>

    {{-- Ringkasan Statistik --}}
    <table class="stats-table">
        <tr>
            <td>
                <strong>{{ $stats['total_umat'] }}</strong>
                TOTAL UMAT
            </td>
            <td>
                <strong>{{ $stats['total_keluarga'] }}</strong>
                TOTAL KELUARGA (KK)
            </td>
            <td>
                <strong>{{ $stats['total_laki'] }}</strong>
                LAKI-LAKI
            </td>
            <td>
                <strong>{{ $stats['total_perempuan'] }}</strong>
                PEREMPUAN
            </td>
        </tr>
    </table>

    {{-- Tabel Data --}}
    <table class="main-table">
        <thead>
            <tr>
                <th width="4%" class="text-center">No</th>
                <th width="28%" class="text-left">Nama Lengkap Umat</th>
                <th width="8%" class="text-center">JK</th>
                <th width="16%">Hub. Keluarga</th>
                <th width="24%" class="text-left">Nama KUB / Wilayah</th>
                <th width="20%" class="text-left">Pendidikan / Pekerjaan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($umatList as $index => $u)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-left">
                        <strong>{{ $u->nama }}</strong>
                        @if($u->status_almarhum)
                            <span>(Alm.)</span>
                        @endif
                        <br>
                        <span style="font-size: 7px; color: #555;">
                            Lahir: {{ $u->tempat_lahir ?? '-' }}, {{ $u->tanggal_lahir ? $u->tanggal_lahir->format('d/m/Y') : '-' }}
                        </span>
                    </td>
                    <td class="text-center">{{ $u->jenis_kelamin == 'Laki-Laki' || $u->jenis_kelamin == 'L' ? 'L' : 'P' }}</td>
                    <td class="text-center">{{ str_replace('_', ' ', $u->hubungan_keluarga ?? '-') }}</td>
                    <td class="text-left">
                        {{ $u->keluarga->kub->nama ?? '-' }}
                        <br>
                        <span style="font-size: 7px; color: #555;">
                            {{ $u->keluarga->kub->wilayah->nama ?? '-' }}
                        </span>
                    </td>
                    <td class="text-left">
                        {{ $u->pendidikan ?? '-' }}
                        <br>
                        <span style="font-size: 7px; color: #555;">{{ $u->pekerjaan ?? '-' }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 15px;">
                        Tidak ditemukan data umat yang sesuai dengan filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td class="text-left">
                    <div style="font-size: 8px; color: #555; margin-top: 40px;">
                        Dicetak pada: {{ date('d/m/Y H:i:s') }}<br>
                        Sistem Informasi Paroki St. Maria Assumpta
                    </div>
                </td>
                <td>
                    Kupang, {{ date('d F Y') }}<br>
                    <strong>Pastor Paroki St. Maria Assumpta</strong>
                    <div class="signature-area"></div>
                    ( <strong>RD. GERARDUS DUKA, PR</strong> )
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
