@extends('layouts.sekretariat')

@section('title', 'Detail Komuni Pertama')

@push('styles')
    <link rel="stylesheet" href="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.css') }}">
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Detail Komuni Pertama</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sekretariat.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sekretariat.sakramen.index') }}">Sakramen</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sekretariat.sakramen.komuni-pertama.index') }}">Komuni Pertama</a></li>
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
                    <h4 class="card-title mb-0">Informasi Komuni Pertama</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('sekretariat.sakramen.komuni-pertama.edit', $sakramen) }}"
                            class="btn btn-sm btn-outline-warning">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        <form action="{{ route('sekretariat.sakramen.komuni-pertama.destroy', $sakramen) }}"
                            method="POST" class="delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="200">Nama Umat</th>
                            <td><strong>{{ $sakramen->umat->nama ?? '-' }}</strong></td>
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
                        }).then(r => { if (r.isConfirmed) form.submit(); });
                    });
                });
            });
        </script>
    @endpush
@endsection
