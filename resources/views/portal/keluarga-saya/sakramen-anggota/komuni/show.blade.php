@extends('layouts.portal')
@section('title', 'Komuni Pertama — ' . $anggota->nama)
@section('content')
<div class="page-heading">
    <div class="page-title mb-3">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><i class="bi bi-cup-hot-fill text-success me-2"></i>Komuni Pertama — {{ $anggota->nama }}</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('portal.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('portal.keluarga-saya.show') }}">Keluarga Saya</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('portal.sakramen-anggota.index', $anggota) }}">Sakramen {{ $anggota->nama }}</a></li>
                        <li class="breadcrumb-item active">Komuni Pertama</li>
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

        @if ($sakramen)
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Data Komuni Pertama</h5>
                    <a href="{{ route('portal.sakramen-anggota.komuni.edit', $anggota) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-pencil-square me-1"></i>Edit
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr><td class="text-muted fw-semibold" style="width:40%">Tanggal Penerimaan</td><td>{{ $sakramen->tanggal_penerimaan?->format('d M Y') }}</td></tr>
                        <tr><td class="text-muted fw-semibold">Pastor Pemberi</td><td>{{ $sakramen->klerus?->nama ?? '-' }}</td></tr>
                    </table>
                </div>
            </div>
        @else
            <div class="alert alert-light-info color-info mb-3">
                <i class="bi bi-info-circle-fill me-2"></i>Data Komuni Pertama <strong>{{ $anggota->nama }}</strong> belum tercatat.
            </div>
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Input Data Komuni Pertama</h5></div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-light-danger color-danger mb-3"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
                    @endif
                    <form action="{{ route('portal.sakramen-anggota.komuni.store', $anggota) }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Penerimaan <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_penerimaan" class="form-control @error('tanggal_penerimaan') is-invalid @enderror"
                                    value="{{ old('tanggal_penerimaan') }}">
                                @error('tanggal_penerimaan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pastor Pemberi</label>
                                <select name="klerus_id" class="form-select">
                                    <option value="">-- Pilih (opsional) --</option>
                                    @foreach ($klerusList as $k)
                                        <option value="{{ $k->id }}" {{ old('klerus_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }} ({{ ucfirst($k->jabatan) }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="d-flex gap-2 justify-content-end mt-4">
                            <a href="{{ route('portal.sakramen-anggota.index', $anggota) }}" class="btn btn-light-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                        </div>
                    </form>
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
