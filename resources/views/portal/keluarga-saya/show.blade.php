@extends('layouts.portal')

@section('title', 'Keluarga Saya')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Keluarga Saya</h3>
                    <p class="text-muted">Informasi keluarga dan daftar anggota.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('portal.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active">Keluarga Saya</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">

            @if (session('success'))
                <div class="alert alert-light-success color-success alert-dismissible fade show">
                    {!! session('success') !!}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- ── INFO KELUARGA ── --}}
            <div class="row">
                {{-- ── INFORMASI KELUARGA ── --}}
                <div class="col-12 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-info-circle-fill me-2 text-primary"></i>Info Keluarga
                            </h5>
                            @if ($isKetuaKeluarga)
                                <div>
                                    <a href="{{ route('portal.keluarga-saya.edit') }}" class="btn btn-sm btn-outline-primary me-1" title="Edit Data Keluarga">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="{{ route('portal.keluarga-saya.cetak') }}" target="_blank" class="btn btn-sm btn-outline-danger" title="Cetak Kartu Keluarga Katholik">
                                        <i class="bi bi-printer-fill"></i>
                                    </a>
                                </div>
                            @else
                                <a href="{{ route('portal.keluarga-saya.cetak') }}" target="_blank" class="btn btn-sm btn-outline-danger" title="Cetak Kartu Keluarga Katholik">
                                    <i class="bi bi-printer-fill"></i>
                                </a>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless mb-0">
                                        <tr>
                                            <td class="text-muted fw-semibold" style="width:45%">Kepala Keluarga</td>
                                            <td>
                                                <strong>{{ $keluarga->kepalaKeluarga?->nama ?? '-' }}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted fw-semibold">Alamat</td>
                                            <td>{{ $keluarga->alamat ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted fw-semibold">Status Tempat Tinggal</td>
                                            <td>{{ $keluarga->status_tempat_tinggal ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless mb-0">
                                        <tr>
                                            <td class="text-muted fw-semibold" style="width:45%">KUB</td>
                                            <td>{{ $keluarga->kub?->nama ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted fw-semibold">Wilayah</td>
                                            <td>{{ $keluarga->kub?->wilayah?->nama ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted fw-semibold">Jumlah Anggota</td>
                                            <td>
                                                <span class="badge bg-light-primary text-primary">
                                                    {{ $keluarga->umat->count() }} orang
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── DAFTAR ANGGOTA ── --}}
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-people-fill me-2 text-primary"></i>
                                Daftar Anggota Keluarga
                            </h5>
                            @if ($isKetuaKeluarga)
                                <a href="{{ route('portal.keluarga-saya.anggota.create') }}"
                                    class="btn btn-sm btn-success">
                                    <i class="bi bi-person-plus me-1"></i>Tambah Anggota
                                </a>
                            @endif
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="table1">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nama</th>
                                            <th>Hubungan</th>
                                            <th>L/P</th>
                                            <th>Tanggal Lahir</th>
                                            <th>Status Sakramen</th>
                                            <th>Status Nikah</th>
                                            <th>No. Telepon</th>
                                            @if ($isKetuaKeluarga)
                                                <th class="text-center">Aksi</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($keluarga->umat as $anggota)
                                            <tr @class(['table-primary' => $anggota->id === $umat->id])>
                                                <td>
                                                    <span class="fw-semibold">{{ $anggota->nama }}</span>
                                                    @if ($anggota->id === $keluarga->kepala_keluarga_id)
                                                        <span class="badge bg-light-success text-success ms-1">KK</span>
                                                    @endif
                                                    @if ($anggota->id === $umat->id)
                                                        <span class="badge bg-light-primary text-primary ms-1">Anda</span>
                                                    @endif
                                                </td>
                                                <td>{{ $anggota->hubungan_keluarga ?? '-' }}</td>
                                                <td>
                                                    @if ($anggota->jenis_kelamin === 'Laki-laki')
                                                        <i class="bi bi-gender-male text-primary"></i>
                                                    @elseif ($anggota->jenis_kelamin === 'Perempuan')
                                                        <i class="bi bi-gender-female text-danger"></i>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>{{ $anggota->tanggal_lahir?->format('d M Y') ?? '-' }}</td>
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
                                                <td>{{ $anggota->status_pernikahan ?? '-' }}</td>
                                                <td>{{ $anggota->no_telepon ?? '-' }}</td>
                                                @if ($isKetuaKeluarga)
                                                    <td class="text-center">
                                                        @if ($anggota->id !== $umat->id)
                                                            <a href="{{ route('portal.sakramen-anggota.index', $anggota) }}"
                                                               class="btn btn-sm btn-outline-info"
                                                               title="Kelola Sakramen {{ $anggota->nama }}">
                                                                <i class="bi bi-award-fill me-1"></i>Sakramen
                                                            </a>
                                                        @else
                                                            <a href="{{ route('portal.sakramen-saya.index') }}"
                                                               class="btn btn-sm btn-outline-primary"
                                                               title="Sakramen Saya">
                                                                <i class="bi bi-award me-1"></i>Saya
                                                            </a>
                                                        @endif
                                                    </td>
                                                @endif
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">
                                                    Belum ada anggota terdaftar.
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
