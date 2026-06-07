@extends('layouts.portal')

@section('title', 'Edit Data Keluarga')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Edit Data Keluarga</h3>
                    <p class="text-muted">Perbarui informasi keluarga Anda.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('portal.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('portal.keluarga-saya.show') }}">Keluarga Saya</a>
                            </li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-house-fill me-2 text-success"></i>
                                Data Keluarga
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('portal.keluarga-saya.update') }}" method="POST">
                                @csrf
                                @method('PUT')

                                @if ($errors->any())
                                    <div class="alert alert-light-danger color-danger mb-3">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="row g-3">

                                    {{-- Alamat --}}
                                    <div class="col-12">
                                        <label for="alamat" class="form-label">
                                            Alamat <span class="text-danger">*</span>
                                        </label>
                                        <textarea name="alamat" id="alamat" rows="3"
                                            class="form-control @error('alamat') is-invalid @enderror"
                                            placeholder="Jl. ...">{{ old('alamat', $keluarga->alamat) }}</textarea>
                                        @error('alamat')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Status Tempat Tinggal --}}
                                    <div class="col-md-6">
                                        <label for="status_tempat_tinggal" class="form-label">
                                            Status Tempat Tinggal <span class="text-danger">*</span>
                                        </label>
                                        <select name="status_tempat_tinggal" id="status_tempat_tinggal"
                                            class="form-select @error('status_tempat_tinggal') is-invalid @enderror">
                                            @foreach (['Rumah Pribadi', 'Kontrak/Kost', 'Dinas'] as $s)
                                                <option value="{{ $s }}"
                                                    {{ old('status_tempat_tinggal', $keluarga->status_tempat_tinggal) === $s ? 'selected' : '' }}>
                                                    {{ $s }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('status_tempat_tinggal')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Kepala Keluarga --}}
                                    <div class="col-md-6">
                                        <label for="kepala_keluarga_id" class="form-label">
                                            Kepala Keluarga
                                        </label>
                                        <select name="kepala_keluarga_id" id="kepala_keluarga_id"
                                            class="form-select @error('kepala_keluarga_id') is-invalid @enderror">
                                            <option value="">-- Pilih --</option>
                                            @foreach ($anggota as $a)
                                                <option value="{{ $a->id }}"
                                                    {{ old('kepala_keluarga_id', $keluarga->kepala_keluarga_id) == $a->id ? 'selected' : '' }}>
                                                    {{ $a->nama }}
                                                    @if ($a->hubungan_keluarga)
                                                        ({{ $a->hubungan_keluarga }})
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('kepala_keluarga_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text text-muted">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Mengubah kepala keluarga akan memindahkan hak edit keluarga ke orang tersebut.
                                        </div>
                                    </div>

                                </div>

                                <div class="d-flex gap-2 justify-content-end mt-4">
                                    <a href="{{ route('portal.keluarga-saya.show') }}"
                                        class="btn btn-light-secondary">Batal</a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-save me-1"></i>Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
