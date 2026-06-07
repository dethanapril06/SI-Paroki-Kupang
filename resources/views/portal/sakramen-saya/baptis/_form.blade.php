{{-- Partial form: digunakan oleh show (POST store) dan form (PUT update) --}}
<form action="{{ $action }}" method="POST">
    @csrf
    @if ($method === 'PUT') @method('PUT') @endif

    @if ($errors->any())
        <div class="alert alert-light-danger color-danger mb-3">
            <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Tanggal Penerimaan <span class="text-danger">*</span></label>
            <input type="date" name="tanggal_penerimaan" class="form-control @error('tanggal_penerimaan') is-invalid @enderror"
                value="{{ old('tanggal_penerimaan', $sakramen?->tanggal_penerimaan?->format('Y-m-d')) }}">
            @error('tanggal_penerimaan')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Tanggal Baptis <span class="text-danger" id="req-tgl-baptis" style="display:none;">*</span></label>
            <input type="date" name="tgl_baptis" id="tgl_baptis" class="form-control"
                value="{{ old('tgl_baptis', $baptis?->tgl_baptis?->format('Y-m-d')) }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Sumber Baptis <span class="text-danger">*</span></label>
            <select name="sumber_baptis" id="sumber_baptis" class="form-select @error('sumber_baptis') is-invalid @enderror">
                <option value="KATOLIK" {{ old('sumber_baptis', $baptis?->sumber_baptis) === 'KATOLIK' ? 'selected' : '' }}>Katolik</option>
                <option value="PROTESTAN" {{ old('sumber_baptis', $baptis?->sumber_baptis) === 'PROTESTAN' ? 'selected' : '' }}>Protestan</option>
            </select>
            @error('sumber_baptis')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Nama Baptis</label>
            <input type="text" name="nama_baptis" class="form-control" value="{{ old('nama_baptis', $baptis?->nama_baptis) }}" placeholder="Nama santo/santa">
        </div>

        {{-- Katolik fields --}}
        <div id="field-katolik" class="col-md-6">
            <label class="form-label">Pastor Pembaptis</label>
            <select name="klerus_id" class="form-select">
                <option value="">-- Pilih (opsional) --</option>
                @foreach ($klerusList as $k)
                    <option value="{{ $k->id }}" {{ old('klerus_id', $sakramen?->klerus_id) == $k->id ? 'selected' : '' }}>
                        {{ $k->nama }} ({{ ucfirst($k->jabatan) }})
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Protestan fields --}}
        <div id="field-protestan" class="col-md-6" style="display:none">
            <label class="form-label">Nama Pemberi (Protestan)</label>
            <input type="text" name="nama_pemberi_protestan" class="form-control" value="{{ old('nama_pemberi_protestan', $baptis?->nama_pemberi_protestan) }}">
        </div>
        <div id="field-gereja" class="col-md-6" style="display:none">
            <label class="form-label">Nama Gereja</label>
            <input type="text" name="nama_gereja_protestan" class="form-control" value="{{ old('nama_gereja_protestan', $baptis?->nama_gereja_protestan) }}">
        </div>

        <div class="col-md-6">
            <label class="form-label">Nama Bapak Baptis</label>
            <input type="text" name="bapak_baptis_nama" class="form-control" value="{{ old('bapak_baptis_nama', $baptis?->bapak_baptis_nama) }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Nama Ibu Baptis</label>
            <input type="text" name="ibu_baptis_nama" class="form-control" value="{{ old('ibu_baptis_nama', $baptis?->ibu_baptis_nama) }}">
        </div>
    </div>

    <div class="d-flex gap-2 justify-content-end mt-4">
        <a href="{{ route('portal.sakramen-saya.baptis') }}" class="btn btn-light-secondary">Batal</a>
        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
    </div>
</form>

@push('scripts')
<script>
    const sel = document.getElementById('sumber_baptis');
    function toggleFields() {
        const isProtestan = sel.value === 'PROTESTAN';
        document.getElementById('field-katolik').style.display  = isProtestan ? 'none' : '';
        document.getElementById('field-protestan').style.display = isProtestan ? '' : 'none';
        document.getElementById('field-gereja').style.display    = isProtestan ? '' : 'none';

        const reqAsterisk = document.getElementById('req-tgl-baptis');
        if (reqAsterisk) {
            reqAsterisk.style.display = isProtestan ? 'inline' : 'none';
        }
    }
    sel.addEventListener('change', toggleFields);
    toggleFields();
</script>
@endpush
