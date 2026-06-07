@extends('layouts.dewan_pastoral')

@section('title', 'Dasbor Dewan Pastoral')

@push('styles')
    <link rel="stylesheet" href="{{ asset('template/assets/extensions/apexcharts/apexcharts.css') }}">
    <style>
        .stats-icon.warning-custom {
            background-color: rgba(255, 179, 0, 0.1) !important;
        }
        .stats-icon.warning-custom i {
            color: #ffb300 !important;
        }
    </style>
@endpush

@section('content')
    <div class="page-content">
        <div class="page-heading">
            <div class="row align-items-center mb-4">
                <div class="col-md-6 col-12">
                    <h3 class="mb-0 text-dark fw-bold">Dasbor Dewan Pastoral</h3>
                    <p class="text-subtitle text-muted mb-0">Informasi ringkas dan statistik dinamika umat paroki.</p>
                </div>
                <div class="col-md-6 col-12 d-flex justify-content-md-end justify-content-start mt-3 mt-md-0">
                    <form action="{{ route('dewan_pastoral.dashboard') }}" method="GET" class="d-flex align-items-center gap-2">
                        <label for="tahun" class="text-muted font-semibold text-nowrap mb-0">Tahun Analisis:</label>
                        <select name="tahun" id="tahun" class="form-select border-warning-custom text-warning-custom bg-white shadow-sm" onchange="this.form.submit()" style="width: 130px; border-color: #ffb300; color: #ffb300; font-weight: 600;">
                            @foreach ($daftarTahun as $t)
                                <option value="{{ $t }}" {{ $t == $tahun ? 'selected' : '' }}>
                                    {{ $t }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
        </div>

        {{-- =====================================================================
             BARIS 1 : Stat Cards Utama (8 kartu)
        ====================================================================== --}}
        <section class="row">
            <div class="col-12">
                <div class="row">

                    {{-- Total Umat --}}
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                        <div class="stats-icon warning-custom mb-2">
                                            <i class="bi bi-people-fill"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Total Umat Aktif</h6>
                                        <h6 class="font-extrabold mb-0">{{ number_format($totalUmat) }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Total Keluarga --}}
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                        <div class="stats-icon green mb-2">
                                            <i class="bi bi-house-fill"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Total KK</h6>
                                        <h6 class="font-extrabold mb-0">{{ number_format($totalKeluarga) }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Total Wilayah --}}
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                        <div class="stats-icon purple mb-2">
                                            <i class="bi bi-map-fill"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Wilayah</h6>
                                        <h6 class="font-extrabold mb-0">{{ $totalWilayah }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Total KUB --}}
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                        <div class="stats-icon blue mb-2">
                                            <i class="bi bi-diagram-3-fill"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">KUB</h6>
                                        <h6 class="font-extrabold mb-0">{{ $totalKub }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Baptis Tahun Ini --}}
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                        <div class="stats-icon warning-custom mb-2">
                                            <i class="bi bi-droplet-fill"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Baptis ({{ $tahun }})</h6>
                                        <h6 class="font-extrabold mb-0">{{ $baptisTahunIni }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Kematian Tahun Ini --}}
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                        <div class="stats-icon red mb-2">
                                            <i class="bi bi-heart-pulse-fill"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Kematian ({{ $tahun }})</h6>
                                        <h6 class="font-extrabold mb-0">{{ $kematianTahunIni }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Mutasi Tahun Ini --}}
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                        <div class="stats-icon green mb-2">
                                            <i class="bi bi-arrow-left-right"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Mutasi ({{ $tahun }})</h6>
                                        <h6 class="font-extrabold mb-0">{{ $mutasiTahunIni }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Anggota DPP Aktif --}}
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                        <div class="stats-icon purple mb-2">
                                            <i class="bi bi-person-badge-fill"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Anggota DPP</h6>
                                        <h6 class="font-extrabold mb-0">{{ $totalDppAktif }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        {{-- =====================================================================
             BARIS 2 : Grafik Umat per Wilayah + Distribusi Jenis Kelamin
        ====================================================================== --}}
        <section class="row">

            {{-- Grafik Batang: Umat per Wilayah --}}
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Sebaran Umat per Wilayah</h4>
                    </div>
                    <div class="card-body">
                        <div id="chart-umat-wilayah"></div>
                    </div>
                </div>
            </div>

            {{-- Donut: Distribusi Jenis Kelamin --}}
            <div class="col-12 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Rasio Jenis Kelamin</h4>
                    </div>
                    <div class="card-body">
                        <div id="chart-jenis-kelamin"></div>
                        <div class="d-flex justify-content-center gap-4 mt-2">
                            <div class="d-flex align-items-center gap-1">
                                <span class="badge bg-primary"
                                    style="width:12px;height:12px;border-radius:50%;padding:0"></span>
                                <small>Laki-laki: <strong>{{ number_format($lakiLaki) }}</strong></small>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <span class="badge bg-danger"
                                    style="width:12px;height:12px;border-radius:50%;padding:0"></span>
                                <small>Perempuan: <strong>{{ number_format($perempuan) }}</strong></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </section>

        {{-- =====================================================================
             BARIS 3 : Sakramen per Jenis (Tahun Ini) + Mutasi per Jenis
        ====================================================================== --}}
        <section class="row">

            {{-- Sakramen tahun ini --}}
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Statistik Sakramen Tahun {{ $tahun }}</h4>
                    </div>
                    <div class="card-body">
                        <div id="chart-sakramen"></div>
                    </div>
                </div>
            </div>

            {{-- Mutasi per Jenis --}}
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Rekapitulasi Mutasi Tahun {{ $tahun }}</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless align-middle">
                            <tbody>
                                <tr>
                                    <td><i class="bi bi-person-fill-dash text-warning me-2"></i> Mutasi Umat</td>
                                    <td class="text-end fw-bold">{{ $mutasiUmat }}</td>
                                    <td style="width:40%">
                                        <div class="progress" style="height:6px">
                                            <div class="progress-bar bg-warning"
                                                style="width:{{ $mutasiTahunIni > 0 ? round(($mutasiUmat / $mutasiTahunIni) * 100) : 0 }}%">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><i class="bi bi-house-dash-fill text-info me-2"></i> Mutasi Keluarga</td>
                                    <td class="text-end fw-bold">{{ $mutasiKeluarga }}</td>
                                    <td>
                                        <div class="progress" style="height:6px">
                                            <div class="progress-bar bg-info"
                                                style="width:{{ $mutasiTahunIni > 0 ? round(($mutasiKeluarga / $mutasiTahunIni) * 100) : 0 }}%">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><i class="bi bi-arrow-repeat text-danger me-2"></i> Mutasi Agama</td>
                                    <td class="text-end fw-bold">{{ $mutasiAgama }}</td>
                                    <td>
                                        <div class="progress" style="height:6px">
                                            <div class="progress-bar bg-danger"
                                                style="width:{{ $mutasiTahunIni > 0 ? round(($mutasiAgama / $mutasiTahunIni) * 100) : 0 }}%">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <hr>
                        <h5 class="mt-3 mb-2 text-muted">Kelompok Kategorial Teraktif</h5>
                        <table class="table table-sm table-borderless align-middle mb-0">
                            <tbody>
                                @forelse ($kategorialList as $k)
                                    <tr>
                                        <td><i class="bi bi-people text-success me-2"></i>{{ $k->nama }}</td>
                                        <td class="text-end fw-bold">{{ $k->anggota_aktif_count }} anggota</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">Belum ada kategorial.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </section>

        {{-- =====================================================================
             BARIS 4 : Tabel Kematian Terbaru + Anggota DPP
        ====================================================================== --}}
        <section class="row">

            {{-- Kematian Terbaru --}}
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Kematian Terbaru</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama</th>
                                        <th>Meninggal</th>
                                        <th>Wilayah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($kematianTerbaru as $k)
                                        <tr>
                                            <td>{{ $k->umat->nama ?? '-' }}</td>
                                            <td>{{ $k->tanggal_meninggal->format('d M Y') }}</td>
                                            <td>{{ $k->umat->keluarga->kub->wilayah->nama ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-3">
                                                Belum ada data kematian.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Anggota DPP --}}
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Pengurus Dewan Pastoral Paroki</h4>
                        <a href="{{ route('dewan_pastoral.keanggotaan.index') }}" class="btn btn-sm btn-outline-warning">
                            Lihat semua
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama</th>
                                        <th>Jabatan</th>
                                        <th>Bidang</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($dppAktif as $d)
                                        <tr>
                                            <td>{{ $d->umat->nama ?? '-' }}</td>
                                            <td>{{ $d->jabatan }}</td>
                                            <td>{{ $d->bidang_tugas ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-3">
                                                Belum ada anggota DPP.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </section>

        {{-- =====================================================================
             BARIS 5 : Sakramen Terbaru
        ====================================================================== --}}
        <section class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Penerimaan Sakramen Terbaru</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama Umat</th>
                                        <th>Jenis Sakramen</th>
                                        <th>Tanggal</th>
                                        <th>Pelayan Sakramen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($sakramenTerbaru as $s)
                                        <tr>
                                            <td>{{ $s->umat->nama ?? '-' }}</td>
                                            <td>{{ str_replace('_', ' ', $s->jenis_sakramen) }}</td>
                                            <td>{{ $s->tanggal_penerimaan->format('d M Y') }}</td>
                                            <td>
                                                @if ($s->klerus_id)
                                                    {{ $s->klerus->nama ?? '-' }}
                                                @elseif ($s->detail && isset($s->detail->nama_pemberi))
                                                    <span class="text-muted">{{ $s->detail->nama_pemberi }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-3">
                                                Belum ada data sakramen.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
@endsection

@push('scripts')
    <script src="{{ asset('template/assets/extensions/apexcharts/apexcharts.min.js') }}"></script>
    <script>
        const wilayahLabels = @json($wilayahLabels);
        const wilayahData = @json($wilayahData);
        const sakramenLabels = @json($sakramenLabels);
        const sakramenData = @json($sakramenData);
        const lakiLaki = {{ $lakiLaki }};
        const perempuan = {{ $perempuan }};

        // ── 1. Grafik Batang: Umat per Wilayah ───────────────────────────────
        new ApexCharts(document.querySelector('#chart-umat-wilayah'), {
            chart: {
                type: 'bar',
                height: 280,
                toolbar: {
                    show: false
                }
            },
            series: [{
                name: 'Jumlah Umat',
                data: wilayahData
            }],
            xaxis: {
                categories: wilayahLabels
            },
            colors: ['#ffb300'],
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    columnWidth: '50%'
                }
            },
            dataLabels: {
                enabled: false
            },
            grid: {
                borderColor: '#f1f1f1'
            },
        }).render();

        // ── 2. Donut: Jenis Kelamin ───────────────────────────────────────────
        new ApexCharts(document.querySelector('#chart-jenis-kelamin'), {
            chart: {
                type: 'donut',
                height: 220
            },
            series: [lakiLaki, perempuan],
            labels: ['Laki-laki', 'Perempuan'],
            colors: ['#435ebe', '#e74c3c'],
            legend: {
                show: false
            },
            dataLabels: {
                enabled: true
            },
        }).render();

        // ── 3. Bar: Sakramen per Jenis ────────────────────────────────────────
        new ApexCharts(document.querySelector('#chart-sakramen'), {
            chart: {
                type: 'bar',
                height: 250,
                toolbar: {
                    show: false
                }
            },
            series: [{
                name: 'Jumlah',
                data: sakramenData
            }],
            xaxis: {
                categories: sakramenLabels
            },
            colors: ['#ffb300'],
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    horizontal: true
                }
            },
            dataLabels: {
                enabled: false
            },
            grid: {
                borderColor: '#f1f1f1'
            },
        }).render();
    </script>
@endpush
