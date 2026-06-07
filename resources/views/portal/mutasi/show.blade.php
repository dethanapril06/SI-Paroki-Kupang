@extends('layouts.portal')

@section('title', 'Detail Request Mutasi')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Detail Request Mutasi</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('portal.mutasi.index') }}">Portal</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('portal.mutasi.index') }}">Riwayat</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Detail</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-12 col-lg-8">

                    {{-- Status Card --}}
                    <div class="card mb-4
                        @if($mutasi->isPending()) border-warning
                        @elseif($mutasi->isDisetujui()) border-success
                        @else border-danger @endif">
                        <div class="card-body d-flex align-items-center gap-3">
                            @if ($mutasi->isPending())
                                <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center"
                                    style="width:56px;height:56px;flex-shrink:0;">
                                    <i class="bi bi-hourglass-split fs-4 text-white"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1 fw-bold text-warning">Menunggu Persetujuan</h5>
                                    <p class="mb-0 text-muted">Request Anda sedang dalam antrian review sekretariat.</p>
                                </div>
                            @elseif ($mutasi->isDisetujui())
                                <div class="rounded-circle bg-success d-flex align-items-center justify-content-center"
                                    style="width:56px;height:56px;flex-shrink:0;">
                                    <i class="bi bi-check-circle fs-4 text-white"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1 fw-bold text-success">Disetujui</h5>
                                    <p class="mb-0 text-muted">
                                        Diproses oleh {{ $mutasi->diprosesOleh->name ?? '-' }}
                                        pada {{ $mutasi->diproses_pada?->format('d M Y, H:i') }}
                                    </p>
                                </div>
                            @else
                                <div class="rounded-circle bg-danger d-flex align-items-center justify-content-center"
                                    style="width:56px;height:56px;flex-shrink:0;">
                                    <i class="bi bi-x-circle fs-4 text-white"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1 fw-bold text-danger">Ditolak</h5>
                                    <p class="mb-0 text-muted">
                                        Diproses oleh {{ $mutasi->diprosesOleh->name ?? '-' }}
                                        pada {{ $mutasi->diproses_pada?->format('d M Y, H:i') }}
                                    </p>
                                </div>
                            @endif
                        </div>
                        @if ($mutasi->catatan_admin)
                            <div class="card-footer bg-transparent">
                                <strong><i class="bi bi-chat-text me-1"></i>Catatan Sekretariat:</strong>
                                <p class="mb-0 mt-1">{{ $mutasi->catatan_admin }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Informasi Umum --}}
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title">Informasi Umum</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td class="fw-semibold text-muted" style="width:40%">Jenis Mutasi</td>
                                    <td>
                                        @if ($mutasi->jenis === 'agama')
                                            <span class="badge bg-warning text-dark">Agama</span>
                                        @elseif ($mutasi->jenis === 'keluarga')
                                            <span class="badge bg-success">Keluarga</span>
                                        @else
                                            <span class="badge bg-info">Umat</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold text-muted">Tanggal Request</td>
                                    <td>{{ $mutasi->tanggal->format('d F Y') }}</td>
                                </tr>
                                @if ($mutasi->keterangan)
                                    <tr>
                                        <td class="fw-semibold text-muted">Keterangan</td>
                                        <td>{{ $mutasi->keterangan }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    {{-- Detail per Jenis --}}
                    @if ($mutasi->jenis === 'umat' && $mutasi->mutasiUmat)
                        @php $detail = $mutasi->mutasiUmat; @endphp
                        <div class="card mb-4">
                            <div class="card-header"><h5 class="card-title">Detail Mutasi Umat</h5></div>
                            <div class="card-body">
                                <table class="table table-borderless mb-0">
                                    <tr><td class="fw-semibold text-muted" style="width:40%">Umat</td>
                                        <td>{{ $detail->umat->nama ?? '-' }}</td></tr>
                                    <tr><td class="fw-semibold text-muted">Sub Jenis</td>
                                        <td>{{ str_replace('_', ' ', $detail->sub_jenis) }}</td></tr>
                                    @if ($detail->nomor_surat)
                                        <tr><td class="fw-semibold text-muted">Nomor Surat</td>
                                            <td>{{ $detail->nomor_surat }}</td></tr>
                                    @endif
                                    <tr><td class="fw-semibold text-muted">Keluarga Asal</td>
                                        <td>{{ $detail->keluargaAsal?->kepalaKeluarga?->nama ?? '-' }}</td></tr>
                                    @if ($detail->parokiTujuan)
                                        <tr><td class="fw-semibold text-muted">Paroki Tujuan</td>
                                            <td>{{ $detail->parokiTujuan->nama }}</td></tr>
                                    @endif
                                    @if ($detail->keuskupanTujuan)
                                        <tr><td class="fw-semibold text-muted">Keuskupan Tujuan</td>
                                            <td>{{ $detail->keuskupanTujuan->nama }}</td></tr>
                                    @endif
                                </table>
                            </div>
                        </div>

                    @elseif ($mutasi->jenis === 'keluarga' && $mutasi->mutasiKeluarga)
                        @php $detail = $mutasi->mutasiKeluarga; @endphp
                        <div class="card mb-4">
                            <div class="card-header"><h5 class="card-title">Detail Mutasi Keluarga</h5></div>
                            <div class="card-body">
                                <table class="table table-borderless mb-0">
                                    <tr><td class="fw-semibold text-muted" style="width:40%">Keluarga</td>
                                        <td>{{ $detail->keluarga?->kepalaKeluarga?->nama ?? '-' }}</td></tr>
                                    <tr><td class="fw-semibold text-muted">Sub Jenis</td>
                                        <td>{{ ucfirst($detail->sub_jenis) }}</td></tr>
                                    @if ($detail->nomor_surat)
                                        <tr><td class="fw-semibold text-muted">Nomor Surat</td>
                                            <td>{{ $detail->nomor_surat }}</td></tr>
                                    @endif
                                    <tr><td class="fw-semibold text-muted">KUB Asal</td>
                                        <td>{{ $detail->kubAsal?->nama ?? '-' }}</td></tr>
                                    <tr><td class="fw-semibold text-muted">Wilayah Asal</td>
                                        <td>{{ $detail->wilayahAsal?->nama ?? '-' }}</td></tr>
                                    @if ($detail->parokiTujuan)
                                        <tr><td class="fw-semibold text-muted">Paroki Tujuan</td>
                                            <td>{{ $detail->parokiTujuan->nama }}</td></tr>
                                    @endif
                                    @if ($detail->keuskupanTujuan)
                                        <tr><td class="fw-semibold text-muted">Keuskupan Tujuan</td>
                                            <td>{{ $detail->keuskupanTujuan->nama }}</td></tr>
                                    @endif
                                </table>
                            </div>
                        </div>

                    @elseif ($mutasi->jenis === 'agama' && $mutasi->mutasiAgama)
                        @php $detail = $mutasi->mutasiAgama; @endphp
                        <div class="card mb-4">
                            <div class="card-header"><h5 class="card-title">Detail Mutasi Agama</h5></div>
                            <div class="card-body">
                                <table class="table table-borderless mb-0">
                                    <tr><td class="fw-semibold text-muted" style="width:40%">Umat</td>
                                        <td>{{ $detail->umat->nama ?? '-' }}</td></tr>
                                    <tr><td class="fw-semibold text-muted">Agama Asal</td>
                                        <td>{{ ucfirst($detail->agama_asal) }}</td></tr>
                                    <tr><td class="fw-semibold text-muted">Agama Tujuan</td>
                                        <td>{{ ucfirst($detail->agama_tujuan) }}</td></tr>
                                </table>
                            </div>
                        </div>
                    @endif

                    <a href="{{ route('portal.mutasi.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </section>
    </div>
@endsection
