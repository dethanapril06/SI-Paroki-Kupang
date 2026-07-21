@extends('layouts.portal')

@section('title', 'Edit Data Umat')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Edit Data Umat</h3>
                    <p class="text-muted">{{ $umat->nama }}</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('portal.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('portal.umat.index') }}">Umat</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('portal.umat.show', $umat) }}">{{ $umat->nama }}</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-9">

                    <div class="alert alert-light-info color-info mb-3 d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-info-circle-fill me-2"></i>
                            Untuk perubahan <strong>Keluarga, KUB, atau Wilayah</strong>, gunakan fitur pengajuan mutasi.
                        </div>
                        <a href="{{ route('portal.mutasi.umat-kub.create', ['umat_id' => $umat->id]) }}" class="btn btn-sm btn-outline-info text-nowrap ms-2">
                            <i class="bi bi-arrow-left-right me-1"></i>Ajukan Mutasi
                        </a>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-light-danger color-danger alert-dismissible fade show mb-3">
                            <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="bi bi-person-fill me-2 text-primary"></i>Data Pribadi</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('portal.umat.update', $umat) }}" method="POST">
                                @csrf
                                @method('PUT')

                                {{-- Identitas Dasar --}}
                                <h6 class="fw-bold text-muted mb-3 border-bottom pb-2">Identitas Dasar</h6>
                                <div class="row g-3 mb-4">
                                    <div class="col-12">
                                        <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $umat->nama) }}">
                                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="tempat_lahir" class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                                        <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control @error('tempat_lahir') is-invalid @enderror" value="{{ old('tempat_lahir', $umat->tempat_lahir) }}">
                                        @error('tempat_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="tanggal_lahir" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                        <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror" value="{{ old('tanggal_lahir', $umat->tanggal_lahir?->format('Y-m-d')) }}">
                                        @error('tanggal_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                        <select name="jenis_kelamin" id="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror">
                                            <option value="Laki-laki" {{ old('jenis_kelamin', $umat->jenis_kelamin) === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="Perempuan" {{ old('jenis_kelamin', $umat->jenis_kelamin) === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                        @error('jenis_kelamin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="hubungan_keluarga" class="form-label">Hubungan dalam KK <span class="text-danger">*</span></label>
                                        <select name="hubungan_keluarga" id="hubungan_keluarga" class="form-select @error('hubungan_keluarga') is-invalid @enderror">
                                            @foreach (['Suami','Istri','Anak','Saudara','Ayah','Ibu','Lainnya'] as $h)
                                                <option value="{{ $h }}" {{ old('hubungan_keluarga', $umat->hubungan_keluarga) === $h ? 'selected' : '' }}>{{ $h }}</option>
                                            @endforeach
                                        </select>
                                        @error('hubungan_keluarga')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="golongan_darah" class="form-label">Golongan Darah</label>
                                        <select name="golongan_darah" id="golongan_darah" class="form-select">
                                            <option value="">-- Pilih --</option>
                                            @foreach (['A','B','AB','O'] as $gd)
                                                <option value="{{ $gd }}" {{ old('golongan_darah', $umat->golongan_darah) === $gd ? 'selected' : '' }}>{{ $gd }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="nama_ayah" class="form-label">Nama Ayah</label>
                                        <input type="text" name="nama_ayah" id="nama_ayah" class="form-control" value="{{ old('nama_ayah', $umat->nama_ayah) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="nama_ibu" class="form-label">Nama Ibu</label>
                                        <input type="text" name="nama_ibu" id="nama_ibu" class="form-control" value="{{ old('nama_ibu', $umat->nama_ibu) }}">
                                    </div>
                                </div>

                                {{-- Kontak & Sosial --}}
                                <h6 class="fw-bold text-muted mb-3 border-bottom pb-2">Kontak & Sosial</h6>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label for="status_pernikahan" class="form-label">Status Pernikahan <span class="text-danger">*</span></label>
                                        <select name="status_pernikahan" id="status_pernikahan" class="form-select @error('status_pernikahan') is-invalid @enderror">
                                            @foreach (['Belum Kawin','Kawin','Cerai Hidup','Cerai Mati'] as $s)
                                                <option value="{{ $s }}" {{ old('status_pernikahan', $umat->status_pernikahan) === $s ? 'selected' : '' }}>{{ $s }}</option>
                                            @endforeach
                                        </select>
                                        @error('status_pernikahan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="no_telepon" class="form-label">No. Telepon</label>
                                        <input type="text" name="no_telepon" id="no_telepon" class="form-control" value="{{ old('no_telepon', $umat->no_telepon) }}" placeholder="08xx-xxxx-xxxx">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="pekerjaan" class="form-label">Pekerjaan</label>
                                        <input type="text" name="pekerjaan" id="pekerjaan" class="form-control" value="{{ old('pekerjaan', $umat->pekerjaan) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="pendidikan" class="form-label">Pendidikan Terakhir</label>
                                        <select name="pendidikan" id="pendidikan" class="form-select">
                                            <option value="">-- Pilih --</option>
                                            @foreach (['Tidak Sekolah','SD','SMP','SMA','D3','S1','S2','S3'] as $p)
                                                <option value="{{ $p }}" {{ old('pendidikan', $umat->pendidikan) === $p ? 'selected' : '' }}>{{ $p }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="penyandang_disabilitas" id="penyandang_disabilitas" value="1" {{ old('penyandang_disabilitas', $umat->penyandang_disabilitas) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="penyandang_disabilitas">Penyandang Disabilitas</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('portal.umat.show', $umat) }}" class="btn btn-light-secondary">Batal</a>
                                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
