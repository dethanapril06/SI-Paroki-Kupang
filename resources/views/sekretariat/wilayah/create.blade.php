@extends('layouts.sekretariat')

@section('title', 'Tambah Wilayah')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Tambah Wilayah</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('sekretariat.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Form Tambah Wilayah
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Tambah Wilayah</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-light-danger color-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="alert alert-light-info color-info mb-4">
                            <i class="bi bi-info-circle me-1"></i>
                            <strong>Catatan:</strong> Ketua Wilayah dapat diatur setelah ada anggota umat dalam wilayah ini.
                        </div>

                        <form class="form form-vertical" method="POST" action="{{ route('sekretariat.wilayah.store') }}">
                            @csrf
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="nama">Nama Wilayah</label>
                                            <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                                placeholder="Masukkan nama wilayah" id="nama" name="nama"
                                                value="{{ old('nama') }}">
                                            @error('nama')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group" id="paroki-group">
                                            <label for="paroki_id">Paroki (pilih salah satu parent)</label>
                                            <select class="form-select @error('paroki_id') is-invalid @enderror"
                                                id="paroki_id" name="paroki_id">
                                                <option value="">-- Pilih Paroki --</option>
                                                @foreach ($paroki as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ old('paroki_id') == $item->id ? 'selected' : '' }}>
                                                        {{ $item->nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('paroki_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group" id="kuasi-group">
                                            <label for="kuasi_id">Kuasi (pilih salah satu parent)</label>
                                            <select class="form-select @error('kuasi_id') is-invalid @enderror"
                                                id="kuasi_id" name="kuasi_id">
                                                <option value="">-- Pilih Kuasi --</option>
                                                @foreach ($kuasi as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ old('kuasi_id') == $item->id ? 'selected' : '' }}>
                                                        {{ $item->nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('kuasi_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group" id="stasi-group">
                                            <label for="stasi_id">Stasi (pilih salah satu parent)</label>
                                            <select class="form-select @error('stasi_id') is-invalid @enderror"
                                                id="stasi_id" name="stasi_id">
                                                <option value="">-- Pilih Stasi --</option>
                                                @foreach ($stasi as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ old('stasi_id') == $item->id ? 'selected' : '' }}>
                                                        {{ $item->nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('stasi_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12 d-flex justify-content-end">
                                        <a href="{{ route('sekretariat.wilayah.index') }}"
                                            class="btn btn-light-secondary me-1 mb-1">Batal</a>
                                        <button type="submit" class="btn btn-primary me-1 mb-1">Simpan</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const parentSelects = {
                    paroki: document.getElementById('paroki_id'),
                    kuasi: document.getElementById('kuasi_id'),
                    stasi: document.getElementById('stasi_id')
                };

                const parentGroups = {
                    paroki: document.getElementById('paroki-group'),
                    kuasi: document.getElementById('kuasi-group'),
                    stasi: document.getElementById('stasi-group')
                };

                function syncParentSelection(sourceKey) {
                    const selectedKeys = Object.keys(parentSelects).filter(function(key) {
                        return parentSelects[key].value;
                    });

                    if (sourceKey && parentSelects[sourceKey].value) {
                        Object.keys(parentSelects).forEach(function(key) {
                            if (key !== sourceKey) {
                                parentSelects[key].value = '';
                            }
                        });
                    }

                    const activeKey = Object.keys(parentSelects).find(function(key) {
                        return parentSelects[key].value;
                    });

                    Object.keys(parentSelects).forEach(function(key) {
                        const isActive = activeKey === key;
                        const hasActive = Boolean(activeKey);

                        if (hasActive && !isActive) {
                            parentGroups[key].classList.add('d-none');
                            parentSelects[key].disabled = true;
                        } else {
                            parentGroups[key].classList.remove('d-none');
                            parentSelects[key].disabled = false;
                        }
                    });
                }

                Object.keys(parentSelects).forEach(function(key) {
                    parentSelects[key].addEventListener('change', function() {
                        syncParentSelection(key);
                    });
                });

                syncParentSelection(null);
            });
        </script>
    @endpush
@endsection
