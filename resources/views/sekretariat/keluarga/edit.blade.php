@extends('layouts.sekretariat')

@section('title', 'Edit Keluarga')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Edit Keluarga</h3>
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
                            <li class="breadcrumb-item">
                                <a href="{{ route('sekretariat.keluarga.show', $keluarga) }}">Detail Keluarga</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Keluarga</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Edit Keluarga</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">

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
                            action="{{ route('sekretariat.keluarga.update', $keluarga) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-body">
                                <div class="row">

                                    {{-- KUB --}}
                                    <div class="col-12">
                                        <div class="alert alert-light-info color-info py-2 mb-2">
                                            <i class="bi bi-info-circle-fill me-2"></i>
                                            <strong>KUB tidak dapat diubah langsung di form ini.</strong> Untuk memindahkan keluarga ke KUB lain, gunakan menu <strong>Mutasi Keluarga</strong> agar riwayat perpindahan tercatat di sistem.
                                        </div>
                                        <div class="form-group">
                                            <label for="kub_id_disabled">KUB</label>
                                            <input type="hidden" name="kub_id" value="{{ $keluarga->kub_id }}">
                                            <select class="form-select bg-light" id="kub_id_disabled" disabled>
                                                @foreach ($kub as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ $keluarga->kub_id == $item->id ? 'selected' : '' }}>
                                                        {{ $item->nama }}
                                                        @if ($item->wilayah)
                                                            (Wilayah: {{ $item->wilayah->nama }})
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Alamat --}}
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="alamat">Alamat <span class="text-danger">*</span></label>
                                            <textarea class="form-control @error('alamat') is-invalid @enderror"
                                                id="alamat" name="alamat" rows="3"
                                                placeholder="Masukkan alamat lengkap keluarga">{{ old('alamat', $keluarga->alamat) }}</textarea>
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
                                                        {{ old('status_tempat_tinggal', $keluarga->status_tempat_tinggal) === $status ? 'selected' : '' }}>
                                                        {{ $status }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('status_tempat_tinggal')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Kepala Keluarga (hanya tampil jika ada anggota) --}}
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="kepala_keluarga_id">Kepala Keluarga</label>
                                            @if ($anggota->isEmpty())
                                                <div class="alert alert-light-warning color-warning mb-0 py-2">
                                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                                    Belum ada anggota umat dalam keluarga ini.
                                                    Tambahkan anggota terlebih dahulu untuk menetapkan kepala keluarga.
                                                </div>
                                            @else
                                                @php
                                                    $rekomendasiId = $keluarga->getRekomendasiKepalaKeluarga()?->id;
                                                    $selectedId = old('kepala_keluarga_id', $keluarga->kepala_keluarga_id ?? $rekomendasiId);
                                                @endphp
                                                <select class="form-select @error('kepala_keluarga_id') is-invalid @enderror"
                                                    id="kepala_keluarga_id" name="kepala_keluarga_id">
                                                    <option value="">-- Pilih Kepala Keluarga (Otomatis) --</option>
                                                    @foreach ($anggota as $umat)
                                                        <option value="{{ $umat->id }}"
                                                            {{ $selectedId == $umat->id ? 'selected' : '' }}>
                                                            [{{ $umat->hubungan_keluarga }}] {{ $umat->nama }}
                                                            @if ($rekomendasiId == $umat->id) (Prioritas Utama) @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <small class="text-muted d-block mt-1">
                                                    <i class="bi bi-info-circle me-1"></i>
                                                    Urutan prioritas: <strong>Suami -> Istri -> Anak -> Lainnya</strong>.
                                                </small>
                                                @error('kepala_keluarga_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Tombol --}}
                                    <div class="col-12 d-flex justify-content-end">
                                        <a href="{{ route('sekretariat.keluarga.show', $keluarga) }}"
                                            class="btn btn-light-secondary me-1 mb-1">Batal</a>
                                        <button type="submit" class="btn btn-primary me-1 mb-1">Simpan Perubahan</button>
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
