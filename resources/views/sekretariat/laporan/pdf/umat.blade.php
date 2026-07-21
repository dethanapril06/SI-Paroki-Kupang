<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $filters['judul'] }}</title>
    <style>
        @page {
            size: A4 portrait;
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
        .group-header {
            background-color: #e6e6e6;
            font-weight: bold;
            font-size: 10px;
            padding: 6px;
            border: 1px solid #000;
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
            Filter KUB: <strong>{{ strtoupper($filters['kub']) }}</strong>
        </div>
    </div>

    @if ($filters['sub_report'] === 'rekap_kub')
        {{-- Ringkasan Statistik --}}
        <table class="stats-table">
            <tr>
                <td style="width: 25%;">
                    <strong>{{ $statsSummary['total_umat'] }}</strong>
                    TOTAL UMAT AKTIF
                </td>
                <td style="width: 25%;">
                    <strong>{{ $statsSummary['total_kk'] }}</strong>
                    TOTAL KEPALA KELUARGA (KK)
                </td>
                <td style="width: 25%;">
                    <strong>{{ $statsSummary['total_laki'] }}</strong>
                    TOTAL LAKI-LAKI
                </td>
                <td style="width: 25%;">
                    <strong>{{ $statsSummary['total_perempuan'] }}</strong>
                    TOTAL PEREMPUAN
                </td>
            </tr>
        </table>

        {{-- Tabel Data --}}
        <table class="main-table">
            <thead>
                <tr>
                    <th rowspan="2" width="15%" class="text-center">Tingkat</th>
                    <th rowspan="2" width="45%" class="text-left">Nama Wilayah / KUB</th>
                    <th rowspan="2" width="16%" class="text-center">Jumlah KK</th>
                    <th colspan="2" width="24%" class="text-center">Jumlah Umat</th>
                </tr>
                <tr>
                    <th class="text-center">Laki-Laki</th>
                    <th class="text-center">Perempuan</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $kubsGrouped = $kubsData->groupBy(function($kub) {
                        return $kub->wilayah->nama ?? 'Tanpa Wilayah';
                    });
                @endphp

                @foreach ($kubsGrouped as $wilayahNama => $kubs)
                    <!-- Wilayah Row -->
                    <tr style="background-color: #f2f2f2; font-weight: bold;">
                        <td class="text-center">Wilayah</td>
                        <td class="text-left">{{ $wilayahNama }}</td>
                        <td class="text-center">{{ $kubs->sum('total_kk') }}</td>
                        <td class="text-center">{{ $kubs->sum('total_laki') }}</td>
                        <td class="text-center">{{ $kubs->sum('total_perempuan') }}</td>
                    </tr>

                    <!-- KUB Rows under this Wilayah -->
                    @foreach ($kubs as $subIndex => $kub)
                        <tr>
                            <td class="text-center" style="color: #555;">KUB</td>
                            <td class="text-left" style="padding-left: 15px;">
                                {{ ($subIndex + 1) . '. ' . $kub->nama }}
                            </td>
                            <td class="text-center">{{ $kub->total_kk }}</td>
                            <td class="text-center">{{ $kub->total_laki }}</td>
                            <td class="text-center">{{ $kub->total_perempuan }}</td>
                        </tr>
                    @endforeach
                @endforeach

                <!-- Total Paroki Row -->
                <tr style="background-color: #e6e6e6; font-weight: bold;">
                    <td colspan="2" class="text-right">TOTAL PAROKI :</td>
                    <td class="text-center">{{ $statsSummary['total_kk'] }}</td>
                    <td class="text-center">{{ $statsSummary['total_laki'] }}</td>
                    <td class="text-center">{{ $statsSummary['total_perempuan'] }}</td>
                </tr>
            </tbody>
        </table>

    @elseif ($filters['sub_report'] === 'kelompok_usia')
        {{-- Ringkasan Statistik --}}
        <table class="stats-table">
            <tr>
                <td style="width: 16.6%;">
                    <strong>{{ $statsSummary['total'] }}</strong>
                    TOTAL UMAT
                </td>
                <td style="width: 16.6%;">
                    <strong>{{ $statsSummary['BIA'] }}</strong>
                    SEKAMI / BIA
                </td>
                <td style="width: 16.6%;">
                    <strong>{{ $statsSummary['BIR'] }}</strong>
                    BIR (REMAJA)
                </td>
                <td style="width: 16.6%;">
                    <strong>{{ $statsSummary['OMK'] }}</strong>
                    OMK
                </td>
                <td style="width: 16.6%;">
                    <strong>{{ $statsSummary['DEWASA'] }}</strong>
                    DEWASA
                </td>
                <td style="width: 16.6%;">
                    <strong>{{ $statsSummary['LANSIA'] }}</strong>
                    LANSIA
                </td>
            </tr>
        </table>

        {{-- Tabel Data dikelompokkan --}}
        @foreach ($umatGrouped as $kelompokUsia => $listUmat)
            <div class="group-header">
                {{ $kelompokUsia }} ({{ $listUmat->count() }} Umat)
            </div>
            <table class="main-table">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="35%" class="text-left">Nama Umat</th>
                        <th width="10%" class="text-center">Umur</th>
                        <th width="8%" class="text-center">JK</th>
                        <th width="22%" class="text-left">KUB</th>
                        <th width="20%" class="text-left">Hub. Keluarga</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($listUmat as $index => $u)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-left">
                                <strong>{{ $u->nama }}</strong><br>
                                <span style="font-size: 7px; color: #555;">
                                    TTL: {{ $u->tempat_lahir ?? '-' }}, {{ $u->tanggal_lahir ? $u->tanggal_lahir->format('d/m/Y') : '-' }}
                                </span>
                            </td>
                            <td class="text-center">{{ $u->age }} Tahun</td>
                            <td class="text-center">{{ $u->jenis_kelamin === 'Laki-laki' || $u->jenis_kelamin === 'L' ? 'L' : 'P' }}</td>
                            <td class="text-left">{{ $u->keluarga->kub->nama ?? '-' }}</td>
                            <td class="text-center">{{ str_replace('_', ' ', $u->hubungan_keluarga ?? '-') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach

    @elseif ($filters['sub_report'] === 'lansia_sakit')
        {{-- Ringkasan Statistik --}}
        <table class="stats-table">
            <tr>
                <td style="width: 33.3%;">
                    <strong>{{ $statsSummary['total'] }}</strong>
                    TOTAL PRIORITAS KUNJUNGAN
                </td>
                <td style="width: 33.3%;">
                    <strong>{{ $statsSummary['lansia'] }}</strong>
                    LANSIA (>= 60 TH)
                </td>
                <td style="width: 33.3%;">
                    <strong>{{ $statsSummary['disabilitas'] }}</strong>
                    SAKIT / DISABILITAS
                </td>
            </tr>
        </table>

        {{-- Tabel Umat --}}
        <table class="main-table">
            <thead>
                <tr>
                    <th width="4%" class="text-center">No</th>
                    <th width="26%" class="text-left">Nama Umat</th>
                    <th width="8%" class="text-center">Umur</th>
                    <th width="8%" class="text-center">JK</th>
                    <th width="24%" class="text-left">Nama KUB / Wilayah</th>
                    <th width="15%" class="text-left">Kondisi</th>
                    <th width="15%" class="text-left">Catatan Khusus / Ket.</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($umatList as $index => $u)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-left">
                            <strong>{{ $u->nama }}</strong><br>
                            <span style="font-size: 7px; color: #555;">No. Telp: {{ $u->no_telepon ?? '-' }}</span>
                        </td>
                        <td class="text-center">{{ $u->age }} Th</td>
                        <td class="text-center">{{ $u->jenis_kelamin === 'Laki-laki' || $u->jenis_kelamin === 'L' ? 'L' : 'P' }}</td>
                        <td class="text-left">
                            {{ $u->keluarga->kub->nama ?? '-' }}<br>
                            <span style="font-size: 7px; color: #555;">{{ $u->keluarga->kub->wilayah->nama ?? '-' }}</span>
                        </td>
                        <td class="text-left"><strong>{{ $u->kondisi }}</strong></td>
                        <td class="text-left" style="font-size: 8px;">
                            {{ $u->keterangan_lain ?: 'Lansia paroki prioritaskan kunjungan sakramen.' }}
                            @if ($u->penyandang_disabilitas)
                                <br><span class="badge bg-dark" style="color:red; font-weight:bold;">Penyandang Disabilitas</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 15px;">
                            Tidak ditemukan data umat lansia atau sakit yang terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    @elseif ($filters['sub_report'] === 'rekap_kategori_summary')
        <div style="font-size: 10px; margin-bottom: 10px; background-color: #f8f9fa; padding: 6px 10px; border-left: 3px solid #435ebe;">
            <strong>Filter Terpasang:</strong> {{ $filters['filter_text'] ?? 'Semua' }}
        </div>
        <table class="stats-table">
            <tr>
                <td style="width: 33.3%;">
                    <strong>{{ $statsSummary['total'] }}</strong>
                    TOTAL UMAT AKTIF
                </td>
                <td style="width: 33.3%;">
                    <strong>{{ $statsSummary['total_laki'] }}</strong>
                    TOTAL LAKI-LAKI
                </td>
                <td style="width: 33.3%;">
                    <strong>{{ $statsSummary['total_perempuan'] }}</strong>
                    TOTAL PEREMPUAN
                </td>
            </tr>
        </table>

        <table class="main-table">
            <thead>
                <tr>
                    <th width="8%" class="text-center">No</th>
                    <th width="44%" class="text-left">{{ $filters['group_title'] ?? 'Kategori' }}</th>
                    <th width="16%" class="text-center">Laki-Laki</th>
                    <th width="16%" class="text-center">Perempuan</th>
                    <th width="16%" class="text-center">Total Umat</th>
                    <th width="14%" class="text-center">Persentase</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($groupedSummary as $i => $row)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td class="text-left"><strong>{{ $row->kategori }}</strong></td>
                        <td class="text-center">{{ number_format($row->laki) }}</td>
                        <td class="text-center">{{ number_format($row->perempuan) }}</td>
                        <td class="text-center font-bold">{{ number_format($row->total) }}</td>
                        <td class="text-center">{{ $row->percent }}%</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center" style="padding: 15px;">Tidak ada data yang sesuai filter.</td>
                    </tr>
                @endforelse
            </tbody>
            @if(count($groupedSummary) > 0)
            <tfoot>
                <tr style="background-color: #eee; font-weight: bold;">
                    <td colspan="2" class="text-center">TOTAL KESELURUHAN</td>
                    <td class="text-center">{{ number_format($groupedSummary->sum('laki')) }}</td>
                    <td class="text-center">{{ number_format($groupedSummary->sum('perempuan')) }}</td>
                    <td class="text-center">{{ number_format($groupedSummary->sum('total')) }}</td>
                    <td class="text-center">100%</td>
                </tr>
            </tfoot>
            @endif
        </table>

    @elseif ($filters['sub_report'] === 'rekap_kategori_roster')
        <div style="font-size: 10px; margin-bottom: 10px; background-color: #f8f9fa; padding: 6px 10px; border-left: 3px solid #435ebe;">
            <strong>Filter Terpasang:</strong> {{ $filters['filter_text'] ?? 'Semua' }}
        </div>
        <table class="stats-table">
            <tr>
                <td style="width: 33.3%;">
                    <strong>{{ $statsSummary['total'] }}</strong>
                    TOTAL UMAT TERFILTER
                </td>
                <td style="width: 33.3%;">
                    <strong>{{ $statsSummary['total_laki'] }}</strong>
                    TOTAL LAKI-LAKI
                </td>
                <td style="width: 33.3%;">
                    <strong>{{ $statsSummary['total_perempuan'] }}</strong>
                    TOTAL PEREMPUAN
                </td>
            </tr>
        </table>

        <table class="main-table">
            <thead>
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th width="22%" class="text-left">Nama Lengkap</th>
                    <th width="15%" class="text-left">KUB / Wilayah</th>
                    <th width="6%" class="text-center">L/P</th>
                    <th width="12%" class="text-center">Usia & Tgl Lahir</th>
                    <th width="14%" class="text-left">Pekerjaan</th>
                    <th width="10%" class="text-center">Gol. Darah</th>
                    <th width="16%" class="text-left">Pendidikan / Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($umatList as $i => $u)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td class="text-left font-bold">{{ $u->nama }}</td>
                        <td class="text-left">
                            {{ $u->keluarga->kub->nama ?? '-' }}<br>
                            <span style="font-size: 7px; color: #555;">{{ $u->keluarga->kub->wilayah->nama ?? '-' }}</span>
                        </td>
                        <td class="text-center">{{ $u->jenis_kelamin === 'Laki-laki' || $u->jenis_kelamin === 'L' ? 'L' : 'P' }}</td>
                        <td class="text-center">
                            {{ $u->tanggal_lahir ? $u->tanggal_lahir->age . ' Th' : '-' }}<br>
                            <span style="font-size: 7px; color: #555;">{{ $u->tanggal_lahir ? $u->tanggal_lahir->format('d/m/Y') : '-' }}</span>
                        </td>
                        <td class="text-left">{{ $u->pekerjaan ?: '-' }}</td>
                        <td class="text-center"><strong>{{ $u->golongan_darah ?: '-' }}</strong></td>
                        <td class="text-left">
                            {{ $u->pendidikan ?: '-' }}<br>
                            <span style="font-size: 7px; color: #555;">{{ $u->status_pernikahan ?: '-' }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 15px;">Tidak ada data umat yang memenuhi filter.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    @php
        // Ambil paroki dari variable yang dilempar, atau fallback
        $currentParoki = $currentParoki ?? null;
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
