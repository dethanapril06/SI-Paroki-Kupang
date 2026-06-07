@extends('layouts.sekretariat')

@section('title', 'Tambah Mutasi Umat')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Tambah Mutasi Umat</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sekretariat.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sekretariat.mutasi.index') }}">Mutasi</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sekretariat.mutasi.umat.index') }}">Mutasi
                                    Umat</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Tambah Mutasi Umat</h4>
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
                            action="{{ route('sekretariat.mutasi.umat.store') }}">
                            @csrf
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
                                                        {{ old('umat_id') == $umat->id ? 'selected' : '' }}>
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
                                    <div class="col-12" id="info-asal" style="display:none">
                                        <div class="alert alert-light-primary color-primary py-2 mb-0">
                                            <small class="fw-bold text-muted d-block mb-1">📍 Posisi Umat Saat Ini
                                                (Asal):</small>
                                            <div class="row g-2 small">
                                                <div class="col-6 col-md-3"><span class="text-muted">KUB:</span> <strong
                                                        id="asal-kub">-</strong></div>
                                                <div class="col-6 col-md-3"><span class="text-muted">Wilayah:</span> <strong
                                                        id="asal-wilayah">-</strong></div>
                                                <div class="col-6 col-md-3"><span class="text-muted">Paroki:</span> <strong
                                                        id="asal-paroki">-</strong></div>
                                                <div class="col-6 col-md-3"><span class="text-muted">Keuskupan:</span>
                                                    <strong id="asal-keuskupan">-</strong></div>
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
                                                <option value="pindah_keluarga_ada"
                                                    {{ old('sub_jenis') === 'pindah_keluarga_ada' ? 'selected' : '' }}>
                                                    Pindah ke Keluarga yang Ada</option>
                                                <option value="pindah_keluarga_baru"
                                                    {{ old('sub_jenis') === 'pindah_keluarga_baru' ? 'selected' : '' }}>
                                                    Pindah ke Keluarga Baru</option>
                                                <option value="paroki"
                                                    {{ old('sub_jenis') === 'paroki' ? 'selected' : '' }}>
                                                    Pindah Paroki</option>
                                                <option value="keuskupan"
                                                    {{ old('sub_jenis') === 'keuskupan' ? 'selected' : '' }}>
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
                                                name="tanggal" value="{{ old('tanggal') }}">
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
                                                id="nomor_surat" name="nomor_surat" value="{{ old('nomor_surat') }}"
                                                placeholder="Opsional">
                                            @error('nomor_surat')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- ===== PINDAH KE KELUARGA YANG SUDAH ADA ===== --}}
                                    <div class="col-12 d-none" id="wrap-pindah-keluarga-ada">
                                        <div class="form-group">
                                            <label for="keluarga_tujuan_id">Keluarga Tujuan <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select @error('keluarga_tujuan_id') is-invalid @enderror"
                                                id="keluarga_tujuan_id" name="keluarga_tujuan_id">
                                                <option value="">-- Pilih Keluarga Tujuan --</option>
                                                @foreach ($keluargaList as $k)
                                                    <option value="{{ $k->id }}"
                                                        {{ old('keluarga_tujuan_id') == $k->id ? 'selected' : '' }}>
                                                        {{ $k->kepalaKeluarga->nama ?? '(-)' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('keluarga_tujuan_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- ===== PINDAH KE KELUARGA BARU ===== --}}
                                    <div class="col-12 d-none" id="wrap-pindah-keluarga-baru">
                                        <div class="card border mb-0">
                                            <div class="card-header py-2">
                                                <small class="fw-bold text-muted">🏠 Data Keluarga Baru</small>
                                            </div>
                                            <div class="card-body">
                                                <div class="row g-3">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label for="alamat_baru">Alamat <span
                                                                    class="text-danger">*</span></label>
                                                            <textarea class="form-control @error('alamat_baru') is-invalid @enderror" id="alamat_baru" name="alamat_baru"
                                                                rows="2">{{ old('alamat_baru') }}</textarea>
                                                            @error('alamat_baru')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-6">
                                                        <div class="form-group">
                                                            <label for="status_tempat_tinggal_baru">Status Tempat Tinggal
                                                                <span class="text-danger">*</span></label>
                                                            <select
                                                                class="form-select @error('status_tempat_tinggal_baru') is-invalid @enderror"
                                                                id="status_tempat_tinggal_baru"
                                                                name="status_tempat_tinggal_baru">
                                                                <option value="">-- Pilih --</option>
                                                                <option value="Rumah Pribadi"
                                                                    {{ old('status_tempat_tinggal_baru') === 'Rumah Pribadi' ? 'selected' : '' }}>
                                                                    Rumah Pribadi</option>
                                                                <option value="Kontrak/Kost"
                                                                    {{ old('status_tempat_tinggal_baru') === 'Kontrak/Kost' ? 'selected' : '' }}>
                                                                    Kontrak/Kost</option>
                                                                <option value="Dinas"
                                                                    {{ old('status_tempat_tinggal_baru') === 'Dinas' ? 'selected' : '' }}>
                                                                    Dinas</option>
                                                            </select>
                                                            @error('status_tempat_tinggal_baru')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    {{-- Toggle Kepala Keluarga --}}
                                                    <div class="col-12">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="jadikan_kepala" name="jadikan_kepala" value="1"
                                                                {{ old('jadikan_kepala') ? 'checked' : '' }}
                                                                onchange="onKepalaToggle()">
                                                            <label class="form-check-label" for="jadikan_kepala">
                                                                Jadikan Kepala Keluarga
                                                            </label>
                                                        </div>
                                                    </div>

                                                    {{-- Hubungan Keluarga (muncul jika toggle aktif) --}}
                                                    <div class="col-12 col-md-6 d-none" id="wrap-hubungan">
                                                        <div class="form-group">
                                                            <label for="hubungan_keluarga_baru">Hubungan Keluarga <span
                                                                    class="text-danger">*</span></label>
                                                            <select
                                                                class="form-select @error('hubungan_keluarga_baru') is-invalid @enderror"
                                                                id="hubungan_keluarga_baru" name="hubungan_keluarga_baru">
                                                                <option value="">-- Pilih --</option>
                                                                <option value="Suami"
                                                                    {{ old('hubungan_keluarga_baru') === 'Suami' ? 'selected' : '' }}>
                                                                    Suami</option>
                                                                <option value="Istri"
                                                                    {{ old('hubungan_keluarga_baru') === 'Istri' ? 'selected' : '' }}>
                                                                    Istri</option>
                                                                <option value="Ayah"
                                                                    {{ old('hubungan_keluarga_baru') === 'Ayah' ? 'selected' : '' }}>
                                                                    Ayah</option>
                                                                <option value="Ibu"
                                                                    {{ old('hubungan_keluarga_baru') === 'Ibu' ? 'selected' : '' }}>
                                                                    Ibu</option>
                                                            </select>
                                                            @error('hubungan_keluarga_baru')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- ===== PINDAH PAROKI ===== --}}
                                    <div class="col-12 d-none" id="wrap-paroki">
                                        <div class="form-group">
                                            <label for="paroki_tujuan_id">Paroki Tujuan <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select @error('paroki_tujuan_id') is-invalid @enderror"
                                                id="paroki_tujuan_id" name="paroki_tujuan_id">
                                                <option value="">-- Pilih Paroki Tujuan --</option>
                                                @foreach ($parokiList as $p)
                                                    <option value="{{ $p->id }}"
                                                        {{ old('paroki_tujuan_id') == $p->id ? 'selected' : '' }}>
                                                        {{ $p->nama }}</option>
                                                @endforeach
                                            </select>
                                            @error('paroki_tujuan_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- ===== PINDAH KEUSKUPAN ===== --}}
                                    <div class="col-12 d-none" id="wrap-keuskupan">
                                        <div class="form-group">
                                            <label for="keuskupan_tujuan_id">Keuskupan Tujuan <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select @error('keuskupan_tujuan_id') is-invalid @enderror"
                                                id="keuskupan_tujuan_id" name="keuskupan_tujuan_id">
                                                <option value="">-- Pilih Keuskupan Tujuan --</option>
                                                @foreach ($keuskupanList as $ks)
                                                    <option value="{{ $ks->id }}"
                                                        {{ old('keuskupan_tujuan_id') == $ks->id ? 'selected' : '' }}>
                                                        {{ $ks->nama }}</option>
                                                @endforeach
                                            </select>
                                            @error('keuskupan_tujuan_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Keterangan --}}
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="keterangan">Keterangan</label>
                                            <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan"
                                                rows="3" placeholder="Opsional">{{ old('keterangan') }}</textarea>
                                            @error('keterangan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12 d-flex justify-content-end">
                                        <a href="{{ route('sekretariat.mutasi.umat.index') }}"
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

    @push('scripts')
        <script>
            const umatHierarchy = @json($umatHierarchy);

            function onUmatChange() {
                const id = document.getElementById('umat_id').value;
                const info = document.getElementById('info-asal');
                if (!id || !umatHierarchy[id]) {
                    info.style.display = 'none';
                    return;
                }
                const h = umatHierarchy[id];
                document.getElementById('asal-kub').textContent = h.kub_nama || '-';
                document.getElementById('asal-wilayah').textContent = h.wilayah_nama || '-';
                document.getElementById('asal-paroki').textContent = h.paroki_nama || '-';
                document.getElementById('asal-keuskupan').textContent = h.keuskupan_nama || '-';
                info.style.display = 'block';
            }

            function onKepalaToggle() {
                const checked = document.getElementById('jadikan_kepala').checked;
                document.getElementById('wrap-hubungan').classList.toggle('d-none', !checked);
            }

            function onJenisChange() {
                const jenis = document.getElementById('sub_jenis').value;
                [
                    'wrap-pindah-keluarga-ada',
                    'wrap-pindah-keluarga-baru',
                    'wrap-paroki',
                    'wrap-keuskupan',
                ].forEach(id => document.getElementById(id).classList.add('d-none'));

                if (jenis === 'pindah_keluarga_ada') {
                    document.getElementById('wrap-pindah-keluarga-ada').classList.remove('d-none');
                } else if (jenis === 'pindah_keluarga_baru') {
                    document.getElementById('wrap-pindah-keluarga-baru').classList.remove('d-none');
                } else if (jenis === 'paroki') {
                    document.getElementById('wrap-paroki').classList.remove('d-none');
                } else if (jenis === 'keuskupan') {
                    document.getElementById('wrap-keuskupan').classList.remove('d-none');
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                onUmatChange();
                onJenisChange();
                onKepalaToggle();
            });
        </script>
    @endpush
@endsection
