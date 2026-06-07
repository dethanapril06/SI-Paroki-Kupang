@extends('layouts.pastor')

@section('title', 'Riwayat Penerimaan Sakramen')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Riwayat Penerimaan Sakramen</h3>
                    <p class="text-subtitle text-muted">
                        Log administrasi penerimaan sakramen paroki (Read-Only)
                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('pastor.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Log Sakramen</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            {{-- Tabs/Navigation --}}
            <div class="card mb-3 shadow-sm border-0">
                <div class="card-body p-2 bg-light">
                    <ul class="nav nav-pills nav-fill justify-content-center d-flex flex-wrap gap-1">
                        @php
                            $tabs = [
                                'semua' => ['label' => 'Semua Sakramen', 'icon' => 'bi-grid-fill'],
                                'baptis' => ['label' => 'Baptis', 'icon' => 'bi-droplet-fill'],
                                'komuni_pertama' => ['label' => 'Komuni Pertama', 'icon' => 'bi-bookmark-star-fill'],
                                'krisma' => ['label' => 'Krisma', 'icon' => 'bi-star-fill'],
                                'pernikahan' => ['label' => 'Pernikahan', 'icon' => 'bi-heart-fill'],
                                'minyak_suci' => ['label' => 'Minyak Suci', 'icon' => 'bi-activity'],
                            ];
                        @endphp
                        @foreach ($tabs as $key => $tab)
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center justify-content-center gap-2 py-2 px-3 {{ $jenis === $key ? 'active bg-primary text-white' : 'text-secondary bg-white' }}"
                                    href="{{ route('pastor.sakramen.index', ['jenis' => $key]) }}"
                                    style="{{ $jenis === $key ? 'background-color: #800020 !important;' : '' }}">
                                    <i class="bi {{ $tab['icon'] }}"></i>
                                    <span>{{ $tab['label'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Data Table Card --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        Kategori: <span class="badge bg-primary">{{ $tabs[$jenis]['label'] ?? 'Semua' }}</span>
                    </h5>
                    <span class="text-muted text-sm">Menampilkan {{ $sakramenList->count() }} data pada halaman ini</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="50" class="text-center">No</th>
                                    <th>Penerima Sakramen</th>
                                    <th class="text-center">Jenis Sakramen</th>
                                    <th>Tanggal Penerimaan</th>
                                    <th>Paroki / Tempat</th>
                                    <th>Pelayan/Klerus</th>
                                    <th>Info Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($sakramenList as $s)
                                    <tr>
                                        <td class="text-center">
                                            {{ ($sakramenList->currentPage() - 1) * $sakramenList->perPage() + $loop->iteration }}
                                        </td>
                                        <td>
                                            @if ($s->umat)
                                                <a href="{{ route('pastor.umat.show', $s->umat) }}" class="fw-bold">
                                                    {{ $s->umat->nama }}
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                            @if ($s->umat?->status_almarhum)
                                                <span class="badge bg-dark ms-1">Alm.</span>
                                            @endif
                                            <br>
                                            <small class="text-muted">KUB: {{ $s->umat?->keluarga?->kub->nama ?? '-' }}</small>
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $badgeColor = match ($s->jenis_sakramen) {
                                                    'BAPTIS' => 'bg-light-info text-info border border-info',
                                                    'KOMUNI_PERTAMA' => 'bg-light-success text-success border border-success',
                                                    'KRISMA' => 'bg-light-warning text-warning border border-warning',
                                                    'PERNIKAHAN' => 'bg-light-danger text-danger border border-danger',
                                                    'MINYAK_SUCI' => 'bg-light-secondary text-secondary border border-secondary',
                                                    default => 'bg-light text-dark',
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeColor }}">
                                                {{ str_replace('_', ' ', $s->jenis_sakramen) }}
                                            </span>
                                        </td>
                                        <td>
                                            <strong>{{ $s->tanggal_penerimaan ? $s->tanggal_penerimaan->format('d M Y') : '-' }}</strong>
                                        </td>
                                        <td>{{ $s->paroki->nama ?? 'Paroki Asal' }}</td>
                                        <td>
                                            @if ($s->klerus_id)
                                                {{ $s->klerus->nama ?? '-' }}
                                            @elseif ($s->detail && isset($s->detail->nama_pemberi))
                                                <span class="text-muted">{{ $s->detail->nama_pemberi }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="d-block">
                                                @if ($s->jenis_sakramen === 'BAPTIS' && $s->baptis)
                                                    <strong>Nama Baptis:</strong> {{ $s->baptis->nama_baptis ?? '-' }}<br>
                                                    <strong>Sumber:</strong> {{ $s->baptis->sumber_baptis ?? '-' }}<br>
                                                    <strong>Wali Bapak:</strong> {{ $s->baptis->nama_bapak_baptis ?? '-' }}<br>
                                                    <strong>Wali Ibu:</strong> {{ $s->baptis->nama_ibu_baptis ?? '-' }}
                                                @elseif ($s->jenis_sakramen === 'KRISMA' && $s->krisma)
                                                    <strong>Nama Krisma:</strong> {{ $s->krisma->nama_krisma ?? '-' }}<br>
                                                    <strong>Uskup:</strong> {{ $s->krisma->uskup->nama ?? '-' }}
                                                @elseif ($s->jenis_sakramen === 'PERNIKAHAN' && $s->pernikahan)
                                                    <strong>Pasangan:</strong> {{ $s->pernikahan->nama_pasangan }} <br>
                                                    <strong>Agama Pasangan:</strong> {{ $s->pernikahan->agama_pasangan }} <br>
                                                    <strong>Jenis:</strong> {{ str_replace('_', ' - ', $s->pernikahan->jenis_pernikahan ?? '-') }}
                                                @elseif ($s->jenis_sakramen === 'MINYAK_SUCI' && $s->minyakSuci)
                                                    <strong>Tempat:</strong> {{ $s->minyakSuci->tempat_terima ?? '-' }}<br>
                                                    <strong>Sebab:</strong> {{ $s->minyakSuci->keterangan_sebab ?? '-' }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            Tidak ditemukan riwayat data sakramen pada kategori ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </section>
    </div>
@endsection
