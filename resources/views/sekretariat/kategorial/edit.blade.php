@extends('layouts.sekretariat')

@section('title', 'Edit Kategorial')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Edit Kategorial</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('sekretariat.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('sekretariat.kategorial.index') }}">Kategorial</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('sekretariat.kategorial.show', $kategorial) }}">{{ $kategorial->nama }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Edit</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Edit Kategorial</h4>
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
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('sekretariat.kategorial.update', $kategorial) }}">
                            @csrf
                            @method('PUT')
                            <div class="row">

                                {{-- Nama --}}
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="nama">Nama Kategorial <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                            id="nama" name="nama" placeholder="Nama kategorial"
                                            value="{{ old('nama', $kategorial->nama) }}">
                                        @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                {{-- Ketua (hanya tampil jika ada anggota) --}}
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="ketua_umat_id">Ketua Kategorial</label>
                                        @if ($anggota->isEmpty())
                                            <div class="alert alert-light-warning color-warning mb-0 py-2">
                                                <i class="bi bi-exclamation-triangle me-1"></i>
                                                Belum ada anggota terdaftar. Tambahkan anggota terlebih dahulu untuk menetapkan ketua.
                                            </div>
                                        @else
                                            <select class="form-select @error('ketua_umat_id') is-invalid @enderror"
                                                id="ketua_umat_id" name="ketua_umat_id">
                                                <option value="">-- Pilih Ketua --</option>
                                                @foreach ($anggota as $u)
                                                    <option value="{{ $u->id }}"
                                                        {{ old('ketua_umat_id', $kategorial->ketua_umat_id) == $u->id ? 'selected' : '' }}>
                                                        {{ $u->nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('ketua_umat_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            <small class="form-text text-muted">
                                                Memilih ketua akan mengubah role user menjadi <code>ketua_kategorial</code>.
                                            </small>
                                        @endif
                                    </div>
                                </div>

                                {{-- Pastor Moderator --}}
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="klerus_id">Pastor Moderator</label>
                                        <select class="form-select @error('klerus_id') is-invalid @enderror"
                                            id="klerus_id" name="klerus_id">
                                            <option value="">-- Pilih Pastor Moderator --</option>
                                            @foreach ($pastors as $pastor)
                                                <option value="{{ $pastor->id }}"
                                                    {{ old('klerus_id', $kategorial->klerus_id) == $pastor->id ? 'selected' : '' }}>
                                                    {{ $pastor->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('klerus_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="col-12 d-flex justify-content-end">
                                    <a href="{{ route('sekretariat.kategorial.show', $kategorial) }}"
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
