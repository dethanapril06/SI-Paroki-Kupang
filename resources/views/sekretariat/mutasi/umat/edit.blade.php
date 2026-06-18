@extends('layouts.sekretariat')

@section('title', 'Edit Mutasi Umat')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Edit Mutasi Umat</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sekretariat.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sekretariat.mutasi.index') }}">Mutasi</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sekretariat.mutasi.umat.index') }}">Mutasi
                                    Umat</a></li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('sekretariat.mutasi.umat.show', $mutasiUmat) }}">Detail</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Edit Mutasi Umat</h4>
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
                            action="{{ route('sekretariat.mutasi.umat.update', $mutasiUmat) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-body">
                                <div class="row g-3">

                                    {{-- Pilih Umat --}}
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="umat_id">Nama Umat <span class="text-danger">*</span></label>
                                            <select class="form-select @error('umat_id') is-invalid @enderror"
                                                id="umat_id" name="umat_id" onchange="onUmatChange()">
                                                <option value="">-- Pilih Umat --</option>
                                                @foreach ($umatList as $umat)
                                                    <option value="{{ $umat->id }}"
                                                        {{ old('umat_id', $mutasiUmat->umat_id) == $umat->id ? 'selected' : '' }}>
                                                        {{ $umat->nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('umat_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Info Asal --}}
                                    <div class="col-12" id="info-asal">
                                        <div class="alert alert-light-primary color-primary py-2 mb-0">
                                            <small class="fw-bold text-muted d-block mb-1">📍 Posisi Umat Saat Ini
                                                (Asal):</small>
                                            <div class="row g-2 small">
                                                <div class="col-6 col-md-3"><span class="text-muted">KUB:</span> <strong
                                                        id="asal-kub">{{ $mutasiUmat->kubAsal->nama ?? '-' }}</strong>
                                                </div>
                                                <div class="col-6 col-md-3"><span class="text-muted">Wilayah:</span> <strong
                                                        id="asal-wilayah">{{ $mutasiUmat->wilayahAsal->nama ?? '-' }}</strong>
                                                </div>
                                                <div class="col-6 col-md-3"><span class="text-muted">Paroki:</span> <strong
                                                        id="asal-paroki">{{ $mutasiUmat->parokiAsal->nama ?? '-' }}</strong>
                                                </div>
                                                <div class="col-6 col-md-3"><span class="text-muted">Keuskupan:</span>
                                                    <strong
                                                        id="asal-keuskupan">{{ $mutasiUmat->keuskupanAsal->nama ?? '-' }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Jenis & Tanggal --}}
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="sub_jenis_text">Jenis Pindah</label>
                                            <input type="text" class="form-control" id="sub_jenis_text" value="Pindah ke Keluarga yang Ada" readonly disabled>
                                            <input type="hidden" name="sub_jenis" id="sub_jenis" value="pindah_keluarga_ada">
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="tanggal">Tanggal Mutasi <span class="text-danger">*</span></label>
                                            <input type="date"
                                                class="form-control @error('tanggal') is-invalid @enderror" id="tanggal"
                                                name="tanggal"
                                                value="{{ old('tanggal', $mutasiUmat->mutasi->tanggal->format('Y-m-d')) }}">
                                            @error('tanggal')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="nomor_surat">Nomor Surat</label>
                                            <input type="text"
                                                class="form-control @error('nomor_surat') is-invalid @enderror"
                                                id="nomor_surat" name="nomor_surat"
                                                value="{{ old('nomor_surat', $mutasiUmat->nomor_surat) }}"
                                                placeholder="Opsional">
                                            @error('nomor_surat')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Pindah Keluarga --}}
                                    <div class="col-12" id="wrap-pindah-keluarga">
                                        <div class="form-group">
                                            <label for="keluarga_tujuan_id">Keluarga Tujuan <span class="text-danger">*</span></label>
                                            <select class="form-select @error('keluarga_tujuan_id') is-invalid @enderror"
                                                id="keluarga_tujuan_id" name="keluarga_tujuan_id">
                                                <option value="">-- Pilih --</option>
                                                @foreach ($keluargaList as $k)
                                                    <option value="{{ $k->id }}"
                                                        {{ old('keluarga_tujuan_id', $mutasiUmat->keluarga_tujuan_id) == $k->id ? 'selected' : '' }}>
                                                        {{ $k->kepalaKeluarga->nama ?? '(-)' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('keluarga_tujuan_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Keterangan --}}
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="keterangan">Keterangan</label>
                                            <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan"
                                                rows="3">{{ old('keterangan', $mutasiUmat->mutasi->keterangan) }}</textarea>
                                            @error('keterangan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12 d-flex justify-content-end">
                                        <a href="{{ route('sekretariat.mutasi.umat.show', $mutasiUmat) }}"
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

    @push('scripts')
        <script>
            const umatHierarchy = @json($umatHierarchy);

            function onUmatChange() {
                const id = document.getElementById('umat_id').value;
                if (!id || !umatHierarchy[id]) return;
                const h = umatHierarchy[id];
                document.getElementById('asal-kub').textContent = h.kub_nama || '-';
                document.getElementById('asal-wilayah').textContent = h.wilayah_nama || '-';
                document.getElementById('asal-paroki').textContent = h.paroki_nama || '-';
                document.getElementById('asal-keuskupan').textContent = h.keuskupan_nama || '-';
            }

             document.addEventListener('DOMContentLoaded', function() {
                onUmatChange();
            });
        </script>
    @endpush
@endsection
