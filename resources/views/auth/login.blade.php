@extends('layouts.guest')

@section('title', 'Masuk - Sistem Informasi Paroki')

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

                <form method="POST" action="{{ route('login') }}" id="loginForm" novalidate>
                    @csrf
                    <div class="form-group position-relative has-icon-left mb-4">
                        <input type="email" name="email" id="email"
                            class="form-control form-control-xl @error('email') is-invalid @enderror" placeholder="Alamat Email"
                            value="{{ old('email') }}" required autofocus autocomplete="username">
                        <div class="form-control-icon">
                            <i class="bi bi-envelope"></i>
                        </div>
                        <div class="invalid-feedback @error('email') d-block @enderror" id="email-error">
                            {{ $errors->first('email') }}
                        </div>
                    </div>
                    <div class="form-group position-relative has-icon-left mb-4">
                        <input type="password" name="password" id="password"
                            class="form-control form-control-xl @error('password') is-invalid @enderror"
                            placeholder="Kata Sandi" required autocomplete="current-password">
                        <div class="form-control-icon">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                        <div class="invalid-feedback @error('password') d-block @enderror" id="password-error">
                            {{ $errors->first('password') }}
                        </div>
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('loginForm');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const emailError = document.getElementById('email-error');
            const passwordError = document.getElementById('password-error');

            function clearFieldError(input, errorEl) {
                input.classList.remove('is-invalid');
                errorEl.classList.remove('d-block');
                errorEl.textContent = '';
            }

            function showFieldError(input, errorEl, message) {
                input.classList.add('is-invalid');
                errorEl.classList.add('d-block');
                errorEl.textContent = message;
            }

            emailInput.addEventListener('input', function () {
                clearFieldError(emailInput, emailError);
            });

            passwordInput.addEventListener('input', function () {
                clearFieldError(passwordInput, passwordError);
            });

            form.addEventListener('submit', function (e) {
                let isValid = true;
                let firstInvalid = null;

                const emailVal = emailInput.value.trim();
                const passwordVal = passwordInput.value;

                if (!emailVal) {
                    showFieldError(emailInput, emailError, 'Alamat email wajib diisi.');
                    isValid = false;
                    if (!firstInvalid) firstInvalid = emailInput;
                } else {
                    // Cek format email sederhana
                    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailPattern.test(emailVal)) {
                        showFieldError(emailInput, emailError, 'Format alamat email tidak valid (contoh: nama@email.com).');
                        isValid = false;
                        if (!firstInvalid) firstInvalid = emailInput;
                    }
                }

                if (!passwordVal) {
                    showFieldError(passwordInput, passwordError, 'Kata sandi wajib diisi.');
                    isValid = false;
                    if (!firstInvalid) firstInvalid = passwordInput;
                }

                if (!isValid) {
                    e.preventDefault();
                    if (firstInvalid) {
                        firstInvalid.focus();
                    }
                }
            });
        });
    </script>
@endsection
