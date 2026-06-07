@extends('layouts.portal')

@section('title', 'Tambah Umat')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Tambah Umat Baru</h3>
                    <p class="text-muted">KUB: <strong>{{ $myKub->nama }}</strong></p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('portal.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('portal.umat.index') }}">Umat</a></li>
                            <li class="breadcrumb-item active">Tambah</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-10">

                    @if ($errors->any())
                        <div class="alert alert-light-danger color-danger alert-dismissible fade show mb-3">
                            <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('portal.umat.store') }}" method="POST">
                        @csrf

                        {{-- Pilih Keluarga --}}
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><i class="bi bi-house-fill me-2 text-success"></i>Pilih Keluarga</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-8">
                                        <label for="keluarga_id" class="form-label">Keluarga <span class="text-danger">*</span></label>
                                        <select name="keluarga_id" id="keluarga_id" class="form-select @error('keluarga_id') is-invalid @enderror">
                                            <option value="">-- Pilih Keluarga dalam KUB --</option>
                                            @foreach ($keluargaList as $k)
                                                <option value="{{ $k->id }}" {{ old('keluarga_id') == $k->id ? 'selected' : '' }}>
                                                    {{ $k->kepalaKeluarga?->nama ?? '(Belum ada KK)' }} — {{ $k->alamat }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('keluarga_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="hubungan_keluarga" class="form-label">Hubungan dalam KK <span class="text-danger">*</span></label>
                                        <select name="hubungan_keluarga" id="hubungan_keluarga" class="form-select @error('hubungan_keluarga') is-invalid @enderror">
                                            <option value="">-- Pilih --</option>
                                            @foreach (['Suami','Istri','Anak','Saudara','Ayah','Ibu','Lainnya'] as $h)
                                                <option value="{{ $h }}" {{ old('hubungan_keluarga') === $h ? 'selected' : '' }}>{{ $h }}</option>
                                            @endforeach
                                        </select>
                                        @error('hubungan_keluarga')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Identitas --}}
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><i class="bi bi-person-fill me-2 text-primary"></i>Identitas</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}">
                                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="tempat_lahir" class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                                        <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control @error('tempat_lahir') is-invalid @enderror" value="{{ old('tempat_lahir') }}">
                                        @error('tempat_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="tanggal_lahir" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                        <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror" value="{{ old('tanggal_lahir') }}">
                                        @error('tanggal_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                        <select name="jenis_kelamin" id="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror">
                                            <option value="">-- Pilih --</option>
                                            <option value="Laki-laki" {{ old('jenis_kelamin') === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="Perempuan" {{ old('jenis_kelamin') === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                        @error('jenis_kelamin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="golongan_darah" class="form-label">Golongan Darah</label>
                                        <select name="golongan_darah" id="golongan_darah" class="form-select">
                                            <option value="">-- Pilih --</option>
                                            @foreach (['A','B','AB','O'] as $gd)
                                                <option value="{{ $gd }}" {{ old('golongan_darah') === $gd ? 'selected' : '' }}>{{ $gd }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="status_pernikahan" class="form-label">Status Pernikahan <span class="text-danger">*</span></label>
                                        <select name="status_pernikahan" id="status_pernikahan" class="form-select @error('status_pernikahan') is-invalid @enderror">
                                            <option value="">-- Pilih --</option>
                                            @foreach (['Belum Kawin','Kawin','Cerai Hidup','Cerai Mati'] as $s)
                                                <option value="{{ $s }}" {{ old('status_pernikahan') === $s ? 'selected' : '' }}>{{ $s }}</option>
                                            @endforeach
                                        </select>
                                        @error('status_pernikahan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="nama_ayah" class="form-label">Nama Ayah</label>
                                        <input type="text" name="nama_ayah" id="nama_ayah" class="form-control" value="{{ old('nama_ayah') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="nama_ibu" class="form-label">Nama Ibu</label>
                                        <input type="text" name="nama_ibu" id="nama_ibu" class="form-control" value="{{ old('nama_ibu') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="no_telepon" class="form-label">No. Telepon</label>
                                        <input type="text" name="no_telepon" id="no_telepon" class="form-control" value="{{ old('no_telepon') }}" placeholder="08xx-xxxx-xxxx">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="pekerjaan" class="form-label">Pekerjaan</label>
                                        <input type="text" name="pekerjaan" id="pekerjaan" class="form-control" value="{{ old('pekerjaan') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="pendidikan" class="form-label">Pendidikan Terakhir</label>
                                        <select name="pendidikan" id="pendidikan" class="form-select">
                                            <option value="">-- Pilih --</option>
                                            @foreach (['Tidak Sekolah','SD','SMP','SMA','D3','S1','S2','S3'] as $p)
                                                <option value="{{ $p }}" {{ old('pendidikan') === $p ? 'selected' : '' }}>{{ $p }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="penyandang_disabilitas" id="penyandang_disabilitas" value="1" {{ old('penyandang_disabilitas') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="penyandang_disabilitas">Penyandang Disabilitas</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Akun Login --}}
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><i class="bi bi-key-fill me-2 text-warning"></i>Akun Login</h5>
                            </div>
                            <div class="card-body">
                                <div class="col-md-8">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="email@contoh.com">
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <div class="form-text"><i class="bi bi-info-circle me-1"></i>Password default: <code>password</code></div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('portal.umat.index') }}" class="btn btn-light-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i>Simpan Umat</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
