@extends('layouts.pastor')

@section('title', 'Detail Umat')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Detail Umat</h3>
                    <p class="text-subtitle text-muted">Informasi profil lengkap umat</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('pastor.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('pastor.umat.index') }}">Daftar Umat</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $umat->nama }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">

                {{-- Kolom Kiri: Profil Pribadi --}}
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Profil Umat</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th width="140">Nama Lengkap</th>
                                    <td>
                                        <strong>{{ $umat->nama }}</strong>
                                        @if ($umat->status_almarhum)
                                            <span class="badge bg-dark ms-1">Almarhum</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Jenis Kelamin</th>
                                    <td>{{ $umat->jenis_kelamin }}</td>
                                </tr>
                                <tr>
                                    <th>Tempat Lahir</th>
                                    <td>{{ $umat->tempat_lahir }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Lahir</th>
                                    <td>{{ $umat->tanggal_lahir->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Golongan Darah</th>
                                    <td>{{ $umat->golongan_darah ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Hubungan KK</th>
                                    <td>{{ $umat->hubungan_keluarga }}</td>
                                </tr>
                                <tr>
                                    <th>Status Nikah</th>
                                    <td>{{ $umat->status_pernikahan }}</td>
                                </tr>
                                <tr>
                                    <th>No. Telepon</th>
                                    <td>{{ $umat->no_telepon ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Pendidikan</th>
                                    <td>{{ $umat->pendidikan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Pekerjaan</th>
                                    <td>{{ $umat->pekerjaan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Nama Ayah</th>
                                    <td>{{ $umat->nama_ayah ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Nama Ibu</th>
                                    <td>{{ $umat->nama_ibu ?? '-' }}</td>
                                </tr>
                                @if ($umat->penyandang_disabilitas)
                                    <tr>
                                        <td colspan="2">
                                            <span class="badge bg-info">Penyandang Disabilitas</span>
                                        </td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan: Struktural & Organisasi --}}
                <div class="col-md-7">

                    {{-- Info Struktural --}}
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Informasi Struktural & Alamat</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th width="120">Keluarga</th>
                                    <td>
                                        @if ($umat->keluarga)
                                            <a href="{{ route('pastor.keluarga.show', $umat->keluarga) }}">
                                                {{ $umat->keluarga->kepalaKeluarga->nama ?? '-' }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Alamat</th>
                                    <td>{{ $umat->keluarga->alamat ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>KUB</th>
                                    <td>{{ $umat->keluarga->kub->nama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Wilayah</th>
                                    <td>{{ $umat->keluarga->kub->wilayah->nama ?? '-' }}</td>
                                </tr>
                            </table>

                            @if ($umat->kubDiketuai->isNotEmpty())
                                <div class="alert alert-light-success color-success py-2 mt-2 mb-0">
                                    <i class="bi bi-star-fill me-1"></i>
                                    Ketua KUB: <strong>{{ $umat->kubDiketuai->pluck('nama')->join(', ') }}</strong>
                                </div>
                            @endif
                            @if ($umat->kategorialDiketuai->isNotEmpty())
                                <div class="alert alert-light-info color-info py-2 mt-2 mb-0">
                                    <i class="bi bi-star-fill me-1"></i>
                                    Ketua Kategorial:
                                    <strong>{{ $umat->kategorialDiketuai->pluck('nama')->join(', ') }}</strong>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Akun Login --}}
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Akun Pengguna</h5>
                        </div>
                        <div class="card-body">
                            @if ($umat->user)
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <th width="120">Email</th>
                                        <td>{{ $umat->user->email }}</td>
                                    </tr>
                                    <tr>
                                        <th>Role</th>
                                        <td>
                                            {{ $umat->user->roles->pluck('label')->join(' · ') }}
                                        </td>
                                    </tr>
                                </table>
                            @else
                                <p class="text-muted mb-0">
                                    <i class="bi bi-exclamation-circle me-1"></i>
                                    Umat belum melakukan registrasi akun login.
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Keanggotaan Kategorial --}}
                    @if ($umat->kategorial->isNotEmpty())
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Keanggotaan Kategorial</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nama Kategorial</th>
                                                <th>Jabatan</th>
                                                <th>Bidang Tugas</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($umat->kategorial as $kat)
                                                <tr>
                                                    <td>{{ $kat->nama }}</td>
                                                    <td>{{ $kat->pivot->jabatan }}</td>
                                                    <td>{{ $kat->pivot->bidang_tugas ?? '-' }}</td>
                                                    <td>
                                                        <span
                                                            class="badge {{ $kat->pivot->status === 'Aktif' ? 'bg-success' : 'bg-secondary' }}">
                                                            {{ $kat->pivot->status }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </section>
    </div>
@endsection
