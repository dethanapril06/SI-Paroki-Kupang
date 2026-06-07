<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kartu Keluarga Katholik - {{ $keluarga->kepalaKeluarga?->nama }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            border: none;
            vertical-align: middle;
            padding: 0;
        }

        .header-logo-cell {
            width: 80px;
            text-align: center;
        }

        .header-logo {
            width: 68px;
            max-height: 68px;
        }

        .header-text {
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .header h3 {
            margin: 5px 0 0;
            font-size: 14px;
            font-weight: normal;
        }

        .header p {
            margin: 3px 0 0;
            font-size: 12px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .info-table td {
            vertical-align: top;
            padding: 3px;
        }

        .info-label {
            font-weight: bold;
            width: 130px;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .main-table th,
        .main-table td {
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: center;
        }

        .main-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 10px;
        }

        .main-table td {
            font-size: 10px;
        }

        .text-left {
            text-align: left !important;
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
            padding: 10px;
        }

        .signature-area {
            height: 80px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <table class="header-table">
                <tr>
                    <td class="header-logo-cell">
                        @if ($logoKiri)
                            <img src="{{ $logoKiri }}" class="header-logo" alt="Logo kiri">
                        @endif
                    </td>
                    <td class="header-text">
                        <h1>KARTU KELUARGA KATHOLIK</h1>
                        <h3>PAROKI KATEDRAL KRISTUS RAJA KUPANG</h3>
                        <p>Keuskupan Agung Kupang</p>
                    </td>
                    <td class="header-logo-cell">
                        @if ($logoKanan)
                            <img src="{{ $logoKanan }}" class="header-logo" alt="Logo kanan">
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <table class="info-table">
            <tr>
                <td class="info-label">Kepala Keluarga</td>
                <td style="width: 10px;">:</td>
                <td><strong>{{ strtoupper($keluarga->kepalaKeluarga?->nama) }}</strong></td>

                <td class="info-label" style="width: 100px;">KUB</td>
                <td style="width: 10px;">:</td>
                <td>{{ $keluarga->kub?->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Alamat Lengkap</td>
                <td>:</td>
                <td>{{ $keluarga->alamat ?? '-' }}</td>

                <td class="info-label">Wilayah</td>
                <td>:</td>
                <td>{{ $keluarga->kub?->wilayah?->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Status Tempat Tinggal</td>
                <td>:</td>
                <td>{{ $keluarga->status_tempat_tinggal ?? '-' }}</td>

                <td class="info-label"></td>
                <td></td>
                <td></td>
            </tr>
        </table>

        <table class="main-table">
            <thead>
                <tr>
                    <th width="3%">No</th>
                    <th width="15%">Nama Lengkap</th>
                    <th width="9%">Status<br>Hubungan</th>
                    <th width="4%">L/P</th>
                    <th width="11%">Tempat Lahir</th>
                    <th width="8%">Tgl Lahir</th>
                    <th width="7%">Gol. Darah</th>
                    <th width="10%">Pendidikan</th>
                    <th width="13%">Pekerjaan</th>
                    <th width="10%">Status<br>Perkawinan</th>
                    <th width="10%">Status<br>Baptis</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($keluarga->umat as $index => $anggota)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="text-left"><strong>{{ $anggota->nama }}</strong></td>
                        <td>{{ $anggota->hubungan_keluarga }}</td>
                        <td>{{ $anggota->jenis_kelamin == 'Laki-laki' ? 'L' : 'P' }}</td>
                        <td>{{ $anggota->tempat_lahir }}</td>
                        <td>{{ $anggota->tanggal_lahir?->format('d/m/Y') }}</td>
                        <td>{{ $anggota->golongan_darah ?? '-' }}</td>
                        <td>{{ $anggota->pendidikan ?? '-' }}</td>
                        <td>{{ $anggota->pekerjaan ?? '-' }}</td>
                        <td>{{ $anggota->status_pernikahan ?? '-' }}</td>
                        <td>
                            @if ($anggota->baptis)
                                Sudah
                            @else
                                Belum
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            <table class="footer-table">
                <tr>
                    <td>
                        Mengetahui,<br>
                        <strong>Ketua KUB</strong>
                        <div class="signature-area"></div>
                        (___________________________)
                    </td>
                    <td>
                        Kupang, {{ now()->format('d F Y') }}<br>
                        <strong>Kepala Keluarga</strong>
                        <div class="signature-area"></div>
                        (<strong>{{ strtoupper($keluarga->kepalaKeluarga?->nama ?? '..........................') }}</strong>)
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
