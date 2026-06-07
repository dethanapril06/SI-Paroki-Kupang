@extends('layouts.portal')

@section('title', 'Ajukan Mutasi Umat')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Ajukan Mutasi Umat</h3>
                    <p class="text-subtitle text-muted">Permintaan perpindahan / perubahan status Anda sebagai umat.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('portal.mutasi.index') }}">Portal</a></li>
                            <li class="breadcrumb-item active">Ajukan Mutasi Umat</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-12 col-lg-8">

                    {{-- Info umat --}}
                    <div class="alert alert-light-info color-info mb-4">
                        <div class="d-flex gap-3 align-items-center">
                            <i class="bi bi-person-circle fs-4"></i>
                            <div>
                                <strong>{{ $umat->nama }}</strong>
                                <div class="small text-muted">
                                    Keluarga: {{ $keluargaAsal?->kepalaKeluarga?->nama ?? '-' }} &nbsp;|&nbsp;
                                    KUB: {{ $keluargaAsal?->kub?->nama ?? '-' }} &nbsp;|&nbsp;
                                    Wilayah: {{ $wilayah?->nama ?? '-' }} &nbsp;|&nbsp;
                                    Paroki: {{ $paroki?->nama ?? '-' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Form Request Mutasi</h5>
                        </div>
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-light-danger color-danger">
                                    <ul class="mb-0 ps-3">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('portal.mutasi.umat.store') }}" method="POST">
                                @csrf

                                {{-- Sub Jenis --}}
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Jenis Perpindahan <span class="text-danger">*</span></label>
                                    <select name="sub_jenis" id="sub_jenis" class="form-select @error('sub_jenis') is-invalid @enderror" required>
                                        <option value="">-- Pilih Jenis --</option>
                                        <option value="pindah_keluarga_ada" {{ old('sub_jenis') === 'pindah_keluarga_ada' ? 'selected' : '' }}>
                                            Pindah ke Keluarga yang Sudah Ada
                                        </option>
                                        <option value="pindah_keluarga_baru" {{ old('sub_jenis') === 'pindah_keluarga_baru' ? 'selected' : '' }}>
                                            Pindah ke Keluarga Baru (Pisah KK)
                                        </option>
                                        <option value="paroki" {{ old('sub_jenis') === 'paroki' ? 'selected' : '' }}>
                                            Pindah ke Paroki Lain
                                        </option>
                                        <option value="keuskupan" {{ old('sub_jenis') === 'keuskupan' ? 'selected' : '' }}>
                                            Pindah ke Luar Keuskupan
                                        </option>
                                    </select>
                                    @error('sub_jenis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                {{-- Tanggal --}}
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Tanggal Perpindahan <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror"
                                        value="{{ old('tanggal', date('Y-m-d')) }}" required>
                                    @error('tanggal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                {{-- Nomor Surat --}}
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Nomor Surat (opsional)</label>
                                    <input type="text" name="nomor_surat" class="form-control @error('nomor_surat') is-invalid @enderror"
                                        value="{{ old('nomor_surat') }}" placeholder="Contoh: 001/MUT/2024">
                                    @error('nomor_surat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                {{-- Pindah ke keluarga yang sudah ada --}}
                                <div id="section-pindah-keluarga-ada" class="d-none">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Keluarga Tujuan <span class="text-danger">*</span></label>
                                        <select name="keluarga_tujuan_id" class="form-select @error('keluarga_tujuan_id') is-invalid @enderror">
                                            <option value="">-- Pilih Keluarga --</option>
                                            @foreach ($keluargaList as $kl)
                                                <option value="{{ $kl->id }}" {{ old('keluarga_tujuan_id') == $kl->id ? 'selected' : '' }}>
                                                    {{ $kl->kepalaKeluarga?->nama ?? 'Keluarga #'.$kl->id }}
                                                    ({{ $kl->kub?->nama ?? '-' }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('keluarga_tujuan_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                {{-- Pindah ke keluarga baru --}}
                                <div id="section-pindah-keluarga-baru" class="d-none">
                                    <div class="alert alert-light-warning color-warning p-3 mb-3">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Sekretariat akan membuatkan KK baru berdasarkan informasi yang Anda berikan.
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Alamat Baru <span class="text-danger">*</span></label>
                                        <textarea name="alamat_baru" class="form-control @error('alamat_baru') is-invalid @enderror"
                                            rows="2" placeholder="Masukkan alamat lengkap">{{ old('alamat_baru') }}</textarea>
                                        @error('alamat_baru') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Status Tempat Tinggal <span class="text-danger">*</span></label>
                                        <select name="status_tempat_tinggal_baru" class="form-select">
                                            <option value="">-- Pilih --</option>
                                            <option value="Rumah Pribadi" {{ old('status_tempat_tinggal_baru') === 'Rumah Pribadi' ? 'selected' : '' }}>Rumah Pribadi</option>
                                            <option value="Kontrak/Kost" {{ old('status_tempat_tinggal_baru') === 'Kontrak/Kost' ? 'selected' : '' }}>Kontrak/Kost</option>
                                            <option value="Dinas" {{ old('status_tempat_tinggal_baru') === 'Dinas' ? 'selected' : '' }}>Dinas</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Pindah Paroki --}}
                                <div id="section-paroki" class="d-none">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Paroki Tujuan <span class="text-danger">*</span></label>
                                        <select name="paroki_tujuan_id" class="form-select @error('paroki_tujuan_id') is-invalid @enderror">
                                            <option value="">-- Pilih Paroki --</option>
                                            @foreach ($parokiList as $p)
                                                <option value="{{ $p->id }}" {{ old('paroki_tujuan_id') == $p->id ? 'selected' : '' }}>
                                                    {{ $p->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('paroki_tujuan_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                {{-- Pindah Keuskupan --}}
                                <div id="section-keuskupan" class="d-none">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Keuskupan Tujuan <span class="text-danger">*</span></label>
                                        <select name="keuskupan_tujuan_id" class="form-select @error('keuskupan_tujuan_id') is-invalid @enderror">
                                            <option value="">-- Pilih Keuskupan --</option>
                                            @foreach ($keuskupanList as $k)
                                                <option value="{{ $k->id }}" {{ old('keuskupan_tujuan_id') == $k->id ? 'selected' : '' }}>
                                                    {{ $k->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('keuskupan_tujuan_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                {{-- Keterangan --}}
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Keterangan / Alasan (opsional)</label>
                                    <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror"
                                        rows="3" placeholder="Jelaskan alasan perpindahan...">{{ old('keterangan') }}</textarea>
                                    @error('keterangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-send me-1"></i> Kirim Request
                                    </button>
                                    <a href="{{ route('portal.mutasi.index') }}" class="btn btn-secondary">Batal</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
<script>
    const subJenis = document.getElementById('sub_jenis');

    function toggleSections() {
        const val = subJenis.value;
        document.getElementById('section-pindah-keluarga-ada').classList.toggle('d-none', val !== 'pindah_keluarga_ada');
        document.getElementById('section-pindah-keluarga-baru').classList.toggle('d-none', val !== 'pindah_keluarga_baru');
        document.getElementById('section-paroki').classList.toggle('d-none', val !== 'paroki');
        document.getElementById('section-keuskupan').classList.toggle('d-none', val !== 'keuskupan');
    }

    subJenis.addEventListener('change', toggleSections);
    toggleSections(); // run on load for old() values
</script>
@endpush
