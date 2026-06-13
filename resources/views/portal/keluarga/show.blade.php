@extends('layouts.portal')

@section('title', 'Detail Keluarga –')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Detail Keluarga</h3>
                    <p class="text-subtitle text-muted">Informasi lengkap dan daftar anggota keluarga.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('portal.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('portal.keluarga.index') }}">Daftar
                                    Keluarga</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Detail</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h4>Data Keluarga</h4>
                        </div>
                        <div class="card-body">
                            <h6>Kepala Keluarga:</h6>
                            <p class="text-primary fw-bold">{{ $keluarga->kepalaKeluarga->nama ?? 'Belum Diatur' }}
                            </p>
                            <h6>Alamat:</h6>
                            <p>{{ $keluarga->alamat }}</p>
                            <h6>Status Tempat Tinggal:</h6>
                            <p>{{ $keluarga->status_tempat_tinggal }}</p>
                            <a href="{{ route('portal.keluarga.edit', $keluarga->id) }}"
                                class="btn btn-sm btn-warning w-100 mt-2">Edit Data Keluarga</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>Daftar Anggota</h4>
                            <a href="{{ route('portal.umat.create', ['keluarga_id' => $keluarga->id]) }}"
                                class="btn btn-sm btn-primary">Tambah Anggota</a>
                        </div>
                        <div class="card-body text-nowrap table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Hubungan</th>
                                        <th>Status Sakramen</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($keluarga->umat as $anggota)
                                        <tr>
                                            <td>{{ $anggota->nama }}</td>
                                            <td>{{ $anggota->hubungan_keluarga }}</td>
                                            <td>
                                                @php
                                                    $sakramens = $anggota->sakramen->pluck('jenis_sakramen')->toArray();
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
                                            <td><span class="badge bg-success">Aktif</span></td>
                                            <td>
                                                <a href="{{ route('portal.umat.show', $anggota->id) }}"
                                                    class="btn btn-sm btn-light-primary">Profil</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
