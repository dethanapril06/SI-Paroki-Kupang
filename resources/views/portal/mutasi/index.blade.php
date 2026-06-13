@extends('layouts.portal')

@section('title', 'Riwayat Request Mutasi Saya')

@push('styles')
    <link rel="stylesheet" href="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.css') }}">
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Riwayat Request Mutasi</h3>
                    <p class="text-subtitle text-muted">Daftar semua permintaan mutasi yang pernah Anda ajukan.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('portal.mutasi.index') }}">Portal</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Riwayat Mutasi</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            {{-- Tombol Ajukan --}}
            <div class="row mb-3">
                <div class="col-12">
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('portal.mutasi.umat.create') }}" class="btn btn-info">
                            <i class="bi bi-person-walking"></i> Ajukan Mutasi Umat
                        </a>
                        <a href="{{ route('portal.mutasi.keluarga.create') }}" class="btn btn-success">
                            <i class="bi bi-house-door"></i> Ajukan Mutasi Keluarga
                        </a>
                        <a href="{{ route('portal.mutasi.agama.create') }}" class="btn btn-warning">
                            <i class="bi bi-cross"></i> Ajukan Mutasi Agama
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Semua Request Mutasi Saya</h5>
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
                                    <th>Jenis</th>
                                    <th>Subjek</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Diproses</th>
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
                                                Keluarga {{ $item->mutasiKeluarga->keluarga->kepalaKeluarga->nama ?? '-' }}
                                            @elseif ($item->jenis === 'umat' && $item->mutasiUmat)
                                                {{ $item->mutasiUmat->umat->nama ?? '-' }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $item->tanggal->format('d M Y') }}</td>
                                        <td>
                                            @if ($item->isPending())
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-hourglass-split me-1"></i>Menunggu
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
                                            @if ($item->diproses_pada)
                                                {{ $item->diproses_pada->format('d M Y') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('portal.mutasi.show', $item) }}"
                                                class="btn btn-sm btn-outline-info" title="Lihat Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                                Belum ada request mutasi yang diajukan.
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $mutasiList->links() }}
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
