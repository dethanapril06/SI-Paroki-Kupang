@extends('layouts.sekretariat')

@section('title', 'Kelola Pengguna')

@push('styles')
    <link rel="stylesheet" href="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.css') }}">
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Kelola Pengguna</h3>
                    <p class="text-muted">Kelola akun login seluruh pengguna sistem.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('sekretariat.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Kelola Pengguna</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">

            @if (session('success'))
                <div class="alert alert-light-success color-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Filter & Pencarian --}}
            <div class="card mb-3">
                <div class="card-body py-3">
                    <form method="GET" action="{{ route('sekretariat.pengguna.index') }}" class="row g-2 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label mb-1">Cari Nama / Email</label>
                            <input type="text" name="search" class="form-control"
                                placeholder="Ketik nama atau email..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label mb-1">Filter Role</label>
                            <select name="role" class="form-select">
                                <option value="">-- Semua Role --</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                                        {{ $role->label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="bi bi-search"></i> Cari
                            </button>
                            <a href="{{ route('sekretariat.pengguna.index') }}" class="btn btn-light-secondary">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Tabel Pengguna --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Daftar Pengguna</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="table-pengguna">
                            <thead>
                                <tr>
                                    <th width="40">No</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Jabatan Struktural</th>
                                    <th>Terhubung ke</th>
                                    <th width="120">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pengguna as $user)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong>{{ $user->name }}</strong></td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            {{-- Multi-badge role dari relasi --}}
                                            @forelse ($user->roles as $role)
                                                @php
                                                    $badgeColor = match($role->name) {
                                                        'sekretariat'      => 'bg-dark',
                                                        'pastor'           => 'bg-danger',
                                                        'dewan_pastoral'   => 'bg-warning text-dark',
                                                        'ketua_kub'        => 'bg-success',
                                                        'ketua_kategorial' => 'bg-info',
                                                        default            => 'bg-secondary',
                                                    };
                                                @endphp
                                                <span class="badge {{ $badgeColor }} me-1">{{ $role->label }}</span>
                                            @empty
                                                <span class="badge bg-light text-muted">Tanpa Role</span>
                                            @endforelse
                                        </td>
                                        <td>
                                            {{-- Jabatan dari data KUB/Kategorial --}}
                                            @if ($user->jabatan_kub)
                                                <div class="small">
                                                    <i class="bi bi-diagram-3 text-success me-1"></i>
                                                    Ketua KUB: <strong>{{ $user->jabatan_kub->nama }}</strong>
                                                </div>
                                            @endif
                                            @if ($user->jabatan_kategorial && $user->jabatan_kategorial->isNotEmpty())
                                                @foreach ($user->jabatan_kategorial as $jk)
                                                    <div class="small">
                                                        <i class="bi bi-collection text-info me-1"></i>
                                                        Ketua Kat.: <strong>{{ $jk->nama }}</strong>
                                                    </div>
                                                @endforeach
                                            @endif
                                            @if (!$user->jabatan_kub && ($user->jabatan_kategorial?->isEmpty() ?? true))
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($user->umat)
                                                <i class="bi bi-person text-primary me-1"></i>
                                                {{ $user->umat->nama }}
                                            @elseif ($user->klerus)
                                                <i class="bi bi-person-fill text-danger me-1"></i>
                                                {{ $user->klerus->nama }}
                                                <small class="text-muted">({{ ucfirst($user->klerus->jabatan) }})</small>
                                            @else
                                                <span class="text-muted">
                                                    <i class="bi bi-shield-fill me-1"></i>Akun Sistem
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                {{-- Edit Role --}}
                                                <a href="{{ route('sekretariat.pengguna.edit', $user) }}"
                                                    class="btn btn-sm btn-outline-primary" title="Edit Akun & Role">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>

                                                {{-- Reset Password --}}
                                                <form action="{{ route('sekretariat.pengguna.reset-password', $user) }}"
                                                    method="POST" class="form-reset-password">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-warning"
                                                        title="Reset Password ke 'password'"
                                                        data-nama="{{ $user->name }}">
                                                        <i class="bi bi-arrow-counterclockwise"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="bi bi-people fs-2 d-block mb-2"></i>
                                            Tidak ada pengguna ditemukan.
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

    @push('scripts')
        <script src="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.form-reset-password').forEach(function (form) {
                    form.addEventListener('submit', function (e) {
                        e.preventDefault();
                        const nama = form.querySelector('[data-nama]').dataset.nama;
                        Swal.fire({
                            title: 'Reset Password?',
                            html: `Password <strong>${nama}</strong> akan direset menjadi <code>password</code>.`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#f59e0b',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Ya, reset',
                            cancelButtonText: 'Batal'
                        }).then(function (result) {
                            if (result.isConfirmed) form.submit();
                        });
                    });
                });
            });
        </script>
    @endpush
@endsection
