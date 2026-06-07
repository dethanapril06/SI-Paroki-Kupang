@extends('layouts.sekretariat')

@section('title', 'Detail Pernikahan')

@push('styles')
    <link rel="stylesheet" href="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.css') }}">
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Detail Pernikahan</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sekretariat.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sekretariat.sakramen.index') }}">Sakramen</a>
                            </li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('sekretariat.sakramen.pernikahan.index') }}">Pernikahan</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Detail</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            @if (session('success'))
                <div class="alert alert-light-success color-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Informasi Pernikahan</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('sekretariat.sakramen.pernikahan.edit', $sakramen) }}"
                            class="btn btn-sm btn-outline-warning">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        <form action="{{ route('sekretariat.sakramen.pernikahan.destroy', $sakramen) }}" method="POST"
                            class="delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">

                        {{-- Data Sakramen --}}
                        <div class="col-md-6">
                            <h6 class="fw-bold text-muted mb-2">Data Sakramen</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <th width="200">Umat (Katolik)</th>
                                    <td>
                                        <a href="{{ route('sekretariat.umat.show', $sakramen->umat) }}">
                                            {{ $sakramen->umat->nama ?? '-' }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Tanggal Penerimaan</th>
                                    <td>{{ $sakramen->tanggal_penerimaan->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Paroki</th>
                                    <td>{{ $sakramen->paroki->nama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Klerus Pemimpin</th>
                                    <td>{{ $sakramen->klerus->nama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Nomor Surat</th>
                                    <td>{{ $sakramen->nomor_surat ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Dicatat pada</th>
                                    <td>{{ $sakramen->created_at->format('d M Y, H:i') }}</td>
                                </tr>
                            </table>
                        </div>

                        {{-- Detail Pernikahan --}}
                        <div class="col-md-6">
                            <h6 class="fw-bold text-muted mb-2">Detail Pernikahan</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <th width="180">Jenis Pernikahan</th>
                                    <td>
                                        <span class="badge bg-warning text-dark">
                                            {{ \App\Models\Pernikahan::JENIS[$sakramen->pernikahan->jenis_pernikahan] ?? '-' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Pasangan</th>
                                    <td>
                                        @if ($sakramen->pernikahan->pasangan_id)
                                            <a href="{{ route('sekretariat.umat.show', $sakramen->pernikahan->pasangan) }}">
                                                {{ $sakramen->pernikahan->pasangan->nama }}
                                            </a>
                                        @else
                                            {{ $sakramen->pernikahan->nama_pasangan }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Agama Pasangan</th>
                                    <td>{{ $sakramen->pernikahan->agama_pasangan }}</td>
                                </tr>
                                <tr>
                                    <th>Izin Beda Gereja</th>
                                    <td>
                                        @if ($sakramen->pernikahan->izin_beda_gereja)
                                            <span class="badge bg-success">Ya</span>
                                        @else
                                            <span class="badge bg-secondary">Tidak</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Dispensasi</th>
                                    <td>
                                        @if ($sakramen->pernikahan->dispensasi)
                                            <span class="badge bg-success">Ya</span>
                                        @else
                                            <span class="badge bg-secondary">Tidak</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Tgl Nikah Katolik</th>
                                    <td>{{ $sakramen->pernikahan->tanggal_nikah_katolik?->format('d M Y') ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Tgl Catatan Sipil</th>
                                    <td>{{ $sakramen->pernikahan->tanggal_catatan_sipil?->format('d M Y') ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @push('scripts')
        <script src="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.delete-form').forEach(function(form) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Yakin ingin menghapus?',
                            text: 'Data tidak bisa dikembalikan.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Ya, hapus',
                            cancelButtonText: 'Batal'
                        }).then(r => {
                            if (r.isConfirmed) form.submit();
                        });
                    });
                });
            });
        </script>
    @endpush
@endsection
