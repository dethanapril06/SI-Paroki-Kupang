@extends('layouts.portal')
@section('title', 'Krisma Saya')
@section('content')
<div class="page-heading">
    <div class="page-title mb-3">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><i class="bi bi-fire text-warning me-2"></i>Sakramen Krisma</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('portal.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('portal.sakramen-saya.index') }}">Sakramen Saya</a></li>
                        <li class="breadcrumb-item active">Krisma</li>
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

        @if ($sakramen && $krisma)
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Data Krisma</h5>
                    <a href="{{ route('portal.sakramen-saya.krisma.edit') }}" class="btn btn-sm btn-outline-warning">
                        <i class="bi bi-pencil-square me-1"></i>Edit
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr><td class="text-muted fw-semibold" style="width:40%">Tanggal Penerimaan</td><td>{{ $sakramen->tanggal_penerimaan?->format('d M Y') }}</td></tr>
                        <tr><td class="text-muted fw-semibold">Nama Krisma</td><td>{{ $krisma->nama_krisma ?? '-' }}</td></tr>
                        <tr><td class="text-muted fw-semibold">Uskup Pemberi</td><td>{{ $sakramen->klerus?->nama ?? '-' }}</td></tr>
                    </table>
                </div>
            </div>
        @else
            <div class="alert alert-light-info color-info mb-3">
                <i class="bi bi-info-circle-fill me-2"></i>Data Krisma Anda belum tercatat.
            </div>
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Input Data Krisma</h5></div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-light-danger color-danger mb-3"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
                    @endif
                    <form action="{{ route('portal.sakramen-saya.krisma.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Penerimaan <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_penerimaan" class="form-control @error('tanggal_penerimaan') is-invalid @enderror" value="{{ old('tanggal_penerimaan') }}">
                                @error('tanggal_penerimaan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Krisma (Firman)</label>
                                <input type="text" name="nama_krisma" class="form-control" value="{{ old('nama_krisma') }}" placeholder="Nama santo/santa pilihan">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Uskup Pemberi Krisma</label>
                                <select name="uskup_id" class="form-select">
                                    <option value="">-- Pilih (opsional) --</option>
                                    @foreach ($uskupList as $u)
                                        <option value="{{ $u->id }}" {{ old('uskup_id') == $u->id ? 'selected' : '' }}>{{ $u->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="d-flex gap-2 justify-content-end mt-4">
                            <a href="{{ route('portal.sakramen-saya.index') }}" class="btn btn-light-secondary">Batal</a>
                            <button type="submit" class="btn btn-warning text-dark"><i class="bi bi-save me-1"></i>Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </section>
</div>
@endsection
