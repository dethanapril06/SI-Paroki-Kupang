@extends('layouts.portal')

@section('title', 'Edit Keluarga –')

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
                            <li class="breadcrumb-item"><a href="{{ route('portal.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('portal.keluarga.show', $keluarga->id) }}">Detail</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('portal.keluarga.update', $keluarga->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            {{-- Kepala Keluarga --}}
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="kepala_keluarga_id">Kepala Keluarga</label>
                                    <select name="kepala_keluarga_id" id="kepala_keluarga_id"
                                        class="form-select @error('kepala_keluarga_id') is-invalid @enderror">
                                        <option value="">-- Pilih Kepala Keluarga --</option>
                                        @foreach ($keluarga->umat as $u)
                                            <option value="{{ $u->id }}"
                                                {{ old('kepala_keluarga_id', $keluarga->kepala_keluarga_id) == $u->id ? 'selected' : '' }}>
                                                {{ $u->nama }} ({{ $u->hubungan_keluarga }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('kepala_keluarga_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Status Tempat Tinggal --}}
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="status_tempat_tinggal">Status Tempat Tinggal</label>
                                    <select name="status_tempat_tinggal" id="status_tempat_tinggal"
                                        class="form-select @error('status_tempat_tinggal') is-invalid @enderror">
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

                            {{-- Alamat --}}
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="alamat">Alamat Lengkap</label>
                                    <textarea name="alamat" id="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3">{{ old('alamat', $keluarga->alamat) }}</textarea>
                                    @error('alamat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 d-flex justify-content-end mt-3">
                                <a href="{{ route('portal.keluarga.show', $keluarga->id) }}"
                                    class="btn btn-light-secondary me-2">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
