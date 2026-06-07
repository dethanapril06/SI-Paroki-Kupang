@extends('layouts.dewan_pastoral')

@section('title', 'Detail Anggota DPP')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Detail Anggota DPP</h3>
                    <p class="text-subtitle text-muted">Informasi lengkap kepengurusan Dewan Pastoral Paroki.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dewan_pastoral.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('dewan_pastoral.keanggotaan.index') }}">Keanggotaan DPP</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Detail</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row g-4">
                {{-- Data Anggota --}}
                <div class="col-12 col-lg-7">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Informasi Keanggotaan</h4>
                            <span class="badge {{ $keanggotaan->status_aktif === 'Aktif' ? 'bg-success' : 'bg-secondary' }}">
                                {{ $keanggotaan->status_aktif }}
                            </span>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped align-middle">
                                <tbody>
                                    <tr>
                                        <th style="width: 35%">Nama Lengkap</th>
                                        <td>{{ $keanggotaan->umat->nama ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Jabatan</th>
                                        <td>
                                            <span class="badge bg-light-warning text-warning-custom font-bold">
                                                {{ $keanggotaan->jabatan }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Bidang Tugas</th>
                                        <td>{{ $keanggotaan->bidang_tugas ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status Keaktifan</th>
                                        <td>{{ $keanggotaan->status_aktif }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Terdaftar</th>
                                        <td>{{ $keanggotaan->created_at->format('d F Y') }}</td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="d-flex gap-2 mt-4">
                                <a href="{{ route('dewan_pastoral.keanggotaan.index') }}"
                                    class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> Kembali
                                </a>
                                <a href="{{ route('dewan_pastoral.keanggotaan.edit', $keanggotaan) }}"
                                    class="btn btn-warning">
                                    <i class="bi bi-pencil-square"></i> Edit Data
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Akun Login Terkait --}}
                <div class="col-12 col-lg-5">
                    <div class="card h-100">
                        <div class="card-header bg-warning bg-opacity-10">
                            <h4 class="card-title text-warning-custom mb-0">
                                <i class="bi bi-shield-lock-fill me-2"></i> Akun Login Sistem
                            </h4>
                        </div>
                        <div class="card-body py-4">
                            @if ($keanggotaan->jabatan === 'Ketua')
                                @if ($user)
                                    <div class="text-center mb-4">
                                        <div class="avatar avatar-xl mb-2">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=ffc107&color=000&size=100&bold=true&font-size=0.4"
                                                alt="Avatar {{ $user->name }}" class="rounded-circle"
                                                style="width: 70px; height: 70px; object-fit: cover;">
                                        </div>
                                        <h6 class="fw-bold mb-1">{{ $user->name }}</h6>
                                        <span class="badge bg-warning text-dark">Dewan Pastoral</span>
                                    </div>

                                    <div class="alert alert-light-secondary color-secondary">
                                        <small class="d-block text-muted">Alamat Email Login:</small>
                                        <strong class="d-block text-dark mb-2">{{ $user->email }}</strong>

                                        <small class="d-block text-muted">Status Akun:</small>
                                        @if($user->deleted_at)
                                            <span class="badge bg-danger">Nonaktif (Soft Deleted)</span>
                                        @else
                                            <span class="badge bg-success">Aktif</span>
                                        @endif
                                    </div>

                                    <div class="bg-light p-3 rounded border text-muted">
                                        <small class="d-block"><i class="bi bi-info-circle-fill me-1"></i> Akun ini dibuat secara otomatis oleh sistem saat data anggota DPP ditambahkan.</small>
                                    </div>
                                @else
                                    <div class="alert alert-light-danger color-danger mb-0">
                                        <h6 class="alert-heading fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i> Akun Login Tidak Ditemukan!</h6>
                                        <p class="mb-0 text-sm">Anggota DPP (Ketua) ini tidak memiliki akun login aktif di sistem. Hubungi administrator/sekretariat jika ini merupakan kesalahan.</p>
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-light-info color-info mb-0">
                                    <h6 class="alert-heading fw-bold"><i class="bi bi-info-circle-fill me-2"></i> Hak Akses Terbatas</h6>
                                    <p class="mb-0 text-sm">Anggota dengan jabatan <strong>{{ $keanggotaan->jabatan }}</strong> tidak memiliki akses login ke Panel Dewan Pastoral Paroki (DPP). Hanya jabatan <strong>Ketua</strong> yang diberikan akses login DPP. Akun personal umat yang bersangkutan tetap aktif untuk mengakses Portal Umat.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
