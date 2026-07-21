@extends('layouts.portal')
@section('title', 'Minyak Suci — ' . $anggota->nama)
@section('content')
<div class="page-heading">
    <div class="page-title mb-3">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><i class="bi bi-moisture text-secondary me-2"></i>Minyak Suci — {{ $anggota->nama }}</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('portal.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('portal.keluarga-saya.show') }}">Keluarga Saya</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('portal.sakramen-anggota.index', $anggota) }}">Sakramen {{ $anggota->nama }}</a></li>
                        <li class="breadcrumb-item active">Minyak Suci</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section">
        @if (session('success'))
            <div class="alert alert-light-success color-success alert-dismissible fade show">
                {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-light-danger color-danger alert-dismissible fade show">
                {{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-4">
            {{-- Form Tambah --}}
            <div class="col-12 col-lg-5">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Tambah Data Minyak Suci</h5></div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-light-danger color-danger mb-3"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
                        @endif
                        <form action="{{ route('portal.sakramen-anggota.minyak-suci.store', $anggota) }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Tanggal Penerimaan <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_penerimaan" class="form-control @error('tanggal_penerimaan') is-invalid @enderror"
                                        value="{{ old('tanggal_penerimaan') }}">
                                    @error('tanggal_penerimaan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Pastor Pemberi</label>
                                    <select name="klerus_id" class="form-select">
                                        <option value="">-- Pilih (opsional) --</option>
                                        @foreach ($klerusList as $k)
                                            <option value="{{ $k->id }}" {{ old('klerus_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Tempat Penerimaan</label>
                                    <input type="text" name="tempat_terima" class="form-control" value="{{ old('tempat_terima') }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Nama Pemberi (manual)</label>
                                    <input type="text" name="nama_pemberi" class="form-control" value="{{ old('nama_pemberi') }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Keterangan / Sebab</label>
                                    <textarea name="keterangan_sebab" class="form-control" rows="2">{{ old('keterangan_sebab') }}</textarea>
                                </div>
                            </div>
                            <div class="d-flex gap-2 justify-content-end mt-3">
                                <button type="submit" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>Tambahkan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Daftar Riwayat --}}
            <div class="col-12 col-lg-7">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Riwayat Minyak Suci</h5></div>
                    <div class="card-body p-0">
                        @forelse ($daftarMinyakSuci as $item)
                            <div class="d-flex align-items-start justify-content-between p-3 border-bottom">
                                <div>
                                    <div class="fw-semibold">{{ $item->tanggal_penerimaan?->format('d M Y') }}</div>
                                    @if ($item->klerus)
                                        <div class="small text-muted">Pastor: {{ $item->klerus->nama }}</div>
                                    @endif
                                    @if ($item->minyakSuci?->tempat_terima)
                                        <div class="small text-muted">Tempat: {{ $item->minyakSuci->tempat_terima }}</div>
                                    @endif
                                    @if ($item->minyakSuci?->keterangan_sebab)
                                        <div class="small text-muted">{{ $item->minyakSuci->keterangan_sebab }}</div>
                                    @endif
                                </div>
                                <a href="{{ route('portal.sakramen-anggota.minyak-suci.edit', [$anggota, $item]) }}"
                                   class="btn btn-sm btn-outline-secondary ms-2">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        @empty
                            <div class="p-4 text-center text-muted">Belum ada data minyak suci.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <a href="{{ route('portal.sakramen-anggota.index', $anggota) }}" class="btn btn-light-secondary">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </section>
</div>
@endsection
