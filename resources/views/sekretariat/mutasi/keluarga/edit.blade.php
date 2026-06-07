@extends('layouts.sekretariat')

@section('title', 'Edit Mutasi Keluarga')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Edit Mutasi Keluarga</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sekretariat.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sekretariat.mutasi.index') }}">Mutasi</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sekretariat.mutasi.keluarga.index') }}">Mutasi
                                    Keluarga</a></li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('sekretariat.mutasi.keluarga.show', $mutasiKeluarga) }}">Detail</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Edit Mutasi Keluarga</h4>
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
                            action="{{ route('sekretariat.mutasi.keluarga.update', $mutasiKeluarga) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-body">
                                <div class="row g-3">

                                    {{-- Pilih Keluarga --}}
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="keluarga_id">Keluarga <span class="text-danger">*</span></label>
                                            <select class="form-select @error('keluarga_id') is-invalid @enderror"
                                                id="keluarga_id" name="keluarga_id" onchange="onKeluargaChange()">
                                                <option value="">-- Pilih Keluarga --</option>
                                                @foreach ($keluargaList as $keluarga)
                                                    <option value="{{ $keluarga->id }}"
                                                        {{ old('keluarga_id', $mutasiKeluarga->keluarga_id) == $keluarga->id ? 'selected' : '' }}>
                                                        {{ $keluarga->kepalaKeluarga->nama ?? '(-)' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('keluarga_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Info Asal --}}
                                    <div class="col-12" id="info-asal">
                                        <div class="alert alert-light-primary color-primary py-2 mb-0">
                                            <small class="fw-bold text-muted d-block mb-1">📍 Posisi Keluarga Saat Ini
                                                (Asal):</small>
                                            <div class="row g-2 small">
                                                <div class="col-6 col-md-3"><span class="text-muted">KUB:</span> <strong
                                                        id="asal-kub">{{ $mutasiKeluarga->kubAsal->nama ?? '-' }}</strong>
                                                </div>
                                                <div class="col-6 col-md-3"><span class="text-muted">Wilayah:</span> <strong
                                                        id="asal-wilayah">{{ $mutasiKeluarga->wilayahAsal->nama ?? '-' }}</strong>
                                                </div>
                                                <div class="col-6 col-md-3"><span class="text-muted">Paroki:</span> <strong
                                                        id="asal-paroki">{{ $mutasiKeluarga->parokiAsal->nama ?? '-' }}</strong>
                                                </div>
                                                <div class="col-6 col-md-3"><span class="text-muted">Keuskupan:</span>
                                                    <strong
                                                        id="asal-keuskupan">{{ $mutasiKeluarga->keuskupanAsal->nama ?? '-' }}</strong>
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
                                                <option value="kub"
                                                    {{ old('sub_jenis', $mutasiKeluarga->sub_jenis) === 'kub' ? 'selected' : '' }}>
                                                    Pindah KUB</option>
                                                <option value="wilayah"
                                                    {{ old('sub_jenis', $mutasiKeluarga->sub_jenis) === 'wilayah' ? 'selected' : '' }}>
                                                    Pindah Wilayah</option>
                                                <option value="paroki"
                                                    {{ old('sub_jenis', $mutasiKeluarga->sub_jenis) === 'paroki' ? 'selected' : '' }}>
                                                    Pindah Paroki</option>
                                                <option value="keuskupan"
                                                    {{ old('sub_jenis', $mutasiKeluarga->sub_jenis) === 'keuskupan' ? 'selected' : '' }}>
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
                                                value="{{ old('tanggal', $mutasiKeluarga->mutasi->tanggal->format('Y-m-d')) }}">
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
                                                value="{{ old('nomor_surat', $mutasiKeluarga->nomor_surat) }}"
                                                placeholder="Opsional">
                                            @error('nomor_surat')
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
                                                <option value="">-- Pilih Keuskupan Tujuan --</option>
                                                @foreach ($keuskupanList as $ks)
                                                    <option value="{{ $ks->id }}"
                                                        {{ old('keuskupan_tujuan_id', $mutasiKeluarga->keuskupan_tujuan_id) == $ks->id ? 'selected' : '' }}>
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
                                                <option value="">-- Pilih Paroki Tujuan --</option>
                                                @foreach ($parokiList as $p)
                                                    <option value="{{ $p->id }}"
                                                        {{ old('paroki_tujuan_id', $mutasiKeluarga->paroki_tujuan_id) == $p->id ? 'selected' : '' }}>
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
                                                <option value="">-- Pilih Wilayah Tujuan --</option>
                                                @foreach ($wilayahList as $w)
                                                    <option value="{{ $w->id }}"
                                                        {{ old('wilayah_tujuan_id', $mutasiKeluarga->wilayah_tujuan_id) == $w->id ? 'selected' : '' }}>
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
                                                <option value="">-- Pilih KUB Tujuan --</option>
                                                @foreach ($kubList as $kub)
                                                    <option value="{{ $kub->id }}"
                                                        {{ old('kub_tujuan_id', $mutasiKeluarga->kub_tujuan_id) == $kub->id ? 'selected' : '' }}>
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
                                                rows="3">{{ old('keterangan', $mutasiKeluarga->mutasi->keterangan) }}</textarea>
                                            @error('keterangan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12 d-flex justify-content-end">
                                        <a href="{{ route('sekretariat.mutasi.keluarga.show', $mutasiKeluarga) }}"
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
            const keluargaHierarchy = @json($keluargaHierarchy);
            const kubByWilayah = @json($kubByWilayah);
            const allKub = @json($kubList->map(fn($k) => ['id' => $k->id, 'nama' => $k->nama]));

            function onKeluargaChange() {
                const id = document.getElementById('keluarga_id').value;
                if (!id || !keluargaHierarchy[id]) return;
                const h = keluargaHierarchy[id];
                document.getElementById('asal-kub').textContent = h.kub_nama || '-';
                document.getElementById('asal-wilayah').textContent = h.wilayah_nama || '-';
                document.getElementById('asal-paroki').textContent = h.paroki_nama || '-';
                document.getElementById('asal-keuskupan').textContent = h.keuskupan_nama || '-';
            }

            function populateKub(items) {
                const el = document.getElementById('kub_tujuan_id');
                el.innerHTML = '<option value="">-- Pilih KUB Tujuan --</option>';
                (items || []).forEach(k => el.innerHTML += `<option value="${k.id}">${k.nama}</option>`);
            }

            function onWilayahChange() {
                const wilayahId = document.getElementById('wilayah_tujuan_id').value;
                populateKub(kubByWilayah[wilayahId] || []);
            }

            function onJenisChange() {
                const jenis = document.getElementById('sub_jenis').value;
                ['wrap-keuskupan', 'wrap-paroki', 'wrap-wilayah', 'wrap-kub'].forEach(id => {
                    document.getElementById(id).classList.add('d-none');
                });
                populateKub(allKub);

                if (jenis === 'kub') {
                    document.getElementById('wrap-kub').classList.remove('d-none');
                } else if (jenis === 'wilayah') {
                    populateKub([]);
                    document.getElementById('wrap-wilayah').classList.remove('d-none');
                    document.getElementById('wrap-kub').classList.remove('d-none');
                } else if (jenis === 'paroki') {
                    document.getElementById('wrap-paroki').classList.remove('d-none');
                } else if (jenis === 'keuskupan') {
                    document.getElementById('wrap-keuskupan').classList.remove('d-none');
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                onJenisChange();
            });
        </script>
    @endpush
@endsection
