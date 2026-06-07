@extends('layouts.dewan_pastoral')

@section('title', 'Edit Anggota DPP')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Edit Anggota DPP</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dewan_pastoral.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('dewan_pastoral.keanggotaan.index') }}">Keanggotaan DPP</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Edit Anggota DPP</h4>
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

                        <form method="POST" action="{{ route('dewan_pastoral.keanggotaan.update', $keanggotaan) }}">
                            @csrf
                            @method('PUT')
                            <div class="row g-3">

                                <div class="col-12"><small class="fw-bold text-muted text-uppercase">Data Umat</small>
                                    <hr class="my-1">
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="id_umat">Umat <span class="text-danger">*</span></label>
                                        <select class="form-select @error('id_umat') is-invalid @enderror" id="id_umat"
                                            name="id_umat">
                                            @foreach ($umatList as $u)
                                                <option value="{{ $u->id }}"
                                                    {{ old('id_umat', $keanggotaan->id_umat) == $u->id ? 'selected' : '' }}>
                                                    {{ $u->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_umat')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12"><small class="fw-bold text-muted text-uppercase">Data Kepengurusan</small>
                                    <hr class="my-1">
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="jabatan">Jabatan <span class="text-danger">*</span></label>
                                        <select class="form-select @error('jabatan') is-invalid @enderror" id="jabatan"
                                            name="jabatan">
                                            @foreach ($listJabatan as $j)
                                                <option value="{{ $j }}"
                                                    {{ old('jabatan', $keanggotaan->jabatan) === $j ? 'selected' : '' }}>
                                                    {{ $j }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('jabatan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="bidang_tugas">Bidang Tugas</label>
                                        <input type="text"
                                            class="form-control @error('bidang_tugas') is-invalid @enderror"
                                            id="bidang_tugas" name="bidang_tugas"
                                            value="{{ old('bidang_tugas', $keanggotaan->bidang_tugas) }}"
                                            placeholder="Opsional, contoh: Liturgi, Sosial">
                                        @error('bidang_tugas')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="status_aktif">Status <span class="text-danger">*</span></label>
                                        <select class="form-select @error('status_aktif') is-invalid @enderror"
                                            id="status_aktif" name="status_aktif">
                                            @foreach ($listStatus as $s)
                                                <option value="{{ $s }}"
                                                    {{ old('status_aktif', $keanggotaan->status_aktif) === $s ? 'selected' : '' }}>
                                                    {{ $s }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('status_aktif')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 d-flex justify-content-end mt-4">
                                    <a href="{{ route('dewan_pastoral.keanggotaan.index') }}"
                                        class="btn btn-light-secondary me-1 mb-1">Batal</a>
                                    <button type="submit" class="btn btn-warning me-1 mb-1">Simpan</button>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
