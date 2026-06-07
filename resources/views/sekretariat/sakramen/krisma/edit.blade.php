@extends('layouts.sekretariat')

@section('title', 'Edit Krisma')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last"><h3>Edit Krisma</h3></div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sekretariat.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sekretariat.sakramen.index') }}">Sakramen</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sekretariat.sakramen.krisma.index') }}">Krisma</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sekretariat.sakramen.krisma.show', $sakramen) }}">Detail</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header"><h4 class="card-title">Form Edit Krisma</h4></div>
                <div class="card-content">
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-light-danger color-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('sekretariat.sakramen.krisma.update', $sakramen) }}">
                            @csrf
                            @method('PUT')
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="umat_id">Nama Umat <span class="text-danger">*</span></label>
                                        <select class="form-select @error('umat_id') is-invalid @enderror" id="umat_id" name="umat_id">
                                            <option value="">-- Pilih Umat --</option>
                                            @foreach ($umat as $u)
                                                <option value="{{ $u->id }}" {{ old('umat_id', $sakramen->umat_id) == $u->id ? 'selected' : '' }}>{{ $u->nama }}</option>
                                            @endforeach
                                        </select>
                                        @error('umat_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="tanggal_penerimaan">Tanggal Penerimaan <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('tanggal_penerimaan') is-invalid @enderror" id="tanggal_penerimaan" name="tanggal_penerimaan" value="{{ old('tanggal_penerimaan', $sakramen->tanggal_penerimaan->format('Y-m-d')) }}">
                                        @error('tanggal_penerimaan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="paroki_id">Paroki</label>
                                        <select class="form-select @error('paroki_id') is-invalid @enderror" id="paroki_id" name="paroki_id">
                                            <option value="">-- Pilih Paroki --</option>
                                            @foreach ($paroki as $p)
                                                <option value="{{ $p->id }}" {{ old('paroki_id', $sakramen->paroki_id) == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                                            @endforeach
                                        </select>
                                        @error('paroki_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="klerus_id">Klerus Pemimpin</label>
                                        <select class="form-select @error('klerus_id') is-invalid @enderror" id="klerus_id" name="klerus_id">
                                            <option value="">-- Pilih Klerus --</option>
                                            @foreach ($klerus as $k)
                                                <option value="{{ $k->id }}" {{ old('klerus_id', $sakramen->klerus_id) == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                                            @endforeach
                                        </select>
                                        @error('klerus_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="uskup_id">Uskup Pemberi Krisma</label>
                                        <select class="form-select @error('uskup_id') is-invalid @enderror" id="uskup_id" name="uskup_id">
                                            <option value="">-- Pilih Uskup --</option>
                                            @foreach ($uskup as $u)
                                                <option value="{{ $u->id }}" {{ old('uskup_id', $sakramen->krisma->uskup_id) == $u->id ? 'selected' : '' }}>{{ $u->nama }}</option>
                                            @endforeach
                                        </select>
                                        @error('uskup_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="nama_krisma">Nama Krisma</label>
                                        <input type="text" class="form-control @error('nama_krisma') is-invalid @enderror" id="nama_krisma" name="nama_krisma" value="{{ old('nama_krisma', $sakramen->krisma->nama_krisma) }}">
                                        @error('nama_krisma')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="nomor_surat">Nomor Surat</label>
                                        <input type="text" class="form-control @error('nomor_surat') is-invalid @enderror" id="nomor_surat" name="nomor_surat" value="{{ old('nomor_surat', $sakramen->nomor_surat) }}">
                                        @error('nomor_surat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-12 d-flex justify-content-end">
                                    <a href="{{ route('sekretariat.sakramen.krisma.show', $sakramen) }}" class="btn btn-light-secondary me-1 mb-1">Batal</a>
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
