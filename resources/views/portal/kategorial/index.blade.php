@extends('layouts.portal')

@section('title', 'Kategorial Saya')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Kategorial Saya</h3>
                    <p class="text-subtitle text-muted">Daftar kategorial yang Anda ketuai.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('portal.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Kategorial Saya</li>
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

            <div class="row">
                @forelse ($kategorialList as $kat)
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="stats-icon purple" style="background-color: #6f42c1 !important;">
                                        <i class="bi bi-people-fill"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-0">{{ $kat->nama }}</h5>
                                        <small class="text-muted">
                                            {{ $kat->anggota_aktif ?? 0 }} anggota aktif
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent d-flex gap-2">
                                <a href="{{ route('portal.kategorial.show', $kat) }}"
                                    class="btn btn-sm btn-primary flex-fill">
                                    <i class="bi bi-people me-1"></i>Kelola Anggota
                                </a>
                                <a href="{{ route('portal.kategorial.edit', $kat) }}"
                                    class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="bi bi-people fs-1 text-muted d-block mb-3"></i>
                                <p class="text-muted">Anda belum mengetuai kategorial apapun.</p>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
@endsection
