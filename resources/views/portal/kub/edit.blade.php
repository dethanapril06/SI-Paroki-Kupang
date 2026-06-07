@extends('layouts.portal') {{-- Pastikan menggunakan layout ketua_kub --}}

@section('title', 'Edit Nama KUB –')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Edit Nama KUB</h3>
                    <p class="text-subtitle text-muted">Anda hanya memiliki akses untuk mengubah nama KUB.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('portal.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('portal.kub.show') }}">Profil KUB</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Nama</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Perubahan Nama</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <form action="{{ route('portal.kub.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="nama">Nama KUB</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="text" id="nama"
                                            class="form-control @error('nama') is-invalid @enderror" name="nama"
                                            value="{{ old('nama', $kub->nama) }}" placeholder="Masukkan Nama KUB Baru">
                                        @error('nama')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Info tambahan (Read-only) agar Ketua KUB tahu wilayahnya tidak bisa diubah di sini --}}
                                    <div class="col-md-4">
                                        <label>Wilayah</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <input type="text" class="form-control" value="{{ $kub->wilayah->nama ?? '-' }}"
                                            disabled>
                                        <small class="text-muted">*Perubahan wilayah hanya dapat dilakukan oleh
                                            Sekretariat.</small>
                                    </div>

                                    <div class="col-12 d-flex justify-content-end mt-3">
                                        <a href="{{ route('portal.kub.show') }}"
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
