@extends('layouts.sekretariat')

@section('title', 'Tambah Mutasi Agama')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Tambah Mutasi Agama</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('sekretariat.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('sekretariat.mutasi.index') }}">Mutasi</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('sekretariat.mutasi.agama.index') }}">Mutasi Agama</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Tambah Mutasi Agama</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">

                        <div class="alert alert-light-info color-info mb-4">
                            <i class="bi bi-info-circle me-1"></i>
                            <strong>Catatan:</strong> Agama asal umat adalah <strong>Katolik</strong>. Form ini mencatat perpindahan agama dari Katolik ke agama lain.
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-light-danger color-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form class="form form-vertical" method="POST"
                            action="{{ route('sekretariat.mutasi.agama.store') }}">
                            @csrf
                            <div class="form-body">
                                <div class="row">

                                    {{-- Umat --}}
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="umat_id">Nama Umat <span class="text-danger">*</span></label>
                                            <select class="form-select @error('umat_id') is-invalid @enderror"
                                                id="umat_id" name="umat_id">
                                                <option value="">-- Pilih Umat --</option>
                                                @foreach ($umatList as $umat)
                                                    <option value="{{ $umat->id }}"
                                                        {{ old('umat_id') == $umat->id ? 'selected' : '' }}>
                                                        {{ $umat->nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('umat_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Agama Tujuan --}}
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="agama_tujuan">Agama Tujuan <span class="text-danger">*</span></label>
                                            <select class="form-select @error('agama_tujuan') is-invalid @enderror"
                                                id="agama_tujuan" name="agama_tujuan">
                                                <option value="">-- Pilih Agama Tujuan --</option>
                                                @foreach (['protestan' => 'Protestan', 'hindu' => 'Hindu', 'budha' => 'Buddha', 'khonghucu' => 'Khonghucu', 'islam' => 'Islam'] as $val => $label)
                                                    <option value="{{ $val }}"
                                                        {{ old('agama_tujuan') === $val ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('agama_tujuan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Tanggal --}}
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="tanggal">Tanggal Mutasi <span class="text-danger">*</span></label>
                                            <input type="date"
                                                class="form-control @error('tanggal') is-invalid @enderror"
                                                id="tanggal" name="tanggal" value="{{ old('tanggal') }}">
                                            @error('tanggal')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Keterangan --}}
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="keterangan">Keterangan</label>
                                            <textarea class="form-control @error('keterangan') is-invalid @enderror"
                                                id="keterangan" name="keterangan" rows="3"
                                                placeholder="Keterangan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                                            @error('keterangan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Tombol --}}
                                    <div class="col-12 d-flex justify-content-end">
                                        <a href="{{ route('sekretariat.mutasi.agama.index') }}"
                                            class="btn btn-light-secondary me-1 mb-1">Batal</a>
                                        <button type="submit" class="btn btn-primary me-1 mb-1">Simpan</button>
                                    </div>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
