@extends('layouts.portal')
@section('title', 'Edit Komuni Pertama')
@section('content')
<div class="page-heading">
    <div class="page-title mb-3">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last"><h3>Edit Komuni Pertama</h3></div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('portal.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('portal.sakramen-saya.index') }}">Sakramen Saya</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('portal.sakramen-saya.komuni') }}">Komuni Pertama</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Edit Komuni Pertama</h5></div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-light-danger color-danger mb-3"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
                @endif
                <form action="{{ route('portal.sakramen-saya.komuni.update') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Penerimaan <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_penerimaan" class="form-control @error('tanggal_penerimaan') is-invalid @enderror"
                                value="{{ old('tanggal_penerimaan', $sakramen->tanggal_penerimaan?->format('Y-m-d')) }}">
                            @error('tanggal_penerimaan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pastor Pemberi</label>
                            <select name="klerus_id" class="form-select">
                                <option value="">-- Pilih (opsional) --</option>
                                @foreach ($klerusList as $k)
                                    <option value="{{ $k->id }}" {{ old('klerus_id', $sakramen->klerus_id) == $k->id ? 'selected' : '' }}>{{ $k->nama }} ({{ ucfirst($k->jabatan) }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="d-flex gap-2 justify-content-end mt-4">
                        <a href="{{ route('portal.sakramen-saya.komuni') }}" class="btn btn-light-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection
