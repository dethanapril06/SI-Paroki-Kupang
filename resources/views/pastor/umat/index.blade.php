@extends('layouts.pastor')

@section('title', 'Direktori Umat')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Direktori Umat</h3>
                    <p class="text-subtitle text-muted">
                        Daftar seluruh umat aktif Paroki (Read-Only)
                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('pastor.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Direktori Umat</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">

            {{-- Filter --}}
            <div class="card mb-3">
                <div class="card-body py-3">
                    <form method="GET" action="{{ route('pastor.umat.index') }}" class="row g-2 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label mb-1 fw-bold">Cari Nama Umat</label>
                            <input type="text" name="search" class="form-control" placeholder="Ketik nama umat..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label mb-1 fw-bold">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select">
                                <option value="">-- Semua --</option>
                                <option value="Laki-laki" {{ request('jenis_kelamin') === 'Laki-laki' ? 'selected' : '' }}>
                                    Laki-laki</option>
                                <option value="Perempuan" {{ request('jenis_kelamin') === 'Perempuan' ? 'selected' : '' }}>
                                    Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="bi bi-search"></i> Cari Umat
                            </button>
                            <a href="{{ route('pastor.umat.index') }}" class="btn btn-light-secondary">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Tabel --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        Total Terdata: <span class="badge bg-primary">{{ $umat->count() }} umat</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Lengkap</th>
                                    <th>JK</th>
                                    <th>Keluarga & KUB / Wilayah</th>
                                    <th>Hubungan Keluarga</th>
                                    <th>Akun Login</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($umat as $u)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $u->nama }}</strong>
                                            @if ($u->status_almarhum)
                                                <span class="badge bg-dark ms-1">Alm.</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($u->jenis_kelamin === 'Laki-laki')
                                                <i class="bi bi-gender-male text-primary fs-5"></i>
                                            @else
                                                <i class="bi bi-gender-female text-danger fs-5"></i>
                                            @endif
                                        </td>
                                        <td>
                                            <small>
                                                <span class="text-muted">KK:</span> {{ $u->keluarga->kepalaKeluarga->nama ?? '-' }}<br>
                                                <span class="text-muted">KUB:</span> {{ $u->keluarga->kub->nama ?? '-' }} |
                                                {{ $u->keluarga->kub->wilayah->nama ?? '-' }}
                                            </small>
                                        </td>
                                        <td>{{ $u->hubungan_keluarga }}</td>
                                        <td>
                                            @if ($u->user)
                                                <span class="badge bg-light-success text-success"><i class="bi bi-check-circle"></i> Terdaftar</span>
                                            @else
                                                <span class="badge bg-light-secondary text-secondary">Belum ada</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('pastor.umat.show', $u) }}"
                                                class="btn btn-sm btn-outline-primary" title="Detail Profil">
                                                <i class="bi bi-eye-fill"></i> Detail Umat
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            Tidak ditemukan data umat.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </section>
    </div>
@endsection
