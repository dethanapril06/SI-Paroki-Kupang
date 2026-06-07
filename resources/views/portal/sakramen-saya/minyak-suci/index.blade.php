@extends('layouts.portal')
@section('title', 'Minyak Suci Saya')
@section('content')
<div class="page-heading">
    <div class="page-title mb-3">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><i class="bi bi-moisture text-secondary me-2"></i>Minyak Suci</h3>
                <p class="text-muted">Riwayat penerimaan Sakramen Minyak Suci.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('portal.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('portal.sakramen-saya.index') }}">Sakramen Saya</a></li>
                        <li class="breadcrumb-item active">Minyak Suci</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section">
        @if (session('success'))
            <div class="alert alert-light-success color-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        <div class="row">
            {{-- Daftar riwayat --}}
            <div class="col-12 col-lg-7 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Riwayat Penerimaan ({{ $daftarMinyakSuci->count() }})</h5>
                    </div>
                    <div class="card-body p-0">
                        @forelse ($daftarMinyakSuci as $s)
                            <div class="d-flex align-items-start gap-3 p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="rounded-circle bg-light-secondary d-flex align-items-center justify-content-center flex-shrink-0"
                                    style="width:40px;height:40px;">
                                    <span class="fw-bold text-secondary">{{ $loop->iteration }}</span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">{{ $s->tanggal_penerimaan?->format('d M Y') }}</div>
                                    <div class="small text-muted">
                                        @if ($s->klerus) {{ $s->klerus->nama }} &bull; @endif
                                        @if ($s->minyakSuci?->tempat_terima) {{ $s->minyakSuci->tempat_terima }} @endif
                                    </div>
                                    @if ($s->minyakSuci?->keterangan_sebab)
                                        <div class="small text-muted fst-italic">{{ $s->minyakSuci->keterangan_sebab }}</div>
                                    @endif
                                </div>
                                <a href="{{ route('portal.sakramen-saya.minyak-suci.edit', $s) }}" class="btn btn-sm btn-outline-secondary flex-shrink-0">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-moisture d-block fs-2 mb-2"></i>
                                Belum ada data Minyak Suci.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Form tambah baru --}}
            <div class="col-12 col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="bi bi-plus-circle me-2"></i>Tambah Data Baru</h5>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-light-danger color-danger mb-3"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
                        @endif
                        <form action="{{ route('portal.sakramen-saya.minyak-suci.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Tanggal Penerimaan <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_penerimaan" class="form-control @error('tanggal_penerimaan') is-invalid @enderror" value="{{ old('tanggal_penerimaan') }}">
                                @error('tanggal_penerimaan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Pastor Pemberi</label>
                                <select name="klerus_id" class="form-select">
                                    <option value="">-- Pilih (opsional) --</option>
                                    @foreach ($klerusList as $k)
                                        <option value="{{ $k->id }}" {{ old('klerus_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tempat Penerimaan</label>
                                <input type="text" name="tempat_terima" class="form-control" value="{{ old('tempat_terima') }}" placeholder="Rumah sakit / rumah / gereja ...">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama Pemberi (manual)</label>
                                <input type="text" name="nama_pemberi" class="form-control" value="{{ old('nama_pemberi') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Keterangan / Sebab</label>
                                <textarea name="keterangan_sebab" class="form-control" rows="2" placeholder="Sakit berat / lansia ...">{{ old('keterangan_sebab') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-secondary w-100">
                                <i class="bi bi-plus me-1"></i>Tambah
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
