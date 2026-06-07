@extends('layouts.sekretariat')

@section('title', 'Detail Kategorial')

@push('styles')
    <link rel="stylesheet" href="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.css') }}">
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Detail Kategorial</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('sekretariat.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('sekretariat.kategorial.index') }}">Kategorial</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $kategorial->nama }}</li>
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

            {{-- Info Kategorial --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">{{ $kategorial->nama }}</h4>
                    <a href="{{ route('sekretariat.kategorial.edit', $kategorial) }}"
                        class="btn btn-sm btn-outline-warning">
                        <i class="bi bi-pencil-square"></i> Edit
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-borderless" style="max-width:400px">
                        <tr>
                            <th width="150">Ketua</th>
                            <td>
                                @if ($kategorial->ketuaUmat)
                                    <strong>{{ $kategorial->ketuaUmat->nama }}</strong>
                                    <span class="badge bg-success ms-1">Ketua</span>
                                @else
                                    <span class="badge bg-warning text-dark">Belum diset</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Jumlah Anggota</th>
                            <td><span class="badge bg-primary">{{ $kategorial->anggota->count() }} orang</span></td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Daftar Anggota --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Daftar Anggota</h5>
                    <a href="{{ route('sekretariat.kategorial.anggota-kategorial.create', $kategorial) }}"
                        class="btn btn-sm btn-primary">
                        <i class="bi bi-person-plus"></i> Tambah Anggota
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Jabatan</th>
                                    <th>Bidang Tugas</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($kategorial->anggotaKategorial as $anggota)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            {{ $anggota->umat->nama }}
                                            @if ($kategorial->ketua_umat_id == $anggota->umat->id)
                                                <span class="badge bg-success ms-1">Ketua</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $jabColor = match ($anggota->jabatan) {
                                                    'Ketua' => 'bg-success',
                                                    'Wakil Ketua' => 'bg-primary',
                                                    'Sekretaris' => 'bg-info',
                                                    'Bendahara' => 'bg-warning text-dark',
                                                    default => 'bg-secondary',
                                                };
                                            @endphp
                                            <span class="badge {{ $jabColor }}">{{ $anggota->jabatan }}</span>
                                        </td>
                                        <td>{{ $anggota->bidang_tugas ?? '-' }}</td>
                                        <td>
                                            @if ($anggota->status === 'Aktif')
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-secondary">Tidak Aktif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('sekretariat.anggota-kategorial.edit', $anggota) }}"
                                                    class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <form
                                                    action="{{ route('sekretariat.anggota-kategorial.destroy', $anggota) }}"
                                                    method="POST" class="form-delete-anggota">
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
                                        <td colspan="6" class="text-center text-muted py-3">
                                            Belum ada anggota. Klik <strong>"Tambah Anggota"</strong> untuk mendaftarkan
                                            umat.
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
            document.querySelectorAll('.form-delete-anggota').forEach(form => {
                form.addEventListener('submit', e => {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Hapus anggota ini?',
                        text: 'Data keanggotaan ini akan dihapus dari kategorial.',
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
        </script>
    @endpush
@endsection
