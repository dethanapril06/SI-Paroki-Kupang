@extends('layouts.sekretariat')

@section('title', 'Detail Anggota DPP')

@push('styles')
    <link rel="stylesheet" href="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.css') }}">
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Detail Anggota DPP</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sekretariat.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('sekretariat.dpp.keanggotaan.index') }}">Keanggotaan DPP</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Detail</li>
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

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Informasi Anggota DPP</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('sekretariat.dpp.keanggotaan.edit', $keanggotaan) }}"
                            class="btn btn-sm btn-outline-warning">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        <form action="{{ route('sekretariat.dpp.keanggotaan.destroy', $keanggotaan) }}" method="POST"
                            class="delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">

                        {{-- Data Umat --}}
                        <div class="col-md-6">
                            <h6 class="fw-bold text-muted mb-2">Data Umat</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <th width="160">Nama</th>
                                    <td>
                                        <a href="{{ route('sekretariat.umat.show', $keanggotaan->umat) }}">
                                            {{ $keanggotaan->umat->nama ?? '-' }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Dicatat pada</th>
                                    <td>{{ $keanggotaan->created_at->format('d M Y, H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Diperbarui</th>
                                    <td>{{ $keanggotaan->updated_at->format('d M Y, H:i') }}</td>
                                </tr>
                            </table>
                        </div>

                        {{-- Data Kepengurusan --}}
                        <div class="col-md-6">
                            <h6 class="fw-bold text-muted mb-2">Data Kepengurusan</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <th width="160">Jabatan</th>
                                    <td>{{ $keanggotaan->jabatan }}</td>
                                </tr>
                                <tr>
                                    <th>Bidang Tugas</th>
                                    <td>{{ $keanggotaan->bidang_tugas ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @if ($keanggotaan->status_aktif === 'Aktif')
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Nonaktif</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>

                        {{-- Akun Login --}}
                        <div class="col-12 mt-2">
                            <h6 class="fw-bold text-muted mb-2">Akun Login</h6>
                            @if ($keanggotaan->jabatan === 'Ketua')
                                @if ($user)
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="160">Email</th>
                                            <td>{{ $user->email }}</td>
                                        </tr>
                                        <tr>
                                            <th>Role</th>
                                            <td>
                                                {{ $user->roles->pluck('label')->join(' · ') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Password</th>
                                            <td>
                                                <span class="text-muted fst-italic">
                                                    <i class="bi bi-lock-fill me-1"></i>
                                                    Tersembunyi — default: <code>password</code>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Akun dibuat</th>
                                            <td>{{ $user->created_at->format('d M Y, H:i') }}</td>
                                        </tr>
                                    </table>
                                @else
                                    <div class="alert alert-light-warning color-warning" role="alert">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                        Akun login untuk Ketua ini belum tersedia.
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-light-info color-info" role="alert">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    Anggota dengan jabatan <strong>{{ $keanggotaan->jabatan }}</strong> tidak memiliki akses login ke Panel Dewan Pastoral Paroki (DPP). Hanya jabatan <strong>Ketua</strong> yang diberikan akses login DPP. Akun personal umat yang bersangkutan tetap aktif untuk mengakses Portal Umat.
                                </div>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>

    @push('scripts')
        <script src="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.delete-form').forEach(function(form) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Yakin ingin menghapus?',
                            text: 'Data kepengurusan DPP akan dihapus dan akses ke Panel DPP akan dicabut. Akun login umat akan tetap aktif sebagai umat biasa.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Ya, hapus',
                            cancelButtonText: 'Batal'
                        }).then(r => {
                            if (r.isConfirmed) form.submit();
                        });
                    });
                });
            });
        </script>
    @endpush
@endsection
