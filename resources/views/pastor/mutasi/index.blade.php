@extends('layouts.pastor')

@section('title', 'Riwayat Mutasi Umat & Keluarga')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Riwayat Mutasi Umat & Keluarga</h3>
                    <p class="text-subtitle text-muted">
                        Daftar riwayat kepindahan, perpindahan KUB, stasi, atau perubahan status agama (Read-Only)
                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('pastor.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Riwayat Mutasi</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            {{-- Status Filters --}}
            <div class="card mb-3 shadow-sm border-0">
                <div class="card-body p-2 bg-light">
                    <ul class="nav nav-pills nav-fill justify-content-center d-flex flex-wrap gap-1">
                        @php
                            $statuses = [
                                'semua' => ['label' => 'Semua Status', 'icon' => 'bi-list-ul', 'class' => 'bg-white text-secondary'],
                                'pending' => ['label' => 'Menunggu Persetujuan (Pending)', 'icon' => 'bi-hourglass-split', 'class' => 'bg-light-warning text-warning'],
                                'disetujui' => ['label' => 'Disetujui', 'icon' => 'bi-check-circle-fill', 'class' => 'bg-light-success text-success'],
                                'ditolak' => ['label' => 'Ditolak', 'icon' => 'bi-x-circle-fill', 'class' => 'bg-light-danger text-danger'],
                            ];
                        @endphp
                        @foreach ($statuses as $key => $sData)
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center justify-content-center gap-2 py-2 px-3 {{ $status === $key ? 'active bg-primary text-white' : 'text-secondary bg-white' }}"
                                    href="{{ route('pastor.mutasi.index', ['status' => $key]) }}"
                                    style="{{ $status === $key ? 'background-color: #800020 !important;' : '' }}">
                                    <i class="bi {{ $sData['icon'] }}"></i>
                                    <span>{{ $sData['label'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Table Card --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        Log Mutasi: <span class="badge bg-primary">{{ ucfirst($status) }}</span>
                    </h5>
                    <span class="text-muted text-sm">Menampilkan {{ $mutasiList->count() }} log mutasi</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0" id="table1">
                            <thead class="table-light">
                                <tr>
                                    <th width="50" class="text-center">No</th>
                                    <th>Tanggal</th>
                                    <th>Jenis Mutasi</th>
                                    <th>Keterangan Objek & Jalur Mutasi</th>
                                    <th class="text-center">Status</th>
                                    <th>Pemohon & Pemroses</th>
                                    <th>Catatan Administrasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($mutasiList as $m)
                                    <tr>
                                        <td class="text-center">
                                            {{ ($mutasiList->currentPage() - 1) * $mutasiList->perPage() + $loop->iteration }}
                                        </td>
                                        <td>
                                            <strong>{{ $m->tanggal ? $m->tanggal->format('d M Y') : '-' }}</strong>
                                        </td>
                                        <td>
                                            @php
                                                $jenisBadge = match ($m->jenis) {
                                                    'umat' => 'bg-light-primary text-primary',
                                                    'keluarga' => 'bg-light-info text-info',
                                                    'agama' => 'bg-light-danger text-danger',
                                                    default => 'bg-light text-dark',
                                                };
                                            @endphp
                                            <span class="badge {{ $jenisBadge }} text-capitalize fw-bold">
                                                Mutasi {{ $m->jenis }}
                                            </span>
                                            @if ($m->jenis === 'umat' && $m->mutasiUmat?->sub_jenis)
                                                <div class="text-xs text-muted mt-1 font-monospace">({{ $m->mutasiUmat->sub_jenis }})</div>
                                            @elseif ($m->jenis === 'keluarga' && $m->mutasiKeluarga?->sub_jenis)
                                                <div class="text-xs text-muted mt-1 font-monospace">({{ $m->mutasiKeluarga->sub_jenis }})</div>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($m->jenis === 'umat' && $m->mutasiUmat)
                                                <strong>Umat:</strong>
                                                <a href="{{ route('pastor.umat.show', $m->mutasiUmat->umat) }}" class="fw-bold">
                                                    {{ $m->mutasiUmat->umat->nama ?? '-' }}
                                                </a>
                                                <div class="mt-1 small">
                                                    @if ($m->mutasiUmat->nomor_surat)
                                                        <div class="text-muted"><i class="bi bi-file-earmark-text me-1"></i>No. Surat: {{ $m->mutasiUmat->nomor_surat }}</div>
                                                    @endif

                                                    @if ($m->mutasiUmat->kubAsal || $m->mutasiUmat->kubTujuan)
                                                        <span class="text-muted">KUB:</span>
                                                        <span class="text-danger font-monospace">{{ $m->mutasiUmat->kubAsal->nama ?? 'Luar Paroki' }}</span>
                                                        <i class="bi bi-arrow-right mx-1 text-primary"></i>
                                                        <span class="text-success font-monospace">{{ $m->mutasiUmat->kubTujuan->nama ?? 'Luar Paroki' }}</span>
                                                        <br>
                                                    @endif

                                                    @if ($m->mutasiUmat->parokiAsal || $m->mutasiUmat->parokiTujuan)
                                                        <span class="text-muted">Paroki:</span>
                                                        <span class="text-danger">{{ $m->mutasiUmat->parokiAsal->nama ?? 'Luar Paroki' }}</span>
                                                        <i class="bi bi-arrow-right mx-1 text-primary"></i>
                                                        <span class="text-success">{{ $m->mutasiUmat->parokiTujuan->nama ?? 'Luar Paroki' }}</span>
                                                    @endif
                                                </div>
                                            @elseif ($m->jenis === 'keluarga' && $m->mutasiKeluarga)
                                                <strong>Kepala KK:</strong>
                                                <a href="{{ route('pastor.keluarga.show', $m->mutasiKeluarga->keluarga) }}" class="fw-bold">
                                                    {{ $m->mutasiKeluarga->keluarga->kepalaKeluarga->nama ?? '-' }}
                                                </a>
                                                <div class="mt-1 small">
                                                    @if ($m->mutasiKeluarga->nomor_surat)
                                                        <div class="text-muted"><i class="bi bi-file-earmark-text me-1"></i>No. Surat: {{ $m->mutasiKeluarga->nomor_surat }}</div>
                                                    @endif

                                                    @if ($m->mutasiKeluarga->kubAsal || $m->mutasiKeluarga->kubTujuan)
                                                        <span class="text-muted">KUB:</span>
                                                        <span class="text-danger font-monospace">{{ $m->mutasiKeluarga->kubAsal->nama ?? 'Luar Paroki' }}</span>
                                                        <i class="bi bi-arrow-right mx-1 text-primary"></i>
                                                        <span class="text-success font-monospace">{{ $m->mutasiKeluarga->kubTujuan->nama ?? 'Luar Paroki' }}</span>
                                                        <br>
                                                    @endif

                                                    @if ($m->mutasiKeluarga->parokiAsal || $m->mutasiKeluarga->parokiTujuan)
                                                        <span class="text-muted">Paroki:</span>
                                                        <span class="text-danger">{{ $m->mutasiKeluarga->parokiAsal->nama ?? 'Luar Paroki' }}</span>
                                                        <i class="bi bi-arrow-right mx-1 text-primary"></i>
                                                        <span class="text-success">{{ $m->mutasiKeluarga->parokiTujuan->nama ?? 'Luar Paroki' }}</span>
                                                    @endif
                                                </div>
                                            @elseif ($m->jenis === 'agama' && $m->mutasiAgama)
                                                <strong>Umat:</strong>
                                                <a href="{{ route('pastor.umat.show', $m->mutasiAgama->umat) }}" class="fw-bold">
                                                    {{ $m->mutasiAgama->umat->nama ?? '-' }}
                                                </a>
                                                <div class="mt-1 small">
                                                    <span class="text-muted">Agama:</span>
                                                    <span class="badge bg-light-danger text-danger">{{ $m->mutasiAgama->agama_asal }}</span>
                                                    <i class="bi bi-arrow-right mx-1 text-primary"></i>
                                                    <span class="badge bg-light-success text-success">{{ $m->mutasiAgama->agama_tujuan }}</span>
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $statusClass = match ($m->status) {
                                                    'pending' => 'bg-light-warning text-warning border border-warning',
                                                    'disetujui' => 'bg-light-success text-success border border-success',
                                                    'ditolak' => 'bg-light-danger text-danger border border-danger',
                                                    default => 'bg-light text-dark',
                                                };
                                            @endphp
                                            <span class="badge {{ $statusClass }} py-2 px-3 fw-bold">
                                                {{ strtoupper($m->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="d-block">
                                                <strong>Pemohon:</strong> <br>
                                                @if ($m->pemohon)
                                                    <span class="text-secondary">{{ $m->pemohon->nama }} (Umat)</span>
                                                @else
                                                    <span class="text-muted">Sekretariat (Internal)</span>
                                                @endif
                                            </small>
                                            <small class="d-block mt-1">
                                                <strong>Diproses Oleh:</strong> <br>
                                                @if ($m->diprosesOleh)
                                                    <span class="text-secondary">{{ $m->diprosesOleh->name }}</span>
                                                    @if ($m->diproses_pada)
                                                        <br><span class="text-xs text-muted">pada {{ $m->diproses_pada->format('d M Y H:i') }}</span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </small>
                                        </td>
                                        <td>
                                            <div class="small">
                                                <strong>Alasan:</strong> <br>
                                                <span class="text-secondary">{{ $m->keterangan ?? '-' }}</span>
                                            </div>
                                            @if ($m->catatan_admin)
                                                <div class="alert alert-light-secondary py-1 px-2 mt-2 mb-0 border small">
                                                    <i class="bi bi-chat-left-text me-1 text-muted"></i>
                                                    <strong>Catatan:</strong> {{ $m->catatan_admin }}
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            Tidak ditemukan riwayat mutasi dengan status ini.
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
                                Menampilkan {{ $mutasiList->firstItem() ?? 0 }} sampai {{ $mutasiList->lastItem() ?? 0 }} dari {{ $mutasiList->total() }} data
                            </small>
                        </div>
                        <div>
                            {{ $mutasiList->links('pagination::bootstrap-5') }}
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </div>
@endsection
