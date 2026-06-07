@extends('layouts.pastor')

@section('title', 'Laporan & Cetak PDF')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Pusat Laporan & Cetak PDF</h3>
                    <p class="text-subtitle text-muted">
                        Pilih kategori laporan, tentukan filter data, dan unduh berkas PDF resmi paroki.
                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('pastor.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Laporan PDF</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                {{-- Laporan Administrasi Sakramen --}}
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header bg-primary text-white py-3 d-flex align-items-center gap-2" style="background-color: #800020 !important;">
                            <i class="bi bi-droplet-half fs-4"></i>
                            <h5 class="card-title text-white mb-0">Laporan Sakramen</h5>
                        </div>
                        <div class="card-body py-4 d-flex flex-column justify-content-between">
                            <div>
                                <p class="text-muted small mb-4">
                                    Cetak daftar penerima sakramen paroki lengkap dengan info baptis, krisma, wali, maupun pernikahan.
                                </p>
                                <form id="form-sakramen" action="{{ route('pastor.laporan.sakramen') }}" method="GET" target="_blank">
                                    <input type="hidden" name="action" id="action-sakramen" value="preview">
                                    
                                    <div class="form-group mb-3">
                                        <label class="form-label font-bold text-sm">Jenis Sakramen</label>
                                        <select class="form-select" name="jenis">
                                            <option value="semua">Semua Sakramen</option>
                                            <option value="BAPTIS">Baptis</option>
                                            <option value="KOMUNI_PERTAMA">Komuni Pertama</option>
                                            <option value="KRISMA">Krisma</option>
                                            <option value="PERNIKAHAN">Pernikahan</option>
                                            <option value="MINYAK_SUCI">Minyak Suci</option>
                                        </select>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label font-bold text-sm">KUB Asal Umat</label>
                                        <select class="form-select" name="kub_id">
                                            <option value="">Semua KUB</option>
                                            @foreach ($kubs as $k)
                                                <option value="{{ $k->id }}">{{ $k->nama }} ({{ $k->wilayah->nama ?? '-' }})</option>
                                            @endforeach
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
                                <button type="button" class="btn btn-outline-primary d-flex align-items-center justify-content-center gap-2" style="border-color: #800020; color: #800020;" onclick="submitReport('sakramen', 'preview')">
                                    <i class="bi bi-eye-fill"></i> Preview PDF
                                </button>
                                <button type="button" class="btn btn-primary d-flex align-items-center justify-content-center gap-2" style="background-color: #800020; border-color: #800020;" onclick="submitReport('sakramen', 'download')">
                                    <i class="bi bi-file-earmark-pdf-fill"></i> Unduh PDF
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Laporan Demografi & Statistik Umat --}}
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header bg-primary text-white py-3 d-flex align-items-center gap-2" style="background-color: #800020 !important;">
                            <i class="bi bi-person-lines-fill fs-4"></i>
                            <h5 class="card-title text-white mb-0">Laporan Statistik Umat</h5>
                        </div>
                        <div class="card-body py-4 d-flex flex-column justify-content-between">
                            <div>
                                <p class="text-muted small mb-4">
                                    Cetak rangkuman jumlah umat, rasio jenis kelamin, jumlah KK, dan daftar lengkap data diri umat per KUB.
                                </p>
                                <form id="form-umat" action="{{ route('pastor.laporan.umat') }}" method="GET" target="_blank">
                                    <input type="hidden" name="action" id="action-umat" value="preview">

                                    <div class="form-group mb-3">
                                        <label class="form-label font-bold text-sm">Pilih KUB</label>
                                        <select class="form-select" name="kub_id">
                                            <option value="">Semua KUB (Seluruh Paroki)</option>
                                            @foreach ($kubs as $k)
                                                <option value="{{ $k->id }}">{{ $k->nama }} ({{ $k->wilayah->nama ?? '-' }})</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label font-bold text-sm">Jenis Kelamin</label>
                                        <select class="form-select" name="jenis_kelamin">
                                            <option value="semua">Semua</option>
                                            <option value="L">Laki-laki</option>
                                            <option value="P">Perempuan</option>
                                        </select>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label font-bold text-sm">Status Kehidupan</label>
                                        <select class="form-select" name="status_almarhum">
                                            <option value="0">Umat Hidup (Aktif)</option>
                                            <option value="1">Umat Meninggal Dunia (Almarhum)</option>
                                            <option value="semua">Semua (Hidup & Alm.)</option>
                                        </select>
                                    </div>
                                </form>
                            </div>

                            <div class="d-grid gap-2 mt-3">
                                <button type="button" class="btn btn-outline-primary d-flex align-items-center justify-content-center gap-2" style="border-color: #800020; color: #800020;" onclick="submitReport('umat', 'preview')">
                                    <i class="bi bi-eye-fill"></i> Preview PDF
                                </button>
                                <button type="button" class="btn btn-primary d-flex align-items-center justify-content-center gap-2" style="background-color: #800020; border-color: #800020;" onclick="submitReport('umat', 'download')">
                                    <i class="bi bi-file-earmark-pdf-fill"></i> Unduh PDF
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Laporan Mutasi & Dinamika Umat --}}
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header bg-primary text-white py-3 d-flex align-items-center gap-2" style="background-color: #800020 !important;">
                            <i class="bi bi-arrow-left-right fs-4"></i>
                            <h5 class="card-title text-white mb-0">Laporan Mutasi Umat</h5>
                        </div>
                        <div class="card-body py-4 d-flex flex-column justify-content-between">
                            <div>
                                <p class="text-muted small mb-4">
                                    Cetak log mutasi kepindahan umat, mutasi keluarga antar KUB, dan perubahan status agama resmi gereja.
                                </p>
                                <form id="form-mutasi" action="{{ route('pastor.laporan.mutasi') }}" method="GET" target="_blank">
                                    <input type="hidden" name="action" id="action-mutasi" value="preview">

                                    <div class="form-group mb-3">
                                        <label class="form-label font-bold text-sm">Jenis Mutasi</label>
                                        <select class="form-select" name="jenis">
                                            <option value="semua">Semua Jenis Mutasi</option>
                                            <option value="umat">Mutasi Umat</option>
                                            <option value="keluarga">Mutasi Keluarga</option>
                                            <option value="agama">Mutasi Agama</option>
                                        </select>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label font-bold text-sm">Status Mutasi</label>
                                        <select class="form-select" name="status">
                                            <option value="semua">Semua Status</option>
                                            <option value="pending">Pending</option>
                                            <option value="disetujui">Disetujui</option>
                                            <option value="ditolak">Ditolak</option>
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
                                <button type="button" class="btn btn-outline-primary d-flex align-items-center justify-content-center gap-2" style="border-color: #800020; color: #800020;" onclick="submitReport('mutasi', 'preview')">
                                    <i class="bi bi-eye-fill"></i> Preview PDF
                                </button>
                                <button type="button" class="btn btn-primary d-flex align-items-center justify-content-center gap-2" style="background-color: #800020; border-color: #800020;" onclick="submitReport('mutasi', 'download')">
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

@push('scripts')
<script>
    function submitReport(type, action) {
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
</script>
@endpush
