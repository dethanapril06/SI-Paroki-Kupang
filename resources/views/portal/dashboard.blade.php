@extends('layouts.portal')

@section('title', 'Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('template/assets/extensions/apexcharts/apexcharts.css') }}">
@endpush

@section('content')
    @php
        /** @var \App\Models\User $authUser */
        $authUser = auth()->user()->loadMissing('roles');
    @endphp

    <div class="page-content">

        {{-- ── GREETING ─────────────────────────────────────────────────────── --}}
        <div class="row mb-1">
            <div class="col-12">
                <div class="card bg-primary text-white mb-0"
                    style="background: linear-gradient(135deg, #435ebe 0%, #6c8ff0 100%) !important; border:none;">
                    <div class="card-body d-flex align-items-center gap-4 py-4">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($umat?->nama ?? auth()->user()->name) }}&background=ffffff&color=435ebe&size=80&bold=true&font-size=0.4"
                            alt="{{ $umat?->nama }}"
                            class="rounded-circle"
                            style="width:72px;height:72px;flex-shrink:0;">
                        <div>
                            <h4 class="fw-bold mb-1 text-white">
                                Selamat datang, {{ $umat?->nama ?? auth()->user()->name }}!
                            </h4>
                            <div class="opacity-75 small">
                                <i class="bi bi-geo-alt me-1"></i>
                                KUB {{ $kub?->nama ?? '-' }} &nbsp;·&nbsp;
                                Wilayah {{ $wilayah?->nama ?? '-' }}
                                &nbsp;·&nbsp;
                                {{ $authUser->roles->pluck('label')->join(' & ') }}
                            </div>
                        </div>
                        @if ($pendingMutasi > 0)
                            <div class="ms-auto text-end">
                                <div class="badge bg-warning text-dark fs-6 px-3 py-2">
                                    <i class="bi bi-hourglass-split me-1"></i>
                                    {{ $pendingMutasi }} Request Pending
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ── STAT CARDS KETUA KUB (hanya jika ketua_kub dan ada data KUB) ── --}}
        @if($authUser->isKetuaKub() && $kubSaya && $kubStats)
            <div class="row mb-1">
                <div class="col-12">
                    <div class="d-flex align-items-center mb-2 mt-3">
                        <i class="bi bi-diagram-3-fill text-primary me-2 fs-5"></i>
                        <h6 class="mb-0 fw-bold">Statistik KUB {{ $kubSaya->nama }}</h6>
                        <a href="{{ route('portal.kub.show') }}" class="btn btn-sm btn-outline-primary ms-auto">
                            <i class="bi bi-eye me-1"></i>Lihat Detail
                        </a>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card text-center">
                        <div class="card-body py-3">
                            <div class="fs-2 fw-bold text-primary">{{ $kubStats['total_umat'] }}</div>
                            <div class="small text-muted">Total Umat</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card text-center">
                        <div class="card-body py-3">
                            <div class="fs-2 fw-bold text-success">{{ $kubStats['total_keluarga'] }}</div>
                            <div class="small text-muted">Total Keluarga</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card text-center">
                        <div class="card-body py-3">
                            <div class="fs-2 fw-bold text-info">{{ $kubStats['baptis_tahun'] }}</div>
                            <div class="small text-muted">Baptis {{ now()->year }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card text-center">
                        <div class="card-body py-3">
                            <div class="fs-2 fw-bold text-warning">{{ $kubStats['mutasi_tahun'] }}</div>
                            <div class="small text-muted">Mutasi {{ now()->year }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabel keluarga dalam KUB --}}
            @if($daftarKeluarga->isNotEmpty())
                <div class="row mb-1">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-house-fill me-2 text-success"></i>Keluarga dalam KUB
                                </h5>
                                <a href="{{ route('portal.keluarga.index') }}" class="btn btn-sm btn-outline-success">
                                    Kelola Keluarga
                                </a>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Kepala Keluarga</th>
                                                <th>Alamat</th>
                                                <th class="text-center">Jumlah Umat</th>
                                                <th width="80">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($daftarKeluarga->take(5) as $kel)
                                                <tr>
                                                    <td>{{ $kel->kepalaKeluarga?->nama ?? '-' }}</td>
                                                    <td>{{ Str::limit($kel->alamat, 40) }}</td>
                                                    <td class="text-center">{{ $kel->total_umat }}</td>
                                                    <td>
                                                        <a href="{{ route('portal.keluarga.show', $kel) }}"
                                                            class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        {{-- ── KATEGORIAL YANG DIPIMPIN (hanya jika ketua_kategorial) ─────── --}}
        @if($authUser->isKetuaKategorial() && $kategorialDiPimpin->isNotEmpty())
            <div class="row mb-1">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-collection-fill me-2" style="color:#6f42c1"></i>Kategorial yang Anda Pimpin
                            </h5>
                            <a href="{{ route('portal.kategorial.index') }}" class="btn btn-sm btn-outline-secondary">
                                Kelola Kategorial
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                @foreach($kategorialDiPimpin as $kat)
                                    <div class="col-12 col-md-6">
                                        <div class="d-flex align-items-center justify-content-between p-3 border rounded">
                                            <div>
                                                <div class="fw-bold">{{ $kat->nama }}</div>
                                                <div class="small text-muted">{{ $kat->anggota_aktif }} anggota aktif</div>
                                            </div>
                                            <a href="{{ route('portal.kategorial.show', $kat) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye me-1"></i>Detail
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ── DATA PRIBADI & KELUARGA ──────────────────────────────────────── --}}
        <section class="row">

            {{-- Info Pribadi --}}
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-person-fill me-2 text-primary"></i>Data Pribadi
                        </h5>
                        @if ($umat)
                            <a href="{{ route('portal.profil.edit') }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil-square me-1"></i>Edit
                            </a>
                        @endif
                    </div>
                    <div class="card-body">
                        @if ($umat)
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td class="text-muted fw-semibold" style="width:45%">Nama Lengkap</td>
                                    <td>{{ $umat->nama }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold">Jenis Kelamin</td>
                                    <td>{{ $umat->jenis_kelamin ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold">Tempat, Tgl Lahir</td>
                                    <td>
                                        {{ $umat->tempat_lahir ?? '-' }},
                                        {{ $umat->tanggal_lahir?->format('d M Y') ?? '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold">Status Pernikahan</td>
                                    <td>{{ $umat->status_pernikahan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold">Pekerjaan</td>
                                    <td>{{ $umat->pekerjaan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold">Pendidikan</td>
                                    <td>{{ $umat->pendidikan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold">No. Telepon</td>
                                    <td>{{ $umat->no_telepon ?? '-' }}</td>
                                </tr>
                            </table>
                        @else
                            <p class="text-muted text-center py-3">Data umat belum terhubung ke akun ini.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Info Keluarga --}}
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-house-fill me-2 text-success"></i>Data Keluarga & Lingkungan
                        </h5>
                        @if ($keluarga)
                            <a href="{{ route('portal.keluarga-saya.show') }}"
                                class="btn btn-sm btn-outline-success">
                                <i class="bi bi-arrow-right me-1"></i>Kelola Keluarga
                            </a>
                        @endif
                    </div>
                    <div class="card-body">
                        @if ($keluarga)
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td class="text-muted fw-semibold" style="width:45%">Kepala Keluarga</td>
                                    <td>
                                        {{ $keluarga->kepalaKeluarga?->nama ?? '-' }}
                                        @if ($keluarga->kepala_keluarga_id === $umat?->id)
                                            <span class="badge bg-light-primary text-primary ms-1">Anda</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold">Hubungan dalam KK</td>
                                    <td>{{ $umat?->hubungan_keluarga ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold">Alamat</td>
                                    <td>{{ $keluarga->alamat ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold">Status Tempat Tinggal</td>
                                    <td>{{ $keluarga->status_tempat_tinggal ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold">KUB</td>
                                    <td>{{ $kub?->nama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold">Wilayah</td>
                                    <td>{{ $wilayah?->nama ?? '-' }}</td>
                                </tr>
                            </table>
                        @else
                            <p class="text-muted text-center py-3">Belum terdaftar dalam keluarga.</p>
                        @endif
                    </div>
                </div>
            </div>

        </section>

        {{-- ── SAKRAMEN + KATEGORIAL YANG DIIKUTI ──────────────────────────── --}}
        <section class="row">

            {{-- Sakramen yang Diterima --}}
            <div class="col-12 col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-droplet-fill me-2 text-info"></i>Sakramen yang Diterima
                        </h5>
                    </div>
                    <div class="card-body">
                        @php
                            $jenisSakramen = [
                                'BAPTIS'         => ['label' => 'Baptis',         'icon' => 'bi-droplet',    'color' => 'info'],
                                'KOMUNI_PERTAMA' => ['label' => 'Komuni Pertama', 'icon' => 'bi-cup-hot',    'color' => 'success'],
                                'KRISMA'         => ['label' => 'Krisma',         'icon' => 'bi-fire',       'color' => 'warning'],
                                'PERNIKAHAN'     => ['label' => 'Pernikahan',     'icon' => 'bi-heart-fill', 'color' => 'danger'],
                                'MINYAK_SUCI'    => ['label' => 'Minyak Suci',   'icon' => 'bi-moisture',   'color' => 'secondary'],
                            ];
                            $sakramenMap = $sakramenDiterima->keyBy('jenis_sakramen');
                        @endphp
                        <div class="row g-3">
                            @foreach ($jenisSakramen as $jenis => $info)
                                @php $item = $sakramenMap->get($jenis); @endphp
                                <div class="col-6 col-md-4">
                                    <div class="d-flex align-items-center gap-2 p-3 rounded
                                        {{ $item ? 'bg-light-'.$info['color'] : 'bg-light' }}">
                                        <i class="bi {{ $info['icon'] }} fs-4
                                            {{ $item ? 'text-'.$info['color'] : 'text-muted' }}"></i>
                                        <div>
                                            <div class="small fw-semibold
                                                {{ $item ? 'text-'.$info['color'] : 'text-muted' }}">
                                                {{ $info['label'] }}
                                            </div>
                                            @if ($item)
                                                <div class="small text-muted">
                                                    {{ $item->tanggal_penerimaan?->format('d M Y') ?? '-' }}
                                                </div>
                                            @else
                                                <div class="small text-muted">Belum</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kategorial yang diikuti --}}
            <div class="col-12 col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-diagram-3-fill me-2" style="color:#6f42c1"></i>Kategorial Aktif
                        </h5>
                    </div>
                    <div class="card-body">
                        @forelse ($kategorialAktif as $kat)
                            <div class="d-flex align-items-center justify-content-between py-2
                                {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div>
                                    <div class="fw-semibold">{{ $kat->nama }}</div>
                                    <div class="small text-muted">
                                        {{ $kat->pivot->jabatan ?? 'Anggota' }}
                                        @if ($kat->pivot->bidang_tugas)
                                            &mdash; {{ $kat->pivot->bidang_tugas }}
                                        @endif
                                    </div>
                                </div>
                                <span class="badge bg-light-success text-success">Aktif</span>
                            </div>
                        @empty
                            <p class="text-muted text-center py-3">
                                <i class="bi bi-people d-block fs-2 mb-2"></i>
                                Belum terdaftar di kategorial apapun.
                            </p>
                        @endforelse
                    </div>
                </div>
            </div>

        </section>

        {{-- ── HISTORI MUTASI + AKSI CEPAT ─────────────────────────────────── --}}
        <section class="row">

            {{-- Histori Mutasi --}}
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-arrow-left-right me-2 text-warning"></i>Histori Request Mutasi
                        </h5>
                        <a href="{{ route('portal.mutasi.index') }}" class="btn btn-sm btn-outline-secondary">
                            Lihat semua
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Jenis</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($mutasiTerbaru as $m)
                                        <tr>
                                            <td>
                                                @if ($m->jenis === 'agama')
                                                    <span class="badge bg-warning text-dark">Agama</span>
                                                @elseif ($m->jenis === 'keluarga')
                                                    <span class="badge bg-success">Keluarga</span>
                                                @else
                                                    <span class="badge bg-info">Umat</span>
                                                @endif
                                            </td>
                                            <td>{{ $m->tanggal->format('d M Y') }}</td>
                                            <td>
                                                @if ($m->isPending())
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="bi bi-hourglass-split me-1"></i>Pending
                                                    </span>
                                                @elseif ($m->isDisetujui())
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle me-1"></i>Disetujui
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="bi bi-x-circle me-1"></i>Ditolak
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-4">
                                                <i class="bi bi-inbox d-block fs-3 mb-2"></i>
                                                Belum ada histori mutasi.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Aksi Cepat --}}
            <div class="col-12 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-lightning-fill me-2 text-warning"></i>Aksi Cepat
                        </h5>
                    </div>
                    <div class="card-body d-grid gap-2">
                        <a href="{{ route('portal.mutasi.umat.create') }}" class="btn btn-outline-info text-start">
                            <i class="bi bi-person-walking me-2"></i>Ajukan Mutasi Umat
                        </a>
                        <a href="{{ route('portal.mutasi.keluarga.create') }}" class="btn btn-outline-success text-start">
                            <i class="bi bi-house-door me-2"></i>Ajukan Mutasi Keluarga
                        </a>
                        <a href="{{ route('portal.mutasi.agama.create') }}" class="btn btn-outline-warning text-start">
                            <i class="bi bi-arrow-repeat me-2"></i>Ajukan Mutasi Agama
                        </a>
                        <hr>
                        @if($authUser->isKetuaKub() && $kubSaya)
                            <a href="{{ route('portal.keluarga.index') }}" class="btn btn-outline-success text-start">
                                <i class="bi bi-house-fill me-2"></i>Kelola Keluarga KUB
                            </a>
                            <a href="{{ route('portal.umat.index') }}" class="btn btn-outline-primary text-start">
                                <i class="bi bi-people-fill me-2"></i>Kelola Umat KUB
                            </a>
                        @endif
                        @if($authUser->isKetuaKategorial())
                            <a href="{{ route('portal.kategorial.index') }}" class="btn btn-outline-secondary text-start">
                                <i class="bi bi-collection-fill me-2"></i>Kelola Kategorial
                            </a>
                        @endif
                        <hr>
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary text-start">
                            <i class="bi bi-person-gear me-2"></i>Edit Profil Akun
                        </a>
                    </div>
                </div>
            </div>

        </section>

    </div>
@endsection
