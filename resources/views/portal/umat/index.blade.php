@extends('layouts.portal')

@section('title', 'Daftar Umat KUB –')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Daftar Umat</h3>
                    <p class="text-subtitle text-muted">Seluruh anggota jemaat di {{ $myKub->nama ?? 'KUB Anda' }}</p>

                    <a href="{{ route('portal.umat.create') }}" class="btn btn-primary mb-3">
                        <i class="bi bi-plus-lg"></i> Tambah Umat Baru
                    </a>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('portal.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Daftar Umat</li>
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
                                    <th>Nama</th>
                                    <th>Gender</th>
                                    <th>Keluarga</th>
                                    <th>Hubungan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($umat as $u)
                                    <tr>
                                        <td>{{ $u->nama }}</td>
                                        <td>{{ $u->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                        <td>{{ $u->keluarga->kepalaKeluarga->nama ?? 'N/A' }}</td>
                                        <td><span class="badge bg-light-secondary">{{ $u->hubungan_keluarga }}</span></td>
                                        <td>
                                            <a href="{{ route('portal.umat.show', $u->id) }}"
                                                class="btn btn-sm btn-outline-primary">Detail</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
