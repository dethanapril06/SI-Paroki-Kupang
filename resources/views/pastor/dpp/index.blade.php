@extends('layouts.pastor')

@section('title', 'Struktur Dewan Pastoral Paroki')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Dewan Pastoral Paroki (DPP)</h3>
                    <p class="text-subtitle text-muted">
                        Daftar pengurus dan anggota Dewan Pastoral Paroki aktif (Read-Only)
                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('pastor.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Pengurus DPP</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            {{-- Filter Card --}}
            <div class="card mb-3 shadow-sm border-0">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0 text-muted"><i class="bi bi-funnel-fill me-1"></i>Filter Pencarian Pengurus</h6>
                </div>
                <div class="card-body py-3">
                    <form method="GET" action="{{ route('pastor.dpp.index') }}" class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label mb-1 fw-bold text-xs">Jabatan DPP</label>
                            <select name="jabatan" class="form-select form-select-sm">
                                <option value="">-- Semua Jabatan --</option>
                                @foreach ($listJabatan as $jab)
                                    <option value="{{ $jab }}" {{ ($filters['jabatan'] ?? '') === $jab ? 'selected' : '' }}>
                                        {{ $jab }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label mb-1 fw-bold text-xs">Status Keanggotaan</label>
                            <select name="status_aktif" class="form-select form-select-sm">
                                <option value="">-- Semua Status --</option>
                                @foreach ($listStatus as $stat)
                                    <option value="{{ $stat }}" {{ ($filters['status_aktif'] ?? '') === $stat ? 'selected' : '' }}>
                                        {{ $stat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label mb-1 fw-bold text-xs">Bidang Tugas</label>
                            <input type="text" name="bidang_tugas" class="form-control form-control-sm" placeholder="Contoh: Liturgi..."
                                value="{{ $filters['bidang_tugas'] ?? '' }}">
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-sm btn-primary flex-fill">
                                <i class="bi bi-search me-1"></i> Filter
                            </button>
                            <a href="{{ route('pastor.dpp.index') }}" class="btn btn-sm btn-light-secondary">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Table Card --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center bg-light-primary py-3">
                    <h5 class="card-title mb-0 text-primary">
                        <i class="bi bi-people-fill me-2"></i>Daftar Pengurus DPP Terdaftar
                    </h5>
                    <span class="badge bg-primary fs-6">{{ $anggota->total() }} Pengurus</span>
                </div>
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="50" class="text-center">No</th>
                                    <th>Nama Pengurus</th>
                                    <th>Jabatan</th>
                                    <th>Bidang Tugas</th>
                                    <th class="text-center">Status</th>
                                    <th>Alamat & KUB</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($anggota as $d)
                                    <tr>
                                        <td class="text-center">
                                            {{ ($anggota->currentPage() - 1) * $anggota->perPage() + $loop->iteration }}
                                        </td>
                                        <td>
                                            @if ($d->umat)
                                                <a href="{{ route('pastor.umat.show', $d->umat) }}" class="fw-bold">
                                                    {{ $d->umat->nama }}
                                                </a>
                                                @if ($d->umat->status_almarhum)
                                                    <span class="badge bg-dark ms-1">Alm.</span>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $jabColor = match ($d->jabatan) {
                                                    'Ketua' => 'bg-danger',
                                                    'Wakil Ketua' => 'bg-warning text-dark',
                                                    'Sekretaris' => 'bg-primary',
                                                    'Bendahara' => 'bg-success',
                                                    'Koordinator Bidang' => 'bg-info',
                                                    default => 'bg-secondary',
                                                };
                                            @endphp
                                            <span class="badge {{ $jabColor }} px-2 py-1">
                                                {{ $d->jabatan }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-secondary">{{ $d->bidang_tugas ?? '-' }}</span>
                                        </td>
                                        <td class="text-center">
                                            @if ($d->status_aktif === 'Aktif')
                                                <span class="badge bg-light-success text-success border border-success px-2 py-1">
                                                    <i class="bi bi-check-circle-fill me-1"></i>Aktif
                                                </span>
                                            @else
                                                <span class="badge bg-light-danger text-danger border border-danger px-2 py-1">
                                                    <i class="bi bi-x-circle-fill me-1"></i>Nonaktif
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <strong>KUB:</strong> {{ $d->umat?->keluarga?->kub->nama ?? '-' }} <br>
                                                <strong>Alamat:</strong> {{ Str::limit($d->umat?->keluarga?->alamat ?? '-', 45) }}
                                            </small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            Tidak ditemukan data pengurus DPP yang sesuai dengan filter.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap">
                        <div>
                            <small class="text-muted">
                                Menampilkan {{ $anggota->firstItem() ?? 0 }} sampai {{ $anggota->lastItem() ?? 0 }} dari {{ $anggota->total() }} pengurus
                            </small>
                        </div>
                        <div>
                            {{ $anggota->links('pagination::bootstrap-5') }}
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </div>
@endsection
