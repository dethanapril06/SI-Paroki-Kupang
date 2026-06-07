@extends('layouts.sekretariat')

@section('title', 'Edit Pernikahan')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Edit Pernikahan</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sekretariat.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sekretariat.sakramen.index') }}">Sakramen</a>
                            </li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('sekretariat.sakramen.pernikahan.index') }}">Pernikahan</a></li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('sekretariat.sakramen.pernikahan.show', $sakramen) }}">Detail</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Edit Pernikahan</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-light-danger color-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $e)
                                        <li>{{ $e }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('sekretariat.sakramen.pernikahan.update', $sakramen) }}">
                            @csrf
                            @method('PUT')
                            <div class="row g-3">

                                {{-- Data Sakramen (parent) --}}
                                <div class="col-12"><small class="fw-bold text-muted">Data Sakramen</small>
                                    <hr class="my-1">
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="umat_id">Umat (Katolik) <span class="text-danger">*</span></label>
                                        <select class="form-select @error('umat_id') is-invalid @enderror" id="umat_id"
                                            name="umat_id">
                                            <option value="">-- Pilih Umat --</option>
                                            @foreach ($umat as $u)
                                                <option value="{{ $u->id }}"
                                                    {{ old('umat_id', $sakramen->umat_id) == $u->id ? 'selected' : '' }}>
                                                    {{ $u->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('umat_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="tanggal_penerimaan">Tanggal Penerimaan <span
                                                class="text-danger">*</span></label>
                                        <input type="date"
                                            class="form-control @error('tanggal_penerimaan') is-invalid @enderror"
                                            id="tanggal_penerimaan" name="tanggal_penerimaan"
                                            value="{{ old('tanggal_penerimaan', $sakramen->tanggal_penerimaan->format('Y-m-d')) }}">
                                        @error('tanggal_penerimaan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="paroki_id">Paroki</label>
                                        <select class="form-select @error('paroki_id') is-invalid @enderror" id="paroki_id"
                                            name="paroki_id">
                                            <option value="">-- Pilih Paroki --</option>
                                            @foreach ($paroki as $p)
                                                <option value="{{ $p->id }}"
                                                    {{ old('paroki_id', $sakramen->paroki_id) == $p->id ? 'selected' : '' }}>
                                                    {{ $p->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('paroki_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="klerus_id">Klerus Pemimpin</label>
                                        <select class="form-select @error('klerus_id') is-invalid @enderror" id="klerus_id"
                                            name="klerus_id">
                                            <option value="">-- Pilih Klerus --</option>
                                            @foreach ($klerus as $k)
                                                <option value="{{ $k->id }}"
                                                    {{ old('klerus_id', $sakramen->klerus_id) == $k->id ? 'selected' : '' }}>
                                                    {{ $k->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('klerus_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="nomor_surat">Nomor Surat</label>
                                        <input type="text"
                                            class="form-control @error('nomor_surat') is-invalid @enderror" id="nomor_surat"
                                            name="nomor_surat" value="{{ old('nomor_surat', $sakramen->nomor_surat) }}">
                                        @error('nomor_surat')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="jenis_pernikahan">Jenis Pernikahan <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select @error('jenis_pernikahan') is-invalid @enderror"
                                            id="jenis_pernikahan" name="jenis_pernikahan">
                                            <option value="">-- Pilih Jenis --</option>
                                            @foreach ($jenisList as $key => $label)
                                                <option value="{{ $key }}"
                                                    {{ old('jenis_pernikahan', $sakramen->pernikahan->jenis_pernikahan) === $key ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('jenis_pernikahan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Pasangan --}}
                                <div class="col-12"><small class="fw-bold text-muted">Data Pasangan</small>
                                    <hr class="my-1">
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="pasangan_id">Pasangan (Umat Terdaftar)</label>
                                        <select class="form-select @error('pasangan_id') is-invalid @enderror"
                                            id="pasangan_id" name="pasangan_id">
                                            <option value="">-- Pilih atau isi manual di bawah --</option>
                                            @foreach ($umat as $u)
                                                <option value="{{ $u->id }}"
                                                    {{ old('pasangan_id', $sakramen->pernikahan->pasangan_id) == $u->id ? 'selected' : '' }}>
                                                    {{ $u->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('pasangan_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="pasangan_nama">Nama Pasangan (Manual)</label>
                                        <input type="text"
                                            class="form-control @error('pasangan_nama') is-invalid @enderror"
                                            id="pasangan_nama" name="pasangan_nama"
                                            value="{{ old('pasangan_nama', $sakramen->pernikahan->pasangan_nama) }}"
                                            placeholder="Isi jika pasangan tidak terdaftar sebagai umat">
                                        @error('pasangan_nama')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="pasangan_agama">Agama Pasangan (Manual)</label>
                                        <input type="text"
                                            class="form-control @error('pasangan_agama') is-invalid @enderror"
                                            id="pasangan_agama" name="pasangan_agama"
                                            value="{{ old('pasangan_agama', $sakramen->pernikahan->pasangan_agama) }}"
                                            placeholder="Isi jika pasangan tidak terdaftar sebagai umat">
                                        @error('pasangan_agama')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Keterangan Tambahan --}}
                                <div class="col-12"><small class="fw-bold text-muted">Keterangan Tambahan</small>
                                    <hr class="my-1">
                                </div>

                                <div class="col-12 col-md-4">
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" id="izin_beda_gereja"
                                            name="izin_beda_gereja" value="1"
                                            {{ old('izin_beda_gereja', $sakramen->pernikahan->izin_beda_gereja) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="izin_beda_gereja">Izin Beda Gereja</label>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" id="dispensasi"
                                            name="dispensasi" value="1"
                                            {{ old('dispensasi', $sakramen->pernikahan->dispensasi) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="dispensasi">Dispensasi</label>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="tanggal_nikah_katolik">Tgl Nikah Katolik</label>
                                        <input type="date" class="form-control" id="tanggal_nikah_katolik"
                                            name="tanggal_nikah_katolik"
                                            value="{{ old('tanggal_nikah_katolik', $sakramen->pernikahan->tanggal_nikah_katolik?->format('Y-m-d')) }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="tanggal_catatan_sipil">Tgl Catatan Sipil</label>
                                        <input type="date" class="form-control" id="tanggal_catatan_sipil"
                                            name="tanggal_catatan_sipil"
                                            value="{{ old('tanggal_catatan_sipil', $sakramen->pernikahan->tanggal_catatan_sipil?->format('Y-m-d')) }}">
                                    </div>
                                </div>

                                <div class="col-12 d-flex justify-content-end">
                                    <a href="{{ route('sekretariat.sakramen.pernikahan.show', $sakramen) }}"
                                        class="btn btn-light-secondary me-1 mb-1">Batal</a>
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
