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
            padding: 5rem 10%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .auth-logo {
            margin-bottom: 3rem !important;
        }

        .auth-title {
            font-size: 2.8rem !important;
            font-weight: 700;
            color: var(--bs-primary);
        }

        .auth-subtitle {
            font-size: 1.1rem !important;
            line-height: 1.6rem !important;
            margin-bottom: 2rem !important;
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
            #auth-left {
                background: transparent !important;
                padding: 4rem 1.5rem;
            }
        }
    </style>

    <div class="row h-100 g-0">
        <div class="col-lg-5 col-12">
            <div id="auth-left">
                <div class="auth-logo">
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('template/assets/compiled/png/logo.png') }}" alt="Logo"
                            style="width: 160px; height: auto;" />
                    </a>
                </div>
                <h1 class="auth-title">Selamat Datang</h1>
                <p class="auth-subtitle text-muted">Silakan masuk menggunakan data akun Anda yang telah terdaftar di sistem paroki.</p>

                @if (session('status'))
                    <div class="alert alert-light-success color-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group position-relative has-icon-left mb-4">
                        <input type="email" name="email" id="email"
                            class="form-control form-control-xl @error('email') is-invalid @enderror" placeholder="Alamat Email"
                            value="{{ old('email') }}" required autofocus autocomplete="username">
                        <div class="form-control-icon">
                            <i class="bi bi-envelope"></i>
                        </div>
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group position-relative has-icon-left mb-4">
                        <input type="password" name="password" id="password"
                            class="form-control form-control-xl @error('password') is-invalid @enderror"
                            placeholder="Kata Sandi" required autocomplete="current-password">
                        <div class="form-control-icon">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-check form-check-lg d-flex align-items-end mb-4">
                        <input class="form-check-input me-2" type="checkbox" name="remember" id="remember"
                            {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label text-muted" for="remember" style="font-size: 0.95rem;">
                            Ingat saya di perangkat ini
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block btn-lg shadow-lg mt-2">Masuk</button>
                    <p class="text-center text-muted mt-3 mb-0" style="font-size: 0.88rem;">
                        Belum terdaftar?
                        <a href="{{ route('umat.register') }}" class="fw-semibold">Daftarkan diri Anda</a>
                    </p>
                </form>
            </div>
        </div>
        <div class="col-lg-7 d-none d-lg-block">
            <div id="auth-right"></div>
        </div>
    </div>
@endsection
