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

        .kategorial-info-box {
            width: 100%;
            border: 1px solid #000;
            padding: 8px;
            background-color: #f9f9f9;
            margin-bottom: 15px;
            font-size: 9px;
        }

        .kategorial-info-box table {
            width: 100%;
            border-collapse: collapse;
        }

        .kategorial-info-box td {
            padding: 2px 0;
            vertical-align: top;
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
    </div>

    @if ($filters['sub_report'] === 'dpp')
        {{-- Profil DPP --}}
        <table class="main-table">
            <thead>
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th width="30%" class="text-left">Nama Lengkap Pengurus</th>
                    <th width="20%" class="text-center">Jabatan DPP</th>
                    <th width="25%" class="text-left">Bidang Tugas</th>
                    <th width="20%" class="text-left">KUB Asal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($dppList as $index => $dpp)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-left">
                            <strong>{{ $dpp->umat->nama ?? '-' }}</strong><br>
                            <span style="font-size: 7px; color: #555;">No. Telp:
                                {{ $dpp->umat->no_telepon ?? '-' }}</span>
                        </td>
                        <td class="text-center"><strong>{{ $dpp->jabatan }}</strong></td>
                        <td class="text-left">{{ $dpp->bidang_tugas ?: '-' }}</td>
                        <td class="text-left">{{ $dpp->umat?->keluarga?->kub->nama ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center" style="padding: 15px;">
                            Tidak ditemukan data pengurus DPP yang aktif.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @elseif ($filters['sub_report'] === 'rekap_kategorial')
        {{-- Rekapitulasi Kelompok Kategorial --}}
        <table class="main-table">
            <thead>
                <tr>
                    <th width="6%" class="text-center">No</th>
                    <th width="44%" class="text-left">Nama Kelompok Kategorial</th>
                    <th width="30%" class="text-left">Nama Ketua Kelompok</th>
                    <th width="20%" class="text-center">Jumlah Anggota Aktif</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($kategorialList as $index => $kat)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-left"><strong>{{ $kat->nama }}</strong></td>
                        <td class="text-left">{{ $kat->ketuaUmat ? $kat->ketuaUmat->nama : 'Belum Ditentukan' }}</td>
                        <td class="text-center"><strong>{{ $kat->anggota_aktif_count }} Umat</strong></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center" style="padding: 15px;">
                            Tidak ditemukan data kelompok kategorial paroki.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @elseif ($filters['sub_report'] === 'detail_kategorial')
        {{-- Detail Anggota Kelompok Kategorial --}}
        <div class="kategorial-info-box">
            <table>
                <tr>
                    <td width="20%"><strong>Kelompok Kategorial</strong></td>
                    <td width="3%">:</td>
                    <td width="77%"><strong>{{ $filters['kategorial_nama'] }}</strong></td>
                </tr>
                <tr>
                    <td><strong>Ketua Kelompok</strong></td>
                    <td>:</td>
                    <td>{{ $filters['ketua'] }}</td>
                </tr>
                <tr>
                    <td><strong>Status Roster</strong></td>
                    <td>:</td>
                    <td>Aktif Paroki Katedral Kristus Raja Kupang</td>
                </tr>
            </table>
        </div>

        <table class="main-table">
            <thead>
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th width="35%" class="text-left">Nama Anggota</th>
                    <th width="10%" class="text-center">JK</th>
                    <th width="25%" class="text-left">KUB Asal</th>
                    <th width="25%" class="text-left">Jabatan di Kelompok</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($anggotaList as $index => $u)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-left">
                            <strong>{{ $u->nama }}</strong><br>
                            <span style="font-size: 7px; color: #555;">No. Telp: {{ $u->no_telepon ?? '-' }}</span>
                        </td>
                        <td class="text-center">
                            {{ $u->jenis_kelamin === 'Laki-laki' || $u->jenis_kelamin === 'L' ? 'L' : 'P' }}</td>
                        <td class="text-left">{{ $u->keluarga->kub->nama ?? '-' }}</td>
                        <td class="text-left"><strong>{{ $u->pivot->jabatan ?: 'Anggota' }}</strong></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted" style="padding: 15px;">
                            Belum ada anggota aktif terdaftar pada kelompok kategorial ini.
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
