@extends('layouts.portal')

@section('title', 'Tambah Anggota Keluarga')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Tambah Anggota Keluarga</h3>
                    <p class="text-muted">Daftarkan anggota baru dalam keluarga Anda. Email bersifat opsional untuk bayi/anak kecil.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('portal.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('portal.keluarga-saya.show') }}">Keluarga Saya</a>
                            </li>
                            <li class="breadcrumb-item active">Tambah Anggota</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-9">

                    {{-- Info keluarga --}}
                    <div class="alert alert-light-success color-success mb-3">
                        <i class="bi bi-house-fill me-2"></i>
                        Menambahkan anggota ke keluarga:
                        <strong>{{ $keluarga->kepalaKeluarga?->nama ?? '-' }}</strong>
                        @if ($keluarga->alamat)
                            — <span class="text-muted">{{ $keluarga->alamat }}</span>
                        @endif
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-person-plus-fill me-2 text-success"></i>Form Anggota Baru
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('portal.keluarga-saya.anggota.store') }}" method="POST">
                                @csrf

                                @if ($errors->any())
                                    <div class="alert alert-light-danger color-danger mb-3">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                {{-- ── IDENTITAS ── --}}
                                <h6 class="fw-bold text-muted mb-3 border-bottom pb-2">Identitas</h6>
                                <div class="row g-3 mb-4">

                                    <div class="col-md-12">
                                        <label for="nama" class="form-label">
                                            Nama Lengkap <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="nama" id="nama"
                                            class="form-control @error('nama') is-invalid @enderror"
                                            value="{{ old('nama') }}" placeholder="Sesuai akta/KTP">
                                        @error('nama')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="tempat_lahir" class="form-label">
                                            Tempat Lahir <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="tempat_lahir" id="tempat_lahir"
                                            class="form-control @error('tempat_lahir') is-invalid @enderror"
                                            value="{{ old('tempat_lahir') }}">
                                        @error('tempat_lahir')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="tanggal_lahir" class="form-label">
                                            Tanggal Lahir <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" name="tanggal_lahir" id="tanggal_lahir"
                                            class="form-control @error('tanggal_lahir') is-invalid @enderror"
                                            value="{{ old('tanggal_lahir') }}">
                                        @error('tanggal_lahir')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="jenis_kelamin" class="form-label">
                                            Jenis Kelamin <span class="text-danger">*</span>
                                        </label>
                                        <select name="jenis_kelamin" id="jenis_kelamin"
                                            class="form-select @error('jenis_kelamin') is-invalid @enderror">
                                            <option value="">-- Pilih --</option>
                                            <option value="Laki-laki" {{ old('jenis_kelamin') === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="Perempuan" {{ old('jenis_kelamin') === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                        @error('jenis_kelamin')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="hubungan_keluarga" class="form-label">
                                            Hubungan dalam KK <span class="text-danger">*</span>
                                        </label>
                                        <select name="hubungan_keluarga" id="hubungan_keluarga"
                                            class="form-select @error('hubungan_keluarga') is-invalid @enderror">
                                            <option value="">-- Pilih --</option>
                                            @foreach (['Suami', 'Istri', 'Anak', 'Saudara', 'Ayah', 'Ibu', 'Lainnya'] as $h)
                                                <option value="{{ $h }}" {{ old('hubungan_keluarga') === $h ? 'selected' : '' }}>{{ $h }}</option>
                                            @endforeach
                                        </select>
                                        @error('hubungan_keluarga')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="nama_ayah" class="form-label">Nama Ayah</label>
                                        <input type="text" name="nama_ayah" id="nama_ayah"
                                            class="form-control @error('nama_ayah') is-invalid @enderror"
                                            value="{{ old('nama_ayah') }}">
                                        @error('nama_ayah')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="nama_ibu" class="form-label">Nama Ibu</label>
                                        <input type="text" name="nama_ibu" id="nama_ibu"
                                            class="form-control @error('nama_ibu') is-invalid @enderror"
                                            value="{{ old('nama_ibu') }}">
                                        @error('nama_ibu')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- ── INFO TAMBAHAN ── --}}
                                <h6 class="fw-bold text-muted mb-3 border-bottom pb-2">Info Tambahan</h6>
                                <div class="row g-3 mb-4">

                                    <div class="col-md-6">
                                        <label for="status_pernikahan" class="form-label">
                                            Status Pernikahan <span class="text-danger">*</span>
                                        </label>
                                        <select name="status_pernikahan" id="status_pernikahan"
                                            class="form-select @error('status_pernikahan') is-invalid @enderror">
                                            <option value="">-- Pilih --</option>
                                            @foreach (['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'] as $s)
                                                <option value="{{ $s }}" {{ old('status_pernikahan') === $s ? 'selected' : '' }}>{{ $s }}</option>
                                            @endforeach
                                        </select>
                                        @error('status_pernikahan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="no_telepon" class="form-label">No. Telepon</label>
                                        <input type="text" name="no_telepon" id="no_telepon"
                                            class="form-control @error('no_telepon') is-invalid @enderror"
                                            value="{{ old('no_telepon') }}" placeholder="08xx-xxxx-xxxx">
                                        @error('no_telepon')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="pekerjaan" class="form-label">Pekerjaan</label>
                                        <input type="text" name="pekerjaan" id="pekerjaan"
                                            class="form-control @error('pekerjaan') is-invalid @enderror"
                                            value="{{ old('pekerjaan') }}">
                                        @error('pekerjaan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="pendidikan" class="form-label">Pendidikan Terakhir</label>
                                        <select name="pendidikan" id="pendidikan"
                                            class="form-select @error('pendidikan') is-invalid @enderror">
                                            <option value="">-- Pilih --</option>
                                            @foreach (['Tidak Sekolah', 'SD', 'SMP', 'SMA', 'D3', 'S1', 'S2', 'S3'] as $p)
                                                <option value="{{ $p }}" {{ old('pendidikan') === $p ? 'selected' : '' }}>{{ $p }}</option>
                                            @endforeach
                                        </select>
                                        @error('pendidikan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="golongan_darah" class="form-label">Golongan Darah</label>
                                        <select name="golongan_darah" id="golongan_darah"
                                            class="form-select @error('golongan_darah') is-invalid @enderror">
                                            <option value="">-- Pilih --</option>
                                            @foreach (['A', 'B', 'AB', 'O'] as $gd)
                                                <option value="{{ $gd }}" {{ old('golongan_darah') === $gd ? 'selected' : '' }}>{{ $gd }}</option>
                                            @endforeach
                                        </select>
                                        @error('golongan_darah')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="penyandang_disabilitas" id="penyandang_disabilitas" value="1"
                                                {{ old('penyandang_disabilitas') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="penyandang_disabilitas">
                                                Penyandang Disabilitas
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                {{-- ── AKUN LOGIN ── --}}
                                <h6 class="fw-bold text-muted mb-3 border-bottom pb-2">Akun Login <span class="badge bg-light-secondary text-secondary fw-normal">Opsional</span></h6>
                                <div class="alert alert-light-info color-info py-2 mb-3">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    <strong>Untuk bayi atau anak kecil</strong>, biarkan email kosong — data tetap tersimpan tanpa akun login.
                                    Akun dapat ditambahkan nanti jika sudah diperlukan.
                                </div>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-8">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" name="email" id="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            value="{{ old('email') }}" placeholder="Kosongkan jika tidak memiliki email">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text text-muted">
                                            <i class="bi bi-key me-1"></i>
                                            Jika email diisi, password default: <code>password</code> (bisa diubah setelah login)
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('portal.dashboard') }}"
                                        class="btn btn-light-secondary">Batal</a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-person-plus me-1"></i>Tambahkan Anggota
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
