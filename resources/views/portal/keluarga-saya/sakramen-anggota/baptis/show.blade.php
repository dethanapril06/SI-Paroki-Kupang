@extends('layouts.portal')
@section('title', 'Baptis ' . $anggota->nama)
@section('content')
<div class="page-heading">
    <div class="page-title mb-3">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><i class="bi bi-droplet-fill text-info me-2"></i>Sakramen Baptis — {{ $anggota->nama }}</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('portal.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('portal.keluarga-saya.show') }}">Keluarga Saya</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('portal.sakramen-anggota.index', $anggota) }}">Sakramen {{ $anggota->nama }}</a></li>
                        <li class="breadcrumb-item active">Baptis</li>
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

        @if ($sakramen && $baptis)
            {{-- DATA SUDAH ADA --}}
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Data Baptis</h5>
                    <a href="{{ route('portal.sakramen-anggota.baptis.edit', $anggota) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-pencil-square me-1"></i>Edit
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr><td class="text-muted fw-semibold" style="width:40%">Tanggal Penerimaan</td><td>{{ $sakramen->tanggal_penerimaan?->format('d M Y') }}</td></tr>
                        <tr><td class="text-muted fw-semibold">Paroki</td><td>{{ $sakramen->paroki->nama ?? '-' }}</td></tr>
                        <tr><td class="text-muted fw-semibold">Nomor Surat</td><td>{{ $sakramen->nomor_surat ?? '-' }}</td></tr>
                        <tr><td class="text-muted fw-semibold">Sumber Baptis</td><td>{{ $baptis->sumber_baptis }}</td></tr>
                        <tr><td class="text-muted fw-semibold">Nama Baptis</td><td>{{ $baptis->nama_baptis ?? '-' }}</td></tr>
                        <tr><td class="text-muted fw-semibold">Tanggal Baptis</td><td>{{ $baptis->tgl_baptis?->format('d M Y') ?? '-' }}</td></tr>
                        @if ($baptis->sumber_baptis === 'PROTESTAN')
                            <tr><td class="text-muted fw-semibold">Tanggal Diterima Katolik</td><td>{{ $baptis->tgl_diterima_katolik?->format('d M Y') ?? '-' }}</td></tr>
                        @endif
                        <tr>
                            <td class="text-muted fw-semibold">Pemberi Baptis</td>
                            <td>
                                @if ($sakramen->klerus)
                                    <span class="badge bg-light-info text-info me-1">Klerus</span>{{ $sakramen->klerus->nama }}
                                @elseif ($baptis->nama_pemberi_protestan)
                                    {{ $baptis->nama_pemberi_protestan }}
                                    @if ($baptis->nama_gereja_protestan)
                                        <small class="text-muted">({{ $baptis->nama_gereja_protestan }})</small>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr><td class="text-muted fw-semibold">Bapak Baptis</td><td>{{ $baptis->bapakBaptis->nama ?? $baptis->bapak_baptis_nama ?? '-' }}</td></tr>
                        <tr><td class="text-muted fw-semibold">Ibu Baptis</td><td>{{ $baptis->ibuBaptis->nama ?? $baptis->ibu_baptis_nama ?? '-' }}</td></tr>
                    </table>
                </div>
            </div>
        @else
            {{-- BELUM ADA DATA — FORM TAMBAH --}}
            <div class="alert alert-light-info color-info mb-3">
                <i class="bi bi-info-circle-fill me-2"></i>Data Baptis <strong>{{ $anggota->nama }}</strong> belum tercatat. Silakan isi formulir berikut.
            </div>
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Input Data Baptis</h5></div>
                <div class="card-body">
                    @include('portal.keluarga-saya.sakramen-anggota.baptis._form', [
                        'action' => route('portal.sakramen-anggota.baptis.store', $anggota),
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
