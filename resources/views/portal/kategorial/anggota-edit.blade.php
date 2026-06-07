@extends('layouts.portal')

@section('title', 'Edit Anggota')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Edit Anggota</h3>
                    <p class="text-subtitle text-muted">{{ $anggota->umat?->nama }}</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('portal.kategorial.index') }}">Kategorial Saya</a></li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('portal.kategorial.show', $kategorial) }}">{{ $kategorial->nama }}</a>
                            </li>
                            <li class="breadcrumb-item active">Edit Anggota</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-12 col-lg-6">
                    <div class="card">
                        <div class="card-header"><h5 class="card-title">Ubah Data Keanggotaan</h5></div>
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-light-danger color-danger">
                                    <ul class="mb-0 ps-3">
                                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nama Umat</label>
                                <input type="text" class="form-control" value="{{ $anggota->umat?->nama }}" readonly>
                            </div>

                            <form action="{{ route('portal.kategorial.anggota.update', [$kategorial, $anggota]) }}"
                                method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Jabatan <span class="text-danger">*</span></label>
                                    <select name="jabatan"
                                        class="form-select @error('jabatan') is-invalid @enderror" required>
                                        @foreach (['Ketua', 'Wakil Ketua', 'Sekretaris', 'Bendahara', 'Anggota'] as $jab)
                                            <option value="{{ $jab }}"
                                                {{ old('jabatan', $anggota->jabatan) === $jab ? 'selected' : '' }}>
                                                {{ $jab }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('jabatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Bidang Tugas</label>
                                    <input type="text" name="bidang_tugas" class="form-control"
                                        value="{{ old('bidang_tugas', $anggota->bidang_tugas) }}"
                                        placeholder="Opsional">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Tanggal Bergabung <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_bergabung"
                                        class="form-control @error('tanggal_bergabung') is-invalid @enderror"
                                        value="{{ old('tanggal_bergabung', \Carbon\Carbon::parse($anggota->tanggal_bergabung)->format('Y-m-d')) }}"
                                        required>
                                    @error('tanggal_bergabung') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                    <select name="status"
                                        class="form-select @error('status') is-invalid @enderror" required>
                                        <option value="Aktif" {{ old('status', $anggota->status) === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                        <option value="Tidak Aktif" {{ old('status', $anggota->status) === 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                    </select>
                                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-1"></i> Simpan
                                    </button>
                                    <a href="{{ route('portal.kategorial.show', $kategorial) }}" class="btn btn-secondary">
                                        Batal
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
