<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Administrasi Sakramen</title>
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
            <h1>PAROKI ST. MARIA ASSUMPTA KUPANG</h1>
            <h3>KEUSKUPAN AGUNG KUPANG</h3>
            <p>Jl. Perintis Kemerdekaan, Kota Kupang, NTT | Telp: (0380) 821956</p>
        </div>
    </div>

    <div class="title-container">
        <h4>Laporan Riwayat Penerimaan Sakramen Umat</h4>
        <div class="filter-info">
            Kategori: <strong>{{ str_replace('_', ' ', strtoupper($filters['jenis'])) }}</strong> | 
            KUB: <strong>{{ strtoupper($filters['kub']) }}</strong> | 
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
                <th width="20%" class="text-left">Penerima Sakramen</th>
                <th width="12%">Jenis Sakramen</th>
                <th width="12%">Tanggal Terima</th>
                <th width="18%" class="text-left">Tempat / Paroki</th>
                <th width="15%" class="text-left">Pelayan (Klerus)</th>
                <th width="20%" class="text-left">Detail Informasi Khusus</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($sakramenList as $index => $s)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-left">
                        <strong>{{ $s->umat->nama ?? '-' }}</strong>
                        @if($s->umat?->status_almarhum)
                            <span>(Alm.)</span>
                        @endif
                        <br>
                        <span style="font-size: 7px; color: #555;">KUB: {{ $s->umat?->keluarga?->kub->nama ?? '-' }}</span>
                    </td>
                    <td class="text-center">
                        {{ str_replace('_', ' ', $s->jenis_sakramen) }}
                    </td>
                    <td class="text-center">
                        {{ $s->tanggal_penerimaan ? $s->tanggal_penerimaan->format('d/m/Y') : '-' }}
                    </td>
                    <td class="text-left">{{ $s->paroki->nama ?? 'Paroki Asal' }}</td>
                    <td class="text-left">
                        {{ $s->klerus->nama ?? ($s->detail->nama_pemberi ?? '-') }}
                    </td>
                    <td class="text-left" style="font-size: 8px;">
                        @if ($s->jenis_sakramen === 'BAPTIS' && $s->baptis)
                            <strong>Nama Baptis:</strong> {{ $s->baptis->nama_baptis ?? '-' }}<br>
                            <strong>Wali Bapak:</strong> {{ $s->baptis->nama_bapak_baptis ?? '-' }}<br>
                            <strong>Wali Ibu:</strong> {{ $s->baptis->nama_ibu_baptis ?? '-' }}
                        @elseif ($s->jenis_sakramen === 'KRISMA' && $s->krisma)
                            <strong>Nama Krisma:</strong> {{ $s->krisma->nama_krisma ?? '-' }}<br>
                            <strong>Uskup:</strong> {{ $s->krisma->uskup->nama ?? '-' }}
                        @elseif ($s->jenis_sakramen === 'PERNIKAHAN' && $s->pernikahan)
                            <strong>Pasangan:</strong> {{ $s->pernikahan->nama_pasangan }}<br>
                            <strong>Agama Pasangan:</strong> {{ $s->pernikahan->agama_pasangan }}<br>
                            <strong>Jenis:</strong> {{ str_replace('_', ' ', $s->pernikahan->jenis_pernikahan ?? '-') }}
                        @elseif ($s->jenis_sakramen === 'MINYAK_SUCI' && $s->minyakSuci)
                            <strong>Tempat:</strong> {{ $s->minyakSuci->tempat_terima ?? '-' }}<br>
                            <strong>Sebab:</strong> {{ $s->minyakSuci->keterangan_sebab ?? '-' }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 15px;">
                        Tidak ditemukan data penerimaan sakramen pada kategori ini.
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
