@extends('layouts.sekretariat')

@section('title', 'Edit Keanggotaan')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Edit Keanggotaan</h3>
                    <p class="text-muted mb-0">
                        <strong>{{ $anggotaKategorial->umat->nama }}</strong> —
                        {{ $anggotaKategorial->kategorial->nama }}
                    </p>
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
                                <a href="{{ route('sekretariat.kategorial.show', $anggotaKategorial->kategorial) }}">
                                    {{ $anggotaKategorial->kategorial->nama }}
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Keanggotaan</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        Edit Keanggotaan: <strong>{{ $anggotaKategorial->umat->nama }}</strong>
                    </h4>
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

                        <form method="POST"
                            action="{{ route('sekretariat.anggota-kategorial.update', $anggotaKategorial) }}">
                            @csrf
                            @method('PUT')
                            <div class="row">

                                {{-- Umat (read-only) --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nama Umat</label>
                                        <input type="text" class="form-control" readonly
                                            value="{{ $anggotaKategorial->umat->nama }}">
                                    </div>
                                </div>

                                {{-- Jabatan --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="jabatan">Jabatan <span class="text-danger">*</span></label>
                                        <select class="form-select @error('jabatan') is-invalid @enderror" id="jabatan"
                                            name="jabatan">
                                            @foreach (['Anggota', 'Wakil Ketua', 'Sekretaris', 'Bendahara', 'Ketua'] as $jab)
                                                <option value="{{ $jab }}"
                                                    {{ old('jabatan', $anggotaKategorial->jabatan) === $jab ? 'selected' : '' }}>
                                                    {{ $jab }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('jabatan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Bidang Tugas --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bidang_tugas">Bidang Tugas</label>
                                        <input type="text"
                                            class="form-control @error('bidang_tugas') is-invalid @enderror"
                                            id="bidang_tugas" name="bidang_tugas"
                                            placeholder="Contoh: Doa, Sosial, Liturgi (opsional)"
                                            value="{{ old('bidang_tugas', $anggotaKategorial->bidang_tugas) }}">
                                        @error('bidang_tugas')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Tanggal Bergabung --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tanggal_bergabung">Tanggal Bergabung <span
                                                class="text-danger">*</span></label>
                                        <input type="date"
                                            class="form-control @error('tanggal_bergabung') is-invalid @enderror"
                                            id="tanggal_bergabung" name="tanggal_bergabung"
                                            value="{{ old('tanggal_bergabung', \Carbon\Carbon::parse($anggotaKategorial->tanggal_bergabung)->format('Y-m-d')) }}"
                                            required>
                                        @error('tanggal_bergabung')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Status --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Status <span class="text-danger">*</span></label>
                                        <select class="form-select @error('status') is-invalid @enderror" id="status"
                                            name="status">
                                            <option value="Aktif"
                                                {{ old('status', $anggotaKategorial->status) === 'Aktif' ? 'selected' : '' }}>
                                                Aktif
                                            </option>
                                            <option value="Tidak Aktif"
                                                {{ old('status', $anggotaKategorial->status) === 'Tidak Aktif' ? 'selected' : '' }}>
                                                Tidak Aktif
                                            </option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 d-flex justify-content-end">
                                    <a href="{{ route('sekretariat.kategorial.show', $anggotaKategorial->kategorial) }}"
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
