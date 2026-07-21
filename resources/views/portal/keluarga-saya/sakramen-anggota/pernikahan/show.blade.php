@extends('layouts.portal')
@section('title', 'Pernikahan — ' . $anggota->nama)
@section('content')
<div class="page-heading">
    <div class="page-title mb-3">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><i class="bi bi-heart-fill text-danger me-2"></i>Pernikahan — {{ $anggota->nama }}</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('portal.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('portal.keluarga-saya.show') }}">Keluarga Saya</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('portal.sakramen-anggota.index', $anggota) }}">Sakramen {{ $anggota->nama }}</a></li>
                        <li class="breadcrumb-item active">Pernikahan</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section">
        @if (session('success'))
            <div class="alert alert-light-success color-success alert-dismissible fade show">
                {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-light-danger color-danger alert-dismissible fade show">
                {{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($sakramen && $pernikahan)
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Data Pernikahan</h5>
                    <a href="{{ route('portal.sakramen-anggota.pernikahan.edit', $anggota) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-pencil-square me-1"></i>Edit
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr><td class="text-muted fw-semibold" style="width:40%">Tanggal Penerimaan</td><td>{{ $sakramen->tanggal_penerimaan?->format('d M Y') }}</td></tr>
                        <tr><td class="text-muted fw-semibold">Jenis Pernikahan</td><td>{{ \App\Models\Pernikahan::JENIS[$pernikahan->jenis_pernikahan] ?? $pernikahan->jenis_pernikahan }}</td></tr>
                        <tr>
                            <td class="text-muted fw-semibold">Pasangan</td>
                            <td>
                                @if ($pernikahan->pasangan_id)
                                    {{ $pernikahan->pasangan?->nama ?? '-' }}
                                    <span class="badge bg-light-info text-info ms-1">Umat</span>
                                @else
                                    {{ $pernikahan->pasangan_nama ?? '-' }}
                                    @if ($pernikahan->pasangan_agama)
                                        <small class="text-muted">({{ $pernikahan->pasangan_agama }})</small>
                                    @endif
                                @endif
                            </td>
                        </tr>
                        <tr><td class="text-muted fw-semibold">Tanggal Nikah Gereja</td><td>{{ $pernikahan->tanggal_nikah_katolik?->format('d M Y') ?? '-' }}</td></tr>
                        <tr><td class="text-muted fw-semibold">Tanggal Catatan Sipil</td><td>{{ $pernikahan->tanggal_catatan_sipil?->format('d M Y') ?? '-' }}</td></tr>
                        <tr><td class="text-muted fw-semibold">Izin Beda Gereja</td><td>{{ $pernikahan->izin_beda_gereja ? 'Ya' : 'Tidak' }}</td></tr>
                        <tr><td class="text-muted fw-semibold">Dispensasi</td><td>{{ $pernikahan->dispensasi ? 'Ya' : 'Tidak' }}</td></tr>
                    </table>
                </div>
            </div>
        @else
            <div class="alert alert-light-info color-info mb-3">
                <i class="bi bi-info-circle-fill me-2"></i>Data Pernikahan <strong>{{ $anggota->nama }}</strong> belum tercatat.
            </div>
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Input Data Pernikahan</h5></div>
                <div class="card-body">
                    @include('portal.keluarga-saya.sakramen-anggota.pernikahan._form', [
                        'action' => route('portal.sakramen-anggota.pernikahan.store', $anggota),
                        'method' => 'POST',
                        'backRoute' => route('portal.sakramen-anggota.index', $anggota),
                    ])
                </div>
            </div>
        @endif

        <div class="mt-3">
            <a href="{{ route('portal.sakramen-anggota.index', $anggota) }}" class="btn btn-light-secondary">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </section>
</div>
@endsection
