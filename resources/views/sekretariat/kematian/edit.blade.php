@extends('layouts.sekretariat')

@section('title', 'Edit Data Kematian')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Edit Data Kematian</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sekretariat.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sekretariat.kematian.index') }}">Kematian</a>
                            </li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('sekretariat.kematian.show', $kematian) }}">Detail</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Edit Kematian</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-light-danger color-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $e)
                                        <li>{{ $e }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('sekretariat.kematian.update', $kematian) }}">
                            @csrf
                            @method('PUT')
                            <div class="row g-3">

                                <div class="col-12"><small class="fw-bold text-muted">Data Umat</small>
                                    <hr class="my-1">
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="umat_id">Umat <span class="text-danger">*</span></label>
                                        <select class="form-select @error('umat_id') is-invalid @enderror" id="umat_id"
                                            name="umat_id">
                                            <option value="">-- Pilih Umat --</option>
                                            @foreach ($umat as $u)
                                                <option value="{{ $u->id }}"
                                                    {{ old('umat_id', $kematian->umat_id) == $u->id ? 'selected' : '' }}>
                                                    {{ $u->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('umat_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12"><small class="fw-bold text-muted">Detail Kematian</small>
                                    <hr class="my-1">
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="tanggal_meninggal">Tanggal Meninggal <span
                                                class="text-danger">*</span></label>
                                        <input type="date"
                                            class="form-control @error('tanggal_meninggal') is-invalid @enderror"
                                            id="tanggal_meninggal" name="tanggal_meninggal"
                                            value="{{ old('tanggal_meninggal', $kematian->tanggal_meninggal->format('Y-m-d')) }}">
                                        @error('tanggal_meninggal')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="tempat_meninggal">Tempat Meninggal <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('tempat_meninggal') is-invalid @enderror"
                                            id="tempat_meninggal" name="tempat_meninggal"
                                            value="{{ old('tempat_meninggal', $kematian->tempat_meninggal) }}">
                                        @error('tempat_meninggal')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12"><small class="fw-bold text-muted">Detail Pemakaman</small>
                                    <hr class="my-1">
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="tanggal_pemakaman">Tanggal Pemakaman</label>
                                        <input type="date"
                                            class="form-control @error('tanggal_pemakaman') is-invalid @enderror"
                                            id="tanggal_pemakaman" name="tanggal_pemakaman"
                                            value="{{ old('tanggal_pemakaman', $kematian->tanggal_pemakaman?->format('Y-m-d')) }}">
                                        @error('tanggal_pemakaman')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="tempat_pemakaman">Tempat Pemakaman</label>
                                        <input type="text"
                                            class="form-control @error('tempat_pemakaman') is-invalid @enderror"
                                            id="tempat_pemakaman" name="tempat_pemakaman"
                                            value="{{ old('tempat_pemakaman', $kematian->tempat_pemakaman) }}">
                                        @error('tempat_pemakaman')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="keterangan">Keterangan</label>
                                        <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan"
                                            rows="3">{{ old('keterangan', $kematian->keterangan) }}</textarea>
                                        @error('keterangan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 d-flex justify-content-end">
                                    <a href="{{ route('sekretariat.kematian.show', $kematian) }}"
                                        class="btn btn-light-secondary me-1 mb-1">Batal</a>
                                    <button type="submit" class="btn btn-primary me-1 mb-1">Simpan Perubahan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
