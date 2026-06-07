@extends('layouts.sekretariat')

@section('title', 'Edit Kub')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Edit Kub</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('sekretariat.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('sekretariat.kub.index') }}">Daftar Kub</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Form Edit Kub
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Edit Kub</h4>
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
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <form class="form form-vertical" method="POST"
                            action="{{ route('sekretariat.kub.update', $kub) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="nama">Nama Kub</label>
                                            <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                                id="nama" name="nama" placeholder="Masukkan nama kub"
                                                value="{{ old('nama', $kub->nama) }}">
                                            @error('nama')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="ketua_umat_id">Ketua KUB</label>
                                            @if ($umat->isEmpty())
                                                <div class="alert alert-light-warning color-warning mb-0 py-2">
                                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                                    Belum ada anggota umat dalam KUB ini. Tambahkan keluarga dan umat terlebih dahulu.
                                                </div>
                                            @else
                                                <select class="form-select @error('ketua_umat_id') is-invalid @enderror"
                                                    id="ketua_umat_id" name="ketua_umat_id">
                                                    <option value="">-- Pilih Ketua KUB --</option>
                                                    @foreach ($umat as $u)
                                                        <option value="{{ $u->id }}"
                                                            {{ old('ketua_umat_id', $kub->ketua_umat_id) == $u->id ? 'selected' : '' }}>
                                                            {{ $u->nama }} ({{ $u->hubungan_keluarga }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('ketua_umat_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="wilayah_id">Wilayah</label>
                                            <select class="form-select @error('wilayah_id') is-invalid @enderror"
                                                id="wilayah_id" name="wilayah_id">
                                                <option value="">-- Pilih Wilayah --</option>
                                                @foreach ($wilayah as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ old('wilayah_id', $kub->wilayah_id) == $item->id ? 'selected' : '' }}>
                                                        {{ $item->nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('wilayah_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12 d-flex justify-content-end">
                                        <a href="{{ route('sekretariat.kub.index') }}"
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
