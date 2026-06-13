@extends('layouts.pastor')

@section('title', 'Detail Umat')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Detail Umat</h3>
                    <p class="text-subtitle text-muted">Informasi profil lengkap umat</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('pastor.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('pastor.umat.index') }}">Daftar Umat</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $umat->nama }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">

                {{-- Kolom Kiri: Profil Pribadi --}}
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Profil Umat</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th width="140">Nama Lengkap</th>
                                    <td>
                                        <strong>{{ $umat->nama }}</strong>
                                        @if ($umat->status_almarhum)
                                            <span class="badge bg-dark ms-1">Almarhum</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Jenis Kelamin</th>
                                    <td>{{ $umat->jenis_kelamin }}</td>
                                </tr>
                                <tr>
                                    <th>Tempat Lahir</th>
                                    <td>{{ $umat->tempat_lahir }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Lahir</th>
                                    <td>{{ $umat->tanggal_lahir->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Golongan Darah</th>
                                    <td>{{ $umat->golongan_darah ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Hubungan KK</th>
                                    <td>{{ $umat->hubungan_keluarga }}</td>
                                </tr>
                                <tr>
                                    <th>Status Nikah</th>
                                    <td>{{ $umat->status_pernikahan }}</td>
                                </tr>
                                <tr>
                                    <th>No. Telepon</th>
                                    <td>{{ $umat->no_telepon ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Pendidikan</th>
                                    <td>{{ $umat->pendidikan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Pekerjaan</th>
                                    <td>{{ $umat->pekerjaan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Nama Ayah</th>
                                    <td>{{ $umat->nama_ayah ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Nama Ibu</th>
                                    <td>{{ $umat->nama_ibu ?? '-' }}</td>
                                </tr>
                                @if ($umat->penyandang_disabilitas)
                                    <tr>
                                        <td colspan="2">
                                            <span class="badge bg-info">Penyandang Disabilitas</span>
                                        </td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan: Struktural & Organisasi --}}
                <div class="col-md-7">

                    {{-- Info Struktural --}}
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Informasi Struktural & Alamat</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th width="120">Keluarga</th>
                                    <td>
                                        @if ($umat->keluarga)
                                            <a href="{{ route('pastor.keluarga.show', $umat->keluarga) }}">
                                                {{ $umat->keluarga->kepalaKeluarga->nama ?? '-' }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Alamat</th>
                                    <td>{{ $umat->keluarga->alamat ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>KUB</th>
                                    <td>{{ $umat->keluarga->kub->nama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Wilayah</th>
                                    <td>{{ $umat->keluarga->kub->wilayah->nama ?? '-' }}</td>
                                </tr>
                            </table>

                            @if ($umat->kubDiketuai->isNotEmpty())
                                <div class="alert alert-light-success color-success py-2 mt-2 mb-0">
                                    <i class="bi bi-star-fill me-1"></i>
                                    Ketua KUB: <strong>{{ $umat->kubDiketuai->pluck('nama')->join(', ') }}</strong>
                                </div>
                            @endif
                            @if ($umat->kategorialDiketuai->isNotEmpty())
                                <div class="alert alert-light-info color-info py-2 mt-2 mb-0">
                                    <i class="bi bi-star-fill me-1"></i>
                                    Ketua Kategorial:
                                    <strong>{{ $umat->kategorialDiketuai->pluck('nama')->join(', ') }}</strong>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Akun Login --}}
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Akun Pengguna</h5>
                        </div>
                        <div class="card-body">
                            @if ($umat->user)
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <th width="120">Email</th>
                                        <td>{{ $umat->user->email }}</td>
                                    </tr>
                                    <tr>
                                        <th>Role</th>
                                        <td>
                                            {{ $umat->user->roles->pluck('label')->join(' · ') }}
                                        </td>
                                    </tr>
                                </table>
                            @else
                                <p class="text-muted mb-0">
                                    <i class="bi bi-exclamation-circle me-1"></i>
                                    Umat belum melakukan registrasi akun login.
                                </p>
                            @endif
                    </div>

                    {{-- Riwayat Sakramen --}}
                    @php
                        $baptis = $umat->sakramen->firstWhere('jenis_sakramen', 'BAPTIS');
                        $komuni = $umat->sakramen->firstWhere('jenis_sakramen', 'KOMUNI_PERTAMA');
                        $krisma = $umat->sakramen->firstWhere('jenis_sakramen', 'KRISMA');
                        $pernikahan = $umat->sakramen->firstWhere('jenis_sakramen', 'PERNIKAHAN');
                        $minyakSuciList = $umat->sakramen->where('jenis_sakramen', 'MINYAK_SUCI')->sortBy('tanggal_penerimaan');
                    @endphp
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Riwayat Sakramen</h5>
                        </div>
                        <div class="card-body">
                            <div class="accordion" id="accordionSakramen">
                                {{-- BAPTIS --}}
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingBaptis">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBaptis">
                                            <i class="bi bi-droplet-fill text-info me-2"></i>
                                            Baptis &nbsp;
                                            @if ($baptis)
                                                <span class="badge bg-light-success text-success ms-auto">Sudah Diterima</span>
                                            @else
                                                <span class="badge bg-light-secondary text-secondary ms-auto">Belum Ada Data</span>
                                            @endif
                                        </button>
                                    </h2>
                                    <div id="collapseBaptis" class="accordion-collapse collapse" data-bs-parent="#accordionSakramen">
                                        <div class="accordion-body">
                                            @if ($baptis)
                                                <table class="table table-sm table-borderless mb-0">
                                                    <tr>
                                                        <th width="180">Nama Baptis</th>
                                                        <td>{{ $baptis->baptis->nama_baptis ?? '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Tanggal Penerimaan</th>
                                                        <td>{{ $baptis->tanggal_penerimaan->format('d M Y') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Paroki</th>
                                                        <td>{{ $baptis->paroki->nama ?? '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Sumber Baptis</th>
                                                        <td>{{ $baptis->baptis->sumber_baptis ?? 'KATOLIK' }}</td>
                                                    </tr>
                                                    @if ($baptis->baptis && $baptis->baptis->sumber_baptis === 'PROTESTAN')
                                                        <tr>
                                                            <th>Gereja Asal</th>
                                                            <td>{{ $baptis->baptis->nama_gereja_protestan ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Nama Pendeta</th>
                                                            <td>{{ $baptis->baptis->nama_pemberi_protestan ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Tgl Diterima Katolik</th>
                                                            <td>{{ $baptis->baptis->tgl_diterima_katolik ? $baptis->baptis->tgl_diterima_katolik->format('d M Y') : '-' }}</td>
                                                        </tr>
                                                    @else
                                                        <tr>
                                                            <th>Pemberi Baptis</th>
                                                            <td>{{ $baptis->klerus->nama ?? '-' }}</td>
                                                        </tr>
                                                    @endif
                                                    <tr>
                                                        <th>Bapak Baptis</th>
                                                        <td>{{ $baptis->baptis->bapakBaptis->nama ?? $baptis->baptis->bapak_baptis_nama ?? '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Ibu Baptis</th>
                                                        <td>{{ $baptis->baptis->ibuBaptis->nama ?? $baptis->baptis->ibu_baptis_nama ?? '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Nomor Surat</th>
                                                        <td>{{ $baptis->nomor_surat ?? '-' }}</td>
                                                    </tr>
                                                </table>
                                            @else
                                                <p class="text-muted mb-0 small">Belum ada data Sakramen Baptis terdaftar.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- KOMUNI PERTAMA --}}
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingKomuni">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseKomuni">
                                            <i class="bi bi-cup-hot-fill text-success me-2"></i>
                                            Komuni Pertama &nbsp;
                                            @if ($komuni)
                                                <span class="badge bg-light-success text-success ms-auto">Sudah Diterima</span>
                                            @else
                                                <span class="badge bg-light-secondary text-secondary ms-auto">Belum Ada Data</span>
                                            @endif
                                        </button>
                                    </h2>
                                    <div id="collapseKomuni" class="accordion-collapse collapse" data-bs-parent="#accordionSakramen">
                                        <div class="accordion-body">
                                            @if ($komuni)
                                                <table class="table table-sm table-borderless mb-0">
                                                    <tr>
                                                        <th width="180">Tanggal Penerimaan</th>
                                                        <td>{{ $komuni->tanggal_penerimaan->format('d M Y') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Paroki</th>
                                                        <td>{{ $komuni->paroki->nama ?? '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Pemberi Sakramen</th>
                                                        <td>{{ $komuni->klerus->nama ?? '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Nomor Surat</th>
                                                        <td>{{ $komuni->nomor_surat ?? '-' }}</td>
                                                    </tr>
                                                </table>
                                            @else
                                                <p class="text-muted mb-0 small">Belum ada data Sakramen Komuni Pertama terdaftar.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- KRISMA --}}
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingKrisma">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseKrisma">
                                            <i class="bi bi-fire text-warning me-2"></i>
                                            Krisma &nbsp;
                                            @if ($krisma)
                                                <span class="badge bg-light-success text-success ms-auto">Sudah Diterima</span>
                                            @else
                                                <span class="badge bg-light-secondary text-secondary ms-auto">Belum Ada Data</span>
                                            @endif
                                        </button>
                                    </h2>
                                    <div id="collapseKrisma" class="accordion-collapse collapse" data-bs-parent="#accordionSakramen">
                                        <div class="accordion-body">
                                            @if ($krisma)
                                                <table class="table table-sm table-borderless mb-0">
                                                    <tr>
                                                        <th width="180">Nama Pelindung Krisma</th>
                                                        <td>{{ $krisma->krisma->nama_krisma ?? '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Tanggal Penerimaan</th>
                                                        <td>{{ $krisma->tanggal_penerimaan->format('d M Y') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Paroki</th>
                                                        <td>{{ $krisma->paroki->nama ?? '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Uskup/Klerus</th>
                                                        <td>{{ $krisma->krisma->uskup->nama ?? $krisma->klerus->nama ?? '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Nomor Surat</th>
                                                        <td>{{ $krisma->nomor_surat ?? '-' }}</td>
                                                    </tr>
                                                </table>
                                            @else
                                                <p class="text-muted mb-0 small">Belum ada data Sakramen Krisma terdaftar.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- PERNIKAHAN --}}
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingPernikahan">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePernikahan">
                                            <i class="bi bi-heart-fill text-danger me-2"></i>
                                            Pernikahan &nbsp;
                                            @if ($pernikahan)
                                                <span class="badge bg-light-success text-success ms-auto">Sudah Diterima</span>
                                            @else
                                                <span class="badge bg-light-secondary text-secondary ms-auto">Belum Ada Data</span>
                                            @endif
                                        </button>
                                    </h2>
                                    <div id="collapsePernikahan" class="accordion-collapse collapse" data-bs-parent="#accordionSakramen">
                                        <div class="accordion-body">
                                            @if ($pernikahan)
                                                <table class="table table-sm table-borderless mb-0">
                                                    <tr>
                                                        <th width="180">Nama Pasangan</th>
                                                        <td>
                                                            @if ($pernikahan->pernikahan && $pernikahan->pernikahan->pasangan)
                                                                <a href="{{ route('pastor.umat.show', $pernikahan->pernikahan->pasangan) }}">
                                                                    {{ $pernikahan->pernikahan->pasangan->nama }}
                                                                </a>
                                                            @else
                                                                {{ $pernikahan->pernikahan->pasangan_nama ?? '-' }}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Agama Pasangan</th>
                                                        <td>{{ $pernikahan->pernikahan->pasangan_agama ?? ($pernikahan->pernikahan->pasangan_id ? 'Katolik' : '-') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Jenis Pernikahan</th>
                                                        <td>{{ $pernikahan->pernikahan ? (\App\Models\Pernikahan::JENIS[$pernikahan->pernikahan->jenis_pernikahan] ?? $pernikahan->pernikahan->jenis_pernikahan) : '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Tanggal Pernikahan</th>
                                                        <td>{{ $pernikahan->tanggal_penerimaan->format('d M Y') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Paroki</th>
                                                        <td>{{ $pernikahan->paroki->nama ?? '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Pastor Sakramen</th>
                                                        <td>{{ $pernikahan->klerus->nama ?? '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Izin Beda Agama/Gereja</th>
                                                        <td>
                                                            <span class="badge {{ $pernikahan->pernikahan && $pernikahan->pernikahan->izin_beda_gereja ? 'bg-light-warning text-warning' : 'bg-light-secondary text-secondary' }}">
                                                                {{ $pernikahan->pernikahan && $pernikahan->pernikahan->izin_beda_gereja ? 'Ya' : 'Tidak' }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Dispensasi</th>
                                                        <td>
                                                            <span class="badge {{ $pernikahan->pernikahan && $pernikahan->pernikahan->dispensasi ? 'bg-light-warning text-warning' : 'bg-light-secondary text-secondary' }}">
                                                                {{ $pernikahan->pernikahan && $pernikahan->pernikahan->dispensasi ? 'Ya' : 'Tidak' }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Tgl Catatan Sipil</th>
                                                        <td>{{ $pernikahan->pernikahan && $pernikahan->pernikahan->tanggal_catatan_sipil ? $pernikahan->pernikahan->tanggal_catatan_sipil->format('d M Y') : '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Nomor Surat</th>
                                                        <td>{{ $pernikahan->nomor_surat ?? '-' }}</td>
                                                    </tr>
                                                </table>
                                            @else
                                                <p class="text-muted mb-0 small">Belum ada data Sakramen Pernikahan terdaftar.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- MINYAK SUCI --}}
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingMinyakSuci">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMinyakSuci">
                                            <i class="bi bi-moisture text-secondary me-2"></i>
                                            Minyak Suci &nbsp;
                                            @if ($minyakSuciList->isNotEmpty())
                                                <span class="badge bg-light-success text-success ms-auto">{{ $minyakSuciList->count() }} Kali</span>
                                            @else
                                                <span class="badge bg-light-secondary text-secondary ms-auto">Belum Ada Data</span>
                                            @endif
                                        </button>
                                    </h2>
                                    <div id="collapseMinyakSuci" class="accordion-collapse collapse" data-bs-parent="#accordionSakramen">
                                        <div class="accordion-body">
                                            @if ($minyakSuciList->isNotEmpty())
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-striped mb-0">
                                                        <thead>
                                                            <tr>
                                                                <th>No</th>
                                                                <th>Tanggal</th>
                                                                <th>Tempat</th>
                                                                <th>Pemberi</th>
                                                                <th>Sebab/Keterangan</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($minyakSuciList as $ms)
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{ $ms->tanggal_penerimaan->format('d M Y') }}</td>
                                                                    <td>{{ $ms->minyakSuci->tempat_terima ?? '-' }}</td>
                                                                    <td>{{ $ms->minyakSuci->nama_pemberi_lengkap ?? '-' }}</td>
                                                                    <td>{{ $ms->minyakSuci->keterangan_sebab ?? '-' }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <p class="text-muted mb-0 small">Belum ada data Sakramen Minyak Suci terdaftar.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Keanggotaan Kategorial --}}
                    @if ($umat->kategorial->isNotEmpty())
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Keanggotaan Kategorial</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nama Kategorial</th>
                                                <th>Jabatan</th>
                                                <th>Bidang Tugas</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($umat->kategorial as $kat)
                                                <tr>
                                                    <td>{{ $kat->nama }}</td>
                                                    <td>{{ $kat->pivot->jabatan }}</td>
                                                    <td>{{ $kat->pivot->bidang_tugas ?? '-' }}</td>
                                                    <td>
                                                        <span
                                                            class="badge {{ $kat->pivot->status === 'Aktif' ? 'bg-success' : 'bg-secondary' }}">
                                                            {{ $kat->pivot->status }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </section>
    </div>
@endsection
