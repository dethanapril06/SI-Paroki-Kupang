@extends('layouts.sekretariat')

@section('title', 'Tambah Keluarga')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Tambah Keluarga</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('sekretariat.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('sekretariat.keluarga.index') }}">Daftar Keluarga</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Tambah Keluarga</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Tambah Keluarga</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">

                        <div class="alert alert-light-info color-info mb-4">
                            <i class="bi bi-info-circle me-1"></i>
                            <strong>Catatan:</strong> Kepala keluarga dapat diatur setelah anggota umat ditambahkan ke keluarga ini.
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

                        <form class="form form-vertical" method="POST" action="{{ route('sekretariat.keluarga.store') }}">
                            @csrf
                            <div class="form-body">
                                <div class="row">

                                    {{-- KUB --}}
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="kub_id">KUB <span class="text-danger">*</span></label>
                                            <select class="form-select @error('kub_id') is-invalid @enderror"
                                                id="kub_id" name="kub_id">
                                                <option value="">-- Pilih KUB --</option>
                                                @foreach ($kub as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ old('kub_id') == $item->id ? 'selected' : '' }}>
                                                        {{ $item->nama }}
                                                        @if ($item->wilayah)
                                                            (Wilayah: {{ $item->wilayah->nama }})
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('kub_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Alamat --}}
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="alamat">Alamat <span class="text-danger">*</span></label>
                                            <textarea class="form-control @error('alamat') is-invalid @enderror"
                                                id="alamat" name="alamat" rows="3"
                                                placeholder="Masukkan alamat lengkap keluarga">{{ old('alamat') }}</textarea>
                                            @error('alamat')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Status Tempat Tinggal --}}
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="status_tempat_tinggal">Status Tempat Tinggal <span class="text-danger">*</span></label>
                                            <select class="form-select @error('status_tempat_tinggal') is-invalid @enderror"
                                                id="status_tempat_tinggal" name="status_tempat_tinggal">
                                                <option value="">-- Pilih Status --</option>
                                                @foreach (['Rumah Pribadi', 'Kontrak/Kost', 'Dinas'] as $status)
                                                    <option value="{{ $status }}"
                                                        {{ old('status_tempat_tinggal') === $status ? 'selected' : '' }}>
                                                        {{ $status }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('status_tempat_tinggal')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Tombol --}}
                                    <div class="col-12 d-flex justify-content-end">
                                        <a href="{{ route('sekretariat.keluarga.index') }}"
                                            class="btn btn-light-secondary me-1 mb-1">Batal</a>
                                        <button type="submit" class="btn btn-primary me-1 mb-1">
                                            Simpan & Lanjut Tambah Anggota
                                        </button>
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
