@extends('layouts.pastor')

@section('title', 'Detail Keluarga')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Detail Keluarga</h3>
                    <p class="text-subtitle text-muted">Informasi lengkap Kartu Keluarga digital</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('pastor.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('pastor.keluarga.index') }}">Daftar Keluarga</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">KK {{ $keluarga->kepalaKeluarga->nama ?? '-' }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                {{-- Detail Keluarga Card --}}
                <div class="col-12">
                    <div class="card mb-4 border-top border-primary border-4 shadow-sm">
                        <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                            <h4 class="mb-0 text-primary">
                                <i class="bi bi-person-vcard-fill me-2"></i>KARTU KELUARGA DIGITAL
                            </h4>
                            <span class="badge bg-primary font-monospace fs-6">No. KK: {{ $keluarga->no_kk ?? '-' }}</span>
                        </div>
                        <div class="card-body pt-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <th width="180"><i class="bi bi-person-fill text-muted me-2"></i>Kepala Keluarga</th>
                                            <td>: <strong>{{ $keluarga->kepalaKeluarga->nama ?? '-' }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th><i class="bi bi-geo-alt-fill text-muted me-2"></i>Alamat Lengkap</th>
                                            <td>: {{ $keluarga->alamat ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th><i class="bi bi-telephone-fill text-muted me-2"></i>No. Telepon Kepala</th>
                                            <td>: {{ $keluarga->kepalaKeluarga->no_telepon ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6 border-start-md">
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <th width="150"><i class="bi bi-diagram-3-fill text-muted me-2"></i>KUB</th>
                                            <td>: <span class="badge bg-light-primary text-primary">{{ $keluarga->kub->nama ?? '-' }}</span></td>
                                        </tr>
                                        <tr>
                                            <th><i class="bi bi-map-fill text-muted me-2"></i>Wilayah</th>
                                            <td>: <span class="badge bg-light-info text-info">{{ $keluarga->kub->wilayah->nama ?? '-' }}</span></td>
                                        </tr>
                                        <tr>
                                            <th><i class="bi bi-people-fill text-muted me-2"></i>Total Anggota</th>
                                            <td>: <span class="badge bg-dark">{{ $keluarga->umat->count() }} Jiwa</span></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Anggota Keluarga Table Card --}}
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-light-secondary py-3">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-people-fill me-2 text-secondary"></i>Susunan Anggota Keluarga
                            </h5>
                        </div>
                        <div class="card-body pt-3">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" width="50">No</th>
                                            <th>Nama Lengkap</th>
                                            <th class="text-center">JK</th>
                                            <th>Hubungan Keluarga</th>
                                            <th>Tempat, Tanggal Lahir</th>
                                            <th>Status Sakramen</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($keluarga->umat as $member)
                                            @php
                                                $sakramens = $member->sakramen->pluck('jenis_sakramen')->toArray();
                                            @endphp
                                            <tr class="{{ $member->status_almarhum ? 'table-dark text-muted' : '' }}">
                                                <td class="text-center">{{ $loop->iteration }}</td>
                                                <td>
                                                    <strong>{{ $member->nama }}</strong>
                                                    @if ($member->status_almarhum)
                                                        <span class="badge bg-dark ms-1">Alm.</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if ($member->jenis_kelamin === 'Laki-laki')
                                                        <i class="bi bi-gender-male text-primary fs-5" title="Laki-laki"></i>
                                                    @else
                                                        <i class="bi bi-gender-female text-danger fs-5" title="Perempuan"></i>
                                                    @endif
                                                </td>
                                                <td>{{ $member->hubungan_keluarga }}</td>
                                                <td>
                                                    {{ $member->tempat_lahir ?? '-' }}, <br>
                                                    <small class="text-muted">{{ $member->tanggal_lahir ? $member->tanggal_lahir->format('d M Y') : '-' }}</small>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        {{-- Baptis badge --}}
                                                        @if (in_array('BAPTIS', $sakramens))
                                                            <span class="badge bg-light-info text-info py-1 px-2 border border-info" title="Sudah Baptis">
                                                                <i class="bi bi-droplet-fill me-1"></i>Baptis
                                                            </span>
                                                        @else
                                                            <span class="badge bg-light text-muted py-1 px-2 border" title="Belum Baptis">
                                                                -
                                                            </span>
                                                        @endif

                                                        {{-- Komuni Pertama badge --}}
                                                        @if (in_array('KOMUNI_PERTAMA', $sakramens))
                                                            <span class="badge bg-light-success text-success py-1 px-2 border border-success" title="Sudah Komuni Pertama">
                                                                <i class="bi bi-bookmark-star-fill me-1"></i>Komuni
                                                            </span>
                                                        @endif

                                                        {{-- Krisma badge --}}
                                                        @if (in_array('KRISMA', $sakramens))
                                                            <span class="badge bg-light-warning text-warning py-1 px-2 border border-warning" title="Sudah Krisma">
                                                                <i class="bi bi-star-fill me-1"></i>Krisma
                                                            </span>
                                                        @endif

                                                        {{-- Pernikahan badge --}}
                                                        @if (in_array('PERNIKAHAN', $sakramens))
                                                            <span class="badge bg-light-danger text-danger py-1 px-2 border border-danger" title="Sudah Menikah">
                                                                <i class="bi bi-heart-fill me-1"></i>Nikah
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('pastor.umat.show', $member) }}"
                                                        class="btn btn-sm btn-outline-primary" title="Detail Profil Umat">
                                                        <i class="bi bi-person-fill"></i> Detail Umat
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-4">
                                                    Belum ada anggota keluarga terdaftar.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
