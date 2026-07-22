@extends('layouts.guest')

@section('content')
    <style>
        :root {
            --bs-body-bg-rgb: 255, 255, 255;
        }

        html[data-bs-theme="dark"] {
            --bs-body-bg-rgb: 28, 28, 45;
        }

        #auth-right {
            height: 100%;
            background: linear-gradient(
                to right,
                var(--bs-body-bg) 0%,
                rgba(var(--bs-body-bg-rgb), 0.95) 5%,
                rgba(var(--bs-body-bg-rgb), 0.7) 15%,
                rgba(var(--bs-body-bg-rgb), 0) 100%
            ), url('{{ asset('images/catholic_login_bg.png') }}') !important;
            background-size: cover !important;
            background-position: center center !important;
            background-repeat: no-repeat !important;
        }

        #auth-left {
            padding: 3rem 10%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow-y: auto;
        }

        .auth-logo {
            margin-bottom: 1.5rem !important;
        }

        .auth-title {
            font-size: 2rem !important;
            font-weight: 700;
            color: var(--bs-primary);
        }

        .auth-subtitle {
            font-size: 0.95rem !important;
            line-height: 1.5rem !important;
            margin-bottom: 1.5rem !important;
        }

        /* Step indicator */
        .step-indicator {
            display: flex;
            align-items: center;
            margin-bottom: 1.8rem;
            gap: 0;
        }

        .step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            position: relative;
        }

        .step-item:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 16px;
            left: 50%;
            width: 100%;
            height: 2px;
            background: var(--bs-border-color);
            z-index: 0;
            transition: background 0.3s;
        }

        .step-item.active::after,
        .step-item.done::after {
            background: var(--bs-primary);
        }

        .step-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 600;
            border: 2px solid var(--bs-border-color);
            background: var(--bs-body-bg);
            color: var(--bs-secondary);
            position: relative;
            z-index: 1;
            transition: all 0.3s;
        }

        .step-item.active .step-circle {
            border-color: var(--bs-primary);
            background: var(--bs-primary);
            color: white;
        }

        .step-item.done .step-circle {
            border-color: var(--bs-success);
            background: var(--bs-success);
            color: white;
        }

        .step-label {
            font-size: 0.7rem;
            margin-top: 0.3rem;
            color: var(--bs-secondary);
        }

        .step-item.active .step-label {
            color: var(--bs-primary);
            font-weight: 600;
        }

        /* Form sections */
        .form-section {
            display: none;
        }

        .form-section.active {
            display: block;
        }

        .section-heading {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--bs-secondary);
            margin-bottom: 1rem;
            padding-bottom: 0.4rem;
            border-bottom: 1px solid var(--bs-border-color);
        }

        /* Toggle keluarga mode */
        .mode-toggle {
            display: flex;
            border: 1.5px solid var(--bs-border-color);
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 1.2rem;
        }

        .mode-toggle input[type="radio"] { display: none; }

        .mode-toggle label {
            flex: 1;
            text-align: center;
            padding: 0.6rem 0.5rem;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            color: var(--bs-secondary);
            transition: all 0.2s;
            line-height: 1.3;
        }

        .mode-toggle input[type="radio"]:checked + label {
            background: var(--bs-primary);
            color: white;
            font-weight: 600;
        }

        .keluarga-panel { display: none; }
        .keluarga-panel.active { display: block; }

        @media screen and (max-width: 991.9px) {
            #auth {
                background: linear-gradient(
                    rgba(var(--bs-body-bg-rgb), 0.92),
                    rgba(var(--bs-body-bg-rgb), 0.92)
                ), url('{{ asset('images/catholic_login_bg.png') }}') !important;
                background-size: cover !important;
                background-position: center center !important;
            }

            #auth-left {
                background: transparent !important;
                padding: 2.5rem 1.5rem;
            }
        }
    </style>

    <div class="row h-100 g-0">
        {{-- Kolom kiri: form registrasi --}}
        <div class="col-lg-5 col-12">
            <div id="auth-left">

                <div class="auth-logo">
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('template/assets/compiled/png/logo.png') }}" alt="Logo"
                            style="width: 140px; height: auto;" />
                    </a>
                </div>

                <h1 class="auth-title">Daftarkan Diri</h1>
                <p class="auth-subtitle text-muted">Isi data berikut untuk mendaftarkan diri ke sistem paroki. Pendaftaran Anda akan ditinjau oleh Ketua KUB.</p>
                
                <div class="alert alert-light-info color-info py-2 mb-4">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <strong>Perhatian:</strong> Pendaftaran akun mandiri hanya diperbolehkan untuk umat berusia <strong>10 tahun ke atas</strong>. Untuk anak di bawah 10 tahun, silakan hubungi Kepala Keluarga, Ketua KUB, atau Sekretariat Paroki.
                </div>

                {{-- Error umum --}}
                @if ($errors->any())
                    <div class="alert alert-light-danger color-danger alert-dismissible fade show" role="alert">
                        <strong>Terdapat kesalahan pada data yang diisi:</strong>
                        <ul class="mb-0 mt-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Step Indicator --}}
                <div class="step-indicator">
                    <div class="step-item active" id="step-ind-1">
                        <div class="step-circle">1</div>
                        <span class="step-label">Keluarga</span>
                    </div>
                    <div class="step-item" id="step-ind-2">
                        <div class="step-circle">2</div>
                        <span class="step-label">Data Diri</span>
                    </div>
                    <div class="step-item" id="step-ind-3">
                        <div class="step-circle">3</div>
                        <span class="step-label">Akun</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('umat.register.store') }}" id="registerForm" novalidate>
                    @csrf

                    {{-- ====================================================
                         STEP 1: Data Keluarga
                    ==================================================== --}}
                    <div class="form-section active" id="step-1">
                        <p class="section-heading">Data Keluarga</p>

                        {{-- Hidden field mode --}}
                        <input type="hidden" name="keluarga_mode" id="keluarga_mode"
                            value="{{ old('keluarga_mode', 'baru') }}">

                        {{-- Toggle: Baru vs Ada --}}
                        <div class="mode-toggle">
                            <input type="radio" name="_mode_radio" id="mode_baru" value="baru"
                                {{ old('keluarga_mode', 'baru') === 'baru' ? 'checked' : '' }}
                                onchange="setKeluargaMode('baru')">
                            <label for="mode_baru">
                                <i class="bi bi-plus-circle me-1"></i> Buat Keluarga Baru
                            </label>

                            <input type="radio" name="_mode_radio" id="mode_ada" value="ada"
                                {{ old('keluarga_mode') === 'ada' ? 'checked' : '' }}
                                onchange="setKeluargaMode('ada')">
                            <label for="mode_ada">
                                <i class="bi bi-search me-1"></i> Bergabung ke Keluarga yang Ada
                            </label>
                        </div>

                        {{-- ── Panel: BUAT BARU ─────────────────────────────── --}}
                        <div class="keluarga-panel {{ old('keluarga_mode', 'baru') === 'baru' ? 'active' : '' }}"
                            id="panel-baru">

                            {{-- KUB --}}
                            <div class="form-group mb-3">
                                <label class="form-label" for="kub_id">KUB <span class="text-danger">*</span></label>
                                <select name="kub_id" id="kub_id"
                                    class="form-select @error('kub_id') is-invalid @enderror">
                                    <option value="">-- Pilih KUB --</option>
                                    @foreach ($kubList as $kub)
                                        <option value="{{ $kub->id }}"
                                            {{ old('kub_id') == $kub->id ? 'selected' : '' }}>
                                            {{ $kub->nama }}
                                            @if ($kub->wilayah) — Wilayah {{ $kub->wilayah->nama }} @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('kub_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Alamat --}}
                            <div class="form-group mb-3">
                                <label class="form-label" for="alamat">Alamat <span class="text-danger">*</span></label>
                                <textarea name="alamat" id="alamat" rows="2"
                                    class="form-control @error('alamat') is-invalid @enderror"
                                    placeholder="Jl. Flores No. 12, RT 03/RW 01">{{ old('alamat') }}</textarea>
                                @error('alamat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Status Tempat Tinggal --}}
                            <div class="form-group mb-3">
                                <label class="form-label" for="status_tempat_tinggal">
                                    Status Tempat Tinggal <span class="text-danger">*</span>
                                </label>
                                <select name="status_tempat_tinggal" id="status_tempat_tinggal"
                                    class="form-select @error('status_tempat_tinggal') is-invalid @enderror">
                                    <option value="">-- Pilih Status --</option>
                                    @foreach (['Rumah Pribadi', 'Kontrak/Kost', 'Dinas'] as $s)
                                        <option value="{{ $s }}"
                                            {{ old('status_tempat_tinggal') == $s ? 'selected' : '' }}>{{ $s }}</option>
                                    @endforeach
                                </select>
                                @error('status_tempat_tinggal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Kepala Keluarga --}}
                            <div class="form-check form-check-lg d-flex align-items-center mb-2 gap-2">
                                <input class="form-check-input" type="checkbox" name="sebagai_kepala_keluarga"
                                    id="sebagai_kepala_keluarga" value="1"
                                    {{ old('sebagai_kepala_keluarga', '1') ? 'checked' : '' }}>
                                <label class="form-check-label" for="sebagai_kepala_keluarga"
                                    style="font-size: 0.9rem;">
                                    <i class="bi bi-house-heart me-1 text-primary"></i>
                                    Jadikan saya sebagai <strong>Kepala Keluarga</strong>
                                </label>
                            </div>
                        </div>

                        {{-- ── Panel: KELUARGA YANG ADA ─────────────────────── --}}
                        <div class="keluarga-panel {{ old('keluarga_mode') === 'ada' ? 'active' : '' }}"
                            id="panel-ada">

                            <div class="form-group mb-3">
                                <label class="form-label" for="keluarga_id">
                                    Pilih Keluarga <span class="text-danger">*</span>
                                </label>
                                <select name="keluarga_id" id="keluarga_id"
                                    class="form-select @error('keluarga_id') is-invalid @enderror">
                                    <option value="">-- Cari / Pilih Keluarga --</option>
                                    @foreach ($keluargaList as $kel)
                                        <option value="{{ $kel->id }}"
                                            {{ old('keluarga_id') == $kel->id ? 'selected' : '' }}>
                                            @if ($kel->kepalaKeluarga)
                                                KK: {{ $kel->kepalaKeluarga->nama }}
                                            @else
                                                Keluarga #{{ $kel->id }}
                                            @endif
                                            — {{ Str::limit($kel->alamat, 40) }}
                                            @if ($kel->kub) ({{ $kel->kub->nama }}) @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('keluarga_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Pilih sesuai nama kepala keluarga atau alamat.
                                </div>
                            </div>

                            {{-- Info box --}}
                            <div class="alert alert-light-info color-info py-2 px-3 mb-0" style="font-size:0.82rem;">
                                <i class="bi bi-people me-1"></i>
                                Data keluarga (KUB, alamat) akan mengikuti keluarga yang dipilih.
                            </div>
                        </div>

                        <button type="button" class="btn btn-primary btn-block btn-lg mt-3" onclick="goToStep(2, true)">
                            Lanjut <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>

                    {{-- ====================================================
                         STEP 2: Data Pribadi
                    ==================================================== --}}
                    <div class="form-section" id="step-2">
                        <p class="section-heading">Data Pribadi</p>

                        <div class="row g-2">
                            <div class="col-12 mb-2">
                                <label class="form-label" for="nama">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama" id="nama"
                                    class="form-control @error('nama') is-invalid @enderror"
                                    value="{{ old('nama') }}" placeholder="Nama sesuai KTP" required>
                                @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-7 mb-2">
                                <label class="form-label" for="tempat_lahir">Tempat Lahir <span class="text-danger">*</span></label>
                                <input type="text" name="tempat_lahir" id="tempat_lahir"
                                    class="form-control @error('tempat_lahir') is-invalid @enderror"
                                    value="{{ old('tempat_lahir') }}" placeholder="Kupang" required>
                                @error('tempat_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-5 mb-2">
                                <label class="form-label" for="tanggal_lahir">Tanggal Lahir <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_lahir" id="tanggal_lahir"
                                    class="form-control @error('tanggal_lahir') is-invalid @enderror"
                                    value="{{ old('tanggal_lahir') }}" required>
                                @error('tanggal_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-6 mb-2">
                                <label class="form-label" for="jenis_kelamin">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select name="jenis_kelamin" id="jenis_kelamin"
                                    class="form-select @error('jenis_kelamin') is-invalid @enderror" required>
                                    <option value="">--</option>
                                    <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('jenis_kelamin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-6 mb-2">
                                <label class="form-label" for="hubungan_keluarga">Hubungan Keluarga <span class="text-danger">*</span></label>
                                <select name="hubungan_keluarga" id="hubungan_keluarga"
                                    class="form-select @error('hubungan_keluarga') is-invalid @enderror" required>
                                    <option value="">--</option>
                                    @foreach (['Suami', 'Istri', 'Anak', 'Saudara', 'Ayah', 'Ibu', 'Lainnya'] as $hub)
                                        <option value="{{ $hub }}" {{ old('hubungan_keluarga') == $hub ? 'selected' : '' }}>{{ $hub }}</option>
                                    @endforeach
                                </select>
                                @error('hubungan_keluarga') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-6 mb-2">
                                <label class="form-label" for="status_pernikahan">Status Pernikahan <span class="text-danger">*</span></label>
                                <select name="status_pernikahan" id="status_pernikahan"
                                    class="form-select @error('status_pernikahan') is-invalid @enderror" required>
                                    <option value="">--</option>
                                    @foreach (['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'] as $sp)
                                        <option value="{{ $sp }}" {{ old('status_pernikahan') == $sp ? 'selected' : '' }}>{{ $sp }}</option>
                                    @endforeach
                                </select>
                                @error('status_pernikahan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-6 mb-2">
                                <label class="form-label" for="no_telepon">No. Telepon <span class="text-danger">*</span></label>
                                <input type="tel" name="no_telepon" id="no_telepon"
                                    class="form-control @error('no_telepon') is-invalid @enderror"
                                    value="{{ old('no_telepon') }}" placeholder="08xx-xxxx-xxxx" required>
                                @error('no_telepon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-6 mb-2">
                                <label class="form-label" for="pendidikan">Pendidikan Terakhir <span class="text-danger">*</span></label>
                                <select name="pendidikan" id="pendidikan"
                                    class="form-select @error('pendidikan') is-invalid @enderror" required>
                                    <option value="">--</option>
                                    @foreach (['Tidak Sekolah', 'SD', 'SMP', 'SMA', 'D3', 'S1', 'S2', 'S3'] as $p)
                                        <option value="{{ $p }}" {{ old('pendidikan') == $p ? 'selected' : '' }}>{{ $p }}</option>
                                    @endforeach
                                </select>
                                @error('pendidikan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-6 mb-2">
                                <label class="form-label" for="pekerjaan">Pekerjaan <span class="text-danger">*</span></label>
                                <input type="text" name="pekerjaan" id="pekerjaan"
                                    class="form-control @error('pekerjaan') is-invalid @enderror"
                                    value="{{ old('pekerjaan') }}" placeholder="PNS, Wiraswasta, dll." required>
                                @error('pekerjaan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-2">
                            <button type="button" class="btn btn-outline-secondary btn-lg flex-fill" onclick="goToStep(1)">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </button>
                            <button type="button" class="btn btn-primary btn-lg flex-fill" onclick="goToStep(3, true)">
                                Lanjut <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>

                    {{-- ====================================================
                         STEP 3: Akun Login
                    ==================================================== --}}
                    <div class="form-section" id="step-3">
                        <p class="section-heading">Akun Login</p>

                        <div class="form-group position-relative has-icon-left mb-3">
                            <input type="email" name="email" id="email"
                                class="form-control form-control-xl @error('email') is-invalid @enderror"
                                placeholder="Alamat Email" value="{{ old('email') }}" required autocomplete="email">
                            <div class="form-control-icon"><i class="bi bi-envelope"></i></div>
                            @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group position-relative has-icon-left mb-3">
                            <input type="password" name="password" id="password"
                                class="form-control form-control-xl @error('password') is-invalid @enderror"
                                placeholder="Kata Sandi (min. 8 karakter)" required autocomplete="new-password">
                            <div class="form-control-icon"><i class="bi bi-shield-lock"></i></div>
                            @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="form-control form-control-xl"
                                placeholder="Konfirmasi Kata Sandi" required autocomplete="new-password">
                            <div class="form-control-icon"><i class="bi bi-shield-check"></i></div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-lg flex-fill" onclick="goToStep(2)">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </button>
                            <button type="submit" class="btn btn-primary btn-lg flex-fill">
                                <i class="bi bi-person-check me-1"></i> Daftar Sekarang
                            </button>
                        </div>
                    </div>

                </form>

                <p class="text-center text-muted mt-3" style="font-size: 0.88rem;">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="fw-semibold">Masuk di sini</a>
                </p>

            </div>
        </div>

        {{-- Kolom kanan: gambar --}}
        <div class="col-lg-7 d-none d-lg-block">
            <div id="auth-right"></div>
        </div>
    </div>

    <script>
        const totalSteps = 3;
        let currentStep = {{ $errors->any() ? 3 : 1 }};

        // ── Toggle mode keluarga baru / ada ──────────────────────────────────
        function setKeluargaMode(mode) {
            document.getElementById('keluarga_mode').value = mode;

            document.querySelectorAll('.keluarga-panel').forEach(p => p.classList.remove('active'));
            document.getElementById('panel-' + mode).classList.add('active');

            // Set required hanya pada field yang aktif
            const isBaru = mode === 'baru';
            ['kub_id', 'alamat', 'status_tempat_tinggal'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.required = isBaru;
            });
            const kelEl = document.getElementById('keluarga_id');
            if (kelEl) kelEl.required = !isBaru;
        }

        // Inisialisasi saat load
        document.addEventListener('DOMContentLoaded', function () {
            const mode = document.getElementById('keluarga_mode').value || 'baru';
            setKeluargaMode(mode);

            document.querySelectorAll('#registerForm input, #registerForm select, #registerForm textarea').forEach(field => {
                field.addEventListener('input', () => field.classList.remove('is-invalid'));
                field.addEventListener('change', () => field.classList.remove('is-invalid'));
            });

            document.getElementById('registerForm').addEventListener('submit', function (event) {
                for (let step = 1; step <= totalSteps; step++) {
                    if (!validateStep(step, false)) {
                        event.preventDefault();
                        goToStep(step);
                        validateStep(step);
                        return;
                    }
                }
            });
        });

        // ── Navigasi step ─────────────────────────────────────────────────────
        function validateStep(step, showFeedback = true) {
            const section = document.getElementById('step-' + step);
            const fields = Array.from(section.querySelectorAll('input, select, textarea'))
                .filter(field => !field.disabled && field.type !== 'hidden');

            for (const field of fields) {
                field.classList.remove('is-invalid');

                if (!field.checkValidity()) {
                    field.classList.add('is-invalid');
                    if (showFeedback) {
                        field.reportValidity();
                        field.focus({ preventScroll: true });
                        field.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    return false;
                }
            }

            return true;
        }

        function goToStep(step, validateBeforeNext = false) {
            if (validateBeforeNext && step > currentStep && !validateStep(currentStep)) {
                return;
            }

            document.querySelectorAll('.form-section').forEach(s => s.classList.remove('active'));
            document.querySelectorAll('.step-item').forEach(s => s.classList.remove('active', 'done'));

            document.getElementById('step-' + step).classList.add('active');

            for (let i = 1; i <= totalSteps; i++) {
                const ind = document.getElementById('step-ind-' + i);
                if (i < step) ind.classList.add('done');
                else if (i === step) ind.classList.add('active');
            }

            currentStep = step;
            document.getElementById('auth-left').scrollTo({ top: 0, behavior: 'smooth' });
        }

        // ── Redirect ke step yang error ───────────────────────────────────────
        @if ($errors->any())
            document.addEventListener('DOMContentLoaded', function() {
                const step1Fields = ['kub_id', 'alamat', 'status_tempat_tinggal', 'keluarga_id'];
                const step2Fields = ['nama', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'hubungan_keluarga', 'status_pernikahan', 'no_telepon', 'pendidikan', 'pekerjaan'];
                const errorKeys = @json(array_keys($errors->toArray()));

                let targetStep = 3;
                if (errorKeys.some(k => step1Fields.includes(k))) targetStep = 1;
                else if (errorKeys.some(k => step2Fields.includes(k))) targetStep = 2;

                goToStep(targetStep);
            });
        @endif
    </script>
@endsection
