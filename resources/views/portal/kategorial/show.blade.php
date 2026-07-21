@extends('layouts.portal')

@section('title', 'Kelola Anggota — ' . $kategorial->nama)

@push('styles')
    <link rel="stylesheet" href="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.css') }}">
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>{{ $kategorial->nama }}</h3>
                    <p class="text-subtitle text-muted mb-0">Kelola anggota kategorial.</p>
                    @if ($kategorial->klerus)
                        <p class="text-subtitle text-muted mt-1">
                            <strong>Pastor Moderator:</strong> {{ $kategorial->klerus->nama }}
                        </p>
                    @endif
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('portal.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('portal.kategorial.index') }}">Kategorial Saya</a></li>
                            <li class="breadcrumb-item active">{{ $kategorial->nama }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            @if (session('success'))
                <div class="alert alert-light-success color-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-light-danger color-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                {{-- Tabel Anggota --}}
                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-people-fill me-2"></i>Daftar Anggota
                            </h5>
                            <span class="badge bg-primary">{{ $kategorial->anggota->count() }} total</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nama</th>
                                            <th>Jabatan</th>
                                            <th>Bidang Tugas</th>
                                            <th>Bergabung</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($kategorial->anggota as $u)
                                            <tr>
                                                <td class="fw-semibold">{{ $u->nama }}</td>
                                                <td>
                                                    <span class="badge
                                                        {{ $u->pivot->jabatan === 'Ketua' ? 'bg-primary' :
                                                           ($u->pivot->jabatan === 'Wakil Ketua' ? 'bg-info' :
                                                           ($u->pivot->jabatan === 'Sekretaris' ? 'bg-warning text-dark' :
                                                           ($u->pivot->jabatan === 'Bendahara' ? 'bg-success' : 'bg-secondary'))) }}">
                                                        {{ $u->pivot->jabatan }}
                                                    </span>
                                                </td>
                                                <td>{{ $u->pivot->bidang_tugas ?? '-' }}</td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($u->pivot->tanggal_bergabung)->format('d M Y') }}
                                                </td>
                                                <td>
                                                    @if ($u->status_almarhum)
                                                        <span class="badge bg-light-secondary text-secondary mb-1">Tidak Aktif</span>
                                                        <span class="badge bg-dark">Almarhum</span>
                                                    @elseif ($u->pivot->status === 'Aktif')
                                                        <span class="badge bg-light-success text-success">Aktif</span>
                                                    @else
                                                        <span class="badge bg-light-danger text-danger">Tidak Aktif</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($u->id !== $kategorial->ketua_umat_id)
                                                        <div class="d-flex gap-1">
                                                            <a href="{{ route('portal.kategorial.anggota.edit', [$kategorial, $u->pivot->id]) }}"
                                                                class="btn btn-sm btn-outline-warning" title="Edit">
                                                                <i class="bi bi-pencil"></i>
                                                            </a>
                                                            <form action="{{ route('portal.kategorial.anggota.destroy', [$kategorial, $u->pivot->id]) }}"
                                                                method="POST" class="delete-anggota-form">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="btn btn-sm btn-outline-danger" title="Keluarkan">
                                                                    <i class="bi bi-person-dash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @else
                                                        <span class="text-muted fst-italic" style="font-size: 0.85rem">Akses Sekretariat</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">
                                                    <i class="bi bi-people d-block fs-2 mb-2"></i>
                                                    Belum ada anggota.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Tambah Anggota --}}
                <div class="col-12 col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-person-plus me-2 text-success"></i>Tambah Anggota
                            </h5>
                        </div>
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-light-danger color-danger mb-3">
                                    <ul class="mb-0 ps-3">
                                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('portal.kategorial.anggota.store', $kategorial) }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Pilih Umat <span class="text-danger">*</span></label>
                                    <select name="umat_id" class="form-select @error('umat_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih --</option>
                                        @foreach ($umatTersedia as $u)
                                            <option value="{{ $u->id }}" {{ old('umat_id') == $u->id ? 'selected' : '' }}>
                                                {{ $u->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('umat_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Jabatan <span class="text-danger">*</span></label>
                                    <select name="jabatan" class="form-select @error('jabatan') is-invalid @enderror" required>
                                        @foreach (['Anggota', 'Wakil Ketua', 'Sekretaris', 'Bendahara'] as $jab)
                                            <option value="{{ $jab }}" {{ old('jabatan') === $jab ? 'selected' : '' }}>
                                                {{ $jab }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('jabatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Bidang Tugas</label>
                                    <input type="text" name="bidang_tugas" class="form-control"
                                        value="{{ old('bidang_tugas') }}" placeholder="Opsional">
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Tanggal Bergabung <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_bergabung"
                                        class="form-control @error('tanggal_bergabung') is-invalid @enderror"
                                        value="{{ old('tanggal_bergabung', date('Y-m-d')) }}" required>
                                    @error('tanggal_bergabung') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bi bi-person-plus me-1"></i> Tambah Anggota
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        document.querySelectorAll('.delete-anggota-form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Keluarkan anggota ini?',
                    text: 'Anggota akan dikeluarkan dari kategorial.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, keluarkan',
                    cancelButtonText: 'Batal'
                }).then(function(result) {
                    if (result.isConfirmed) form.submit();
                });
            });
        });
    </script>
@endpush
