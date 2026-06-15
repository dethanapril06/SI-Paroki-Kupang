@extends('layouts.sekretariat')

@section('title', 'Import Data')

@push('styles')
    <link rel="stylesheet" href="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.css') }}">
@endpush

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Import Data</h3>
                <p class="text-subtitle text-muted">Import data massal dari file Excel ke dalam sistem.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('sekretariat.dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Import Data</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">

        {{-- Alert Sukses --}}
        @if (session('success'))
            <div class="alert alert-light-success color-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Alert Error Umum --}}
        @if (session('error'))
            <div class="alert alert-light-danger color-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-x-circle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Daftar Error Detail --}}
        @if (session('import_errors'))
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Detail Kesalahan Import
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-2">Import dihentikan. Perbaiki kesalahan berikut lalu coba lagi:</p>
                    <ul class="mb-0">
                        @foreach(session('import_errors') as $err)
                            <li class="text-danger">{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- Informasi Urutan Import --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><i class="bi bi-info-circle me-2"></i>Panduan Urutan Import</h5>
            </div>
            <div class="card-body">
                <p class="mb-3">Import data harus dilakukan secara <strong>berurutan</strong> karena ada ketergantungan antar data:</p>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    @php
                        $urutan = [
                            ['label' => '1. Wilayah',       'color' => 'primary'],
                            ['label' => '2. KUB',           'color' => 'info'],
                            ['label' => '3. Keluarga',      'color' => 'success'],
                            ['label' => '4. Umat',          'color' => 'warning'],
                            ['label' => '5. Sakramen',      'color' => 'danger'],
                        ];
                    @endphp
                    @foreach($urutan as $i => $step)
                        <span class="badge bg-{{ $step['color'] }} fs-6 px-3 py-2">{{ $step['label'] }}</span>
                        @if(!$loop->last)
                            <i class="bi bi-arrow-right text-muted fs-5"></i>
                        @endif
                    @endforeach
                </div>
                <p class="text-muted mt-3 mb-0 small">
                    <i class="bi bi-exclamation-triangle me-1 text-warning"></i>
                    Jika data parent belum ada (misal: KUB belum diimport saat import Keluarga), import akan dihentikan dengan pesan error.
                </p>
            </div>
        </div>

        {{-- Grid Kartu Import --}}
        @php
            $groups = [
                'Struktur Wilayah' => ['wilayah', 'kub', 'keluarga', 'umat'],
                'Sakramen'         => ['baptis', 'komuni-pertama', 'krisma', 'pernikahan', 'minyak-suci'],
            ];
        @endphp

        @foreach($groups as $groupName => $keys)
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="bi bi-{{ $groupName === 'Sakramen' ? 'cross-circle' : 'diagram-3' }} me-2"></i>
                    {{ $groupName }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($keys as $jenis)
                        @php $info = $jenisImport[$jenis]; @endphp
                        <div class="col-12 col-md-6 col-xl-4">
                            <div class="card border h-100 import-card" id="card-{{ $jenis }}">
                                <div class="card-header d-flex justify-content-between align-items-center py-2">
                                    <span class="fw-semibold">
                                        <i class="bi {{ $info['icon'] }} me-2 text-primary"></i>{{ $info['label'] }}
                                    </span>
                                    <span class="badge bg-light-secondary text-secondary rounded-pill">
                                        Urutan {{ $info['urutan'] }}
                                    </span>
                                </div>
                                <div class="card-body d-flex flex-column gap-3">

                                    {{-- Download Template --}}
                                    <a href="{{ route('sekretariat.import.template', $jenis) }}"
                                       class="btn btn-outline-success w-100"
                                       id="btn-template-{{ $jenis }}">
                                        <i class="bi bi-file-earmark-excel me-2"></i>Download Template
                                    </a>

                                    {{-- Form Upload --}}
                                    <form action="{{ route('sekretariat.import.proses', $jenis) }}"
                                          method="POST"
                                          enctype="multipart/form-data"
                                          class="import-form"
                                          id="form-{{ $jenis }}">
                                        @csrf
                                        <div class="mb-2">
                                            <label for="file-{{ $jenis }}" class="form-label small text-muted mb-1">
                                                Upload file Excel (.xlsx)
                                            </label>
                                            <input type="file"
                                                   class="form-control form-control-sm"
                                                   name="file"
                                                   id="file-{{ $jenis }}"
                                                   accept=".xlsx,.xls,.csv"
                                                   required>
                                        </div>
                                        <button type="submit"
                                                class="btn btn-primary w-100"
                                                id="btn-import-{{ $jenis }}">
                                            <i class="bi bi-upload me-2"></i>Import {{ $info['label'] }}
                                        </button>
                                    </form>

                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach

    </section>
</div>

@push('scripts')
<script src="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.import-form').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                const fileInput = form.querySelector('input[type="file"]');
                if (!fileInput.value) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'File Belum Dipilih',
                        text: 'Silakan pilih file Excel terlebih dahulu.',
                    });
                    return;
                }

                const label = form.querySelector('button[type="submit"]').textContent.trim();

                Swal.fire({
                    title: 'Konfirmasi Import',
                    html: `Anda akan mengimport data <b>${label.replace('Import ', '')}</b>.<br>
                           Pastikan file sudah sesuai template dan data parent sudah ada di sistem.<br><br>
                           <span class="text-danger"><b>Import tidak dapat dibatalkan.</b></span>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#435ebe',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Import Sekarang',
                    cancelButtonText: 'Batal',
                }).then(function (result) {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'Sedang memproses...',
                            text: 'Mohon tunggu, sedang mengimpor data.',
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading(),
                        });
                        form.submit();
                    }
                });
            });
        });

        // Highlight card jika ini jenis yang error
        @if(session('import_jenis'))
            const errorJenis = '{{ session('import_jenis') }}';
            const errorCard  = document.getElementById('card-' + errorJenis);
            if (errorCard) {
                errorCard.classList.add('border-danger');
                errorCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        @endif
    });
</script>
@endpush
@endsection
