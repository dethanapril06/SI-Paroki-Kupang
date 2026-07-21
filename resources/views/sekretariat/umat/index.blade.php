@extends('layouts.sekretariat')

@section('title', 'Daftar Umat')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Daftar Umat</h3>
                    <p class="text-subtitle text-muted">
                        Daftar seluruh umat Paroki
                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('sekretariat.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Daftar Umat</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">

            @if (session('success'))
                <div class="alert alert-light-success color-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Filter --}}
            <div class="card mb-3">
                <div class="card-body py-3">
                    <form method="GET" action="{{ route('sekretariat.umat.index') }}" class="row g-2 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label mb-1">Cari Nama</label>
                            <input type="text" name="search" class="form-control" placeholder="Ketik nama umat..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label mb-1">Jenis Kelamin</label>
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
                                <i class="bi bi-search"></i> Cari
                            </button>
                            <a href="{{ route('sekretariat.umat.index') }}" class="btn btn-light-secondary">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Tabel --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        Total: <span class="badge bg-primary">{{ $umat->count() }} umat</span>
                    </h5>
                    <a href="{{ route('sekretariat.umat.create') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg"></i> Tambah Umat
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="table1">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>JK</th>
                                    <th>Keluarga / KUB / Wilayah</th>
                                    <th>Status</th>
                                    <th>Akun</th>
                                    <th>Aksi</th>
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
                                                <i class="bi bi-gender-male text-primary"></i>
                                            @else
                                                <i class="bi bi-gender-female text-danger"></i>
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
                                                <span class="badge bg-success"><i class="bi bi-check-lg"></i> Ada</span>
                                            @else
                                                <span class="badge bg-secondary">Belum</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('sekretariat.umat.show', $u) }}"
                                                    class="btn btn-sm btn-outline-info" title="Detail">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('sekretariat.umat.edit', $u) }}"
                                                    class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            Belum ada data umat.
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
