@extends('layouts.portal')

@section('title', 'Profil KUB –')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Profil KUB</h3>
                    <p class="text-subtitle text-muted">Detail informasi kelompok umat basis Anda.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('portal.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Profil KUB</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                {{-- Bagian Kiri: Informasi Utama --}}
                <div class="col-12 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-center align-items-center flex-column">
                                <div class="avatar avatar-xl bg-primary mb-3">
                                    <span class="avatar-content"><i class="bi bi-diagram-3"></i></span>
                                </div>
                                <h3 class="text-center">{{ $kub->nama }}</h3>
                                <p class="text-small">Wilayah {{ $kub->wilayah->nama ?? '-' }}</p>
                            </div>
                            <hr>
                            <div class="mt-3">
                                <h6>Ketua KUB:</h6>
                                <p>{{ $kub->ketuaUmat->nama_lengkap ?? 'Belum ditentukan' }}</p>

                                <div class="d-grid gap-2 mt-4">
                                    <a href="{{ route('portal.kub.edit') }}" class="btn btn-primary">
                                        <i class="bi bi-pencil-square me-2"></i> Edit Nama KUB
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bagian Kanan: List Keluarga --}}
                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Daftar Keluarga di {{ $kub->nama }}</h4>
                            <a href="{{ route('portal.keluarga.index') }}" class="btn btn-sm btn-outline-primary">Lihat
                                Semua</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="table1">
                                    <thead>
                                        <tr>
                                            <th>Nama Keluarga</th>
                                            <th>Alamat</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($kub->keluarga->take(5) as $kel)
                                            <tr>
                                                <td>{{ $kel->kepalaKeluarga ? $kel->kepalaKeluarga->nama : 'Kepala Keluarga Belum Diatur' }}
                                                </td>
                                                <td>{{ Str::limit($kel->alamat, 40) }}</td>
                                                <td>
                                                    <a href="{{ route('portal.keluarga.show', $kel->id) }}"
                                                        class="btn btn-sm btn-light">Detail</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center">Belum ada data keluarga.</td>
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
