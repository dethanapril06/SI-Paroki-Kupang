@extends('layouts.pastor')

@section('title', 'Rekan Klerus')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Rekan Klerus</h3>
                    <p class="text-subtitle text-muted">
                        Daftar klerus (Pastor Paroki, Pastor Rekan, Uskup) pelayan umat Paroki (Read-Only)
                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('pastor.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Rekan Klerus</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                @forelse ($klerus as $k)
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 mb-4">
                        <div class="card h-100 shadow-sm border-0 border-bottom border-primary border-3 text-center">
                            <div class="card-body pt-4">
                                {{-- Avatar with clergy colors --}}
                                <div class="avatar avatar-xl mb-3 shadow">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($k->nama) }}&background=800020&color=fff&size=128&bold=true&font-size=0.35"
                                        alt="{{ $k->nama }}"
                                        style="width: 90px; height: 90px; object-fit: cover; border: 3px solid #800020;">
                                </div>
                                <h5 class="mb-1 text-dark">{{ $k->nama }}</h5>
                                <p class="text-muted text-sm mb-3">
                                    <span class="badge bg-light-primary text-primary px-3 py-2 rounded-pill font-monospace">
                                        <i class="bi bi-briefcase-fill me-1"></i>{{ $k->jabatan ?? 'Klerus' }}
                                    </span>
                                </p>

                                <div class="d-flex justify-content-center align-items-center gap-2 mb-2">
                                    @if ($k->status_aktif === 'Aktif' || $k->status_aktif == 1 || $k->status_aktif === '1' || $k->status_aktif === true)
                                        <span class="badge bg-success py-1 px-3 rounded">
                                            <i class="bi bi-check-circle-fill me-1"></i>Aktif Melayani
                                        </span>
                                    @else
                                        <span class="badge bg-secondary py-1 px-3 rounded">
                                            <i class="bi bi-pause-circle-fill me-1"></i>Nonaktif
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="card-footer bg-light py-2 text-center text-xs text-muted font-monospace">
                                SI Paroki Kupang
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="card">
                            <div class="card-body">
                                <i class="bi bi-person-fill-slash text-muted fs-1 mb-3"></i>
                                <h5 class="text-muted">Tidak ditemukan data klerus.</h5>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
@endsection
