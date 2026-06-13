@extends('layouts.sekretariat')

@section('title', 'Daftar Keanggotaan DPP')

@push('styles')
    <link rel="stylesheet" href="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.css') }}">
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Dewan Pastoral Paroki</h3>
                    <a href="{{ route('sekretariat.dpp.keanggotaan.create') }}" class="btn btn-primary my-2">
                        <i class="bi bi-plus-lg"></i>
                    </a>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sekretariat.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Keanggotaan DPP</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="card-title mb-0">Data Keanggotaan DPP</h5>
                    <form method="GET" action="{{ route('sekretariat.dpp.keanggotaan.index') }}"
                        class="d-flex gap-2 flex-wrap">
                        <select name="jabatan" class="form-select form-select-sm" style="width:auto">
                            <option value="">-- Semua Jabatan --</option>
                            @foreach ($listJabatan as $j)
                                <option value="{{ $j }}"
                                    {{ ($filters['jabatan'] ?? '') === $j ? 'selected' : '' }}>
                                    {{ $j }}
                                </option>
                            @endforeach
                        </select>
                        <select name="status_aktif" class="form-select form-select-sm" style="width:auto">
                            <option value="">-- Semua Status --</option>
                            @foreach ($listStatus as $s)
                                <option value="{{ $s }}"
                                    {{ ($filters['status_aktif'] ?? '') === $s ? 'selected' : '' }}>
                                    {{ $s }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-funnel"></i>
                        </button>
                        @if (array_filter($filters))
                            <a href="{{ route('sekretariat.dpp.keanggotaan.index') }}"
                                class="btn btn-sm btn-outline-danger" title="Reset filter">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        @endif
                    </form>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-light-success color-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped" id="table1">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Umat</th>
                                    <th>Jabatan</th>
                                    <th>Bidang Tugas</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($anggota as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->umat->nama ?? '-' }}</td>
                                        <td>{{ $item->jabatan }}</td>
                                        <td>{{ $item->bidang_tugas ?? '-' }}</td>
                                        <td>
                                            @if ($item->status_aktif === 'Aktif')
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-secondary">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('sekretariat.dpp.keanggotaan.show', $item) }}"
                                                    class="btn btn-sm btn-outline-info" title="Detail">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('sekretariat.dpp.keanggotaan.edit', $item) }}"
                                                    class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <form action="{{ route('sekretariat.dpp.keanggotaan.destroy', $item) }}"
                                                    method="POST" class="delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Belum ada data keanggotaan DPP.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">{{ $anggota->links() }}</div>
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
                            title: 'Yakin ingin menghapus data ini?',
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
