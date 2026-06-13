@extends('layouts.portal')

@section('title', 'Manajemen Pendaftaran KUB')

@push('styles')
    <link rel="stylesheet" href="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.css') }}">
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Manajemen Pendaftaran KUB</h3>
                    <p class="text-muted">Tinjau dan setujui pendaftaran mandiri umat untuk {{ $myKub->nama }}.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('portal.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Pendaftaran</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">

            {{-- Notifikasi --}}
            @if (session('success'))
                <div class="alert alert-light-success color-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-light-danger color-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Kartu Statistik --}}
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <a href="{{ route('portal.pendaftaran.index', ['status' => 'pending']) }}"
                        class="card text-decoration-none {{ $status === 'pending' ? 'border-warning border-2' : '' }}">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div style="width:48px;height:48px;border-radius:12px;background:rgba(var(--bs-warning-rgb),0.15);display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-hourglass-split text-warning fs-4"></i>
                            </div>
                            <div>
                                <div class="fw-bold fs-4 lh-1">{{ $counts['pending'] }}</div>
                                <div class="text-muted small">Menunggu Persetujuan</div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('portal.pendaftaran.index', ['status' => 'active']) }}"
                        class="card text-decoration-none {{ $status === 'active' ? 'border-success border-2' : '' }}">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div style="width:48px;height:48px;border-radius:12px;background:rgba(var(--bs-success-rgb),0.15);display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-check-circle text-success fs-4"></i>
                            </div>
                            <div>
                                <div class="fw-bold fs-4 lh-1">{{ $counts['active'] }}</div>
                                <div class="text-muted small">Sudah Disetujui</div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('portal.pendaftaran.index', ['status' => 'rejected']) }}"
                        class="card text-decoration-none {{ $status === 'rejected' ? 'border-danger border-2' : '' }}">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div style="width:48px;height:48px;border-radius:12px;background:rgba(var(--bs-danger-rgb),0.15);display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-x-circle text-danger fs-4"></i>
                            </div>
                            <div>
                                <div class="fw-bold fs-4 lh-1">{{ $counts['rejected'] }}</div>
                                <div class="text-muted small">Ditolak</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            {{-- Tabel Pendaftar --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        Daftar Pendaftar -
                        @if ($status === 'pending')
                            <span class="badge bg-warning text-dark ms-1">Menunggu</span>
                        @elseif ($status === 'active')
                            <span class="badge bg-success ms-1">Disetujui</span>
                        @else
                            <span class="badge bg-danger ms-1">Ditolak</span>
                        @endif
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="table1">
                            <thead class="table-light">
                                <tr>
                                    <th width="40">No</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>KUB / Wilayah</th>
                                    <th>Alamat</th>
                                    <th>Mendaftar</th>
                                    @if ($status === 'pending')
                                        <th width="160">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pendaftar as $user)
                                    <tr>
                                        <td>{{ $pendaftar->firstItem() + $loop->index }}</td>
                                        <td>
                                            <strong>{{ $user->name }}</strong>
                                            @if ($user->umat)
                                                <div class="text-muted small">
                                                    {{ $user->umat->jenis_kelamin }} &bull;
                                                    {{ $user->umat->tanggal_lahir?->format('d M Y') }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @if ($user->umat?->keluarga?->kub)
                                                <div>{{ $user->umat->keluarga->kub->nama }}</div>
                                                @if ($user->umat->keluarga->kub->wilayah)
                                                    <small class="text-muted">Wilayah {{ $user->umat->keluarga->kub->wilayah->nama }}</small>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="small">{{ Str::limit($user->umat?->keluarga?->alamat, 50) }}</span>
                                        </td>
                                        <td>
                                            <span class="small text-muted">
                                                {{ $user->created_at->format('d M Y') }}<br>
                                                {{ $user->created_at->diffForHumans() }}
                                            </span>
                                        </td>
                                        @if ($status === 'pending')
                                            <td>
                                                <div class="d-flex gap-1">
                                                    {{-- Tombol Setujui --}}
                                                    <form action="{{ route('portal.pendaftaran.approve', $user) }}"
                                                        method="POST" class="form-approve">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success"
                                                            title="Setujui pendaftaran" data-nama="{{ $user->name }}">
                                                            <i class="bi bi-check-lg"></i> Setujui
                                                        </button>
                                                    </form>

                                                    {{-- Tombol Tolak --}}
                                                    <form action="{{ route('portal.pendaftaran.reject', $user) }}"
                                                        method="POST" class="form-reject">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                            title="Tolak pendaftaran" data-nama="{{ $user->name }}">
                                                            <i class="bi bi-x-lg"></i> Tolak
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-5">
                                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                            Tidak ada pendaftar dengan status ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($pendaftar->hasPages())
                    <div class="card-footer">
                        {{ $pendaftar->links() }}
                    </div>
                @endif
            </div>

        </section>
    </div>

    @push('scripts')
        <script src="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.form-approve').forEach(function (form) {
                    form.addEventListener('submit', function (e) {
                        e.preventDefault();
                        const nama = form.querySelector('[data-nama]').dataset.nama;
                        Swal.fire({
                            title: 'Setujui Pendaftaran?',
                            html: `Akun <strong>${nama}</strong> akan diaktifkan dan umat dapat login.`,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Ya, Setujui',
                            cancelButtonText: 'Batal'
                        }).then(function (result) {
                            if (result.isConfirmed) form.submit();
                        });
                    });
                });

                document.querySelectorAll('.form-reject').forEach(function (form) {
                    form.addEventListener('submit', function (e) {
                        e.preventDefault();
                        const nama = form.querySelector('[data-nama]').dataset.nama;
                        Swal.fire({
                            title: 'Tolak Pendaftaran?',
                            html: `Pendaftaran <strong>${nama}</strong> akan ditolak.`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc3545',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Ya, Tolak',
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
