@extends('layouts.sekretariat')

@section('title', 'Tambah Baptis')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Tambah Baptis</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sekretariat.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sekretariat.sakramen.index') }}">Sakramen</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sekretariat.sakramen.baptis.index') }}">Baptis</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Tambah Baptis</h4>
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
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form class="form form-vertical" method="POST"
                            action="{{ route('sekretariat.sakramen.baptis.store') }}">
                            @csrf
                            <div class="form-body">
                                <div class="row g-3">

                                    {{-- Pilih Umat --}}
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="umat_id">Nama Umat <span class="text-danger">*</span></label>
                                            <select class="form-select @error('umat_id') is-invalid @enderror"
                                                id="umat_id" name="umat_id">
                                                <option value="">-- Pilih Umat --</option>
                                                @foreach ($umat as $u)
                                                    <option value="{{ $u->id }}"
                                                        {{ old('umat_id') == $u->id ? 'selected' : '' }}>
                                                        {{ $u->nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('umat_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Tanggal Penerimaan --}}
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="tanggal_penerimaan">Tanggal Penerimaan <span class="text-danger">*</span></label>
                                            <input type="date"
                                                class="form-control @error('tanggal_penerimaan') is-invalid @enderror"
                                                id="tanggal_penerimaan" name="tanggal_penerimaan"
                                                value="{{ old('tanggal_penerimaan') }}">
                                            @error('tanggal_penerimaan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Paroki --}}
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="paroki_id">Paroki</label>
                                            <select class="form-select @error('paroki_id') is-invalid @enderror"
                                                id="paroki_id" name="paroki_id">
                                                <option value="">-- Pilih Paroki --</option>
                                                @foreach ($paroki as $p)
                                                    <option value="{{ $p->id }}"
                                                        {{ old('paroki_id') == $p->id ? 'selected' : '' }}>
                                                        {{ $p->nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('paroki_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Nomor Surat --}}
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="nomor_surat">Nomor Surat</label>
                                            <input type="text"
                                                class="form-control @error('nomor_surat') is-invalid @enderror"
                                                id="nomor_surat" name="nomor_surat"
                                                value="{{ old('nomor_surat') }}" placeholder="Opsional">
                                            @error('nomor_surat')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12"><hr class="my-1"></div>

                                    {{-- Sumber Baptis --}}
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="sumber_baptis">Sumber Baptis <span class="text-danger">*</span></label>
                                            <select class="form-select @error('sumber_baptis') is-invalid @enderror"
                                                id="sumber_baptis" name="sumber_baptis" onchange="onSumberChange()">
                                                <option value="">-- Pilih Sumber --</option>
                                                <option value="KATOLIK" {{ old('sumber_baptis') === 'KATOLIK' ? 'selected' : '' }}>Katolik</option>
                                                <option value="PROTESTAN" {{ old('sumber_baptis') === 'PROTESTAN' ? 'selected' : '' }}>Protestan</option>
                                            </select>
                                            @error('sumber_baptis')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Tanggal Baptis --}}
                                    <div class="col-12 col-md-6" id="wrap-tgl-baptis">
                                        <div class="form-group">
                                            <label for="tgl_baptis">Tanggal Baptis <span class="text-danger" id="req-tgl-baptis">*</span></label>
                                            <input type="date"
                                                class="form-control @error('tgl_baptis') is-invalid @enderror"
                                                id="tgl_baptis" name="tgl_baptis" value="{{ old('tgl_baptis') }}">
                                            @error('tgl_baptis')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Nama Baptis --}}
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="nama_baptis">Nama Baptis</label>
                                            <input type="text"
                                                class="form-control @error('nama_baptis') is-invalid @enderror"
                                                id="nama_baptis" name="nama_baptis"
                                                value="{{ old('nama_baptis') }}" placeholder="Opsional">
                                            @error('nama_baptis')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Klerus (Katolik) --}}
                                    <div class="col-12 col-md-6 d-none" id="wrap-klerus">
                                        <div class="form-group">
                                            <label for="baptis_klerus_id">Pemberi Baptis (Klerus) <span class="text-danger">*</span></label>
                                            <select class="form-select @error('baptis_klerus_id') is-invalid @enderror"
                                                id="baptis_klerus_id" name="baptis_klerus_id">
                                                <option value="">-- Pilih Klerus --</option>
                                                @foreach ($klerusKatolik as $k)
                                                    <option value="{{ $k->id }}"
                                                        {{ old('baptis_klerus_id') == $k->id ? 'selected' : '' }}>
                                                        {{ $k->nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('baptis_klerus_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Pemberi Protestan --}}
                                    <div class="col-12 col-md-6 d-none" id="wrap-protestan">
                                        <div class="form-group">
                                            <label for="nama_pemberi_protestan">Nama Pemberi Baptis <span class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('nama_pemberi_protestan') is-invalid @enderror"
                                                id="nama_pemberi_protestan" name="nama_pemberi_protestan"
                                                value="{{ old('nama_pemberi_protestan') }}">
                                            @error('nama_pemberi_protestan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6 d-none" id="wrap-gereja">
                                        <div class="form-group">
                                            <label for="nama_gereja_protestan">Nama Gereja <span class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('nama_gereja_protestan') is-invalid @enderror"
                                                id="nama_gereja_protestan" name="nama_gereja_protestan"
                                                value="{{ old('nama_gereja_protestan') }}">
                                            @error('nama_gereja_protestan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6 d-none" id="wrap-tgl-katolik">
                                        <div class="form-group">
                                            <label for="tgl_diterima_katolik">Tanggal Diterima Katolik <span class="text-danger">*</span></label>
                                            <input type="date"
                                                class="form-control @error('tgl_diterima_katolik') is-invalid @enderror"
                                                id="tgl_diterima_katolik" name="tgl_diterima_katolik"
                                                value="{{ old('tgl_diterima_katolik') }}">
                                            @error('tgl_diterima_katolik')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12"><hr class="my-1"><small class="text-muted fw-bold">Wali Baptis (minimal salah satu)</small></div>

                                    {{-- Bapak Baptis --}}
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="bapak_baptis_id">Bapak Baptis (Umat Terdaftar)</label>
                                            <select class="form-select @error('bapak_baptis_id') is-invalid @enderror"
                                                id="bapak_baptis_id" name="bapak_baptis_id">
                                                <option value="">-- Pilih atau isi manual --</option>
                                                @foreach ($umatWali as $u)
                                                    <option value="{{ $u->id }}"
                                                        {{ old('bapak_baptis_id') == $u->id ? 'selected' : '' }}>
                                                        {{ $u->nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="bapak_baptis_nama">Bapak Baptis (Manual)</label>
                                            <input type="text"
                                                class="form-control @error('bapak_baptis_nama') is-invalid @enderror"
                                                id="bapak_baptis_nama" name="bapak_baptis_nama"
                                                value="{{ old('bapak_baptis_nama') }}" placeholder="Jika tidak terdaftar">
                                        </div>
                                    </div>

                                    {{-- Ibu Baptis --}}
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="ibu_baptis_id">Ibu Baptis (Umat Terdaftar)</label>
                                            <select class="form-select @error('ibu_baptis_id') is-invalid @enderror"
                                                id="ibu_baptis_id" name="ibu_baptis_id">
                                                <option value="">-- Pilih atau isi manual --</option>
                                                @foreach ($umatWali as $u)
                                                    <option value="{{ $u->id }}"
                                                        {{ old('ibu_baptis_id') == $u->id ? 'selected' : '' }}>
                                                        {{ $u->nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="ibu_baptis_nama">Ibu Baptis (Manual)</label>
                                            <input type="text"
                                                class="form-control @error('ibu_baptis_nama') is-invalid @enderror"
                                                id="ibu_baptis_nama" name="ibu_baptis_nama"
                                                value="{{ old('ibu_baptis_nama') }}" placeholder="Jika tidak terdaftar">
                                        </div>
                                    </div>

                                    <div class="col-12 d-flex justify-content-end">
                                        <a href="{{ route('sekretariat.sakramen.baptis.index') }}"
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
            function onSumberChange() {
                const sumber = document.getElementById('sumber_baptis').value;
                document.getElementById('wrap-klerus').classList.toggle('d-none', sumber !== 'KATOLIK');
                document.getElementById('wrap-protestan').classList.toggle('d-none', sumber !== 'PROTESTAN');
                document.getElementById('wrap-gereja').classList.toggle('d-none', sumber !== 'PROTESTAN');
                document.getElementById('wrap-tgl-katolik').classList.toggle('d-none', sumber !== 'PROTESTAN');
                document.getElementById('wrap-tgl-baptis').classList.toggle('d-none', sumber !== 'PROTESTAN');

                const reqAsterisk = document.getElementById('req-tgl-baptis');
                if (reqAsterisk) {
                    reqAsterisk.style.display = (sumber === 'PROTESTAN') ? 'inline' : 'none';
                }
            }
            document.addEventListener('DOMContentLoaded', onSumberChange);
        </script>
    @endpush
@endsection
