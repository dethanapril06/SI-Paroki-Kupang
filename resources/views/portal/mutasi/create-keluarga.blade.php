@extends('layouts.portal')

@section('title', 'Ajukan Mutasi Keluarga')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Ajukan Mutasi Keluarga</h3>
                    <p class="text-subtitle text-muted">Request perpindahan keluarga (hanya Kepala Keluarga).</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('portal.mutasi.index') }}">Portal</a></li>
                            <li class="breadcrumb-item active">Ajukan Mutasi Keluarga</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-12 col-lg-8">
                    <div class="alert alert-light-success color-success mb-4">
                        <i class="bi bi-house-door-fill me-2"></i>
                        <strong>Keluarga {{ $umat->nama }}</strong> &mdash;
                        KUB: {{ $keluarga->kub?->nama ?? '-' }} | Wilayah: {{ $wilayah?->nama ?? '-' }} | Paroki: {{ $paroki?->nama ?? '-' }}
                    </div>

                    <div class="card">
                        <div class="card-header"><h5 class="card-title">Form Request Mutasi Keluarga</h5></div>
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-light-danger color-danger">
                                    <ul class="mb-0 ps-3">
                                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('portal.mutasi.keluarga.store') }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Jenis Perpindahan <span class="text-danger">*</span></label>
                                    <select name="sub_jenis" id="sub_jenis" class="form-select @error('sub_jenis') is-invalid @enderror" required>
                                        <option value="">-- Pilih Jenis --</option>
                                        <option value="kub" {{ old('sub_jenis') === 'kub' ? 'selected' : '' }}>Pindah KUB</option>
                                        <option value="wilayah" {{ old('sub_jenis') === 'wilayah' ? 'selected' : '' }}>Pindah Wilayah</option>
                                        <option value="paroki" {{ old('sub_jenis') === 'paroki' ? 'selected' : '' }}>Pindah Paroki Lain</option>
                                        <option value="keuskupan" {{ old('sub_jenis') === 'keuskupan' ? 'selected' : '' }}>Pindah Luar Keuskupan</option>
                                    </select>
                                    @error('sub_jenis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror"
                                        value="{{ old('tanggal', date('Y-m-d')) }}" required>
                                    @error('tanggal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Nomor Surat (opsional)</label>
                                    <input type="text" name="nomor_surat" class="form-control" value="{{ old('nomor_surat') }}">
                                </div>

                                <div id="section-paroki" class="d-none mb-3">
                                    <label class="form-label fw-semibold">Paroki Tujuan</label>
                                    <select name="paroki_tujuan_id" class="form-select">
                                        <option value="">-- Pilih Paroki --</option>
                                        @foreach ($parokiList as $p)
                                            <option value="{{ $p->id }}" {{ old('paroki_tujuan_id') == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div id="section-keuskupan" class="d-none mb-3">
                                    <label class="form-label fw-semibold">Keuskupan Tujuan</label>
                                    <select name="keuskupan_tujuan_id" class="form-select">
                                        <option value="">-- Pilih Keuskupan --</option>
                                        @foreach ($keuskupanList as $k)
                                            <option value="{{ $k->id }}" {{ old('keuskupan_tujuan_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div id="section-kub-wilayah-info" class="alert alert-light-info color-info d-none mb-3">
                                    <i class="bi bi-info-circle me-1"></i>
                                    KUB/Wilayah tujuan akan ditetapkan oleh sekretariat setelah request diproses.
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Keterangan (opsional)</label>
                                    <textarea name="keterangan" class="form-control" rows="3"
                                        placeholder="Jelaskan alasan perpindahan...">{{ old('keterangan') }}</textarea>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-success">
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
        document.getElementById('section-paroki').classList.toggle('d-none', val !== 'paroki');
        document.getElementById('section-keuskupan').classList.toggle('d-none', val !== 'keuskupan');
        document.getElementById('section-kub-wilayah-info').classList.toggle('d-none', val !== 'kub' && val !== 'wilayah');
    }
    subJenis.addEventListener('change', toggleSections);
    toggleSections();
</script>
@endpush
