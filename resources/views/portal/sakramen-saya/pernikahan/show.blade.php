@extends('layouts.portal')
@section('title', 'Pernikahan Saya')
@section('content')
<div class="page-heading">
    <div class="page-title mb-3">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><i class="bi bi-heart-fill text-danger me-2"></i>Sakramen Pernikahan</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('portal.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('portal.sakramen-saya.index') }}">Sakramen Saya</a></li>
                        <li class="breadcrumb-item active">Pernikahan</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section">
        @if (session('success'))
            <div class="alert alert-light-success color-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if (session('error'))
            <div class="alert alert-light-danger color-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        @if ($sakramen && $pernikahan)
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Data Pernikahan</h5>
                    <a href="{{ route('portal.sakramen-saya.pernikahan.edit') }}" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-pencil-square me-1"></i>Edit
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr><td class="text-muted fw-semibold" style="width:45%">Tanggal Penerimaan</td><td>{{ $sakramen->tanggal_penerimaan?->format('d M Y') }}</td></tr>
                        <tr><td class="text-muted fw-semibold">Nama Pasangan</td><td>{{ $pernikahan->nama_pasangan ?? '-' }}</td></tr>
                        <tr><td class="text-muted fw-semibold">Agama Pasangan</td><td>{{ $pernikahan->agama_pasangan ?? '-' }}</td></tr>
                        <tr><td class="text-muted fw-semibold">Jenis Pernikahan</td><td>{{ \App\Models\Pernikahan::JENIS[$pernikahan->jenis_pernikahan] ?? '-' }}</td></tr>
                        <tr><td class="text-muted fw-semibold">Tgl Nikah Katolik</td><td>{{ $pernikahan->tanggal_nikah_katolik?->format('d M Y') ?? '-' }}</td></tr>
                        <tr><td class="text-muted fw-semibold">Tgl Catatan Sipil</td><td>{{ $pernikahan->tanggal_catatan_sipil?->format('d M Y') ?? '-' }}</td></tr>
                        <tr><td class="text-muted fw-semibold">Izin Beda Gereja</td><td>{{ $pernikahan->izin_beda_gereja ? 'Ya' : 'Tidak' }}</td></tr>
                        <tr><td class="text-muted fw-semibold">Dispensasi</td><td>{{ $pernikahan->dispensasi ? 'Ya' : 'Tidak' }}</td></tr>
                    </table>
                </div>
            </div>
        @else
            <div class="alert alert-light-info color-info mb-3">
                <i class="bi bi-info-circle-fill me-2"></i>Data Pernikahan Anda belum tercatat.
            </div>
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Input Data Pernikahan</h5></div>
                <div class="card-body">
                    @include('portal.sakramen-saya.pernikahan._form', ['action' => route('portal.sakramen-saya.pernikahan.store'), 'method' => 'POST'])
                </div>
            </div>
        @endif
    </section>
</div>
@endsection
