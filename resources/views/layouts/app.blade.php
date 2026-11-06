<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'SIG Sekolah') - SIG Sekolah</title>

    <!-- Leaflet CSS -->
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    >

    <!-- Tailwind CSS dan JavaScript Laravel -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: #f1f5f9;
        }

        /* Warna utama navbar */
        .navbar-gradient {
            background: #0B6687;
        }

        /* Menu navigasi */
        .nav-link {
            padding: 9px 13px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            color: #E0F2FE;
            transition: all .2s ease;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, .15);
            color: white;
        }

        .nav-link.active {
            background: rgba(255, 255, 255, .20);
            color: white;
            font-weight: 700;
        }

        /* Animasi halaman */
        .main-container {
            animation: fadeIn .4s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Notifikasi */
        .notification {
            border-radius: 12px;
            padding: 15px 18px;
            margin-bottom: 18px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, .05);
        }

        @media (max-width: 640px) {
            .nav-link {
                font-size: 12px;
                padding: 8px 10px;
            }
        }
    </style>
</head>

<body class="min-h-screen flex flex-col text-slate-800">

    <!-- NAVBAR -->
    <nav class="navbar-gradient text-white shadow-lg relative z-50">

        <div class="max-w-7xl mx-auto px-4 sm:px-6">

            <!-- HEADER -->
            <div class="flex flex-wrap items-center justify-between gap-4 py-4">

                <!-- LOGO DAN NAMA APLIKASI -->
                <a href="{{ url('/') }}" class="flex items-center gap-3">

                    <div class="w-11 h-11 rounded-xl bg-white/15 border border-white/20 flex items-center justify-center font-bold text-lg">
                        SIG
                    </div>

                    <div>
                        <h1 class="font-bold text-xl tracking-wide">
                            Sistem Informasi Geografis Sekolah
                        </h1>

                        <p class="text-blue-100 text-xs mt-1">
                            Peta Persebaran Sekolah Kota Banjarmasin
                        </p>
                    </div>

                </a>

                <!-- INFORMASI PENGGUNA -->
                @auth

                    <div class="flex items-center gap-3">

                        <div class="hidden sm:block text-right">

                            <p class="font-semibold text-sm">
                                {{ auth()->user()->name }}
                            </p>

                            <span class="text-xs text-blue-100">
                                {{ auth()->user()->roleLabel() }}
                            </span>

                        </div>

                        <div class="w-10 h-10 rounded-full bg-white text-[#0B6687] flex items-center justify-center font-bold text-lg shadow">

                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}

                        </div>

                    </div>

                @else

                    <a href="{{ route('login') }}"
                       class="bg-white text-[#0B6687] hover:bg-blue-50 px-5 py-2.5 rounded-xl font-semibold text-sm shadow transition">

                        Login

                    </a>

                @endauth

            </div>

            <!-- MENU NAVIGASI -->
            <div class="flex flex-wrap items-center gap-2 pb-4 border-t border-white/20 pt-3">

                <!-- PETA PUBLIK -->
                <a href="{{ url('/') }}"
                   class="nav-link {{ request()->is('/') ? 'active' : '' }}">

                    Peta Publik

                </a>

                @auth

                    <!-- DASHBOARD -->
                    <a href="{{ route('dashboard') }}"
                       class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">

                        Dashboard

                    </a>

                    <!-- UBAH KATA SANDI -->
                    <a href="{{ route('password.edit') }}"
                       class="nav-link {{ request()->routeIs('password.*') ? 'active' : '' }}">

                        Ubah Kata Sandi

                    </a>

                    <!-- MENU ADMIN -->
                    @if (auth()->user()->isAdmin())

                        <a href="{{ route('admin.users.index') }}"
                           class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">

                            Pengguna

                        </a>

                    @endif

                    <!-- MENU DINAS -->
                    @if (auth()->user()->isDinas())

                        <a href="{{ route('dinas.sekolah.index') }}"
                           class="nav-link {{ request()->routeIs('dinas.sekolah.*') ? 'active' : '' }}">

                            Data Sekolah

                        </a>

                    @endif

                    <!-- MENU OPERATOR SEKOLAH -->
                    @if (auth()->user()->isSekolah() && auth()->user()->sekolah_id)

                        <a href="{{ route('sekolah.edit', auth()->user()->sekolah_id) }}"
                           class="nav-link {{ request()->routeIs('sekolah.*') ? 'active' : '' }}">

                            Sekolah Saya

                        </a>

                    @endif

                    <!-- TOMBOL LOGOUT -->
                    <form method="POST"
                          action="{{ route('logout') }}"
                          class="sm:ml-auto">

                        @csrf

                        <button
                            type="submit"
                            class="bg-red-500 hover:bg-red-600 text-white px-5 py-2 rounded-xl text-sm font-semibold shadow transition">

                            Keluar

                        </button>

                    </form>

                @endauth

            </div>

        </div>

    </nav>


    <!-- KONTEN UTAMA -->
    <main class="main-container flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 py-7">

        <!-- NOTIFIKASI BERHASIL -->
        @if (session('success'))

            <div class="notification bg-green-50 border border-green-200 text-green-800">

                <div class="flex items-center gap-3">

                    <span class="text-xl">✓</span>

                    <div>

                        <p class="font-bold text-sm">
                            Berhasil
                        </p>

                        <p class="text-sm">
                            {{ session('success') }}
                        </p>

                    </div>

                </div>

            </div>

        @endif


        <!-- NOTIFIKASI KESALAHAN -->
        @if (session('error'))

            <div class="notification bg-red-50 border border-red-200 text-red-800">

                <div class="flex items-center gap-3">

                    <span class="text-xl">!</span>

                    <div>

                        <p class="font-bold text-sm">
                            Terjadi Kesalahan
                        </p>

                        <p class="text-sm">
                            {{ session('error') }}
                        </p>

                    </div>

                </div>

            </div>

        @endif


        <!-- KESALAHAN VALIDASI -->
        @if ($errors->any())

            <div class="notification bg-red-50 border border-red-200 text-red-800">

                <p class="font-bold mb-2">
                    Periksa kembali data yang dimasukkan:
                </p>

                <ul class="list-disc ml-5 text-sm space-y-1">

                    @foreach ($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        @endif


        <!-- ISI HALAMAN -->
        @yield('content')

    </main>


    <!-- FOOTER -->
    <footer class="bg-[#075B7D] text-white mt-10">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6">

            <div class="flex flex-col sm:flex-row justify-between items-center gap-3">

                <div>

                    <p class="font-bold text-white">
                        SIG Sekolah Banjarmasin
                    </p>

                    <p class="text-xs text-blue-100 mt-1">
                        Sistem Informasi Geografis Data Sekolah
                    </p>

                </div>

                <p class="text-xs text-blue-100 text-center">
                    © {{ date('Y') }} SIG Sekolah. All rights reserved.
                </p>

            </div>

        </div>

    </footer>


    <!-- LEAFLET JAVASCRIPT -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- SCRIPT HALAMAN -->
    @stack('scripts')

</body>
</html>