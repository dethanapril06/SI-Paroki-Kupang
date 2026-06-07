@extends('layouts.sekretariat')

@section('title', 'Detail Kematian')

@push('styles')
    <link rel="stylesheet" href="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.css') }}">
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Detail Kematian</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sekretariat.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sekretariat.kematian.index') }}">Kematian</a>
                            </li>
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
                    <h4 class="card-title mb-0">Informasi Kematian</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('sekretariat.kematian.edit', $kematian) }}"
                            class="btn btn-sm btn-outline-warning">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        <form action="{{ route('sekretariat.kematian.destroy', $kematian) }}" method="POST"
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
                                    <th width="180">Nama</th>
                                    <td>
                                        <a href="{{ route('sekretariat.umat.show', $kematian->umat) }}">
                                            {{ $kematian->umat->nama ?? '-' }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Dicatat pada</th>
                                    <td>{{ $kematian->created_at->format('d M Y, H:i') }}</td>
                                </tr>
                            </table>
                        </div>

                        {{-- Detail Kematian --}}
                        <div class="col-md-6">
                            <h6 class="fw-bold text-muted mb-2">Detail Kematian</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <th width="180">Tanggal Meninggal</th>
                                    <td>{{ $kematian->tanggal_meninggal->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Tempat Meninggal</th>
                                    <td>{{ $kematian->tempat_meninggal }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Pemakaman</th>
                                    <td>{{ $kematian->tanggal_pemakaman?->format('d M Y') ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Tempat Pemakaman</th>
                                    <td>{{ $kematian->tempat_pemakaman ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Keterangan</th>
                                    <td>{{ $kematian->keterangan ?? '-' }}</td>
                                </tr>
                            </table>
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
                            text: 'Status almarhum umat akan dikembalikan ke aktif.',
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
