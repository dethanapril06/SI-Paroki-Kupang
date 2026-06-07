<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Pastor Paroki</title>

    <link rel="shortcut icon"
        href="data:image/svg+xml,%3csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%2033%2034'%20fill-rule='evenodd'%20stroke-linejoin='round'%20stroke-miterlimit='2'%20xmlns:v='https://vecta.io/nano'%3e%3cpath%20d='M3%2027.472c0%204.409%206.18%205.552%2013.5%205.552%207.281%200%2013.5-1.103%2013.5-5.513s-6.179-5.552-13.5-5.552c-7.281%200-13.5%201.103-13.5%205.513z'%20fill='%23800020'%20fill-rule='nonzero'/%3e%3ccircle%20cx='16.5'%20cy='8.8'%20r='8.8'%20fill='%23b22222'/%3e%3c/svg%3e"
        type="image/x-icon">

    <link rel="stylesheet" href="{{ asset('template/assets/extensions/simple-datatables/style.css') }}" />
    <link rel="stylesheet" crossorigin href="{{ asset('template/assets/compiled/css/table-datatable.css') }}" />
    <link rel="stylesheet" crossorigin href="{{ asset('template/assets/compiled/css/app.css') }}">
    <link rel="stylesheet" crossorigin href="{{ asset('template/assets/compiled/css/app-dark.css') }}">
    <link rel="stylesheet" crossorigin href="{{ asset('template/assets/compiled/css/iconly.css') }}">
    @stack('styles')
</head>

<body>
    <script src="{{ asset('template/assets/static/js/initTheme.js') }}"></script>
    <div id="app">
        <div id="sidebar">
            <div class="sidebar-wrapper active">
                <div class="sidebar-header position-relative">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="logo">
                            <a href="{{ route('pastor.dashboard') }}">
                                <img src="{{ asset('template/assets/compiled/png/logo.png') }}" alt="Logo"
                                    style="width:150px; height:auto;" />
                            </a>
                        </div>
                        <div class="theme-toggle d-flex gap-2 align-items-center mt-2">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                aria-hidden="true" role="img" class="iconify iconify--system-uicons" width="20"
                                height="20" preserveAspectRatio="xMidYMid meet" viewBox="0 0 21 21">
                                <g fill="none" fill-rule="evenodd" stroke="currentColor" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path
                                        d="M10.5 14.5c2.219 0 4-1.763 4-3.982a4.003 4.003 0 0 0-4-4.018c-2.219 0-4 1.781-4 4c0 2.219 1.781 4 4 4zM4.136 4.136L5.55 5.55m9.9 9.9l1.414 1.414M1.5 10.5h2m14 0h2M4.135 16.863L5.55 15.45m9.899-9.9l1.414-1.415M10.5 19.5v-2m0-14v-2"
                                        opacity=".3"></path>
                                    <g transform="translate(-210 -1)">
                                        <path d="M220.5 2.5v2m6.5.5l-1.5 1.5"></path>
                                        <circle cx="220.5" cy="11.5" r="4"></circle>
                                        <path d="m214 5l1.5 1.5m5 14v-2m6.5-.5l-1.5-1.5M214 18l1.5-1.5m-4-5h2m14 0h2">
                                        </path>
                                    </g>
                                </g>
                            </svg>
                            <div class="form-check form-switch fs-6">
                                <input class="form-check-input me-0" type="checkbox" id="toggle-dark"
                                    style="cursor: pointer" />
                                <label class="form-check-label"></label>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                aria-hidden="true" role="img" class="iconify iconify--mdi" width="20"
                                height="20" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="m17.75 4.09l-2.53 1.94l.91 3.06l-2.63-1.81l-2.63 1.81l.91-3.06l-2.53-1.94L12.44 4l1.06-3l1.06 3l3.19.09m3.5 6.91l-1.64 1.25l.59 1.98l-1.7-1.17l-1.7 1.17l.59-1.98L15.75 11l2.06-.05L18.5 9l.69 1.95l2.06.05m-2.28 4.95c.83-.08 1.72 1.1 1.19 1.85c-.32.45-.66.87-1.08 1.27C15.17 23 8.84 23 4.94 19.07c-3.91-3.9-3.91-10.24 0-14.14c.4-.4.82-.76 1.27-1.08c.75-.53 1.93.36 1.85 1.19c-.27 2.86.69 5.83 2.89 8.02a9.96 9.96 0 0 0 8.02 2.89m-1.64 2.02a12.08 12.08 0 0 1-7.8-3.47c-2.17-2.19-3.33-5-3.49-7.82c-2.81 3.14-2.7 7.96.31 10.98c3.02 3.01 7.84 3.12 10.98.31Z">
                                </path>
                            </svg>
                        </div>
                        <div class="sidebar-toggler x">
                            <a href="#" class="sidebar-hide d-xl-none d-block"><i
                                    class="bi bi-x bi-middle"></i></a>
                        </div>
                    </div>
                </div>
                <div class="sidebar-menu">

                    @php
                        $authUser = auth()->user()->loadMissing('roles');
                    @endphp

                    <ul class="menu">
                        <li class="sidebar-title">Menu Utama</li>

                        <li class="sidebar-item {{ request()->routeIs('pastor.dashboard') ? 'active' : '' }}">
                            <a href="{{ route('pastor.dashboard') }}" class="sidebar-link">
                                <i class="bi bi-grid-fill"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <li class="sidebar-title">Direktori Pastoral</li>

                        <li class="sidebar-item {{ request()->routeIs('pastor.umat.*') ? 'active' : '' }}">
                            <a href="{{ route('pastor.umat.index') }}" class="sidebar-link">
                                <i class="bi bi-person-lines-fill"></i>
                                <span>Direktori Umat</span>
                            </a>
                        </li>

                        <li class="sidebar-item {{ request()->routeIs('pastor.keluarga.*') ? 'active' : '' }}">
                            <a href="{{ route('pastor.keluarga.index') }}" class="sidebar-link">
                                <i class="bi bi-person-vcard-fill"></i>
                                <span>Direktori Keluarga</span>
                            </a>
                        </li>

                        <li class="sidebar-item {{ request()->routeIs('pastor.klerus.*') ? 'active' : '' }}">
                            <a href="{{ route('pastor.klerus.index') }}" class="sidebar-link">
                                <i class="bi bi-person-fill"></i>
                                <span>Rekan Klerus</span>
                            </a>
                        </li>

                        <li class="sidebar-title">Pelayanan Sakramen & Mutasi</li>

                        <li class="sidebar-item {{ request()->routeIs('pastor.sakramen.*') ? 'active' : '' }}">
                            <a href="{{ route('pastor.sakramen.index') }}" class="sidebar-link">
                                <i class="bi bi-droplet-half"></i>
                                <span>Penerimaan Sakramen</span>
                            </a>
                        </li>

                        <li class="sidebar-item {{ request()->routeIs('pastor.mutasi.*') ? 'active' : '' }}">
                            <a href="{{ route('pastor.mutasi.index') }}" class="sidebar-link">
                                <i class="bi bi-arrow-left-right"></i>
                                <span>Riwayat Mutasi</span>
                            </a>
                        </li>

                        <li class="sidebar-title">Laporan & Arsip</li>

                        <li class="sidebar-item {{ request()->routeIs('pastor.laporan.*') ? 'active' : '' }}">
                            <a href="{{ route('pastor.laporan.index') }}" class="sidebar-link">
                                <i class="bi bi-file-earmark-pdf-fill" style="color: #800020;"></i>
                                <span>Laporan PDF</span>
                            </a>
                        </li>

                        <li class="sidebar-title">Organisasi Paroki</li>

                        <li class="sidebar-item {{ request()->routeIs('pastor.dpp.*') ? 'active' : '' }}">
                            <a href="{{ route('pastor.dpp.index') }}" class="sidebar-link">
                                <i class="bi bi-people-fill"></i>
                                <span>Dewan Pastoral Paroki (DPP)</span>
                            </a>
                        </li>

                        <li class="sidebar-item {{ request()->routeIs('pastor.kategorial.*') ? 'active' : '' }}">
                            <a href="{{ route('pastor.kategorial.index') }}" class="sidebar-link">
                                <i class="bi bi-diagram-3-fill"></i>
                                <span>Kelompok Kategorial</span>
                            </a>
                        </li>

                        <li class="sidebar-title">Pengaturan Akun</li>

                        <li class="sidebar-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                            <a href="{{ route('profile.edit') }}" class="sidebar-link">
                                <i class="bi bi-person-fill"></i>
                                <span>Profil Saya</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div id="main" class="layout-navbar navbar-fixed">
            <header>
                <nav class="navbar navbar-expand navbar-light navbar-top">
                    <div class="container-fluid">
                        <a href="#" class="burger-btn d-block">
                            <i class="bi bi-justify fs-3"></i>
                        </a>

                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <div class="dropdown ms-auto">
                                <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="user-menu d-flex">
                                        <div class="user-name text-end me-3">
                                            <h6 class="mb-0 text-gray-600">{{ auth()->user()->name }}</h6>
                                            <p class="mb-0 text-sm text-gray-600">
                                                {{ $authUser->roles->pluck('label')->join(' · ') }}
                                            </p>
                                        </div>
                                        <div class="user-img d-flex align-items-center">
                                            <div class="avatar avatar-md">
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=800020&color=fff&size=64&bold=true&font-size=0.4"
                                                    alt="{{ auth()->user()->name }}" />
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton"
                                    style="min-width: 11rem">
                                    <li>
                                        <h6 class="dropdown-header">
                                            Halo, {{ auth()->user()->name }}!
                                        </h6>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('profile.edit') }}"><i
                                                class="icon-mid bi bi-person me-2"></i>
                                            Profil Saya</a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider" />
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="icon-mid bi bi-box-arrow-left me-2"></i>
                                                Logout
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>
            </header>
            <div id="main-content">
                @yield('content')
            </div>
            <footer>
                <div class="footer clearfix mb-0 text-muted">
                    <div class="float-start">
                        <p>2026 &copy; SI Paroki Kupang</p>
                    </div>
                    <div class="float-end">
                        <p>
                            Sistem Informasi Pelayanan Umat
                        </p>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="{{ asset('template/assets/static/js/components/dark.js') }}"></script>
    <script src="{{ asset('template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>

    <script src="{{ asset('template/assets/compiled/js/app.js') }}"></script>

    <script src="{{ asset('template/assets/extensions/simple-datatables/umd/simple-datatables.js') }}"></script>
    <script src="{{ asset('template/assets/static/js/pages/simple-datatables.js') }}"></script>

    @stack('scripts')
</body>

</html>
