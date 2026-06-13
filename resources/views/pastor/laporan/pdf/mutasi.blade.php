<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Mutasi & Dinamika Umat</title>
    <style>
        @page {
            size: A4 landscape;
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
            <h1>PAROKI Kathedral Kristus Raja Kupang KUPANG</h1>
            <h3>KEUSKUPAN AGUNG KUPANG</h3>
            <p>Jl. Perintis Kemerdekaan, Kota Kupang, NTT | Telp: (0380) 821956</p>
        </div>
    </div>

    <div class="title-container">
        <h4>Laporan Riwayat Mutasi & Dinamika Umat</h4>
        <div class="filter-info">
            Jenis: <strong>{{ strtoupper($filters['jenis']) }}</strong> | 
            Status: <strong>{{ strtoupper($filters['status']) }}</strong> | 
            Periode: <strong>
                {{ $filters['tanggal_mulai'] ? date('d M Y', strtotime($filters['tanggal_mulai'])) : 'Awal' }}
                s/d 
                {{ $filters['tanggal_selesai'] ? date('d M Y', strtotime($filters['tanggal_selesai'])) : 'Hari Ini' }}
            </strong>
        </div>
    </div>

    <table class="main-table">
        <thead>
            <tr>
                <th width="3%" class="text-center">No</th>
                <th width="10%">Tanggal</th>
                <th width="12%">Jenis Mutasi</th>
                <th width="35%" class="text-left">Keterangan Umat & Jalur Kepindahan</th>
                <th width="10%">Status</th>
                <th width="15%" class="text-left">Diajukan/Diproses</th>
                <th width="15%" class="text-left">Alasan / Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($mutasiList as $index => $m)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">
                        <strong>{{ $m->tanggal ? $m->tanggal->format('d/m/Y') : '-' }}</strong>
                    </td>
                    <td class="text-center">
                        <span style="text-transform: capitalize; font-weight: bold;">Mutasi {{ $m->jenis }}</span>
                    </td>
                    <td class="text-left" style="font-size: 9px;">
                        @if ($m->jenis === 'umat' && $m->mutasiUmat)
                            <strong>Umat:</strong> {{ $m->mutasiUmat->umat->nama ?? '-' }}<br>
                            @if ($m->mutasiUmat->nomor_surat)
                                <span style="font-size: 7px; color: #555;">No. Surat: {{ $m->mutasiUmat->nomor_surat }}</span><br>
                            @endif
                            <span>KUB Asal:</span> {{ $m->mutasiUmat->kubAsal->nama ?? 'Luar Paroki' }} <br>
                            <span>KUB Tujuan:</span> {{ $m->mutasiUmat->kubTujuan->nama ?? 'Luar Paroki' }}
                            
                        @elseif ($m->jenis === 'keluarga' && $m->mutasiKeluarga)
                            <strong>Kepala KK:</strong> {{ $m->mutasiKeluarga->keluarga->kepalaKeluarga->nama ?? '-' }}<br>
                            @if ($m->mutasiKeluarga->nomor_surat)
                                <span style="font-size: 7px; color: #555;">No. Surat: {{ $m->mutasiKeluarga->nomor_surat }}</span><br>
                            @endif
                            <span>KUB Asal:</span> {{ $m->mutasiKeluarga->kubAsal->nama ?? 'Luar Paroki' }} <br>
                            <span>KUB Tujuan:</span> {{ $m->mutasiKeluarga->kubTujuan->nama ?? 'Luar Paroki' }}

                        @elseif ($m->jenis === 'agama' && $m->mutasiAgama)
                            <strong>Umat:</strong> {{ $m->mutasiAgama->umat->nama ?? '-' }}<br>
                            <span>Agama Asal:</span> {{ $m->mutasiAgama->agama_asal }} <br>
                            <span>Agama Baru:</span> {{ $m->mutasiAgama->agama_tujuan }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">
                        {{ strtoupper($m->status) }}
                    </td>
                    <td class="text-left" style="font-size: 8px;">
                        <strong>Pemohon:</strong> {{ $m->pemohon->nama ?? 'Sekretariat' }}<br>
                        <strong>Pemroses:</strong> {{ $m->diprosesOleh->name ?? '-' }}
                    </td>
                    <td class="text-left" style="font-size: 8px;">
                        <strong>Alasan:</strong> {{ $m->keterangan ?? '-' }}<br>
                        @if($m->catatan_admin)
                            <strong>Catatan Admin:</strong> {{ $m->catatan_admin }}
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 15px;">
                        Tidak ditemukan riwayat mutasi umat yang sesuai dengan filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @php
        $currentParoki = null;
        $currentUser = \Illuminate\Support\Facades\Auth::user();
        if ($currentUser && $currentUser->umat && $currentUser->umat->keluarga && $currentUser->umat->keluarga->kub && $currentUser->umat->keluarga->kub->wilayah) {
            $currentParoki = $currentUser->umat->keluarga->kub->wilayah->paroki;
        }
        if (!$currentParoki) {
            $currentParoki = \App\Models\Paroki::with('klerus')->first();
        } else {
            $currentParoki->loadMissing('klerus');
        }
        $parokiNamaSign = $currentParoki ? $currentParoki->nama : 'Kathedral Kristus Raja Kupang';
        $pastorNamaSign = $currentParoki && $currentParoki->klerus ? $currentParoki->klerus->nama : 'RD. GERARDUS DUKA, PR';
    @endphp
    <div class="footer">
        <table class="footer-table">
            <tr>
                <td class="text-left">
                    <div style="font-size: 8px; color: #555; margin-top: 40px;">
                        Dicetak pada: {{ date('d/m/Y H:i:s') }}<br>
                        Sistem Informasi Paroki {{ $parokiNamaSign }}
                    </div>
                </td>
                <td>
                    Kupang, {{ date('d F Y') }}<br>
                    <strong>Pastor Paroki {{ $parokiNamaSign }}</strong>
                    <div class="signature-area"></div>
                    ( <strong>{{ $pastorNamaSign }}</strong> )
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
