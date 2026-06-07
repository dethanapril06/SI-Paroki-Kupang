@extends('layouts.sekretariat')

@section('title', 'Daftar Sakramen')

@push('styles')
    <link rel="stylesheet" href="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.css') }}">
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Daftar Sakramen</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('sekretariat.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Daftar Sakramen</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            {{-- Shortcut Tambah --}}
            <div class="row mb-3">
                <div class="col-12">
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('sekretariat.sakramen.baptis.create') }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-lg"></i> Tambah Baptis
                        </a>
                        <a href="{{ route('sekretariat.sakramen.komuni-pertama.create') }}" class="btn btn-sm btn-success">
                            <i class="bi bi-plus-lg"></i> Tambah Komuni Pertama
                        </a>
                        <a href="{{ route('sekretariat.sakramen.krisma.create') }}" class="btn btn-sm btn-info">
                            <i class="bi bi-plus-lg"></i> Tambah Krisma
                        </a>
                        <a href="{{ route('sekretariat.sakramen.pernikahan.create') }}" class="btn btn-sm btn-warning">
                            <i class="bi bi-plus-lg"></i> Tambah Pernikahan
                        </a>
                        <a href="{{ route('sekretariat.sakramen.minyak-suci.create') }}" class="btn btn-sm btn-danger">
                            <i class="bi bi-plus-lg"></i> Tambah Minyak Suci
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Semua Data Sakramen</h5>
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
                                    <th>Jenis</th>
                                    <th>Umat</th>
                                    <th>Tanggal Penerimaan</th>
                                    <th>Paroki</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($sakramenList as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                                @php
                                                $jenisLabel = [
                                                    'BAPTIS'         => 'Baptis',
                                                    'KOMUNI_PERTAMA' => 'Komuni Pertama',
                                                    'KRISMA'         => 'Krisma',
                                                    'PERNIKAHAN'     => 'Pernikahan',
                                                    'MINYAK_SUCI'    => 'Minyak Suci',
                                                ];
                                                $badgeClass = match ($item->jenis_sakramen) {
                                                    'BAPTIS'         => 'bg-primary',
                                                    'KOMUNI_PERTAMA' => 'bg-success',
                                                    'KRISMA'         => 'bg-info',
                                                    'PERNIKAHAN'     => 'bg-warning text-dark',
                                                    'MINYAK_SUCI'    => 'bg-danger',
                                                    default          => 'bg-secondary',
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">
                                                {{ $jenisLabel[$item->jenis_sakramen] ?? $item->jenis_sakramen }}
                                            </span>
                                        </td>
                                        <td>{{ $item->umat->nama ?? '-' }}</td>
                                        <td>{{ $item->tanggal_penerimaan->format('d M Y') }}</td>
                                        <td>{{ $item->paroki->nama ?? '-' }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                {{-- Tombol detail ke child controller masing-masing --}}
                                                @if ($item->jenis_sakramen === 'BAPTIS')
                                                    <a href="{{ route('sekretariat.sakramen.baptis.show', $item) }}"
                                                        class="btn btn-sm btn-outline-info" title="Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                @elseif ($item->jenis_sakramen === 'KOMUNI_PERTAMA')
                                                    <a href="{{ route('sekretariat.sakramen.komuni-pertama.show', $item) }}"
                                                        class="btn btn-sm btn-outline-info" title="Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                @elseif ($item->jenis_sakramen === 'KRISMA')
                                                    <a href="{{ route('sekretariat.sakramen.krisma.show', $item) }}"
                                                        class="btn btn-sm btn-outline-info" title="Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                @elseif ($item->jenis_sakramen === 'PERNIKAHAN')
                                                    <a href="{{ route('sekretariat.sakramen.pernikahan.show', $item) }}"
                                                        class="btn btn-sm btn-outline-info" title="Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                @elseif ($item->jenis_sakramen === 'MINYAK_SUCI')
                                                    <a href="{{ route('sekretariat.sakramen.minyak-suci.show', $item) }}"
                                                        class="btn btn-sm btn-outline-info" title="Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                @endif

                                                <form action="{{ route('sekretariat.sakramen.destroy', $item) }}"
                                                    method="POST" class="delete-sakramen-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-sm btn-outline-danger btn-delete-sakramen"
                                                        title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Belum ada data sakramen.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $sakramenList->links() }}
                    </div>
                </div>
            </div>
        </section>
    </div>

    @push('scripts')
        <script src="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.delete-sakramen-form').forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        event.preventDefault();
                        Swal.fire({
                            title: 'Yakin ingin menghapus data ini?',
                            text: 'Data sakramen yang dihapus tidak bisa dikembalikan.',
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
