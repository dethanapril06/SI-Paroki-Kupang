@extends('layouts.sekretariat')

@section('title', 'Detail Keluarga')

@push('styles')
    <link rel="stylesheet" href="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.css') }}">
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Detail Keluarga</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('sekretariat.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('sekretariat.keluarga.index') }}">Daftar Keluarga</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Detail Keluarga</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">

            @if (session('success'))
                <div class="alert alert-light-success color-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Info Keluarga --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Informasi Keluarga</h4>
                    <a href="{{ route('sekretariat.keluarga.edit', $keluarga) }}"
                        class="btn btn-sm btn-outline-warning">
                        <i class="bi bi-pencil-square"></i> Edit
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="160">Kepala Keluarga</th>
                                    <td>
                                        @if ($keluarga->kepalaKeluarga)
                                            <strong>{{ $keluarga->kepalaKeluarga->nama }}</strong>
                                        @else
                                            <span class="badge bg-warning text-dark">Belum diset</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>KUB</th>
                                    <td>{{ $keluarga->kub->nama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Wilayah</th>
                                    <td>{{ $keluarga->kub->wilayah->nama ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="160">Alamat</th>
                                    <td>{{ $keluarga->alamat }}</td>
                                </tr>
                                <tr>
                                    <th>Status Tempat Tinggal</th>
                                    <td>{{ $keluarga->status_tempat_tinggal }}</td>
                                </tr>
                                <tr>
                                    <th>Jumlah Anggota</th>
                                    <td>
                                        <span class="badge bg-primary">{{ $keluarga->umat->count() }} orang</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Daftar Anggota --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Daftar Anggota Keluarga</h5>
                    {{-- Tombol Tambah Anggota --}}
                    <a href="{{ route('sekretariat.keluarga.umat.create', $keluarga) }}" class="btn btn-sm btn-primary">
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
                                    <th>Hubungan Keluarga</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Tanggal Lahir</th>
                                    <th>Status Sakramen</th>
                                    <th>Status Pernikahan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($keluarga->umat as $umat)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            {{ $umat->nama }}
                                            @if ($keluarga->kepala_keluarga_id == $umat->id)
                                                <span class="badge bg-success ms-1">KK</span>
                                            @endif
                                        </td>
                                        <td>{{ $umat->hubungan_keluarga }}</td>
                                        <td>{{ $umat->jenis_kelamin }}</td>
                                        <td>{{ $umat->tanggal_lahir->format('d M Y') }}</td>
                                        <td>
                                            @php
                                                $sakramens = $umat->sakramen->pluck('jenis_sakramen')->toArray();
                                            @endphp
                                            <div class="d-flex flex-wrap gap-1">
                                                @if (in_array('BAPTIS', $sakramens))
                                                    <span class="badge bg-light-info text-info py-1 px-2 border border-info" title="Sudah Baptis">
                                                        <i class="bi bi-droplet-fill me-1"></i>Baptis
                                                    </span>
                                                @endif

                                                @if (in_array('KOMUNI_PERTAMA', $sakramens))
                                                    <span class="badge bg-light-success text-success py-1 px-2 border border-success" title="Sudah Komuni Pertama">
                                                        <i class="bi bi-bookmark-star-fill me-1"></i>Komuni
                                                    </span>
                                                @endif

                                                @if (in_array('KRISMA', $sakramens))
                                                    <span class="badge bg-light-warning text-warning py-1 px-2 border border-warning" title="Sudah Krisma">
                                                        <i class="bi bi-star-fill me-1"></i>Krisma
                                                    </span>
                                                @endif

                                                @if (in_array('PERNIKAHAN', $sakramens))
                                                    <span class="badge bg-light-danger text-danger py-1 px-2 border border-danger" title="Sudah Menikah">
                                                        <i class="bi bi-heart-fill me-1"></i>Nikah
                                                    </span>
                                                @endif

                                                @if (empty($sakramens))
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ $umat->status_pernikahan }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('sekretariat.umat.edit', $umat) }}"
                                                    class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <form action="{{ route('sekretariat.umat.destroy', $umat) }}"
                                                    method="POST" class="delete-umat-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">
                                            Belum ada anggota. Klik <strong>"Tambah Anggota"</strong> untuk menambahkan umat ke keluarga ini.
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
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.delete-umat-form').forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        event.preventDefault();
                        Swal.fire({
                            title: 'Hapus anggota ini?',
                            text: 'Data anggota yang dihapus tidak bisa dikembalikan.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Ya, hapus',
                            cancelButtonText: 'Batal'
                        }).then(function(result) {
                            if (result.isConfirmed) form.submit();
                        });
                    });
                });
            });
        </script>
    @endpush
@endsection
