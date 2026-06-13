@extends('layouts.pastor')

@section('title', 'Kelompok Kategorial Paroki')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Kelompok Kategorial</h3>
                    <p class="text-subtitle text-muted">
                        Daftar kelompok kategorial dan organisasi kerasulan paroki Kupang (Read-Only)
                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('pastor.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Kelompok Kategorial</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                @forelse ($kategorial as $k)
                    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 mb-4">
                        <div class="card h-100 shadow-sm border-0 border-top border-primary border-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar avatar-md bg-light-primary text-primary rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 45px; height: 45px;">
                                            <i class="bi bi-people-fill fs-4"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-0 text-dark">{{ $k->nama }}</h5>
                                            <small class="text-muted">Apostolik Paroki</small>
                                        </div>
                                    </div>
                                    <span class="badge bg-light-info text-info rounded-pill px-3 py-1 font-monospace">
                                        {{ $k->anggota_count ?? 0 }} Anggota
                                    </span>
                                </div>

                                <hr class="my-3">

                                <div class="bg-light p-3 rounded mb-2">
                                    <small class="text-muted d-block uppercase fw-bold font-monospace text-xs mb-1">Ketua Organisasi</small>
                                    @if ($k->ketuaUmat)
                                        <a href="{{ route('pastor.umat.show', $k->ketuaUmat) }}" class="fw-bold d-flex align-items-center gap-2">
                                            <i class="bi bi-person-fill text-primary"></i>
                                            <span>{{ $k->ketuaUmat->nama }}</span>
                                        </a>
                                        <small class="text-muted d-block mt-1 ps-4">
                                            KUB: {{ $k->ketuaUmat->keluarga?->kub->nama ?? '-' }}
                                        </small>
                                    @else
                                        <span class="text-muted font-italic"><i class="bi bi-exclamation-circle me-1"></i>Belum ditentukan</span>
                                    @endif
                                </div>

                                <div class="bg-light p-3 rounded mb-2 mt-2">
                                    <small class="text-muted d-block uppercase fw-bold font-monospace text-xs mb-1">Pastor Moderator</small>
                                    @if ($k->klerus)
                                        <span class="fw-bold d-flex align-items-center gap-2">
                                            <i class="bi bi-person-badge-fill text-primary"></i>
                                            <span>{{ $k->klerus->nama }}</span>
                                        </span>
                                    @else
                                        <span class="text-muted font-italic"><i class="bi bi-exclamation-circle me-1"></i>Belum ditentukan</span>
                                    @endif
                                </div>
                            </div>
                            <div class="card-footer bg-light py-2 d-flex justify-content-between align-items-center">
                                <span class="text-xs text-muted">Paroki Kupang</span>
                                <span class="badge bg-success text-xs">Aktif</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="card">
                            <div class="card-body">
                                <i class="bi bi-diagram-3-fill text-muted fs-1 mb-3"></i>
                                <h5 class="text-muted">Belum ada data kelompok kategorial terdaftar.</h5>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
@endsection
