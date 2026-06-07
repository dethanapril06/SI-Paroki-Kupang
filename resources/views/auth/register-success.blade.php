@extends('layouts.guest')

@section('content')
    <style>
        :root { --bs-body-bg-rgb: 255, 255, 255; }
        html[data-bs-theme="dark"] { --bs-body-bg-rgb: 28, 28, 45; }

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
            padding: 5rem 10%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(var(--bs-success-rgb), 0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            animation: scaleIn 0.5s ease-out;
        }

        .success-icon i {
            font-size: 2.5rem;
            color: var(--bs-success);
        }

        @keyframes scaleIn {
            from { transform: scale(0); opacity: 0; }
            to   { transform: scale(1); opacity: 1; }
        }

        .timeline-item {
            display: flex;
            gap: 1rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--bs-border-color);
        }

        .timeline-item:last-child { border-bottom: none; }

        .timeline-num {
            width: 28px;
            height: 28px;
            min-width: 28px;
            border-radius: 50%;
            background: var(--bs-primary);
            color: white;
            font-size: 0.75rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 2px;
        }

        @media screen and (max-width: 991.9px) {
            #auth {
                background: linear-gradient(
                    rgba(var(--bs-body-bg-rgb), 0.92),
                    rgba(var(--bs-body-bg-rgb), 0.92)
                ), url('{{ asset('images/catholic_login_bg.png') }}') !important;
                background-size: cover !important;
                background-position: center center !important;
            }
            #auth-left { background: transparent !important; padding: 4rem 1.5rem; }
        }
    </style>

    <div class="row h-100 g-0">
        <div class="col-lg-5 col-12">
            <div id="auth-left">
                <a href="{{ url('/') }}" class="d-block mb-4">
                    <img src="{{ asset('template/assets/compiled/png/logo.png') }}" alt="Logo"
                        style="width: 140px; height: auto;" />
                </a>

                <div class="success-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>

                <h1 style="font-size: 1.9rem; font-weight: 700; color: var(--bs-success);" class="mb-2">
                    Pendaftaran Terkirim!
                </h1>
                <p class="text-muted mb-4" style="font-size: 0.95rem; line-height: 1.6;">
                    Terima kasih telah mendaftarkan diri. Pendaftaran Anda sedang dalam proses tinjauan oleh ketua KUB anda.
                </p>

                <div class="card mb-4">
                    <div class="card-body p-3">
                        <p class="fw-semibold mb-2" style="font-size: 0.85rem; color: var(--bs-secondary);">APA SELANJUTNYA?</p>
                        <div class="timeline-item">
                            <div class="timeline-num">1</div>
                            <div>
                                <strong style="font-size: 0.9rem;">Tinjauan ketua KUB</strong>
                                <p class="text-muted mb-0" style="font-size: 0.82rem;">Data Anda akan diverifikasi oleh ketua KUB Anda.</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-num">2</div>
                            <div>
                                <strong style="font-size: 0.9rem;">Persetujuan Akun</strong>
                                <p class="text-muted mb-0" style="font-size: 0.82rem;">Setelah disetujui, akun Anda akan diaktifkan.</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-num">3</div>
                            <div>
                                <strong style="font-size: 0.9rem;">Login ke Sistem</strong>
                                <p class="text-muted mb-0" style="font-size: 0.82rem;">Gunakan email dan kata sandi yang Anda daftarkan untuk masuk.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <a href="{{ route('login') }}" class="btn btn-primary btn-block btn-lg">
                    <i class="bi bi-box-arrow-in-right me-2"></i> Kembali ke Halaman Login
                </a>
            </div>
        </div>

        <div class="col-lg-7 d-none d-lg-block">
            <div id="auth-right"></div>
        </div>
    </div>
@endsection
