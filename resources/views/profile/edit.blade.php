@php
    /** @var \App\Models\User $authUser */
    $authUser = auth()->user()->loadMissing('roles');

    if ($authUser->isSekretariat()) {
        $layout         = 'layouts.sekretariat';
        $dashboardRoute = 'sekretariat.dashboard';
    } elseif ($authUser->isPastor()) {
        $layout         = 'layouts.pastor';
        $dashboardRoute = 'pastor.dashboard';
    } elseif ($authUser->isDewanPastoral()) {
        $layout         = 'layouts.dewan_pastoral';
        $dashboardRoute = 'dewan_pastoral.dashboard';
    } elseif ($authUser->isUmat()) {
        $layout         = 'layouts.portal';
        $dashboardRoute = 'portal.dashboard';
    } else {
        $layout         = 'layouts.guest';
        $dashboardRoute = 'login';
    }
@endphp

@extends($layout)

@section('title', 'Profil Saya')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Profil Saya</h3>
                    <p class="text-subtitle text-muted">Kelola informasi akun Anda</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route($dashboardRoute) }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Profil</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">

                {{-- Sidebar Profile Card --}}
                <div class="col-md-4 col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            @php
                                $name      = auth()->user()->name;
                                $avatarUrl = 'https://ui-avatars.com/api/?name=' .
                                    urlencode($name) .
                                    '&background=435ebe&color=fff&size=128&bold=true&font-size=0.4';
                            @endphp
                            <div class="avatar avatar-xl mb-3 mx-auto">
                                <img src="{{ $avatarUrl }}" alt="Avatar {{ $name }}" class="rounded-circle"
                                    style="width: 80px; height: 80px; object-fit: cover;">
                            </div>
                            <h5 class="mb-1 fw-bold">{{ $name }}</h5>
                            <p class="text-muted mb-2">{{ auth()->user()->email }}</p>
                            {{-- Multi-badge role --}}
                            @foreach ($authUser->roles as $role)
                                @php
                                    $badgeColor = match($role->name) {
                                        'sekretariat'      => 'bg-dark',
                                        'pastor'           => 'bg-danger',
                                        'dewan_pastoral'   => 'bg-warning text-dark',
                                        'ketua_kub'        => 'bg-success',
                                        'ketua_kategorial' => 'bg-info',
                                        default            => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $badgeColor }} me-1">{{ $role->label }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Forms Column --}}
                <div class="col-md-8 col-12">

                    {{-- Update Profile Information --}}
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i class="bi bi-person-fill me-2 text-primary"></i>
                                Informasi Profil
                            </h4>
                        </div>
                        <div class="card-body">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    {{-- Update Password --}}
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i class="bi bi-shield-lock-fill me-2 text-warning"></i>
                                Ubah Password
                            </h4>
                        </div>
                        <div class="card-body">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    {{-- Delete Account --}}
                    <div class="card border border-danger mb-4">
                        <div class="card-header bg-danger bg-opacity-10">
                            <h4 class="card-title text-danger">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                Hapus Akun
                            </h4>
                        </div>
                        <div class="card-body">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </div>
@endsection
