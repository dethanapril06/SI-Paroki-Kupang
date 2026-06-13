<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>{{ $filters['judul'] }}</title>
    <style>
        @page {
            size: A4 {{ $filters['sub_report'] === 'belum_sakramen' ? 'portrait' : 'landscape' }};
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
            width: 16.6%;
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

        .main-table th,
        .main-table td {
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
        @if ($filters['sub_report'] !== 'belum_sakramen')
            <div class="filter-info">
                Kategori Sakramen: <strong>{{ str_replace('_', ' ', strtoupper($filters['jenis'])) }}</strong> |
                KUB: <strong>{{ strtoupper($filters['kub']) }}</strong> |
                Periode: <strong>
                    {{ $filters['tanggal_mulai'] ? date('d M Y', strtotime($filters['tanggal_mulai'])) : 'Awal' }}
                    s/d
                    {{ $filters['tanggal_selesai'] ? date('d M Y', strtotime($filters['tanggal_selesai'])) : 'Hari Ini' }}
                </strong>
            </div>
        @endif
    </div>

    @if ($filters['sub_report'] === 'rekap')
        {{-- Ringkasan Statistik --}}
        <table class="stats-table">
            <tr>
                <td>
                    <strong>{{ $stats['total'] }}</strong>
                    TOTAL TERDATA
                </td>
                <td>
                    <strong>{{ $stats['baptis'] }}</strong>
                    BAPTIS
                </td>
                <td>
                    <strong>{{ $stats['komuni'] }}</strong>
                    KOMUNI I
                </td>
                <td>
                    <strong>{{ $stats['krisma'] }}</strong>
                    KRISMA
                </td>
                <td>
                    <strong>{{ $stats['pernikahan'] }}</strong>
                    PERNIKAHAN
                </td>
                <td>
                    <strong>{{ $stats['minyak_suci'] }}</strong>
                    MINYAK SUCI
                </td>
            </tr>
        </table>

        {{-- Tabel Data --}}
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
                            @if ($s->umat?->status_almarhum)
                                <span>(Alm.)</span>
                            @endif
                            <br>
                            <span style="font-size: 7px; color: #555;">KUB:
                                {{ $s->umat?->keluarga?->kub->nama ?? '-' }}</span>
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
                                <strong>Wali Bpk:</strong> {{ $s->baptis->nama_bapak_baptis ?? '-' }}<br>
                                <strong>Wali Ibu:</strong> {{ $s->baptis->nama_ibu_baptis ?? '-' }}
                            @elseif ($s->jenis_sakramen === 'KRISMA' && $s->krisma)
                                <strong>Nama Krisma:</strong> {{ $s->krisma->nama_krisma ?? '-' }}<br>
                                <strong>Uskup:</strong> {{ $s->krisma->uskup->nama ?? '-' }}
                            @elseif ($s->jenis_sakramen === 'PERNIKAHAN' && $s->pernikahan)
                                <strong>Pasangan:</strong> {{ $s->pernikahan->nama_pasangan }}<br>
                                <strong>Agama:</strong> {{ $s->pernikahan->agama_pasangan }}<br>
                                <strong>Jenis:</strong>
                                {{ str_replace('_', ' ', $s->pernikahan->jenis_pernikahan ?? '-') }}
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
                            Tidak ditemukan data penerimaan sakramen yang sesuai.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @elseif ($filters['sub_report'] === 'register')
        {{-- Buku Register Sakramen Tahunan --}}
        <table class="main-table">
            <thead>
                <tr>
                    <th width="4%" class="text-center">No</th>
                    <th width="24%" class="text-left">Nama Penerima Sakramen</th>
                    <th width="12%">Jenis</th>
                    <th width="12%">Tanggal Lahir</th>
                    <th width="12%">Tanggal Terima</th>
                    <th width="18%" class="text-left">Pelayan (Klerus)</th>
                    <th width="18%" class="text-left">Informasi Penunjang</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sakramenList as $index => $s)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-left">
                            <strong>{{ $s->umat->nama ?? '-' }}</strong><br>
                            <span style="font-size: 7px; color: #555;">KUB:
                                {{ $s->umat?->keluarga?->kub->nama ?? '-' }}</span>
                        </td>
                        <td class="text-center">{{ str_replace('_', ' ', $s->jenis_sakramen) }}</td>
                        <td class="text-center">
                            {{ $s->umat?->tanggal_lahir ? $s->umat->tanggal_lahir->format('d/m/Y') : '-' }}
                        </td>
                        <td class="text-center">
                            {{ $s->tanggal_penerimaan ? $s->tanggal_penerimaan->format('d/m/Y') : '-' }}
                        </td>
                        <td class="text-left">{{ $s->klerus->nama ?? ($s->detail->nama_pemberi ?? '-') }}</td>
                        <td class="text-left" style="font-size: 8px;">
                            @if ($s->jenis_sakramen === 'BAPTIS' && $s->baptis)
                                <strong>Nama Baptis:</strong> {{ $s->baptis->nama_baptis ?? '-' }}<br>
                                <strong>Ayah:</strong> {{ $s->umat->nama_ayah ?? '-' }}<br>
                                <strong>Ibu:</strong> {{ $s->umat->nama_ibu ?? '-' }}
                            @elseif ($s->jenis_sakramen === 'PERNIKAHAN' && $s->pernikahan)
                                <strong>Pasangan:</strong> {{ $s->pernikahan->nama_pasangan }}<br>
                                <strong>Saksi:</strong> {{ $s->pernikahan->nama_saksi_1 ?? '-' }} &
                                {{ $s->pernikahan->nama_saksi_2 ?? '-' }}
                            @elseif ($s->jenis_sakramen === 'KRISMA' && $s->krisma)
                                <strong>Nama Krisma:</strong> {{ $s->krisma->nama_krisma ?? '-' }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 15px;">
                            Tidak ditemukan data register dalam 1 tahun terakhir yang sesuai filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @elseif ($filters['sub_report'] === 'belum_sakramen')
        {{-- Statistik Belum Sakramen per KUB --}}
        <table class="main-table">
            <thead>
                <tr>
                    <th rowspan="2" width="12%" class="text-center">Tingkat</th>
                    <th rowspan="2" width="40%" class="text-left">Nama Wilayah / KUB</th>
                    <th rowspan="2" width="15%" class="text-center">Total Umat</th>
                    <th colspan="3" width="33%" class="text-center">Belum Menerima Sakramen</th>
                </tr>
                <tr>
                    <th class="text-center">Baptis</th>
                    <th class="text-center">Komuni I</th>
                    <th class="text-center">Krisma</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $kubsGrouped = $kubsData->sortBy(function($kub) {
                        return ($kub->wilayah->nama ?? '') . ' - ' . $kub->nama;
                    })->groupBy(function($kub) {
                        return $kub->wilayah->nama ?? 'Tanpa Wilayah';
                    });
                @endphp

                @foreach ($kubsGrouped as $wilayahNama => $kubs)
                    <!-- Wilayah Row -->
                    <tr style="background-color: #f2f2f2; font-weight: bold;">
                        <td class="text-center">Wilayah</td>
                        <td class="text-left">{{ $wilayahNama }}</td>
                        <td class="text-center">{{ $kubs->sum('total_umat') }}</td>
                        <td class="text-center">{{ $kubs->sum('belum_baptis') }}</td>
                        <td class="text-center">{{ $kubs->sum('belum_komuni') }}</td>
                        <td class="text-center">{{ $kubs->sum('belum_krisma') }}</td>
                    </tr>

                    <!-- KUB Rows under this Wilayah -->
                    @foreach ($kubs as $subIndex => $kub)
                        <tr>
                            <td class="text-center" style="color: #555;">KUB</td>
                            <td class="text-left" style="padding-left: 15px;">
                                {{ ($subIndex + 1) . '. ' . $kub->nama }}
                            </td>
                            <td class="text-center">{{ $kub->total_umat }}</td>
                            <td class="text-center"
                                style="{{ $kub->belum_baptis > 0 ? 'font-weight: bold; color: red;' : '' }}">
                                {{ $kub->belum_baptis }}
                            </td>
                            <td class="text-center"
                                style="{{ $kub->belum_komuni > 0 ? 'font-weight: bold; color: red;' : '' }}">
                                {{ $kub->belum_komuni }}
                            </td>
                            <td class="text-center"
                                style="{{ $kub->belum_krisma > 0 ? 'font-weight: bold; color: red;' : '' }}">
                                {{ $kub->belum_krisma }}
                            </td>
                        </tr>
                    @endforeach
                @endforeach

                <!-- Total Paroki Row -->
                <tr style="background-color: #e6e6e6; font-weight: bold;">
                    <td colspan="2" class="text-right">TOTAL PAROKI :</td>
                    <td class="text-center">{{ $kubsData->sum('total_umat') }}</td>
                    <td class="text-center">{{ $kubsData->sum('belum_baptis') }}</td>
                    <td class="text-center">{{ $kubsData->sum('belum_komuni') }}</td>
                    <td class="text-center">{{ $kubsData->sum('belum_krisma') }}</td>
                </tr>
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
                        Pusat Layanan Sekretariat Paroki {{ $parokiNamaSign }}
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
