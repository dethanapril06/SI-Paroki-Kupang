@extends('layouts.portal')

@section('title', 'Ajukan Mutasi Agama')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Ajukan Mutasi Agama</h3>
                    <p class="text-subtitle text-muted">Request perpindahan agama keluar dari Katolik.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('portal.mutasi.index') }}">Portal</a></li>
                            <li class="breadcrumb-item active">Ajukan Mutasi Agama</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-12 col-lg-6">

                    <div class="alert alert-light-warning color-warning mb-4">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Perhatian:</strong> Request ini akan dicatat dan menunggu konfirmasi dari sekretariat paroki.
                        Data Anda tidak akan langsung berubah sebelum disetujui.
                    </div>

                    <div class="card">
                        <div class="card-header"><h5 class="card-title">Form Request Mutasi Agama</h5></div>
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-light-danger color-danger">
                                    <ul class="mb-0 ps-3">
                                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('portal.mutasi.agama.store') }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Nama Umat</label>
                                    <input type="text" class="form-control" value="{{ $umat->nama }}" readonly>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Agama Asal</label>
                                    <input type="text" class="form-control" value="Katolik" readonly>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Agama Tujuan <span class="text-danger">*</span></label>
                                    <select name="agama_tujuan" class="form-select @error('agama_tujuan') is-invalid @enderror" required>
                                        <option value="">-- Pilih Agama --</option>
                                        @foreach (['protestan' => 'Protestan', 'islam' => 'Islam', 'hindu' => 'Hindu', 'budha' => 'Buddha', 'khonghucu' => 'Khonghucu'] as $val => $label)
                                            <option value="{{ $val }}" {{ old('agama_tujuan') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('agama_tujuan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror"
                                        value="{{ old('tanggal', date('Y-m-d')) }}" required>
                                    @error('tanggal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Keterangan (opsional)</label>
                                    <textarea name="keterangan" class="form-control" rows="3"
                                        placeholder="Tuliskan alasan jika ada...">{{ old('keterangan') }}</textarea>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="bi bi-send me-1"></i> Kirim Request
                                    </button>
                                    <a href="{{ route('portal.mutasi.index') }}" class="btn btn-secondary">Batal</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
