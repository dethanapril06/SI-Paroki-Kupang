@extends('layouts.sekretariat')

@section('title', 'Pusat Laporan & Cetak PDF')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-4">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Pusat Laporan & Cetak PDF</h3>
                    <p class="text-subtitle text-muted">
                        Pilih kategori laporan paroki, tentukan filter data secara spesifik, dan cetak berkas PDF resmi
                        paroki.
                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('sekretariat.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Laporan PDF</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                {{-- CARD 1: Laporan Sakramen --}}
                <div class="col-md-6 col-lg-6 mb-4">
                    <div class="card h-100 shadow-sm border-0 transition-hover">
                        <div class="card-header bg-primary text-white py-3 d-flex align-items-center gap-2"
                            style="background-color: #435ebe !important;">
                            <i class="bi bi-droplet-half fs-4"></i>
                            <h5 class="card-title text-white mb-0">1. Laporan Administrasi Sakramen</h5>
                        </div>
                        <div class="card-body py-4 d-flex flex-column justify-content-between">
                            <div>
                                <p class="text-muted small mb-4">
                                    Cetak rekap sakramen lengkap, register tahunan, atau petakan KUB dengan anak belum
                                    menerima inisiasi sakramen.
                                </p>
                                <form id="form-sakramen" action="{{ route('sekretariat.laporan.sakramen') }}" method="GET"
                                    target="_blank">
                                    <input type="hidden" name="action" id="action-sakramen" value="preview">

                                    <div class="form-group mb-3">
                                        <label class="form-label font-bold text-sm">Pilih Jenis Laporan</label>
                                        <select class="form-select border-primary-hover" name="sub_report"
                                            id="sub-report-sakramen" onchange="toggleSakramenFilters()">
                                            <option value="rekap">Rekapitulasi Penerimaan Sakramen</option>
                                            <option value="register">Buku Register Sakramen Tahunan (Baptis/Nikah)</option>
                                            <option value="belum_sakramen">Statistik Belum Sakramen per KUB</option>
                                        </select>
                                    </div>

                                    <div id="sakramen-common-filters">
                                        <div class="form-group mb-3">
                                            <label class="form-label font-bold text-sm">Jenis Sakramen</label>
                                            <select class="form-select" name="jenis" id="jenis-sakramen">
                                                <option value="semua">Semua Sakramen</option>
                                                <option value="BAPTIS">Baptis</option>
                                                <option value="KOMUNI_PERTAMA">Komuni Pertama</option>
                                                <option value="KRISMA">Krisma</option>
                                                <option value="PERNIKAHAN">Pernikahan</option>
                                                <option value="MINYAK_SUCI">Minyak Suci</option>
                                            </select>
                                        </div>

                                        <div class="form-group mb-3" id="kub-sakramen-container">
                                            <label class="form-label font-bold text-sm">KUB Umat</label>
                                            <select class="form-select" name="kub_id">
                                                <option value="">Semua KUB (Seluruh Paroki)</option>
                                                @foreach ($kubs as $k)
                                                    <option value="{{ $k->id }}">{{ $k->nama }}
                                                        ({{ $k->wilayah->nama ?? '-' }})</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="row" id="date-sakramen-container">
                                            <div class="col-6 mb-3">
                                                <div class="form-group">
                                                    <label class="form-label font-bold text-xs">Mulai Tanggal</label>
                                                    <input type="date" class="form-control" name="tanggal_mulai"
                                                        id="tgl-mulai-sakramen">
                                                </div>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <div class="form-group">
                                                    <label class="form-label font-bold text-xs">Sampai Tanggal</label>
                                                    <input type="date" class="form-control" name="tanggal_selesai"
                                                        id="tgl-selesai-sakramen">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="d-grid gap-2 mt-3">
                                <button type="button"
                                    class="btn btn-outline-primary d-flex align-items-center justify-content-center gap-2"
                                    style="border-color: #435ebe; color: #435ebe;"
                                    onclick="submitReport('sakramen', 'preview')">
                                    <i class="bi bi-eye-fill"></i> Preview PDF
                                </button>
                                <button type="button"
                                    class="btn btn-primary d-flex align-items-center justify-content-center gap-2"
                                    style="background-color: #435ebe; border-color: #435ebe;"
                                    onclick="submitReport('sakramen', 'download')">
                                    <i class="bi bi-file-earmark-pdf-fill"></i> Unduh PDF
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD 2: Laporan Demografi & Umat --}}
                <div class="col-md-6 col-lg-6 mb-4">
                    <div class="card h-100 shadow-sm border-0 transition-hover">
                        <div class="card-header bg-primary text-white py-3 d-flex align-items-center gap-2"
                            style="background-color: #435ebe !important;">
                            <i class="bi bi-person-lines-fill fs-4"></i>
                            <h5 class="card-title text-white mb-0">2. Laporan Demografi & Statistik Umat</h5>
                        </div>
                        <div class="card-body py-4 d-flex flex-column justify-content-between">
                            <div>
                                <p class="text-muted small mb-4">
                                    Cetak rekapitulasi KK/Umat per KUB, demografi kelompok usia, atau daftar roster umat
                                    lansia & sakit.
                                </p>
                                <form id="form-umat" action="{{ route('sekretariat.laporan.umat') }}" method="GET"
                                    target="_blank">
                                    <input type="hidden" name="action" id="action-umat" value="preview">

                                    <div class="form-group mb-3">
                                        <label class="form-label font-bold text-sm">Pilih Jenis Laporan</label>
                                        <select class="form-select border-primary-hover" name="sub_report"
                                            id="sub-report-umat">
                                            <option value="rekap_kub">Rekapitulasi Jumlah Umat & KK per KUB</option>
                                            <option value="kelompok_usia">Statistik Umat Berdasarkan Kelompok Usia</option>
                                            <option value="lansia_sakit">Daftar Umat Lansia & Sakit (Prioritas Kunjungan)
                                            </option>
                                        </select>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label font-bold text-sm">Filter Berdasarkan KUB</label>
                                        <select class="form-select" name="kub_id">
                                            <option value="">Semua KUB (Seluruh Paroki)</option>
                                            @foreach ($kubs as $k)
                                                <option value="{{ $k->id }}">{{ $k->nama }}
                                                    ({{ $k->wilayah->nama ?? '-' }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </form>
                            </div>

                            <div class="d-grid gap-2 mt-3">
                                <button type="button"
                                    class="btn btn-outline-primary d-flex align-items-center justify-content-center gap-2"
                                    style="border-color: #435ebe; color: #435ebe;"
                                    onclick="submitReport('umat', 'preview')">
                                    <i class="bi bi-eye-fill"></i> Preview PDF
                                </button>
                                <button type="button"
                                    class="btn btn-primary d-flex align-items-center justify-content-center gap-2"
                                    style="background-color: #435ebe; border-color: #435ebe;"
                                    onclick="submitReport('umat', 'download')">
                                    <i class="bi bi-file-earmark-pdf-fill"></i> Unduh PDF
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD 3: Laporan Mutasi --}}
                <div class="col-md-6 col-lg-6 mb-4">
                    <div class="card h-100 shadow-sm border-0 transition-hover">
                        <div class="card-header bg-primary text-white py-3 d-flex align-items-center gap-2"
                            style="background-color: #435ebe !important;">
                            <i class="bi bi-arrow-left-right fs-4"></i>
                            <h5 class="card-title text-white mb-0">3. Laporan Mutasi & Dinamika Umat</h5>
                        </div>
                        <div class="card-body py-4 d-flex flex-column justify-content-between">
                            <div>
                                <p class="text-muted small mb-4">
                                    Cetak laporan pertumbuhan umat (laju kelahiran/kematian/mutasi) atau daftar log
                                    perpindahan resmi yang disetujui.
                                </p>
                                <form id="form-mutasi" action="{{ route('sekretariat.laporan.mutasi') }}" method="GET"
                                    target="_blank">
                                    <input type="hidden" name="action" id="action-mutasi" value="preview">

                                    <div class="form-group mb-3">
                                        <label class="form-label font-bold text-sm">Pilih Jenis Laporan</label>
                                        <select class="form-select border-primary-hover" name="sub_report"
                                            id="sub-report-mutasi">
                                            <option value="dinamika">Laporan Pertumbuhan Umat (Kelahiran, Wafat, Mutasi)
                                            </option>
                                            <option value="log_disetujui">Daftar Log Mutasi Resmi Disetujui</option>
                                        </select>
                                    </div>

                                    <div class="row">
                                        <div class="col-6 mb-3">
                                            <div class="form-group">
                                                <label class="form-label font-bold text-xs">Mulai Tanggal</label>
                                                <input type="date" class="form-control" name="tanggal_mulai">
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="form-group">
                                                <label class="form-label font-bold text-xs">Sampai Tanggal</label>
                                                <input type="date" class="form-control" name="tanggal_selesai">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="d-grid gap-2 mt-3">
                                <button type="button"
                                    class="btn btn-outline-primary d-flex align-items-center justify-content-center gap-2"
                                    style="border-color: #435ebe; color: #435ebe;"
                                    onclick="submitReport('mutasi', 'preview')">
                                    <i class="bi bi-eye-fill"></i> Preview PDF
                                </button>
                                <button type="button"
                                    class="btn btn-primary d-flex align-items-center justify-content-center gap-2"
                                    style="background-color: #435ebe; border-color: #435ebe;"
                                    onclick="submitReport('mutasi', 'download')">
                                    <i class="bi bi-file-earmark-pdf-fill"></i> Unduh PDF
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD 4: Laporan Organisasi --}}
                <div class="col-md-6 col-lg-6 mb-4">
                    <div class="card h-100 shadow-sm border-0 transition-hover">
                        <div class="card-header bg-primary text-white py-3 d-flex align-items-center gap-2"
                            style="background-color: #435ebe !important;">
                            <i class="bi bi-diagram-3 fs-4"></i>
                            <h5 class="card-title text-white mb-0">4. Laporan Struktur Organisasi & Kelompok</h5>
                        </div>
                        <div class="card-body py-4 d-flex flex-column justify-content-between">
                            <div>
                                <p class="text-muted small mb-4">
                                    Cetak profil pengurus Dewan Pastoral Paroki (DPP) atau daftar rekapitulasi / roster
                                    anggota kelompok kategorial.
                                </p>
                                <form id="form-organisasi" action="{{ route('sekretariat.laporan.organisasi') }}"
                                    method="GET" target="_blank">
                                    <input type="hidden" name="action" id="action-organisasi" value="preview">

                                    <div class="form-group mb-3">
                                        <label class="form-label font-bold text-sm">Pilih Jenis Laporan</label>
                                        <select class="form-select border-primary-hover" name="sub_report"
                                            id="sub-report-organisasi" onchange="toggleOrganisasiFilters()">
                                            <option value="dpp">Profil Dewan Pastoral Paroki (DPP) Aktif</option>
                                            <option value="rekap_kategorial">Rekapitulasi Anggota Kelompok Kategorial
                                            </option>
                                            <option value="detail_kategorial">Detail Roster Pengurus & Anggota Kelompok
                                                Kategorial</option>
                                        </select>
                                    </div>

                                    <div class="form-group mb-3" id="kategorial-select-container" style="display: none;">
                                        <label class="form-label font-bold text-sm">Pilih Kelompok Kategorial</label>
                                        <select class="form-select" name="kategorial_id" id="kategorial-id">
                                            <option value="">Pilih Kelompok...</option>
                                            @foreach ($kategorials as $kat)
                                                <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </form>
                            </div>

                            <div class="d-grid gap-2 mt-3">
                                <button type="button"
                                    class="btn btn-outline-primary d-flex align-items-center justify-content-center gap-2"
                                    style="border-color: #435ebe; color: #435ebe;"
                                    onclick="submitReport('organisasi', 'preview')">
                                    <i class="bi bi-eye-fill"></i> Preview PDF
                                </button>
                                <button type="button"
                                    class="btn btn-primary d-flex align-items-center justify-content-center gap-2"
                                    style="background-color: #435ebe; border-color: #435ebe;"
                                    onclick="submitReport('organisasi', 'download')">
                                    <i class="bi bi-file-earmark-pdf-fill"></i> Unduh PDF
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('styles')
    <style>
        .transition-hover {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .transition-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
        }

        .border-primary-hover:focus {
            border-color: #435ebe;
            box-shadow: 0 0 0 0.25rem rgba(67, 94, 190, 0.25);
        }
    </style>
@endpush

@push('scripts')
    <script>
        function submitReport(type, action) {
            // Validation check for kategorial detail
            if (type === 'organisasi') {
                const subReport = document.getElementById('sub-report-organisasi').value;
                const kategorialId = document.getElementById('kategorial-id').value;
                if (subReport === 'detail_kategorial' && !kategorialId) {
                    alert('Silakan pilih salah satu kelompok kategorial terlebih dahulu!');
                    return;
                }
            }

            // Tentukan mode aksi (preview / download)
            document.getElementById('action-' + type).value = action;

            // Tentukan behaviour target window
            const form = document.getElementById('form-' + type);
            if (action === 'preview') {
                form.target = '_blank';
            } else {
                form.removeAttribute('target');
            }

            form.submit();
        }

        function toggleSakramenFilters() {
            const subReport = document.getElementById('sub-report-sakramen').value;
            const commonFilters = document.getElementById('sakramen-common-filters');
            const jenisSelect = document.getElementById('jenis-sakramen');
            const kubContainer = document.getElementById('kub-sakramen-container');
            const dateContainer = document.getElementById('date-sakramen-container');

            if (subReport === 'belum_sakramen') {
                commonFilters.style.display = 'none';
            } else {
                commonFilters.style.display = 'block';
                if (subReport === 'register') {
                    // Buku register biasanya untuk Baptis / Nikah, default ke Baptis
                    jenisSelect.value = 'BAPTIS';
                } else {
                    jenisSelect.value = 'semua';
                }
            }
        }

        function toggleOrganisasiFilters() {
            const subReport = document.getElementById('sub-report-organisasi').value;
            const container = document.getElementById('kategorial-select-container');

            if (subReport === 'detail_kategorial') {
                container.style.display = 'block';
            } else {
                container.style.display = 'none';
            }
        }

        // Run once on load
        document.addEventListener('DOMContentLoaded', function() {
            toggleSakramenFilters();
            toggleOrganisasiFilters();
        });
    </script>
@endpush
