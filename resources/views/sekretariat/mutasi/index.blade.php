@extends('layouts.sekretariat')

@section('title', 'Daftar Mutasi')

@push('styles')
    <link rel="stylesheet" href="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.css') }}">
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Daftar Mutasi</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('sekretariat.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Daftar Mutasi</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            {{-- Shortcut Tambah --}}
            <div class="row mb-3">
                <div class="col-12">
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('sekretariat.mutasi.agama.create') }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-lg"></i> Tambah Mutasi Agama
                        </a>
                        <a href="{{ route('sekretariat.mutasi.keluarga.create') }}" class="btn btn-sm btn-success">
                            <i class="bi bi-plus-lg"></i> Tambah Mutasi Keluarga
                        </a>
                        <a href="{{ route('sekretariat.mutasi.umat.create') }}" class="btn btn-sm btn-info">
                            <i class="bi bi-plus-lg"></i> Tambah Mutasi Umat
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h5 class="card-title mb-0">Semua Data Mutasi</h5>
                    @if ($pendingCount > 0)
                        <span class="badge bg-warning text-dark fs-6">
                            <i class="bi bi-hourglass-split me-1"></i>{{ $pendingCount }} Menunggu Persetujuan
                        </span>
                    @endif
                </div>

                {{-- Filter Tabs --}}
                <div class="card-body pb-0 border-bottom">
                    <ul class="nav nav-tabs" id="statusTab">
                        @foreach (['semua' => 'Semua', 'pending' => 'Pending', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak'] as $key => $label)
                            <li class="nav-item">
                                <a class="nav-link {{ $status === $key ? 'active' : '' }}"
                                    href="{{ route('sekretariat.mutasi.index', ['status' => $key]) }}">
                                    {{ $label }}
                                    @if ($key === 'pending' && $pendingCount > 0)
                                        <span class="badge bg-warning text-dark ms-1">{{ $pendingCount }}</span>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-light-success color-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-light-danger color-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="table1">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Jenis Mutasi</th>
                                    <th>Umat / Keluarga</th>
                                    <th>Keterangan</th>
                                    <th>Pemohon</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($mutasiList as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            @if ($item->jenis === 'agama')
                                                <span class="badge bg-warning text-dark">Agama</span>
                                            @elseif ($item->jenis === 'keluarga')
                                                <span class="badge bg-success">Keluarga</span>
                                            @else
                                                <span class="badge bg-info">Umat</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->jenis === 'agama' && $item->mutasiAgama)
                                                {{ $item->mutasiAgama->umat->nama ?? '-' }}
                                            @elseif ($item->jenis === 'keluarga' && $item->mutasiKeluarga)
                                                {{ $item->mutasiKeluarga->keluarga->kepalaKeluarga->nama ?? '-' }}
                                            @elseif ($item->jenis === 'umat' && $item->mutasiUmat)
                                                {{ $item->mutasiUmat->umat->nama ?? '-' }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-wrap" style="max-width: 250px; display: inline-block;">
                                                {{ $item->keterangan ?? '-' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($item->pemohon)
                                                <span class="text-primary">
                                                    <i class="bi bi-person me-1"></i>{{ $item->pemohon->nama }}
                                                </span>
                                            @else
                                                <span class="text-muted fst-italic">Sekretariat</span>
                                            @endif
                                        </td>
                                        <td>{{ $item->tanggal->format('d M Y') }}</td>
                                        <td>
                                            @if ($item->isPending())
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-hourglass-split me-1"></i>Pending
                                                </span>
                                            @elseif ($item->isDisetujui())
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle me-1"></i>Disetujui
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-x-circle me-1"></i>Ditolak
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1 flex-wrap">
                                                {{-- Tombol Detail --}}
                                                @if ($item->jenis === 'agama' && $item->mutasiAgama)
                                                    <a href="{{ route('sekretariat.mutasi.agama.show', $item->mutasiAgama) }}"
                                                        class="btn btn-sm btn-outline-info" title="Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                @elseif ($item->jenis === 'keluarga' && $item->mutasiKeluarga)
                                                    <a href="{{ route('sekretariat.mutasi.keluarga.show', $item->mutasiKeluarga) }}"
                                                        class="btn btn-sm btn-outline-info" title="Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                @elseif ($item->jenis === 'umat' && $item->mutasiUmat)
                                                    <a href="{{ route('sekretariat.mutasi.umat.show', $item->mutasiUmat) }}"
                                                        class="btn btn-sm btn-outline-info" title="Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                @endif

                                                {{-- Tombol Approve / Reject (hanya untuk pending) --}}
                                                @if ($item->isPending())
                                                    <form action="{{ route('sekretariat.mutasi.approve', $item) }}"
                                                        method="POST" class="approve-form">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success" title="Setujui">
                                                            <i class="bi bi-check-lg"></i>
                                                        </button>
                                                    </form>

                                                    <button type="button" class="btn btn-sm btn-danger btn-reject"
                                                        title="Tolak"
                                                        data-id="{{ $item->id }}"
                                                        data-url="{{ route('sekretariat.mutasi.reject', $item) }}">
                                                        <i class="bi bi-x-lg"></i>
                                                    </button>
                                                @endif

                                                {{-- Hapus --}}
                                                <form action="{{ route('sekretariat.mutasi.destroy', $item) }}"
                                                    method="POST" class="delete-mutasi-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-sm btn-outline-danger btn-delete-mutasi"
                                                        title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                                Belum ada data mutasi
                                                @if ($status !== 'semua') dengan status "{{ $status }}" @endif.
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">{{ $mutasiList->links() }}</div>
                </div>
            </div>
        </section>
    </div>

    {{-- Modal Tolak --}}
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="reject-form" method="POST" action="">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="rejectModalLabel">
                            <i class="bi bi-x-circle text-danger me-2"></i>Tolak Request Mutasi
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Alasan Penolakan <span class="text-danger">*</span></label>
                            <textarea name="catatan_admin" class="form-control" rows="4" required
                                placeholder="Jelaskan alasan penolakan request ini..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-x-circle me-1"></i>Tolak Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Konfirmasi Approve
                document.querySelectorAll('.approve-form').forEach(function (form) {
                    form.addEventListener('submit', function (e) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Setujui request ini?',
                            text: 'Data mutasi akan langsung dieksekusi setelah disetujui.',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#198754',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Ya, Setujui',
                            cancelButtonText: 'Batal'
                        }).then(function (result) {
                            if (result.isConfirmed) form.submit();
                        });
                    });
                });

                // Tombol Reject → buka modal
                document.querySelectorAll('.btn-reject').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        const url = btn.dataset.url;
                        document.getElementById('reject-form').action = url;
                        new bootstrap.Modal(document.getElementById('rejectModal')).show();
                    });
                });

                // Konfirmasi Hapus
                document.querySelectorAll('.delete-mutasi-form').forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        event.preventDefault();
                        Swal.fire({
                            title: 'Yakin ingin menghapus data ini?',
                            text: 'Data mutasi yang dihapus tidak bisa dikembalikan.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Ya, hapus',
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
