@extends('layouts.sekretariat')

@section('title', 'Tambah Umat')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Tambah Umat</h3>
                    @isset($keluarga)
                        <p class="text-muted mb-0">
                            KUB: <strong>{{ $keluarga->kub->nama ?? '-' }}</strong> &mdash;
                            Wilayah: <strong>{{ $keluarga->kub->wilayah->nama ?? '-' }}</strong>
                        </p>
                    @endisset
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('sekretariat.dashboard') }}">Dashboard</a>
                            </li>
                            @isset($keluarga)
                                <li class="breadcrumb-item">
                                    <a href="{{ route('sekretariat.keluarga.index') }}">Daftar Keluarga</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('sekretariat.keluarga.show', $keluarga) }}">Detail Keluarga</a>
                                </li>
                            @else
                                <li class="breadcrumb-item">
                                    <a href="{{ route('sekretariat.umat.index') }}">Daftar Umat</a>
                                </li>
                            @endisset
                            <li class="breadcrumb-item active" aria-current="page">Tambah Umat</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Tambah Umat</h4>
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

                        {{-- Tentukan action form berdasarkan mode --}}
                        @isset($keluarga)
                            <form class="form form-vertical" method="POST"
                                action="{{ route('sekretariat.keluarga.umat.store', $keluarga) }}">
                            @else
                                <form class="form form-vertical" method="POST" action="{{ route('sekretariat.umat.store') }}">
                                @endisset
                                @csrf

                                {{-- ===== PILIH KELUARGA (hanya di mode standalone) ===== --}}
                                @isset($keluargaList)
                                    <h6 class="text-muted fw-bold text-uppercase mb-3 mt-2">Keluarga</h6>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="keluarga_id">Keluarga <span class="text-danger">*</span></label>
                                                <select class="form-select @error('keluarga_id') is-invalid @enderror"
                                                    id="keluarga_id" name="keluarga_id">
                                                    <option value="">-- Pilih Keluarga --</option>
                                                    @foreach ($keluargaList as $kel)
                                                        <option value="{{ $kel->id }}"
                                                            {{ old('keluarga_id') == $kel->id ? 'selected' : '' }}>
                                                            {{ $kel->kepalaKeluarga?->nama ?? 'Nama Kepala Keluarga Tidak Diketahui' }}
                                                            (KUB: {{ $kel->kub->nama ?? '-' }},
                                                            Wilayah: {{ $kel->kub->wilayah->nama ?? '-' }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('keluarga_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                @endisset

                                {{-- ===== DATA PRIBADI ===== --}}
                                <h6 class="text-muted fw-bold text-uppercase mb-3 mt-2">Data Pribadi</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nama">Nama Lengkap <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                                id="nama" name="nama" placeholder="Masukkan nama lengkap"
                                                value="{{ old('nama') }}">
                                            @error('nama')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="hubungan_keluarga">Hubungan Keluarga <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select @error('hubungan_keluarga') is-invalid @enderror"
                                                id="hubungan_keluarga" name="hubungan_keluarga">
                                                <option value="">-- Pilih --</option>
                                                @foreach (['Suami', 'Istri', 'Anak', 'Saudara', 'Ayah', 'Ibu', 'Lainnya'] as $hub)
                                                    <option value="{{ $hub }}"
                                                        {{ old('hubungan_keluarga') === $hub ? 'selected' : '' }}>
                                                        {{ $hub }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('hubungan_keluarga')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="jenis_kelamin">Jenis Kelamin <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select @error('jenis_kelamin') is-invalid @enderror"
                                                id="jenis_kelamin" name="jenis_kelamin">
                                                <option value="">-- Pilih --</option>
                                                <option value="Laki-laki"
                                                    {{ old('jenis_kelamin') === 'Laki-laki' ? 'selected' : '' }}>Laki-laki
                                                </option>
                                                <option value="Perempuan"
                                                    {{ old('jenis_kelamin') === 'Perempuan' ? 'selected' : '' }}>Perempuan
                                                </option>
                                            </select>
                                            @error('jenis_kelamin')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tempat_lahir">Tempat Lahir <span
                                                    class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('tempat_lahir') is-invalid @enderror"
                                                id="tempat_lahir" name="tempat_lahir"
                                                placeholder="Kota/kabupaten tempat lahir"
                                                value="{{ old('tempat_lahir') }}">
                                            @error('tempat_lahir')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tanggal_lahir">Tanggal Lahir <span
                                                    class="text-danger">*</span></label>
                                            <input type="date"
                                                class="form-control @error('tanggal_lahir') is-invalid @enderror"
                                                id="tanggal_lahir" name="tanggal_lahir"
                                                value="{{ old('tanggal_lahir') }}">
                                            @error('tanggal_lahir')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="golongan_darah">Golongan Darah</label>
                                            <select class="form-select @error('golongan_darah') is-invalid @enderror"
                                                id="golongan_darah" name="golongan_darah">
                                                <option value="">-- Pilih (opsional) --</option>
                                                @foreach (['A', 'B', 'AB', 'O'] as $gol)
                                                    <option value="{{ $gol }}"
                                                        {{ old('golongan_darah') === $gol ? 'selected' : '' }}>
                                                        {{ $gol }}</option>
                                                @endforeach
                                            </select>
                                            @error('golongan_darah')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- ===== DATA ORANG TUA ===== --}}
                                <hr>
                                <h6 class="text-muted fw-bold text-uppercase mb-3">Data Orang Tua</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nama_ayah">Nama Ayah</label>
                                            <input type="text"
                                                class="form-control @error('nama_ayah') is-invalid @enderror"
                                                id="nama_ayah" name="nama_ayah" placeholder="Nama ayah kandung"
                                                value="{{ old('nama_ayah') }}">
                                            @error('nama_ayah')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nama_ibu">Nama Ibu</label>
                                            <input type="text"
                                                class="form-control @error('nama_ibu') is-invalid @enderror"
                                                id="nama_ibu" name="nama_ibu" placeholder="Nama ibu kandung"
                                                value="{{ old('nama_ibu') }}">
                                            @error('nama_ibu')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- ===== DATA TAMBAHAN ===== --}}
                                <hr>
                                <h6 class="text-muted fw-bold text-uppercase mb-3">Data Tambahan</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="status_pernikahan">Status Pernikahan <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select @error('status_pernikahan') is-invalid @enderror"
                                                id="status_pernikahan" name="status_pernikahan">
                                                <option value="">-- Pilih --</option>
                                                @foreach (['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'] as $sp)
                                                    <option value="{{ $sp }}"
                                                        {{ old('status_pernikahan') === $sp ? 'selected' : '' }}>
                                                        {{ $sp }}</option>
                                                @endforeach
                                            </select>
                                            @error('status_pernikahan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="no_telepon">No. Telepon</label>
                                            <input type="text"
                                                class="form-control @error('no_telepon') is-invalid @enderror"
                                                id="no_telepon" name="no_telepon" placeholder="Contoh: 081234567890"
                                                value="{{ old('no_telepon') }}">
                                            @error('no_telepon')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="pendidikan">Pendidikan Terakhir</label>
                                            <select class="form-select @error('pendidikan') is-invalid @enderror"
                                                id="pendidikan" name="pendidikan">
                                                <option value="">-- Pilih (opsional) --</option>
                                                @foreach (['Tidak Sekolah', 'SD', 'SMP', 'SMA', 'D3', 'S1', 'S2', 'S3'] as $pend)
                                                    <option value="{{ $pend }}"
                                                        {{ old('pendidikan') === $pend ? 'selected' : '' }}>
                                                        {{ $pend }}</option>
                                                @endforeach
                                            </select>
                                            @error('pendidikan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="pekerjaan">Pekerjaan</label>
                                            <input type="text"
                                                class="form-control @error('pekerjaan') is-invalid @enderror"
                                                id="pekerjaan" name="pekerjaan" placeholder="Pekerjaan saat ini"
                                                value="{{ old('pekerjaan') }}">
                                            @error('pekerjaan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- ===== STATUS ===== --}}
                                <hr>
                                <h6 class="text-muted fw-bold text-uppercase mb-3">Status Khusus</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="penyandang_disabilitas"
                                                name="penyandang_disabilitas" value="1"
                                                {{ old('penyandang_disabilitas') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="penyandang_disabilitas">Penyandang
                                                Disabilitas</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="status_almarhum"
                                                name="status_almarhum" value="1"
                                                {{ old('status_almarhum') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="status_almarhum">Sudah Meninggal
                                                (Almarhum)</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="keterangan_lain"
                                                name="keterangan_lain" value="1"
                                                {{ old('keterangan_lain') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="keterangan_lain">Keterangan Lain</label>
                                        </div>
                                    </div>
                                </div>

                                {{-- ===== AKUN LOGIN ===== --}}
                                <hr>
                                <h6 class="text-muted fw-bold text-uppercase mb-3">Akun Login <span class="badge bg-light-danger text-danger fw-normal text-lowercase">Wajib</span></h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email"
                                                class="form-control @error('email') is-invalid @enderror" id="email"
                                                name="email" placeholder="Kosongkan jika tidak memiliki email"
                                                value="{{ old('email') }}">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                <i class="bi bi-key me-1"></i>Jika diisi, password default: <strong>password</strong>
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                {{-- ===== TOMBOL ===== --}}
                                <div class="row mt-3">
                                    <div class="col-12 d-flex justify-content-end">
                                        @isset($keluarga)
                                            <a href="{{ route('sekretariat.keluarga.show', $keluarga) }}"
                                                class="btn btn-light-secondary me-1 mb-1">Batal</a>
                                        @else
                                            <a href="{{ route('sekretariat.umat.index') }}"
                                                class="btn btn-light-secondary me-1 mb-1">Batal</a>
                                        @endisset
                                        <button type="submit" class="btn btn-primary me-1 mb-1">Simpan Umat</button>
                                    </div>
                                </div>

                            </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
