@extends('layouts.sekretariat')

@section('title', 'Tambah Klerus')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Tambah Klerus</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('sekretariat.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Form Tambah Klerus
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Tambah Klerus</h4>
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

                        <form class="form form-vertical" method="POST" action="{{ route('sekretariat.klerus.store') }}">
                            @csrf
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="nama">Nama Klerus</label>
                                            <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                                placeholder="Masukkan nama klerus" id="nama" name="nama"
                                                value="{{ old('nama') }}">
                                            @error('nama')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="jabatan">Jabatan</label>
                                            <select class="form-select @error('jabatan') is-invalid @enderror"
                                                id="jabatan" name="jabatan">
                                                <option value="">-- Pilih Jabatan --</option>
                                                <option value="pastor" {{ old('jabatan') == 'pastor' ? 'selected' : '' }}>
                                                    Pastor</option>
                                                <option value="uskup" {{ old('jabatan') == 'uskup' ? 'selected' : '' }}>
                                                    Uskup</option>
                                            </select>
                                            @error('jabatan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="status_aktif">Status</label>
                                            <select class="form-select @error('status_aktif') is-invalid @enderror"
                                                id="status_aktif" name="status_aktif">
                                                <option value="">-- Pilih Status --</option>
                                                <option value="Aktif"
                                                    {{ old('status_aktif') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                                <option value="Meninggal"
                                                    {{ old('status_aktif') == 'Meninggal' ? 'selected' : '' }}>Meninggal
                                                </option>
                                                <option value="Emeritus"
                                                    {{ old('status_aktif') == 'Emeritus' ? 'selected' : '' }}>Emeritus
                                                </option>
                                            </select>
                                            @error('status_aktif')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-end">
                                        <a href="{{ route('sekretariat.klerus.index') }}"
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
