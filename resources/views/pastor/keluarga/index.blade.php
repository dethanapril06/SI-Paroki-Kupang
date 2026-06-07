@extends('layouts.pastor')

@section('title', 'Direktori Keluarga')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Direktori Keluarga</h3>
                    <p class="text-subtitle text-muted">
                        Daftar Kartu Keluarga (KK) aktif di Paroki (Read-Only)
                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('pastor.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Direktori Keluarga</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        Total Terdata: <span class="badge bg-primary">{{ $keluarga->count() }} Keluarga</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="table1">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>No. KK</th>
                                    <th>Kepala Keluarga</th>
                                    <th>KUB / Wilayah</th>
                                    <th>Jumlah Anggota</th>
                                    <th>Alamat</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($keluarga as $k)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <span class="font-monospace fw-bold">{{ $k->no_kk ?? '-' }}</span>
                                        </td>
                                        <td>
                                            <strong>{{ $k->kepalaKeluarga->nama ?? '-' }}</strong>
                                        </td>
                                        <td>
                                            <small>
                                                <strong>KUB:</strong> {{ $k->kub->nama ?? '-' }}<br>
                                                <strong>Wilayah:</strong> {{ $k->kub->wilayah->nama ?? '-' }}
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light-info text-info fw-bold">
                                                {{ $k->umat_count ?? 0 }} Jiwa
                                            </span>
                                        </td>
                                        <td>
                                            <small>{{ Str::limit($k->alamat, 60) }}</small>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('pastor.keluarga.show', $k) }}"
                                                class="btn btn-sm btn-outline-primary" title="Detail Keluarga">
                                                <i class="bi bi-eye-fill"></i> Detail KK
                                            </a>
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
