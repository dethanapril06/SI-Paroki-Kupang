@extends('layouts.portal')

@section('title', 'Daftar Keluarga –')

@push('styles')
    <link rel="stylesheet" href="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.css') }}">
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Daftar Keluarga</h3>
                    <p class="text-subtitle text-muted">Mengelola data keluarga di KUB Anda.</p>
                    <a href="{{ route('portal.keluarga.create') }}" class="btn btn-primary mb-3">
                        <i class="bi bi-plus-lg"></i> Tambah Keluarga
                    </a>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('portal.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Daftar Keluarga</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="table1">
                            <thead>
                                <tr>
                                    <th>Nama Keluarga (Kepala)</th>
                                    <th>Alamat</th>
                                    <th>Jumlah Anggota</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($keluarga as $item)
                                    <tr>
                                        <td>{{ $item->kepalaKeluarga->nama ?? 'Belum Diatur' }}</td>
                                        <td>{{ Str::limit($item->alamat, 50) }}</td>
                                        <td><span class="badge bg-light-info">{{ $item->umat_count }} Orang</span></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('portal.keluarga.show', $item->id) }}"
                                                    class="btn btn-sm btn-outline-primary">Detail</a>
                                                <a href="{{ route('portal.keluarga.edit', $item->id) }}"
                                                    class="btn btn-sm btn-outline-warning">Edit</a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada data keluarga di KUB ini.</td>
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
