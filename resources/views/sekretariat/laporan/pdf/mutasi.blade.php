<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $filters['judul'] }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 1.2cm 1.5cm 1.5cm 1.5cm;
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
        
        /* Statistik Summary Box */
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
        }
        .stats-table td strong {
            font-size: 13px;
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
            vertical-align: middle;
        }
        .main-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
        }
        .section-header {
            font-weight: bold;
            font-size: 10px;
            border-bottom: 1.5px solid #000;
            padding-bottom: 3px;
            margin-top: 15px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .text-left {
            text-align: left !important;
        }
        .text-center {
            text-align: center !important;
        }
        .text-right {
            text-align: right !important;
        }
        .footer {
            width: 100%;
            margin-top: 30px;
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
        @if ($logoKiri)
            <img src="{{ $logoKiri }}" class="header-logo header-logo-left" alt="Logo kiri">
        @endif
        @if ($logoKanan)
            <img src="{{ $logoKanan }}" class="header-logo header-logo-right" alt="Logo kanan">
        @endif
        <div class="header-text">
            <h1>PAROKI KATEDRAL KRISTUS RAJA KUPANG</h1>
            <h3>KEUSKUPAN AGUNG KUPANG</h3>
            <p>Jl. Perintis Kemerdekaan, Kota Kupang, NTT | Telp: (0380) 821956</p>
        </div>
    </div>

    <div class="title-container">
        <h4>{{ $filters['judul'] }}</h4>
        <div class="filter-info">
            Periode Laporan: <strong>
                {{ $filters['tanggal_mulai'] ? date('d M Y', strtotime($filters['tanggal_mulai'])) : 'Awal' }}
                s/d 
                {{ $filters['tanggal_selesai'] ? date('d M Y', strtotime($filters['tanggal_selesai'])) : 'Hari Ini' }}
            </strong>
        </div>
    </div>

    @if ($filters['sub_report'] === 'dinamika')
        {{-- Ringkasan Laju Pertumbuhan --}}
        <table class="stats-table">
            <tr>
                <td style="width: 25%;">
                    <strong>+ {{ $stats['lahir'] }}</strong>
                    KELAHIRAN (UMAT BARU)
                </td>
                <td style="width: 25%;">
                    <strong>- {{ $stats['wafat'] }}</strong>
                    KEMATIAN (WAFAT)
                </td>
                <td style="width: 25%;">
                    <strong>+ {{ $stats['masuk'] }}</strong>
                    MUTASI MASUK PAROKI
                </td>
                <td style="width: 25%;">
                    <strong>- {{ $stats['keluar'] }}</strong>
                    MUTASI KELUAR PAROKI
                </td>
            </tr>
        </table>

        {{-- Section 1: Kelahiran --}}
        <div class="section-header">A. Daftar Kelahiran / Umat Baru Terdaftar</div>
        <table class="main-table">
            <thead>
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th width="35%" class="text-left">Nama Lengkap Anak</th>
                    <th width="15%" class="text-center">Tanggal Lahir</th>
                    <th width="25%" class="text-left">Nama Orang Tua (Ayah / Ibu)</th>
                    <th width="20%" class="text-left">KUB</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($kelahiranList as $index => $k)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-left"><strong>{{ $k->nama }}</strong></td>
                        <td class="text-center">{{ $k->tanggal_lahir ? $k->tanggal_lahir->format('d/m/Y') : '-' }}</td>
                        <td class="text-left">{{ $k->nama_ayah ?? '-' }} / {{ $k->nama_ibu ?? '-' }}</td>
                        <td class="text-left">{{ $k->keluarga->kub->nama ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted" style="padding: 10px;">Tidak ada data kelahiran baru terdaftar pada periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Section 2: Kematian --}}
        <div class="section-header">B. Daftar Kematian (Almarhum / Almarhumah)</div>
        <table class="main-table">
            <thead>
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th width="35%" class="text-left">Nama Umat Berpulang</th>
                    <th width="15%" class="text-center">Tanggal Meninggal</th>
                    <th width="25%" class="text-left">Tempat Meninggal / Makam</th>
                    <th width="20%" class="text-left">KUB Asal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($kematianList as $index => $d)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-left"><strong>{{ $d->umat->nama ?? '-' }}</strong></td>
                        <td class="text-center">{{ $d->tanggal_meninggal ? $d->tanggal_meninggal->format('d/m/Y') : '-' }}</td>
                        <td class="text-left">{{ $d->tempat_meninggal ?? '-' }} / {{ $d->tempat_pemakaman ?? '-' }}</td>
                        <td class="text-left">{{ $d->umat?->keluarga?->kub->nama ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted" style="padding: 10px;">Tidak ada data umat meninggal dunia pada periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Section 3: Mutasi Masuk --}}
        <div class="section-header">C. Log Mutasi Masuk (Dari Luar Paroki)</div>
        <table class="main-table">
            <thead>
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th width="35%" class="text-left">Nama Umat / Keluarga</th>
                    <th width="15%" class="text-center">Tanggal Masuk</th>
                    <th width="25%" class="text-left">Asal Paroki / Keuskupan</th>
                    <th width="20%" class="text-left">Tujuan KUB</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($mutasiMasuk as $index => $m)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-left">
                            @if ($m->jenis === 'umat' && $m->mutasiUmat)
                                <strong>{{ $m->mutasiUmat->umat->nama ?? '-' }}</strong> (Umat)
                            @elseif ($m->jenis === 'keluarga' && $m->mutasiKeluarga)
                                <strong>Kel. {{ $m->mutasiKeluarga->keluarga->kepalaKeluarga->nama ?? '-' }}</strong> (Keluarga)
                            @endif
                        </td>
                        <td class="text-center">{{ $m->tanggal ? $m->tanggal->format('d/m/Y') : '-' }}</td>
                        <td class="text-left">
                            @if ($m->jenis === 'umat')
                                {{ $m->mutasiUmat->parokiAsal->nama ?? 'Paroki Luar' }} / {{ $m->mutasiUmat->keuskupanAsal->nama ?? 'Keuskupan Luar' }}
                            @else
                                {{ $m->mutasiKeluarga->parokiAsal->nama ?? 'Paroki Luar' }} / {{ $m->mutasiKeluarga->keuskupanAsal->nama ?? 'Keuskupan Luar' }}
                            @endif
                        </td>
                        <td class="text-left">
                            @if ($m->jenis === 'umat')
                                {{ $m->mutasiUmat->kubTujuan->nama ?? '-' }}
                            @else
                                {{ $m->mutasiKeluarga->kubTujuan->nama ?? '-' }}
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted" style="padding: 10px;">Tidak ada log mutasi masuk disetujui pada periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Section 4: Mutasi Keluar --}}
        <div class="section-header">D. Log Mutasi Keluar (Ke Luar Paroki)</div>
        <table class="main-table">
            <thead>
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th width="35%" class="text-left">Nama Umat / Keluarga</th>
                    <th width="15%" class="text-center">Tanggal Keluar</th>
                    <th width="25%" class="text-left">KUB Asal</th>
                    <th width="20%" class="text-left">Tujuan Paroki / Keuskupan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($mutasiKeluar as $index => $m)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-left">
                            @if ($m->jenis === 'umat' && $m->mutasiUmat)
                                <strong>{{ $m->mutasiUmat->umat->nama ?? '-' }}</strong> (Umat)
                            @elseif ($m->jenis === 'keluarga' && $m->mutasiKeluarga)
                                <strong>Kel. {{ $m->mutasiKeluarga->keluarga->kepalaKeluarga->nama ?? '-' }}</strong> (Keluarga)
                            @endif
                        </td>
                        <td class="text-center">{{ $m->tanggal ? $m->tanggal->format('d/m/Y') : '-' }}</td>
                        <td class="text-left">
                            @if ($m->jenis === 'umat')
                                {{ $m->mutasiUmat->kubAsal->nama ?? '-' }}
                            @else
                                {{ $m->mutasiKeluarga->kubAsal->nama ?? '-' }}
                            @endif
                        </td>
                        <td class="text-left">
                            @if ($m->jenis === 'umat')
                                {{ $m->mutasiUmat->parokiTujuan->nama ?? 'Paroki Baru' }} / {{ $m->mutasiUmat->keuskupanTujuan->nama ?? 'Keuskupan Baru' }}
                            @else
                                {{ $m->mutasiKeluarga->parokiTujuan->nama ?? 'Paroki Baru' }} / {{ $m->mutasiKeluarga->keuskupanTujuan->nama ?? 'Keuskupan Baru' }}
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted" style="padding: 10px;">Tidak ada log mutasi keluar disetujui pada periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    @elseif ($filters['sub_report'] === 'log_disetujui')
        {{-- Daftar Log Mutasi Disetujui --}}
        <table class="main-table">
            <thead>
                <tr>
                    <th width="3%" class="text-center">No</th>
                    <th width="10%" class="text-center">Tanggal</th>
                    <th width="20%" class="text-left">Umat / Keluarga</th>
                    <th width="10%" class="text-center">Jenis</th>
                    <th width="12%" class="text-center">Sub-Jenis</th>
                    <th width="15%" class="text-left">Asal (KUB/Paroki)</th>
                    <th width="15%" class="text-left">Tujuan (KUB/Paroki)</th>
                    <th width="15%" class="text-left">Nomor Surat</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($mutasiList as $index => $m)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $m->tanggal ? $m->tanggal->format('d/m/Y') : '-' }}</td>
                        <td class="text-left">
                            @if ($m->jenis === 'umat' && $m->mutasiUmat)
                                <strong>{{ $m->mutasiUmat->umat->nama ?? '-' }}</strong><br>
                                <span style="font-size: 7px; color:#555;">Keluarga: {{ $m->mutasiUmat->keluargaAsal->nama ?? '-' }}</span>
                            @elseif ($m->jenis === 'keluarga' && $m->mutasiKeluarga)
                                <strong>Kel. {{ $m->mutasiKeluarga->keluarga->kepalaKeluarga->nama ?? '-' }}</strong><br>
                                <span style="font-size: 7px; color:#555;">No. KK: {{ $m->mutasiKeluarga->keluarga->no_kk ?? '-' }}</span>
                            @elseif ($m->jenis === 'agama' && $m->mutasiAgama)
                                <strong>{{ $m->mutasiAgama->umat->nama ?? '-' }}</strong><br>
                                <span style="font-size: 7px; color:#555;">Agama Asal: {{ $m->mutasiAgama->agama_asal ?? '-' }} &rarr; Katolik</span>
                            @endif
                        </td>
                        <td class="text-center">{{ strtoupper($m->jenis) }}</td>
                        <td class="text-center">
                            @if ($m->jenis === 'umat' && $m->mutasiUmat)
                                {{ str_replace('_', ' ', $m->mutasiUmat->sub_jenis) }}
                            @elseif ($m->jenis === 'keluarga' && $m->mutasiKeluarga)
                                {{ str_replace('_', ' ', $m->mutasiKeluarga->sub_jenis) }}
                            @else
                                Perpindahan Agama
                            @endif
                        </td>
                        <td class="text-left">
                            @if ($m->jenis === 'umat' && $m->mutasiUmat)
                                {{ $m->mutasiUmat->kubAsal->nama ?? '-' }} 
                                {{ $m->mutasiUmat->parokiAsal ? '(' . $m->mutasiUmat->parokiAsal->nama . ')' : '' }}
                            @elseif ($m->jenis === 'keluarga' && $m->mutasiKeluarga)
                                {{ $m->mutasiKeluarga->kubAsal->nama ?? '-' }}
                                {{ $m->mutasiKeluarga->parokiAsal ? '(' . $m->mutasiKeluarga->parokiAsal->nama . ')' : '' }}
                            @else
                                Non-Katolik
                            @endif
                        </td>
                        <td class="text-left">
                            @if ($m->jenis === 'umat' && $m->mutasiUmat)
                                {{ $m->mutasiUmat->kubTujuan->nama ?? '-' }}
                                {{ $m->mutasiUmat->parokiTujuan ? '(' . $m->mutasiUmat->parokiTujuan->nama . ')' : '' }}
                            @elseif ($m->jenis === 'keluarga' && $m->mutasiKeluarga)
                                {{ $m->mutasiKeluarga->kubTujuan->nama ?? '-' }}
                                {{ $m->mutasiKeluarga->parokiTujuan ? '(' . $m->mutasiKeluarga->parokiTujuan->nama . ')' : '' }}
                            @else
                                Paroki Assumpta
                            @endif
                        </td>
                        <td class="text-center" style="font-size: 8px;">
                            @if ($m->jenis === 'umat' && $m->mutasiUmat)
                                {{ $m->mutasiUmat->nomor_surat ?: '-' }}
                            @elseif ($m->jenis === 'keluarga' && $m->mutasiKeluarga)
                                {{ $m->mutasiKeluarga->nomor_surat ?: '-' }}
                            @else
                                {{ $m->mutasiAgama->nomor_surat ?: '-' }}
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 15px;">
                            Tidak ditemukan log mutasi resmi dalam periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    @php
        $currentParoki = null;
        $currentUser = Auth::user();
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
                        Sekretariat Paroki {{ $parokiNamaSign }}
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
