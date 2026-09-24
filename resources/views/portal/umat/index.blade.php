@extends('layouts.portal')

@section('title', 'Daftar Umat KUB')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Daftar Umat</h3>
                    <p class="text-subtitle text-muted">Seluruh anggota jemaat di {{ $myKub->nama ?? 'KUB Anda' }}</p>

                    <div class="d-flex gap-2 mb-3 flex-wrap">
                        <a href="{{ route('portal.umat.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-lg"></i> Tambah Umat Baru
                        </a>
                        <a href="{{ route('portal.mutasi.umat-kub.create') }}" class="btn btn-outline-warning">
                            <i class="bi bi-arrow-left-right"></i> Ajukan Mutasi Umat
                        </a>
                    </div>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('portal.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Daftar Umat</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        @if(isset($notifMutasiDisetujui) && $notifMutasiDisetujui->isNotEmpty())
            <div class="alert alert-light-danger color-danger border border-danger alert-dismissible fade show mb-4" role="alert">
                <h5 class="alert-heading d-flex align-items-center gap-2 mb-2 text-danger">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    Pemberitahuan Mutasi Disetujui (Data Dikeluarkan dari KUB)
                </h5>
                <p class="mb-2 small">
                    Permohonan mutasi berikut telah <strong>disetujui</strong> oleh Sekretariat Paroki. Data umat/keluarga terkait secara otomatis telah <strong>dikeluarkan / dinonaktifkan</strong> dari daftar jemaat aktif KUB:
                </p>
                <div class="table-responsive bg-white rounded p-2 border border-danger-subtle">
                    <table class="table table-sm table-borderless align-middle mb-0">
                        <thead>
                            <tr class="border-bottom text-muted small">
                                <th>Nama Umat / Keluarga</th>
                                <th>Jenis Mutasi</th>
                                <th>Tujuan</th>
                                <th>Tgl Disetujui</th>
                                <th>Status di KUB</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($notifMutasiDisetujui as $nm)
                                <tr>
                                    <td>
                                        <strong>
                                            @if($nm->jenis === 'umat')
                                                {{ $nm->mutasiUmat?->umat?->nama ?? 'Umat' }}
                                            @elseif($nm->jenis === 'keluarga')
                                                Keluarga {{ $nm->mutasiKeluarga?->keluarga?->kepalaKeluarga?->nama ?? '-' }}
                                            @else
                                                {{ $nm->mutasiAgama?->umat?->nama ?? 'Umat' }}
                                            @endif
                                        </strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger">
                                            @if($nm->jenis === 'umat')
                                                {{ str_replace('_', ' ', ucwords($nm->mutasiUmat?->sub_jenis ?? 'Pindah')) }}
                                            @elseif($nm->jenis === 'keluarga')
                                                {{ str_replace('_', ' ', ucwords($nm->mutasiKeluarga?->sub_jenis ?? 'Pindah')) }}
                                            @else
                                                Mutasi Agama
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        @if($nm->jenis === 'umat')
                                            {{ $nm->mutasiUmat?->parokiTujuan?->nama ?? $nm->mutasiUmat?->keuskupanTujuan?->nama ?? $nm->mutasiUmat?->kubTujuan?->nama ?? '-' }}
                                        @elseif($nm->jenis === 'keluarga')
                                            {{ $nm->mutasiKeluarga?->parokiTujuan?->nama ?? $nm->mutasiKeluarga?->kubTujuan?->nama ?? '-' }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        {{ $nm->diproses_pada ? $nm->diproses_pada->translatedFormat('d M Y') : ($nm->updated_at ? $nm->updated_at->translatedFormat('d M Y') : '-') }}
                                    </td>
                                    <td>
                                        <span class="badge bg-light-danger text-danger border border-danger">Dihapus / Non-aktif</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <section class="section">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="table1">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Keluarga</th>
                                    <th>Hubungan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($umat as $u)
                                    <tr>
                                        <td>{{ $u->nama }}</td>
                                        <td>{{ in_array($u->jenis_kelamin, ['L', 'Laki-laki']) ? 'Laki-laki' : 'Perempuan' }}</td>
                                        <td>{{ $u->keluarga->kepalaKeluarga->nama ?? '-' }}</td>
                                        <td><span class="badge bg-light-secondary">{{ $u->hubungan_keluarga }}</span></td>
                                        <td>
                                            <a href="{{ route('portal.umat.show', $u->id) }}"
                                                class="btn btn-sm btn-outline-primary">Detail</a>
                                            <a href="{{ route('portal.mutasi.umat-kub.create', ['umat_id' => $u->id]) }}"
                                                class="btn btn-sm btn-outline-warning" title="Ajukan Mutasi untuk umat ini">Mutasi</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
