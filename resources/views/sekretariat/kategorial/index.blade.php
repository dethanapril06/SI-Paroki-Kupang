@extends('layouts.sekretariat')

@section('title', 'Daftar Kategorial')

@push('styles')
    <link rel="stylesheet" href="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.css') }}">
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Daftar Kategorial</h3>
                    <a href="{{ route('sekretariat.kategorial.create') }}" class="btn btn-primary my-2">
                        <i class="bi bi-plus-lg"></i>
                    </a>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('sekretariat.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Kategorial</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Data Kategorial</h5>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-light-success color-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped" id="table1">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Kategorial</th>
                                    <th>Ketua</th>
                                    <th>Pastor Moderator</th>
                                    <th>Jumlah Anggota</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($kategorial as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->nama }}</td>
                                        <td>
                                            @if ($item->ketuaUmat)
                                                {{ $item->ketuaUmat->nama }}
                                            @else
                                                <span class="badge bg-warning text-dark">Belum diset</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->klerus)
                                                {{ $item->klerus->nama }}
                                            @else
                                                <span class="badge bg-warning text-dark">Belum diset</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $item->anggota_count }} anggota</span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('sekretariat.kategorial.show', $item) }}"
                                                    class="btn btn-sm btn-outline-info" title="Detail">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('sekretariat.kategorial.edit', $item) }}"
                                                    class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <form action="{{ route('sekretariat.kategorial.destroy', $item) }}"
                                                    method="POST" class="form-delete-kategorial">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Belum ada data kategorial.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @push('scripts')
        <script src="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.js') }}"></script>
        <script>
            document.querySelectorAll('.form-delete-kategorial').forEach(form => {
                form.addEventListener('submit', e => {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Hapus kategorial ini?',
                        text: 'Semua data anggota dalam kategorial ini juga akan terhapus.',
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
        </script>
    @endpush
@endsection
