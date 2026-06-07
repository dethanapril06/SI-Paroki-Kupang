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

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection
