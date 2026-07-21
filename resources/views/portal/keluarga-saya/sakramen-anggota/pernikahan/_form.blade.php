{{-- Partial form pernikahan anggota --}}
<form action="{{ $action }}" method="POST">
    @csrf
    @if ($method === 'PUT') @method('PUT') @endif
    @if ($errors->any())
        <div class="alert alert-light-danger color-danger mb-3"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif
    @php
        $selectedPasanganId = old('pasangan_id', $pernikahan?->pasangan_id);
        $manualPasangan = blank($selectedPasanganId);
    @endphp
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Tanggal Penerimaan Sakramen <span class="text-danger">*</span></label>
            <input type="date" name="tanggal_penerimaan" class="form-control @error('tanggal_penerimaan') is-invalid @enderror"
                value="{{ old('tanggal_penerimaan', $sakramen?->tanggal_penerimaan?->format('Y-m-d')) }}">
            @error('tanggal_penerimaan')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Jenis Pernikahan <span class="text-danger">*</span></label>
            <select name="jenis_pernikahan" class="form-select @error('jenis_pernikahan') is-invalid @enderror">
                <option value="">-- Pilih --</option>
                @foreach (\App\Models\Pernikahan::JENIS as $key => $label)
                    <option value="{{ $key }}" {{ old('jenis_pernikahan', $pernikahan?->jenis_pernikahan) === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @error('jenis_pernikahan')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
            <small class="fw-bold text-muted">Data Pasangan</small>
            <hr class="my-1">
        </div>

        <div class="col-md-6">
            <label class="form-label" for="pasangan_id">Pasangan (Umat Terdaftar)</label>
            <select name="pasangan_id" id="pasangan_id" class="form-select @error('pasangan_id') is-invalid @enderror">
                <option value="">-- Pilih atau isi manual --</option>
                @foreach ($umatList ?? collect() as $u)
                    <option value="{{ $u->id }}" {{ (string) $selectedPasanganId === (string) $u->id ? 'selected' : '' }}>
                        {{ $u->nama }}
                    </option>
                @endforeach
            </select>
            @error('pasangan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label" for="pasangan_agama_display">Agama Otomatis</label>
            <input type="text" id="pasangan_agama_display" class="form-control" value="Katholik" readonly {{ $manualPasangan ? 'disabled' : '' }}>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="pasangan_nama">Nama Pasangan (Manual) <span class="text-danger pasangan-manual-required">*</span></label>
            <input type="text" name="pasangan_nama" id="pasangan_nama" class="form-control @error('pasangan_nama') is-invalid @enderror"
                value="{{ old('pasangan_nama', $pernikahan?->pasangan_nama) }}"
                placeholder="Isi jika pasangan tidak terdaftar sebagai umat"
                {{ $manualPasangan ? '' : 'disabled' }}>
            @error('pasangan_nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label" for="pasangan_agama">Agama Pasangan (Manual) <span class="text-danger pasangan-manual-required">*</span></label>
            <input type="text" name="pasangan_agama" id="pasangan_agama" class="form-control @error('pasangan_agama') is-invalid @enderror"
                value="{{ old('pasangan_agama', $pernikahan?->pasangan_agama) }}"
                placeholder="Isi jika pasangan tidak terdaftar sebagai umat"
                {{ $manualPasangan ? '' : 'disabled' }}>
            @error('pasangan_agama')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Tanggal Nikah Gereja</label>
            <input type="date" name="tanggal_nikah_katolik" class="form-control"
                value="{{ old('tanggal_nikah_katolik', $pernikahan?->tanggal_nikah_katolik?->format('Y-m-d')) }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Tanggal Catatan Sipil</label>
            <input type="date" name="tanggal_catatan_sipil" class="form-control"
                value="{{ old('tanggal_catatan_sipil', $pernikahan?->tanggal_catatan_sipil?->format('Y-m-d')) }}">
        </div>
        <div class="col-12">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="izin_beda_gereja" id="izin_beda_gereja" value="1"
                    {{ old('izin_beda_gereja', $pernikahan?->izin_beda_gereja) ? 'checked' : '' }}>
                <label class="form-check-label" for="izin_beda_gereja">Memiliki Izin Beda Gereja</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="dispensasi" id="dispensasi" value="1"
                    {{ old('dispensasi', $pernikahan?->dispensasi) ? 'checked' : '' }}>
                <label class="form-check-label" for="dispensasi">Dispensasi</label>
            </div>
        </div>
    </div>
    <div class="d-flex gap-2 justify-content-end mt-4">
        <a href="{{ $backRoute }}" class="btn btn-light-secondary">Batal</a>
        <button type="submit" class="btn btn-danger"><i class="bi bi-save me-1"></i>Simpan</button>
    </div>
</form>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const pasanganSelect = document.getElementById('pasangan_id');
            const namaManual = document.getElementById('pasangan_nama');
            const agamaManual = document.getElementById('pasangan_agama');
            const agamaOtomatis = document.getElementById('pasangan_agama_display');
            const requiredLabels = document.querySelectorAll('.pasangan-manual-required');

            if (!pasanganSelect || !namaManual || !agamaManual || !agamaOtomatis) return;

            function syncPasanganFields() {
                const hasPasangan = pasanganSelect.value !== '';
                namaManual.disabled = hasPasangan;
                agamaManual.disabled = hasPasangan;
                agamaOtomatis.disabled = !hasPasangan;
                requiredLabels.forEach(function (label) {
                    label.classList.toggle('d-none', hasPasangan);
                });
            }

            pasanganSelect.addEventListener('change', syncPasanganFields);
            syncPasanganFields();
        });
    </script>
@endpush
