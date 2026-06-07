@extends('layouts.sekretariat')

@section('title', 'Tambah Stasi')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Tambah Stasi</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('sekretariat.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Form Tambah Stasi
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Tambah Stasi</h4>
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

                        <form class="form form-vertical" method="POST" action="{{ route('sekretariat.stasi.store') }}">
                            @csrf
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="nama">Nama Stasi</label>
                                            <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                                placeholder="Masukkan nama stasi" id="nama" name="nama"
                                                value="{{ old('nama') }}">
                                            @error('nama')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="alamat">Alamat</label>
                                            <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="3"
                                                placeholder="Masukkan alamat stasi">{{ old('alamat') }}</textarea>
                                            @error('alamat')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="koordinator">Koordinator</label>
                                            <input type="text"
                                                class="form-control @error('koordinator') is-invalid @enderror"
                                                placeholder="Masukkan nama koordinator" id="koordinator" name="koordinator"
                                                value="{{ old('koordinator') }}">
                                            @error('koordinator')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group" id="paroki-group">
                                            <label for="paroki_id">Paroki (pilih salah satu dengan Kuasi)</label>
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
                                            <label for="kuasi_id">Kuasi (pilih salah satu dengan Paroki)</label>
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

                                    <div class="col-12 d-flex justify-content-end">
                                        <a href="{{ route('sekretariat.stasi.index') }}"
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
                const parokiSelect = document.getElementById('paroki_id');
                const kuasiSelect = document.getElementById('kuasi_id');
                const parokiGroup = document.getElementById('paroki-group');
                const kuasiGroup = document.getElementById('kuasi-group');

                function syncParentSelection(source) {
                    const parokiValue = parokiSelect.value;
                    const kuasiValue = kuasiSelect.value;

                    if (source === 'paroki' && parokiValue) {
                        kuasiSelect.value = '';
                    }

                    if (source === 'kuasi' && kuasiValue) {
                        parokiSelect.value = '';
                    }

                    if (parokiSelect.value) {
                        kuasiGroup.classList.add('d-none');
                        kuasiSelect.disabled = true;
                    } else {
                        kuasiGroup.classList.remove('d-none');
                        kuasiSelect.disabled = false;
                    }

                    if (kuasiSelect.value) {
                        parokiGroup.classList.add('d-none');
                        parokiSelect.disabled = true;
                    } else {
                        parokiGroup.classList.remove('d-none');
                        parokiSelect.disabled = false;
                    }
                }

                parokiSelect.addEventListener('change', function() {
                    syncParentSelection('paroki');
                });

                kuasiSelect.addEventListener('change', function() {
                    syncParentSelection('kuasi');
                });

                syncParentSelection(null);
            });
        </script>
    @endpush
@endsection
