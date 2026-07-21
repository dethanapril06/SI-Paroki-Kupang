{{-- Partial form baptis anggota: digunakan oleh show (POST store) dan form (PUT update) --}}
<form action="{{ $action }}" method="POST">
    @csrf
    @if ($method === 'PUT') @method('PUT') @endif

    @if ($errors->any())
        <div class="alert alert-light-danger color-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="form-body">
        <div class="row g-3">

            {{-- Tanggal Penerimaan --}}
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label for="tanggal_penerimaan">Tanggal Penerimaan <span class="text-danger">*</span></label>
                    <input type="date"
                        class="form-control @error('tanggal_penerimaan') is-invalid @enderror"
                        id="tanggal_penerimaan" name="tanggal_penerimaan"
                        value="{{ old('tanggal_penerimaan', $sakramen?->tanggal_penerimaan?->format('Y-m-d')) }}">
                    @error('tanggal_penerimaan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Paroki --}}
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label for="paroki_id">Paroki</label>
                    <select class="form-select @error('paroki_id') is-invalid @enderror"
                        id="paroki_id" name="paroki_id">
                        <option value="">-- Pilih Paroki --</option>
                        @foreach ($parokiList as $p)
                            <option value="{{ $p->id }}"
                                {{ old('paroki_id', $sakramen?->paroki_id) == $p->id ? 'selected' : '' }}>
                                {{ $p->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('paroki_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Nomor Surat --}}
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label for="nomor_surat">Nomor Surat</label>
                    <input type="text"
                        class="form-control @error('nomor_surat') is-invalid @enderror"
                        id="nomor_surat" name="nomor_surat"
                        value="{{ old('nomor_surat', $sakramen?->nomor_surat) }}" placeholder="Opsional">
                    @error('nomor_surat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-12"><hr class="my-1"></div>

            {{-- Sumber Baptis --}}
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label for="sumber_baptis">Sumber Baptis <span class="text-danger">*</span></label>
                    <select class="form-select @error('sumber_baptis') is-invalid @enderror"
                        id="sumber_baptis" name="sumber_baptis" onchange="onSumberChange()">
                        <option value="">-- Pilih Sumber --</option>
                        <option value="KATOLIK" {{ old('sumber_baptis', $baptis?->sumber_baptis) === 'KATOLIK' ? 'selected' : '' }}>Katolik</option>
                        <option value="PROTESTAN" {{ old('sumber_baptis', $baptis?->sumber_baptis) === 'PROTESTAN' ? 'selected' : '' }}>Protestan</option>
                    </select>
                    @error('sumber_baptis')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Tanggal Baptis --}}
            <div class="col-12 col-md-6 d-none" id="wrap-tgl-baptis">
                <div class="form-group">
                    <label for="tgl_baptis">Tanggal Baptis <span class="text-danger" id="req-tgl-baptis">*</span></label>
                    <input type="date"
                        class="form-control @error('tgl_baptis') is-invalid @enderror"
                        id="tgl_baptis" name="tgl_baptis"
                        value="{{ old('tgl_baptis', $baptis?->tgl_baptis?->format('Y-m-d')) }}">
                    @error('tgl_baptis')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Nama Baptis --}}
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label for="nama_baptis">Nama Baptis</label>
                    <input type="text"
                        class="form-control @error('nama_baptis') is-invalid @enderror"
                        id="nama_baptis" name="nama_baptis"
                        value="{{ old('nama_baptis', $baptis?->nama_baptis) }}" placeholder="Opsional">
                    @error('nama_baptis')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Pastor Pembaptis (Katolik) --}}
            <div class="col-12 col-md-6 d-none" id="wrap-klerus">
                <div class="form-group">
                    <label for="klerus_id">Pastor Pembaptis <span class="text-danger">*</span></label>
                    <select name="klerus_id" id="klerus_id" class="form-select @error('klerus_id') is-invalid @enderror">
                        <option value="">-- Pilih Klerus --</option>
                        @foreach ($klerusList as $k)
                            <option value="{{ $k->id }}" {{ old('klerus_id', $sakramen?->klerus_id) == $k->id ? 'selected' : '' }}>
                                {{ $k->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('klerus_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Pemberi Protestan --}}
            <div class="col-12 col-md-6 d-none" id="wrap-protestan">
                <div class="form-group">
                    <label for="nama_pemberi_protestan">Nama Pemberi Baptis <span class="text-danger">*</span></label>
                    <input type="text"
                        class="form-control @error('nama_pemberi_protestan') is-invalid @enderror"
                        id="nama_pemberi_protestan" name="nama_pemberi_protestan"
                        value="{{ old('nama_pemberi_protestan', $baptis?->nama_pemberi_protestan) }}">
                    @error('nama_pemberi_protestan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Nama Gereja --}}
            <div class="col-12 col-md-6 d-none" id="wrap-gereja">
                <div class="form-group">
                    <label for="nama_gereja_protestan">Nama Gereja <span class="text-danger">*</span></label>
                    <input type="text"
                        class="form-control @error('nama_gereja_protestan') is-invalid @enderror"
                        id="nama_gereja_protestan" name="nama_gereja_protestan"
                        value="{{ old('nama_gereja_protestan', $baptis?->nama_gereja_protestan) }}">
                    @error('nama_gereja_protestan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Tanggal Diterima Katolik --}}
            <div class="col-12 col-md-6 d-none" id="wrap-tgl-katolik">
                <div class="form-group">
                    <label for="tgl_diterima_katolik">Tanggal Diterima Katolik <span class="text-danger">*</span></label>
                    <input type="date"
                        class="form-control @error('tgl_diterima_katolik') is-invalid @enderror"
                        id="tgl_diterima_katolik" name="tgl_diterima_katolik"
                        value="{{ old('tgl_diterima_katolik', $baptis?->tgl_diterima_katolik?->format('Y-m-d')) }}">
                    @error('tgl_diterima_katolik')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-12"><hr class="my-1"><small class="text-muted fw-bold">Wali Baptis (minimal salah satu)</small></div>

            {{-- Bapak Baptis --}}
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label for="bapak_baptis_id">Bapak Baptis (Umat Terdaftar)</label>
                    <select class="form-select @error('bapak_baptis_id') is-invalid @enderror"
                        id="bapak_baptis_id" name="bapak_baptis_id">
                        <option value="">-- Pilih atau isi manual --</option>
                        @foreach ($umatWali as $u)
                            <option value="{{ $u->id }}"
                                {{ old('bapak_baptis_id', $baptis?->bapak_baptis_id) == $u->id ? 'selected' : '' }}>
                                {{ $u->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label for="bapak_baptis_nama">Bapak Baptis (Manual)</label>
                    <input type="text"
                        class="form-control @error('bapak_baptis_nama') is-invalid @enderror"
                        id="bapak_baptis_nama" name="bapak_baptis_nama"
                        value="{{ old('bapak_baptis_nama', $baptis?->bapak_baptis_nama) }}" placeholder="Jika tidak terdaftar">
                </div>
            </div>

            {{-- Ibu Baptis --}}
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label for="ibu_baptis_id">Ibu Baptis (Umat Terdaftar)</label>
                    <select class="form-select @error('ibu_baptis_id') is-invalid @enderror"
                        id="ibu_baptis_id" name="ibu_baptis_id">
                        <option value="">-- Pilih atau isi manual --</option>
                        @foreach ($umatWali as $u)
                            <option value="{{ $u->id }}"
                                {{ old('ibu_baptis_id', $baptis?->ibu_baptis_id) == $u->id ? 'selected' : '' }}>
                                {{ $u->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label for="ibu_baptis_nama">Ibu Baptis (Manual)</label>
                    <input type="text"
                        class="form-control @error('ibu_baptis_nama') is-invalid @enderror"
                        id="ibu_baptis_nama" name="ibu_baptis_nama"
                        value="{{ old('ibu_baptis_nama', $baptis?->ibu_baptis_nama) }}" placeholder="Jika tidak terdaftar">
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2 justify-content-end mt-4">
        <a href="{{ $backRoute }}" class="btn btn-light-secondary">Batal</a>
        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
    </div>
</form>

@push('scripts')
<script>
    function onSumberChange() {
        const sumber = document.getElementById('sumber_baptis').value;
        document.getElementById('wrap-klerus').classList.toggle('d-none', sumber !== 'KATOLIK');
        document.getElementById('wrap-protestan').classList.toggle('d-none', sumber !== 'PROTESTAN');
        document.getElementById('wrap-gereja').classList.toggle('d-none', sumber !== 'PROTESTAN');
        document.getElementById('wrap-tgl-katolik').classList.toggle('d-none', sumber !== 'PROTESTAN');
        document.getElementById('wrap-tgl-baptis').classList.toggle('d-none', sumber !== 'PROTESTAN');

        const reqAsterisk = document.getElementById('req-tgl-baptis');
        if (reqAsterisk) {
            reqAsterisk.style.display = (sumber === 'PROTESTAN') ? 'inline' : 'none';
        }
    }
    document.addEventListener('DOMContentLoaded', onSumberChange);
</script>
@endpush
