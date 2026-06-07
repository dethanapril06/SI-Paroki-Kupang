@extends('layouts.sekretariat')

@section('title', 'Detail Mutasi Keluarga')

@push('styles')
    <link rel="stylesheet" href="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.css') }}">
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Detail Mutasi Keluarga</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sekretariat.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sekretariat.mutasi.index') }}">Mutasi</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sekretariat.mutasi.keluarga.index') }}">Mutasi Keluarga</a></li>
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
            @if (session('error'))
                <div class="alert alert-light-danger color-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Informasi Mutasi Keluarga</h4>
                    <div class="d-flex gap-2">
                        @if (in_array($mutasiKeluarga->sub_jenis, ['kub', 'wilayah']))
                            <a href="{{ route('sekretariat.mutasi.keluarga.edit', $mutasiKeluarga) }}" class="btn btn-sm btn-outline-warning">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                        @endif
                        <form action="{{ route('sekretariat.mutasi.keluarga.destroy', $mutasiKeluarga) }}" method="POST" class="delete-keluarga-form">
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
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="180">Kepala Keluarga</th>
                                    <td><strong>{{ $mutasiKeluarga->keluarga->kepalaKeluarga->nama ?? '-' }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Jenis Pindah</th>
                                    <td>
                                        <span class="badge bg-success text-capitalize">
                                            {{ str_replace('_', ' ', $mutasiKeluarga->sub_jenis) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Nomor Surat</th>
                                    <td>{{ $mutasiKeluarga->nomor_surat ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Mutasi</th>
                                    <td>{{ $mutasiKeluarga->mutasi->tanggal->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Keterangan</th>
                                    <td>{{ $mutasiKeluarga->mutasi->keterangan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Dicatat pada</th>
                                    <td>{{ $mutasiKeluarga->mutasi->created_at->format('d M Y, H:i') }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h6 class="fw-bold text-muted mb-2">Detail Asal & Tujuan</h6>
                            <table class="table table-borderless">
                                @if ($mutasiKeluarga->sub_jenis === 'kub')
                                    <tr>
                                        <th width="150">KUB Asal</th>
                                        <td>{{ $mutasiKeluarga->kubAsal->nama ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>KUB Tujuan</th>
                                        <td>{{ $mutasiKeluarga->kubTujuan->nama ?? '-' }}</td>
                                    </tr>
                                @elseif ($mutasiKeluarga->sub_jenis === 'wilayah')
                                    <tr>
                                        <th width="150">Wilayah Asal</th>
                                        <td>{{ $mutasiKeluarga->wilayahAsal->nama ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Wilayah Tujuan</th>
                                        <td>{{ $mutasiKeluarga->wilayahTujuan->nama ?? '-' }}</td>
                                    </tr>
                                @elseif ($mutasiKeluarga->sub_jenis === 'paroki')
                                    <tr>
                                        <th width="150">Paroki Asal</th>
                                        <td>{{ $mutasiKeluarga->parokiAsal->nama ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Paroki Tujuan</th>
                                        <td>{{ $mutasiKeluarga->parokiTujuan->nama ?? '-' }}</td>
                                    </tr>
                                @elseif ($mutasiKeluarga->sub_jenis === 'keuskupan')
                                    <tr>
                                        <th width="150">Keuskupan Asal</th>
                                        <td>{{ $mutasiKeluarga->keuskupanAsal->nama ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Keuskupan Tujuan</th>
                                        <td>{{ $mutasiKeluarga->keuskupanTujuan->nama ?? '-' }}</td>
                                    </tr>
                                @endif
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
                document.querySelectorAll('.delete-keluarga-form').forEach(function(form) {
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
                        }).then(r => { if (r.isConfirmed) form.submit(); });
                    });
                });
            });
        </script>
    @endpush
@endsection
