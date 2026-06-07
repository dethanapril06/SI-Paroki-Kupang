@extends('layouts.sekretariat')

@section('title', 'Daftar Mutasi Keluarga')

@push('styles')
    <link rel="stylesheet" href="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.css') }}">
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Daftar Mutasi Keluarga</h3>
                    <a href="{{ route('sekretariat.mutasi.keluarga.create') }}" class="btn btn-primary my-2">
                        <i class="bi bi-plus-lg"></i>
                    </a>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('sekretariat.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('sekretariat.mutasi.index') }}">Mutasi</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Mutasi Keluarga</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Data Mutasi Keluarga</h5>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-light-success color-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped" id="table1">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Keluarga</th>
                                    <th>Jenis Pindah</th>
                                    <th>Asal</th>
                                    <th>Tujuan</th>
                                    <th>Tanggal</th>
                                    <th>No. Surat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($mutasiKeluargaList as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->keluarga->kepalaKeluarga->nama ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-success text-capitalize">
                                                {{ str_replace('_', ' ', $item->sub_jenis) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($item->sub_jenis === 'kub')
                                                {{ $item->kubAsal->nama ?? '-' }}
                                            @elseif ($item->sub_jenis === 'wilayah')
                                                {{ $item->wilayahAsal->nama ?? '-' }}
                                            @elseif ($item->sub_jenis === 'paroki')
                                                {{ $item->parokiAsal->nama ?? '-' }}
                                            @elseif ($item->sub_jenis === 'keuskupan')
                                                {{ $item->keuskupanAsal->nama ?? '-' }}
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->sub_jenis === 'kub')
                                                {{ $item->kubTujuan->nama ?? '-' }}
                                            @elseif ($item->sub_jenis === 'wilayah')
                                                {{ $item->wilayahTujuan->nama ?? '-' }}
                                            @elseif ($item->sub_jenis === 'paroki')
                                                {{ $item->parokiTujuan->nama ?? '-' }}
                                            @elseif ($item->sub_jenis === 'keuskupan')
                                                {{ $item->keuskupanTujuan->nama ?? '-' }}
                                            @endif
                                        </td>
                                        <td>{{ $item->mutasi->tanggal->format('d M Y') }}</td>
                                        <td>{{ $item->nomor_surat ?? '-' }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('sekretariat.mutasi.keluarga.show', $item) }}"
                                                    class="btn btn-sm btn-outline-info" title="Detail">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @if (in_array($item->sub_jenis, ['kub', 'wilayah']))
                                                    <a href="{{ route('sekretariat.mutasi.keluarga.edit', $item) }}"
                                                        class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                @endif
                                                <form action="{{ route('sekretariat.mutasi.keluarga.destroy', $item) }}"
                                                    method="POST" class="delete-keluarga-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-sm btn-outline-danger btn-delete-keluarga"
                                                        title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Belum ada data mutasi keluarga.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $mutasiKeluargaList->links() }}
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
                    form.addEventListener('submit', function(event) {
                        event.preventDefault();
                        Swal.fire({
                            title: 'Yakin ingin menghapus data ini?',
                            text: 'Data mutasi keluarga yang dihapus tidak bisa dikembalikan.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Ya, hapus',
                            cancelButtonText: 'Batal'
                        }).then(function(result) {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    });
                });
            });
        </script>
    @endpush
@endsection
