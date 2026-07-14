<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Paroki</title>

    <link rel="shortcut icon"
        href="data:image/svg+xml,%3csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%2033%2034'%20fill-rule='evenodd'%20stroke-linejoin='round'%20stroke-miterlimit='2'%20xmlns:v='https://vecta.io/nano'%3e%3cpath%20d='M3%2027.472c0%204.409%206.18%205.552%2013.5%205.552%207.281%200%2013.5-1.103%2013.5-5.513s-6.179-5.552-13.5-5.552c-7.281%200-13.5%201.103-13.5%205.513z'%20fill='%23435ebe'%20fill-rule='nonzero'/%3e%3ccircle%20cx='16.5'%20cy='8.8'%20r='8.8'%20fill='%2341bbdd'/%3e%3c/svg%3e"
        type="image/x-icon">
    <link rel="shortcut icon"
        href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACEAAAAiCAYAAADRcLDBAAAEs2lUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4KPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iWE1QIENvcmUgNS41LjAiPgogPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4KICA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIgogICAgeG1sbnM6ZXhpZj0iaHR0cDovL25zLmFkb2JlLmNvbS9leGlmLzEuMC8iCiAgICB4bWxuczp0aWZmPSJodHRwOi8vbnMuYWRvYmUuY29tL3RpZmYvMS4wLyIKICAgIHhtbG5zOnBob3Rvc2hvcD0iaHR0cDovL25zLmFkb2JlLmNvbS9waG90b3Nob3AvMS4wLyIKICAgIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIKICAgIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIgogICAgeG1sbnM6c3RFdnQ9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZUV2ZW50IyIKICAgZXhpZjpQaXhlbFhEaW1lbnNpb249IjMzIgogICBleGlmOlBpeGVsWURpbWVuc2lvbj0iMzQiCiAgIGV4aWY6Q29sb3JTcGFjZT0iMSIKICAgdGlmZjpJbWFnZVdpZHRoPSIzMyIKICAgdGlmZjpJbWFnZUxlbmd0aD0iMzQiCiAgIHRpZmY6UmVzb2x1dGlvblVuaXQ9IjIiCiAgIHRpZmY6WFJlc29sdXRpb249Ijk2LjAiCiAgIHRpZmY6WVJlc29sdXRpb249Ijk2LjAiCiAgIHBob3Rvc2hvcDpDb2xvck1vZGU9IjMiCiAgIHBob3Rvc2hvcDpJQ0NQcm9maWxlPSJzUkdCIElFQzYxOTY2LTIuMSIKICAgeG1wOk1vZGlmeURhdGU9IjIwMjItMDMtMzFUMTA6NTA6MjMrMDI6MDAiCiAgIHhtcDpNZXRhZGF0YURhdGU9IjIwMjItMDMtMzFUMTA6NTA6MjMrMDI6MDAiPgogICA8eG1wTU06SGlzdG9yeT4KICAgIDxyZGY6U2VxPgogICAgIDxyZGY6bGkKICAgICAgc3RFdnQ6YWN0aW9uPSJwcm9kdWNlZCIKICAgICAgc3RFdnQ6c29mdHdhcmVBZ2VudD0iQWZmaW5pdHkgRGVzaWduZXIgMS4xMC4xIgogICAgICBzdEV2dDp3aGVuPSIyMDIyLTAzLTMxVDEwOjUwOjIzKzAyOjAwIi8+CiAgICA8L3JkZjpTZXE+CiAgIDwveG1wTU06SGlzdG9yeT4KICA8L3JkZjpEZXNjcmlwdGlvbj4KIDwvcmRmOlJERj4KPC94OnhtcG1ldGE+Cjw/eHBhY2tldCBlbmQ9InIiPz5V57uAAAABgmlDQ1BzUkdCIElFQzYxOTY2LTIuMQAAKJF1kc8rRFEUxz9maORHo1hYKC9hISNGTWwsRn4VFmOUX5uZZ36oeTOv954kW2WrKLHxa8FfwFZZK0WkZClrYoOe87ypmWTO7dzzud97z+nec8ETzaiaWd4NWtYyIiNhZWZ2TvE946WZSjqoj6mmPjE1HKWkfdxR5sSbgFOr9Ll/rXoxYapQVik8oOqGJTwqPL5i6Q5vCzeo6dii8KlwpyEXFL519LjLLw6nXP5y2IhGBsFTJ6ykijhexGra0ITl5bRqmWU1fx/nJTWJ7PSUxBbxJkwijBBGYYwhBgnRQ7/MIQIE6ZIVJfK7f/MnyUmuKrPOKgZLpEhj0SnqslRPSEyKnpCRYdXp/9++msneoFu9JgwVT7b91ga+LfjetO3PQ9v+PgLvI1xkC/m5A+h7F32zoLXug38dzi4LWnwHzjeg8UGPGbFfySvuSSbh9QRqZ6H+Gqrm3Z7l9zm+h+iafNUV7O5Bu5z3L/wAdthn7QIme0YAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAJTSURBVFiF7Zi9axRBGIefEw2IdxFBRQsLWUTBaywSK4ubdSGVIY1Y6HZql8ZKCGIqwX/AYLmCgVQKfiDn7jZeEQMWfsSAHAiKqPiB5mIgELWYOW5vzc3O7niHhT/YZvY37/swM/vOzJbIqVq9uQ04CYwCI8AhYAlYAB4Dc7HnrOSJWcoJcBS4ARzQ2F4BZ2LPmTeNuykHwEWgkQGAet9QfiMZjUSt3hwD7psGTWgs9pwH1hC1enMYeA7sKwDxBqjGnvNdZzKZjqmCAKh+U1kmEwi3IEBbIsugnY5avTkEtIAtFhBrQCX2nLVehqyRqFoCAAwBh3WGLAhbgCRIYYinwLolwLqKUwwi9pxV4KUlxKKKUwxC6ZElRCPLYAJxGfhSEOCz6m8HEXvOB2CyIMSk6m8HoXQTmMkJcA2YNTHm3congOvATo3tE3A29pxbpnFzQSiQPcB55IFmFNgFfEQeahaAGZMpsIJIAZWAHcDX2HN+2cT6r39GxmvC9aPNwH5gO1BOPFuBVWAZue0vA9+A12EgjPadnhCuH1WAE8ivYAQ4ohKaagV4gvxi5oG7YSA2vApsCOH60WngKrA3R9IsvQUuhIGY00K4flQG7gHH/mLytB4C42EgfrQb0mV7us8AAMeBS8mGNMR4nwHamtBB7B4QRNdaS0M8GxDEog7iyoAguvJ0QYSBuAOcAt71Kfl7wA8DcTvZ2KtOlJEr+ByyQtqqhTyHTIeB+ONeqi3brh+VgIN0fohUgWGggizZFTplu12yW8iy/YLOGWMpDMTPXnl+Az9vj2HERYqPAAAAAElFTkSuQmCC"
        type="image/png">

    <link rel="stylesheet" href="{{ asset('template/assets/extensions/simple-datatables/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/extensions/choices.js/public/assets/styles/choices.min.css') }}" />

    <link rel="stylesheet" crossorigin href="{{ asset('template/assets/compiled/css/table-datatable.css') }}" />
    <link rel="stylesheet" crossorigin href="{{ asset('template/assets/compiled/css/app.css') }}">
    <link rel="stylesheet" crossorigin href="{{ asset('template/assets/compiled/css/app-dark.css') }}">
    <link rel="stylesheet" crossorigin href="{{ asset('template/assets/compiled/css/iconly.css') }}">
    <style>
        .choices {
            margin-bottom: 0;
        }

        .choices__inner {
            min-height: 38px;
            border-color: var(--bs-border-color);
            background-color: var(--bs-body-bg);
            border-radius: .375rem;
        }

        .choices[data-type*=select-one] .choices__inner {
            padding-bottom: 3.5px;
        }

        .choices__list--dropdown,
        .choices__list[aria-expanded] {
            z-index: 1060;
        }
    </style>
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
                            <a href="{{ route('sekretariat.dashboard') }}">
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

                        <li class="sidebar-item {{ request()->routeIs('sekretariat.dashboard') ? 'active' : '' }}">
                            <a href="{{ route('sekretariat.dashboard') }}" class="sidebar-link">
                                <i class="bi bi-grid-fill"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <li
                            class="sidebar-item has-sub {{ request()->routeIs('sekretariat.keuskupan.*') || request()->routeIs('sekretariat.paroki.*') || request()->routeIs('sekretariat.kuasi.*') || request()->routeIs('sekretariat.stasi.*') || request()->routeIs('sekretariat.wilayah.*') || request()->routeIs('sekretariat.kub.*') ? 'active' : '' }}">
                            <a href="#" class="sidebar-link">
                                <i class="bi bi-stack"></i>
                                <span>Master Data</span>
                            </a>

                            <ul class="submenu">
                                <li
                                    class="submenu-item {{ request()->routeIs('sekretariat.keuskupan.*') ? 'active' : '' }}">
                                    <a href="{{ route('sekretariat.keuskupan.index') }}"
                                        class="submenu-link">Keuskupan</a>
                                </li>

                                <li
                                    class="submenu-item {{ request()->routeIs('sekretariat.paroki.*') ? 'active' : '' }}">
                                    <a href="{{ route('sekretariat.paroki.index') }}" class="submenu-link">Paroki</a>
                                </li>

                                <li
                                    class="submenu-item {{ request()->routeIs('sekretariat.kuasi.*') ? 'active' : '' }}">
                                    <a href="{{ route('sekretariat.kuasi.index') }}" class="submenu-link">Kuasi</a>
                                </li>

                                <li
                                    class="submenu-item {{ request()->routeIs('sekretariat.stasi.*') ? 'active' : '' }}">
                                    <a href="{{ route('sekretariat.stasi.index') }}" class="submenu-link">Stasi</a>
                                </li>

                                <li
                                    class="submenu-item {{ request()->routeIs('sekretariat.wilayah.*') ? 'active' : '' }}">
                                    <a href="{{ route('sekretariat.wilayah.index') }}" class="submenu-link">Wilayah</a>
                                </li>

                                <li class="submenu-item {{ request()->routeIs('sekretariat.kub.*') ? 'active' : '' }}">
                                    <a href="{{ route('sekretariat.kub.index') }}" class="submenu-link">KUB</a>
                                </li>

                            </ul>
                        </li>

                        <li class="sidebar-item {{ request()->routeIs('sekretariat.klerus.*') ? 'active' : '' }}">
                            <a href="{{ route('sekretariat.klerus.index') }}" class="sidebar-link">
                                <i class="bi bi-person-fill"></i>
                                <span>Klerus</span>
                            </a>
                        </li>

                        <li class="sidebar-item {{ request()->routeIs('sekretariat.keluarga.*') ? 'active' : '' }}">
                            <a href="{{ route('sekretariat.keluarga.index') }}" class="sidebar-link">
                                <i class="bi bi-person-vcard-fill"></i>
                                <span>Keluarga</span>
                            </a>
                        </li>

                        <li class="sidebar-item {{ request()->routeIs('sekretariat.umat.*') ? 'active' : '' }}">
                            <a href="{{ route('sekretariat.umat.index') }}" class="sidebar-link">
                                <i class="bi bi-person-lines-fill"></i>
                                <span>Umat</span>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->routeIs('sekretariat.kematian.*') ? 'active' : '' }}">
                            <a href="{{ route('sekretariat.kematian.index') }}" class="sidebar-link">
                                <i class="bi bi-patch-exclamation"></i>
                                <span>Kematian Umat</span>
                            </a>
                        </li>

                        <li class="sidebar-item {{ request()->routeIs('sekretariat.pengguna.*') ? 'active' : '' }}">
                            <a href="{{ route('sekretariat.pengguna.index') }}" class="sidebar-link">
                                <i class="bi bi-people-fill"></i>
                                <span>Kelola Pengguna</span>
                            </a>
                        </li>

                        <li
                            class="sidebar-item {{ request()->routeIs('sekretariat.kategorial.*') || request()->routeIs('sekretariat.anggota-kategorial.*') ? 'active' : '' }}">
                            <a href="{{ route('sekretariat.kategorial.index') }}" class="sidebar-link">
                                <i class="bi bi-diagram-3-fill"></i>
                                <span>Kategorial</span>
                            </a>
                        </li>
                        <li
                            class="sidebar-item {{ request()->routeIs('sekretariat.dpp.keanggotaan.*') || request()->routeIs('sekretariat.anggota-kategorial.*') ? 'active' : '' }}">
                            <a href="{{ route('sekretariat.dpp.keanggotaan.index') }}" class="sidebar-link">
                                <i class="bi bi-people-fill"></i>
                                <span>DPP</span>
                            </a>
                        </li>

                        <li
                            class="sidebar-item has-sub {{ request()->routeIs('sekretariat.mutasi.*') ? 'active' : '' }}">
                            <a href="#" class="sidebar-link">
                                <i class="bi bi-arrow-left-right"></i>
                                <span>Mutasi</span>
                            </a>

                            <ul class="submenu">
                                <li
                                    class="submenu-item {{ request()->routeIs('sekretariat.mutasi.index') || request()->routeIs('sekretariat.mutasi.destroy') ? 'active' : '' }}">
                                    <a href="{{ route('sekretariat.mutasi.index') }}" class="submenu-link">Semua
                                        Mutasi</a>
                                </li>

                                <li
                                    class="submenu-item {{ request()->routeIs('sekretariat.mutasi.agama.*') ? 'active' : '' }}">
                                    <a href="{{ route('sekretariat.mutasi.agama.index') }}"
                                        class="submenu-link">Mutasi Agama</a>
                                </li>

                                <li
                                    class="submenu-item {{ request()->routeIs('sekretariat.mutasi.keluarga.*') ? 'active' : '' }}">
                                    <a href="{{ route('sekretariat.mutasi.keluarga.index') }}"
                                        class="submenu-link">Mutasi Keluarga</a>
                                </li>

                                <li
                                    class="submenu-item {{ request()->routeIs('sekretariat.mutasi.umat.*') ? 'active' : '' }}">
                                    <a href="{{ route('sekretariat.mutasi.umat.index') }}"
                                        class="submenu-link">Mutasi Umat</a>
                                </li>
                            </ul>
                        </li>

                        <li
                            class="sidebar-item has-sub {{ request()->routeIs('sekretariat.sakramen.*') ? 'active' : '' }}">
                            <a href="#" class="sidebar-link">
                                <i class="bi bi-droplet-half"></i>
                                <span>Sakramen</span>
                            </a>

                            <ul class="submenu">
                                <li
                                    class="submenu-item {{ request()->routeIs('sekretariat.sakramen.index') || request()->routeIs('sekretariat.sakramen.destroy') ? 'active' : '' }}">
                                    <a href="{{ route('sekretariat.sakramen.index') }}" class="submenu-link">Semua
                                        Sakramen</a>
                                </li>

                                <li
                                    class="submenu-item {{ request()->routeIs('sekretariat.sakramen.baptis.*') ? 'active' : '' }}">
                                    <a href="{{ route('sekretariat.sakramen.baptis.index') }}"
                                        class="submenu-link">Baptis</a>
                                </li>

                                <li
                                    class="submenu-item {{ request()->routeIs('sekretariat.sakramen.komuni-pertama.*') ? 'active' : '' }}">
                                    <a href="{{ route('sekretariat.sakramen.komuni-pertama.index') }}"
                                        class="submenu-link">Komuni Pertama</a>
                                </li>

                                <li
                                    class="submenu-item {{ request()->routeIs('sekretariat.sakramen.krisma.*') ? 'active' : '' }}">
                                    <a href="{{ route('sekretariat.sakramen.krisma.index') }}"
                                        class="submenu-link">Krisma</a>
                                </li>

                                <li
                                    class="submenu-item {{ request()->routeIs('sekretariat.sakramen.pernikahan.*') ? 'active' : '' }}">
                                    <a href="{{ route('sekretariat.sakramen.pernikahan.index') }}"
                                        class="submenu-link">Pernikahan</a>
                                </li>

                                <li
                                    class="submenu-item {{ request()->routeIs('sekretariat.sakramen.minyak-suci.*') ? 'active' : '' }}">
                                    <a href="{{ route('sekretariat.sakramen.minyak-suci.index') }}"
                                        class="submenu-link">Minyak Suci</a>
                                </li>
                            </ul>
                        </li>
                        <li class="sidebar-item {{ request()->routeIs('sekretariat.import.*') ? 'active' : '' }}">
                            <a href="{{ route('sekretariat.import.index') }}" class="sidebar-link">
                                <i class="bi bi-file-earmark-arrow-up-fill"></i>
                                <span>Import Data</span>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->routeIs('sekretariat.laporan.*') ? 'active' : '' }}">
                            <a href="{{ route('sekretariat.laporan.index') }}" class="sidebar-link">
                                <i class="bi bi-file-earmark-pdf-fill"></i>
                                <span>Laporan PDF</span>
                            </a>
                        </li>
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
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=435ebe&color=fff&size=64&bold=true&font-size=0.4"
                                                    alt="{{ auth()->user()->name }}" />
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton"
                                    style="min-width: 11rem">
                                    <li>
                                        <h6 class="dropdown-header">
                                            Hello, {{ auth()->user()->name }}!
                                        </h6>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('profile.edit') }}"><i
                                                class="icon-mid bi bi-person me-2"></i>
                                            My Profile</a>
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
                        <p>2023 &copy; SI Paroki</p>
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
    <script src="{{ asset('template/assets/extensions/choices.js/public/assets/scripts/choices.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('select[name="umat_id"], select[name="ketua_umat_id"]').forEach(function (select) {
                if (select.dataset.choicesInitialized === 'true') {
                    return;
                }

                select.dataset.choicesInitialized = 'true';

                new Choices(select, {
                    searchEnabled: true,
                    shouldSort: false,
                    itemSelectText: '',
                    searchPlaceholderValue: 'Cari nama umat...',
                    noResultsText: 'Umat tidak ditemukan',
                    noChoicesText: 'Tidak ada pilihan',
                    allowHTML: false
                });
            });
        });
    </script>



    <!-- Need: Apexcharts -->
    <script src="{{ asset('template/assets/extensions/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('template/assets/static/js/pages/dashboard.js') }}"></script>

    @stack('scripts')

</body>

</html>
