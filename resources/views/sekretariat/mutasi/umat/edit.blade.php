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
                                            <label for="sub_jenis">Jenis Pindah <span class="text-danger">*</span></label>
                                            <select class="form-select @error('sub_jenis') is-invalid @enderror"
                                                id="sub_jenis" name="sub_jenis" onchange="onJenisChange()">
                                                <option value="">-- Pilih Jenis --</option>
                                                <option value="pindah_keluarga"
                                                    {{ old('sub_jenis', $mutasiUmat->sub_jenis) === 'pindah_keluarga' ? 'selected' : '' }}>
                                                    Pindah Keluarga</option>
                                                <option value="kub"
                                                    {{ old('sub_jenis', $mutasiUmat->sub_jenis) === 'kub' ? 'selected' : '' }}>
                                                    Pindah KUB</option>
                                                <option value="wilayah"
                                                    {{ old('sub_jenis', $mutasiUmat->sub_jenis) === 'wilayah' ? 'selected' : '' }}>
                                                    Pindah Wilayah</option>
                                                <option value="paroki"
                                                    {{ old('sub_jenis', $mutasiUmat->sub_jenis) === 'paroki' ? 'selected' : '' }}>
                                                    Pindah Paroki</option>
                                                <option value="keuskupan"
                                                    {{ old('sub_jenis', $mutasiUmat->sub_jenis) === 'keuskupan' ? 'selected' : '' }}>
                                                    Pindah Keuskupan</option>
                                            </select>
                                            @error('sub_jenis')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
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
                                    <div class="col-12 d-none" id="wrap-pindah-keluarga">
                                        <div class="form-group">
                                            <label for="keluarga_tujuan_id">Keluarga Tujuan</label>
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

                                    {{-- Keuskupan Tujuan --}}
                                    <div class="col-12 d-none" id="wrap-keuskupan">
                                        <div class="form-group">
                                            <label for="keuskupan_tujuan_id">Keuskupan Tujuan</label>
                                            <select class="form-select @error('keuskupan_tujuan_id') is-invalid @enderror"
                                                id="keuskupan_tujuan_id" name="keuskupan_tujuan_id">
                                                <option value="">-- Pilih --</option>
                                                @foreach ($keuskupanList as $ks)
                                                    <option value="{{ $ks->id }}"
                                                        {{ old('keuskupan_tujuan_id', $mutasiUmat->keuskupan_tujuan_id) == $ks->id ? 'selected' : '' }}>
                                                        {{ $ks->nama }}</option>
                                                @endforeach
                                            </select>
                                            @error('keuskupan_tujuan_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Paroki Tujuan --}}
                                    <div class="col-12 d-none" id="wrap-paroki">
                                        <div class="form-group">
                                            <label for="paroki_tujuan_id">Paroki Tujuan</label>
                                            <select class="form-select @error('paroki_tujuan_id') is-invalid @enderror"
                                                id="paroki_tujuan_id" name="paroki_tujuan_id">
                                                <option value="">-- Pilih --</option>
                                                @foreach ($parokiList as $p)
                                                    <option value="{{ $p->id }}"
                                                        {{ old('paroki_tujuan_id', $mutasiUmat->paroki_tujuan_id) == $p->id ? 'selected' : '' }}>
                                                        {{ $p->nama }}</option>
                                                @endforeach
                                            </select>
                                            @error('paroki_tujuan_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Wilayah Tujuan --}}
                                    <div class="col-12 d-none" id="wrap-wilayah">
                                        <div class="form-group">
                                            <label for="wilayah_tujuan_id">Wilayah Tujuan</label>
                                            <select class="form-select @error('wilayah_tujuan_id') is-invalid @enderror"
                                                id="wilayah_tujuan_id" name="wilayah_tujuan_id"
                                                onchange="onWilayahChange()">
                                                <option value="">-- Pilih --</option>
                                                @foreach ($wilayahList as $w)
                                                    <option value="{{ $w->id }}"
                                                        {{ old('wilayah_tujuan_id', $mutasiUmat->wilayah_tujuan_id) == $w->id ? 'selected' : '' }}>
                                                        {{ $w->nama }}</option>
                                                @endforeach
                                            </select>
                                            @error('wilayah_tujuan_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- KUB Tujuan --}}
                                    <div class="col-12 d-none" id="wrap-kub">
                                        <div class="form-group">
                                            <label for="kub_tujuan_id">KUB Tujuan</label>
                                            <select class="form-select @error('kub_tujuan_id') is-invalid @enderror"
                                                id="kub_tujuan_id" name="kub_tujuan_id">
                                                <option value="">-- Pilih --</option>
                                                @foreach ($kubList as $kub)
                                                    <option value="{{ $kub->id }}"
                                                        {{ old('kub_tujuan_id', $mutasiUmat->kub_tujuan_id) == $kub->id ? 'selected' : '' }}>
                                                        {{ $kub->nama }}</option>
                                                @endforeach
                                            </select>
                                            @error('kub_tujuan_id')
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

            function onJenisChange() {
                const jenis = document.getElementById('sub_jenis').value;
                ['wrap-pindah-keluarga', 'wrap-keuskupan', 'wrap-paroki'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.classList.add('d-none');
                });

                if (jenis === 'pindah_keluarga_ada' || jenis === 'pindah_keluarga_baru') {
                    const el = document.getElementById('wrap-pindah-keluarga');
                    if (el) el.classList.remove('d-none');
                } else if (jenis === 'paroki') {
                    const el = document.getElementById('wrap-paroki');
                    if (el) el.classList.remove('d-none');
                } else if (jenis === 'keuskupan') {
                    const el = document.getElementById('wrap-keuskupan');
                    if (el) el.classList.remove('d-none');
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                onUmatChange();
                onJenisChange();
            });
        </script>
    @endpush
@endsection
