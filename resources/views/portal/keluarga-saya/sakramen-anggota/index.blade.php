@extends('layouts.portal')
@section('title', 'Sakramen ' . $anggota->nama)
@section('content')
<div class="page-heading">
    <div class="page-title mb-3">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><i class="bi bi-award-fill text-info me-2"></i>Sakramen {{ $anggota->nama }}</h3>
                <p class="text-muted">Kelola data sakramen anggota keluarga.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('portal.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('portal.keluarga-saya.show') }}">Keluarga Saya</a></li>
                        <li class="breadcrumb-item active">Sakramen {{ $anggota->nama }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section">

        {{-- Info anggota --}}
        <div class="alert alert-light-info color-info mb-4 d-flex align-items-center gap-2">
            <i class="bi bi-person-fill fs-5"></i>
            <span>
                Anda sedang mengelola sakramen atas nama
                <strong>{{ $anggota->nama }}</strong>
                <span class="badge bg-light-secondary text-secondary ms-1">{{ $anggota->hubungan_keluarga ?? '-' }}</span>
            </span>
        </div>

        @php
        $items = [
            'BAPTIS'         => ['label' => 'Baptis',         'icon' => 'bi-droplet-fill',   'color' => 'info',      'route' => 'portal.sakramen-anggota.baptis'],
            'KOMUNI_PERTAMA' => ['label' => 'Komuni Pertama', 'icon' => 'bi-cup-hot-fill',   'color' => 'success',   'route' => 'portal.sakramen-anggota.komuni'],
            'KRISMA'         => ['label' => 'Krisma',         'icon' => 'bi-fire',            'color' => 'warning',   'route' => 'portal.sakramen-anggota.krisma'],
            'PERNIKAHAN'     => ['label' => 'Pernikahan',     'icon' => 'bi-heart-fill',      'color' => 'danger',    'route' => 'portal.sakramen-anggota.pernikahan'],
            'MINYAK_SUCI'    => ['label' => 'Minyak Suci',   'icon' => 'bi-moisture',        'color' => 'secondary', 'route' => 'portal.sakramen-anggota.minyak-suci'],
        ];
        @endphp

        <div class="row g-3">
            @foreach ($items as $jenis => $info)
                @php $s = $sakramenList->get($jenis); @endphp
                <div class="col-12 col-sm-6 col-lg-4">
                    <a href="{{ route($info['route'], $anggota) }}" class="text-decoration-none">
                        <div class="card h-100 border-0 shadow-sm" style="transition:.2s" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='none'">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-light-{{ $info['color'] }}"
                                    style="width:56px;height:56px;flex-shrink:0;">
                                    <i class="bi {{ $info['icon'] }} fs-4 text-{{ $info['color'] }}"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $info['label'] }}</div>
                                    @if ($s)
                                        <div class="small text-muted">{{ $s->tanggal_penerimaan?->format('d M Y') }}</div>
                                        <span class="badge bg-light-success text-success small">Sudah diterima</span>
                                    @else
                                        <span class="badge bg-light-secondary text-muted small">Belum ada data</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            <a href="{{ route('portal.keluarga-saya.show') }}" class="btn btn-light-secondary">
                <i class="bi bi-arrow-left me-1"></i>Kembali ke Keluarga Saya
            </a>
        </div>

    </section>
</div>
@endsection
