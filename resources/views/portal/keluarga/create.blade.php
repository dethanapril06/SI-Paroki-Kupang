@extends('layouts.portal')

@section('title', 'Tambah Keluarga –')

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
                            <li class="breadcrumb-item"><a href="{{ route('portal.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('portal.keluarga.index') }}">Daftar
                                    Keluarga</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('portal.keluarga.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="alamat">Alamat Lengkap Keluarga</label>
                                    <textarea name="alamat" id="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3">{{ old('alamat') }}</textarea>
                                    @error('alamat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="status_tempat_tinggal">Status Tempat Tinggal</label>
                                    <select name="status_tempat_tinggal"
                                        class="form-select @error('status_tempat_tinggal') is-invalid @enderror">
                                        <option value="">-- Pilih Status --</option>
                                        @foreach (['Rumah Pribadi', 'Kontrak/Kost', 'Dinas'] as $status)
                                            <option value="{{ $status }}"
                                                {{ old('status_tempat_tinggal') == $status ? 'selected' : '' }}>
                                                {{ $status }}</option>
                                        @endforeach
                                    </select>
                                    @error('status_tempat_tinggal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 d-flex justify-content-end">
                                <a href="{{ route('portal.keluarga.index') }}"
                                    class="btn btn-light-secondary me-2">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan & Lanjut Tambah Anggota</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
