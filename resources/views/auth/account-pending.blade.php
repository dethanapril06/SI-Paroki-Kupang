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

        .pending-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(var(--bs-warning-rgb), 0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            animation: pulse 2s infinite;
        }

        .pending-icon i {
            font-size: 2.5rem;
            color: var(--bs-warning);
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
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

                <div class="pending-icon">
                    <i class="bi bi-hourglass-split"></i>
                </div>

                <h1 style="font-size: 1.9rem; font-weight: 700; color: var(--bs-warning);" class="mb-2">
                    Akun Sedang Ditinjau
                </h1>
                <p class="text-muted mb-4" style="font-size: 0.95rem; line-height: 1.7;">
                    Pendaftaran Anda telah diterima dan sedang dalam proses tinjauan oleh ketua KUB Anda.
                    Anda belum dapat masuk sebelum akun disetujui.
                </p>

                <div class="alert alert-light-warning color-warning mb-4" role="alert">
                    <div class="d-flex gap-2 align-items-start">
                        <i class="bi bi-info-circle-fill mt-1"></i>
                        <div style="font-size: 0.88rem;">
                            Jika pendaftaran Anda sudah berlangsung lama, silakan hubungi ketua KUB Anda
                            secara langsung untuk konfirmasi.
                        </div>
                    </div>
                </div>

                <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-block btn-lg">
                    <i class="bi bi-arrow-left me-2"></i> Kembali ke Login
                </a>
            </div>
        </div>

        <div class="col-lg-7 d-none d-lg-block">
            <div id="auth-right"></div>
        </div>
    </div>
@endsection
