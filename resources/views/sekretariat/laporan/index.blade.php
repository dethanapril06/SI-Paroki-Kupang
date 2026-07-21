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
                                            id="sub-report-umat" onchange="toggleUmatFilters()">
                                            <option value="rekap_kub">Rekapitulasi Jumlah Umat & KK per KUB</option>
                                            <option value="kelompok_usia">Statistik Umat Berdasarkan Kelompok Usia</option>
                                            <option value="lansia_sakit">Daftar Umat Lansia & Sakit (Prioritas Kunjungan)</option>
                                            <option value="rekap_kategori">Rekap & Roster Umat per Kategori (Pekerjaan, Gol. Darah, dll.)</option>
                                        </select>
                                    </div>

                                    <div id="btn-filter-kategori-container" class="mb-3" style="display: none;">
                                        <button type="button" class="btn btn-outline-primary btn-sm w-100 d-flex align-items-center justify-content-center gap-2 font-bold py-2 shadow-sm" onclick="openModalFilterUmat()" style="border-color: #435ebe; color: #435ebe; background-color: #f2f5ff;">
                                            <i class="bi bi-funnel-fill"></i> Atur Filter Kategori & Rekap Lengkap...
                                        </button>
                                    </div>

                                    <div class="form-group mb-3" id="kub-umat-container">
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

{{-- Modal Filter Rekap & Roster Umat per Kategori --}}
<div class="modal fade" id="modalFilterUmat" tabindex="-1" aria-labelledby="modalFilterUmatLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #435ebe 0%, #2b3990 100%);">
                <h5 class="modal-title font-bold d-flex align-items-center gap-2 text-white" id="modalFilterUmatLabel">
                    <i class="bi bi-funnel-fill fs-4"></i> Filter & Rekapitulasi Umat Per Kategori
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="background-color: #f8f9fa;">
                <form id="form-modal-filter-umat" action="{{ route('sekretariat.laporan.umat') }}" method="GET" target="_blank">
                    <input type="hidden" name="sub_report" value="rekap_kategori">
                    <input type="hidden" name="action" id="modal-action-umat" value="preview">

                    {{-- 1. Pilih Tampilan Output --}}
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 10px;">
                        <div class="card-body p-3">
                            <label class="form-label font-bold text-sm text-primary mb-2 d-block">
                                <i class="bi bi-layout-text-window-reverse me-1"></i> Pilih Tampilan Output Laporan
                            </label>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-check border rounded p-3 h-100 bg-white shadow-sm-hover" style="cursor: pointer;" onclick="document.getElementById('out-summary').click();">
                                        <input class="form-check-input" type="radio" name="output_type" id="out-summary" value="summary" checked onchange="toggleGroupBySelect()">
                                        <label class="form-check-label font-bold d-block" for="out-summary" style="cursor: pointer;">
                                            Rekapitulasi Statistik Ringkas
                                        </label>
                                        <small class="text-muted">Tabel ringkasan jumlah & persentase umat berdasarkan kategori.</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check border rounded p-3 h-100 bg-white shadow-sm-hover" style="cursor: pointer;" onclick="document.getElementById('out-roster').click();">
                                        <input class="form-check-input" type="radio" name="output_type" id="out-roster" value="roster" onchange="toggleGroupBySelect()">
                                        <label class="form-check-label font-bold d-block" for="out-roster" style="cursor: pointer;">
                                            Daftar Roster Umat Lengkap
                                        </label>
                                        <small class="text-muted">Tabel detail nama, KUB, usia, dan data pribadi umat yang memenuhi filter.</small>
                                    </div>
                                </div>
                            </div>

                            {{-- Group By (Hanya jika pilih Rekapitulasi Summary) --}}
                            <div id="container-group-by" class="mt-3 pt-3 border-top">
                                <label class="form-label font-bold text-sm mb-1">Kelompokkan Rekapitulasi Berdasarkan:</label>
                                <select class="form-select border-primary" name="group_by" id="modal-group-by">
                                    <option value="pekerjaan">Pekerjaan</option>
                                    <option value="golongan_darah">Golongan Darah</option>
                                    <option value="pendidikan">Tingkat Pendidikan</option>
                                    <option value="status_pernikahan">Status Pernikahan</option>
                                    <option value="jenis_kelamin">Jenis Kelamin</option>
                                    <option value="hubungan_keluarga">Hubungan Keluarga (Suami/Istri/Anak)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Filter Kriteria Spesifik --}}
                    <div class="card border-0 shadow-sm" style="border-radius: 10px;">
                        <div class="card-body p-3">
                            <label class="form-label font-bold text-sm text-primary mb-3 d-block">
                                <i class="bi bi-filter-square me-1"></i> Filter Kriteria Umat (Opsional / Bisa Dikombinasikan)
                            </label>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-xs font-bold text-muted mb-1">Wilayah / KUB</label>
                                    <select class="form-select form-select-sm" name="kub_id">
                                        <option value="">-- Semua KUB (Seluruh Paroki) --</option>
                                        @foreach ($kubs as $k)
                                            <option value="{{ $k->id }}">{{ $k->nama }} ({{ $k->wilayah->nama ?? '-' }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-xs font-bold text-muted mb-1">Pekerjaan</label>
                                    <select class="form-select form-select-sm" name="pekerjaan">
                                        <option value="semua">-- Semua Pekerjaan --</option>
                                        @if(!empty($pekerjaanList))
                                            @foreach ($pekerjaanList as $p)
                                                <option value="{{ $p }}">{{ $p }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-xs font-bold text-muted mb-1">Golongan Darah</label>
                                    <select class="form-select form-select-sm" name="golongan_darah">
                                        <option value="semua">-- Semua Gol. Darah --</option>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                        <option value="AB">AB</option>
                                        <option value="O">O</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-xs font-bold text-muted mb-1">Tingkat Pendidikan</label>
                                    <select class="form-select form-select-sm" name="pendidikan">
                                        <option value="semua">-- Semua Pendidikan --</option>
                                        <option value="Tidak Sekolah">Tidak/Belum Sekolah</option>
                                        <option value="SD">SD/Sederajat</option>
                                        <option value="SMP">SMP/Sederajat</option>
                                        <option value="SMA">SMA/SMK/Sederajat</option>
                                        <option value="D1/D2/D3">Diploma (D1-D3)</option>
                                        <option value="S1/D4">Sarjana (S1/D4)</option>
                                        <option value="S2">Magister (S2)</option>
                                        <option value="S3">Doktor (S3)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-xs font-bold text-muted mb-1">Status Pernikahan</label>
                                    <select class="form-select form-select-sm" name="status_pernikahan">
                                        <option value="semua">-- Semua Status --</option>
                                        <option value="Belum Kawin">Belum Kawin</option>
                                        <option value="Kawin">Kawin / Menikah</option>
                                        <option value="Cerai Hidup">Cerai Hidup</option>
                                        <option value="Cerai Mati">Cerai Mati</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-xs font-bold text-muted mb-1">Jenis Kelamin</label>
                                    <select class="form-select form-select-sm" name="jenis_kelamin">
                                        <option value="semua">-- Laki-laki & Perempuan --</option>
                                        <option value="Laki-laki">Laki-laki</option>
                                        <option value="Perempuan">Perempuan</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-white border-top p-3 d-flex justify-content-between">
                <button type="button" class="btn btn-light-secondary px-4 font-bold" data-bs-dismiss="modal">Tutup</button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary d-flex align-items-center gap-2 font-bold px-3" style="border-color: #435ebe; color: #435ebe;" onclick="submitModalFilterUmat('preview')">
                        <i class="bi bi-eye-fill"></i> Preview PDF
                    </button>
                    <button type="button" class="btn btn-primary d-flex align-items-center gap-2 font-bold px-3 shadow-sm" style="background-color: #435ebe; border-color: #435ebe;" onclick="submitModalFilterUmat('download')">
                        <i class="bi bi-file-earmark-pdf-fill"></i> Unduh PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        function submitReport(type, action) {
            // Jika memilih rekap kategori, arahkan ke modal
            if (type === 'umat') {
                const subReport = document.getElementById('sub-report-umat').value;
                if (subReport === 'rekap_kategori') {
                    openModalFilterUmat();
                    return;
                }
            }

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

        function toggleUmatFilters() {
            const subReport = document.getElementById('sub-report-umat').value;
            const btnContainer = document.getElementById('btn-filter-kategori-container');
            const kubContainer = document.getElementById('kub-umat-container');

            if (subReport === 'rekap_kategori') {
                if (btnContainer) btnContainer.style.display = 'block';
                if (kubContainer) kubContainer.style.display = 'none';
                openModalFilterUmat();
            } else {
                if (btnContainer) btnContainer.style.display = 'none';
                if (kubContainer) kubContainer.style.display = 'block';
            }
        }

        function openModalFilterUmat() {
            const modalEl = document.getElementById('modalFilterUmat');
            if (modalEl) {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        }

        function toggleGroupBySelect() {
            const outType = document.querySelector('input[name="output_type"]:checked')?.value;
            const container = document.getElementById('container-group-by');
            if (container) {
                container.style.display = (outType === 'summary') ? 'block' : 'none';
            }
        }

        function submitModalFilterUmat(action) {
            const form = document.getElementById('form-modal-filter-umat');
            const actionInput = document.getElementById('modal-action-umat');
            actionInput.value = action;

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
            toggleUmatFilters();
        });
    </script>
@endpush
