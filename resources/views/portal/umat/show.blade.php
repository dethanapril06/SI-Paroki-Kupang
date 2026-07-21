@extends('layouts.portal')

@section('title', 'Detail Umat — ' . $umat->nama)

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Detail Umat</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('portal.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('portal.umat.index') }}">Umat</a></li>
                            <li class="breadcrumb-item active">{{ $umat->nama }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">

                {{-- Sidebar Profil --}}
                <div class="col-lg-4 mb-3">
                    <div class="card text-center">
                        <div class="card-body py-4">
                            <div class="avatar avatar-xl mx-auto mb-3"
                                style="background: linear-gradient(135deg,#435ebe,#6c84e0); border-radius:50%; width:80px; height:80px; display:flex; align-items:center; justify-content:center;">
                                <span class="fw-bold text-white fs-3">{{ substr($umat->nama, 0, 1) }}</span>
                            </div>
                            <h5 class="mb-1">{{ $umat->nama }}</h5>
                            <p class="text-muted small mb-2">{{ $umat->hubungan_keluarga ?? '-' }}</p>

                            @if ($umat->status_almarhum)
                                <span class="badge bg-secondary mb-2">Almarhum/ah</span>
                            @else
                                <span class="badge bg-light-success text-success mb-2">Aktif</span>
                            @endif

                            @if ($umat->penyandang_disabilitas)
                                <br><span class="badge bg-light-warning text-warning">Penyandang Disabilitas</span>
                            @endif

                            <hr>
                            <div class="text-start px-2">
                                <small class="text-muted">Keluarga</small>
                                <p class="fw-semibold mb-1">{{ $umat->keluarga?->kepalaKeluarga?->nama ?? '-' }}</p>
                                <small class="text-muted">Alamat</small>
                                <p class="fw-semibold mb-1 small">{{ $umat->keluarga?->alamat ?? '-' }}</p>
                                <small class="text-muted">KUB</small>
                                <p class="fw-semibold mb-0">{{ $umat->keluarga?->kub?->nama ?? '-' }}</p>
                            </div>

                            <div class="d-grid gap-2 mt-3">
                                <a href="{{ route('portal.umat.edit', $umat) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-pencil-square me-1"></i>Edit Data
                                </a>
                                <a href="{{ route('portal.mutasi.umat-kub.create', ['umat_id' => $umat->id]) }}" class="btn btn-outline-warning btn-sm">
                                    <i class="bi bi-arrow-left-right me-1"></i>Ajukan Mutasi
                                </a>
                                <form action="{{ route('portal.umat.destroy', $umat) }}" method="POST"
                                    onsubmit="return confirm('Yakin hapus data {{ $umat->nama }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                        <i class="bi bi-trash me-1"></i>Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Detail Data --}}
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <ul class="nav nav-tabs" id="umatTab">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#biodata">Biodata</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#kontak">Kontak & Sosial</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#sakramen">Riwayat Sakramen</a>
                                </li>
                            </ul>
                            <div class="tab-content mt-3">

                                {{-- Tab Biodata --}}
                                <div class="tab-pane fade show active" id="biodata">
                                    <table class="table table-borderless mb-0">
                                        <tr>
                                            <td class="text-muted fw-semibold" style="width:40%">Nama Lengkap</td>
                                            <td>{{ $umat->nama }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted fw-semibold">Jenis Kelamin</td>
                                            <td>{{ $umat->jenis_kelamin ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted fw-semibold">Tempat, Tgl Lahir</td>
                                            <td>{{ $umat->tempat_lahir ?? '-' }}, {{ $umat->tanggal_lahir?->format('d M Y') ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted fw-semibold">Golongan Darah</td>
                                            <td>{{ $umat->golongan_darah ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted fw-semibold">Nama Ayah</td>
                                            <td>{{ $umat->nama_ayah ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted fw-semibold">Nama Ibu</td>
                                            <td>{{ $umat->nama_ibu ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted fw-semibold">Hubungan dalam KK</td>
                                            <td>{{ $umat->hubungan_keluarga ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>

                                {{-- Tab Kontak & Sosial --}}
                                <div class="tab-pane fade" id="kontak">
                                    <table class="table table-borderless mb-0">
                                        <tr>
                                            <td class="text-muted fw-semibold" style="width:40%">No. Telepon</td>
                                            <td>{{ $umat->no_telepon ?? '-' }}</td>
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
                                            <td class="text-muted fw-semibold">Status Keaktifan</td>
                                            <td>
                                                @if ($umat->status_keaktifan === 'aktif')
                                                    <span class="badge bg-light-success text-success">Aktif</span>
                                                @else
                                                    <span class="badge bg-light-secondary text-secondary">{{ ucfirst($umat->status_keaktifan ?? '-') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                {{-- Tab Riwayat Sakramen --}}
                                <div class="tab-pane fade" id="sakramen">
                                    @php
                                        $baptis = $umat->sakramen->firstWhere('jenis_sakramen', 'BAPTIS');
                                        $komuni = $umat->sakramen->firstWhere('jenis_sakramen', 'KOMUNI_PERTAMA');
                                        $krisma = $umat->sakramen->firstWhere('jenis_sakramen', 'KRISMA');
                                        $pernikahan = $umat->sakramen->firstWhere('jenis_sakramen', 'PERNIKAHAN');
                                        $minyakSuciList = $umat->sakramen->where('jenis_sakramen', 'MINYAK_SUCI')->sortBy('tanggal_penerimaan');
                                    @endphp
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
                                                                        <a href="{{ route('portal.umat.show', $pernikahan->pernikahan->pasangan) }}">
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
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection
