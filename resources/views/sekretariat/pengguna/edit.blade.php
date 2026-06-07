@extends('layouts.sekretariat')

@section('title', 'Edit Akun: ' . $user->name)

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Edit Akun Pengguna</h3>
                    <p class="text-muted">Ubah email dan kelola role: <strong>{{ $user->name }}</strong></p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('sekretariat.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('sekretariat.pengguna.index') }}">Kelola Pengguna</a>
                            </li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">

                {{-- ── Panel kiri: Smart Info Jabatan ── --}}
                <div class="col-12 col-lg-4">

                    {{-- Info User --}}
                    <div class="card mb-3">
                        <div class="card-body text-center py-4">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=435ebe&color=fff&size=80&bold=true&font-size=0.4"
                                alt="{{ $user->name }}" class="rounded-circle mb-3" style="width:72px;height:72px;">
                            <h5 class="mb-1">{{ $user->name }}</h5>
                            <p class="text-muted small mb-2">{{ $user->email }}</p>
                            {{-- Badge role saat ini --}}
                            @forelse ($user->roles as $role)
                                @php
                                    $badgeColor = match($role->name) {
                                        'sekretariat'      => 'bg-dark',
                                        'pastor'           => 'bg-danger',
                                        'dewan_pastoral'   => 'bg-warning text-dark',
                                        'ketua_kub'        => 'bg-success',
                                        'ketua_kategorial' => 'bg-info',
                                        default            => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $badgeColor }} me-1">{{ $role->label }}</span>
                            @empty
                                <span class="badge bg-light text-muted">Belum ada role</span>
                            @endforelse
                        </div>
                    </div>

                    {{-- Smart Info Jabatan dari Data --}}
                    @if($jabatanKub || $jabatanKategorial->isNotEmpty())
                        <div class="card mb-3 border-info">
                            <div class="card-header bg-light-info">
                                <h6 class="card-title mb-0 text-info">
                                    <i class="bi bi-info-circle-fill me-2"></i>Jabatan dari Data Sistem
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small mb-3">
                                    Pengguna ini tercatat memegang jabatan berikut. Gunakan tombol
                                    <strong>"Sesuaikan"</strong> untuk pre-check role yang sesuai.
                                </p>

                                @if($jabatanKub)
                                    <div class="d-flex align-items-center gap-2 mb-2 p-2 bg-light rounded">
                                        <i class="bi bi-diagram-3-fill text-success fs-5"></i>
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold small">Ketua KUB</div>
                                            <div class="text-muted small">{{ $jabatanKub->nama }}</div>
                                        </div>
                                        <span class="badge bg-success">KUB</span>
                                    </div>
                                @endif

                                @foreach($jabatanKategorial as $jk)
                                    <div class="d-flex align-items-center gap-2 mb-2 p-2 bg-light rounded">
                                        <i class="bi bi-collection-fill text-info fs-5"></i>
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold small">Ketua Kategorial</div>
                                            <div class="text-muted small">{{ $jk->nama }}</div>
                                        </div>
                                        <span class="badge bg-info">Kat.</span>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                    @else
                        <div class="card mb-3">
                            <div class="card-body text-center py-3 text-muted">
                                <i class="bi bi-person-dash d-block fs-2 mb-2"></i>
                                <small>Tidak ada jabatan ketua yang tercatat untuk pengguna ini.</small>
                            </div>
                        </div>
                    @endif

                </div>

                {{-- ── Panel kanan: Form Edit ── --}}
                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-pencil-square me-2"></i>Form Edit Akun
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('sekretariat.pengguna.update', $user) }}"
                                method="POST" id="form-edit-pengguna">
                                @csrf
                                @method('PUT')

                                @if ($errors->any())
                                    <div class="alert alert-light-danger color-danger">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                {{-- Email --}}
                                <div class="form-group mb-4">
                                    <label for="email" class="form-label fw-semibold">Email Akun</label>
                                    <input type="email" name="email" id="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email', $user->email) }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Info Role saat ini --}}
                                <div class="form-group mb-4">
                                    <label class="form-label fw-semibold">Role Pengguna (Read-only)</label>
                                    <div class="p-3 border rounded bg-light">
                                        @forelse ($user->roles as $role)
                                            @php
                                                $badgeColor = match($role->name) {
                                                    'sekretariat'      => 'bg-dark',
                                                    'pastor'           => 'bg-danger',
                                                    'dewan_pastoral'   => 'bg-warning text-dark',
                                                    'ketua_kub'        => 'bg-success',
                                                    'ketua_kategorial' => 'bg-info',
                                                    default            => 'bg-secondary',
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeColor }} me-1 fs-6">{{ $role->label }}</span>
                                        @empty
                                            <span class="badge bg-light text-muted">Belum ada role</span>
                                        @endforelse
                                    </div>
                                    <small class="text-muted d-block mt-2">
                                        * Role dikelola otomatis oleh sistem berdasarkan jabatan fisik (Ketua KUB, Ketua Kategorial, Dewan Pastoral Paroki, Klerus).
                                    </small>
                                </div>

                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('sekretariat.pengguna.index') }}"
                                        class="btn btn-light-secondary">Batal</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-1"></i>Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>


@endsection
