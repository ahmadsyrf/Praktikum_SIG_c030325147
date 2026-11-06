@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<style>
    .dashboard-header {
        background: #0B6687;
        border-radius: 18px;
        padding: 30px;
        color: white;
        box-shadow: 0 8px 20px rgba(11, 102, 135, .15);
    }

    .dashboard-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 5px 18px rgba(15, 23, 42, .04);
        transition: .25s ease;
    }

    .dashboard-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(15, 23, 42, .08);
    }

    .dashboard-icon {
        width: 58px;
        height: 58px;
        border-radius: 15px;
        background: #E0F2FE;
        color: #0B6687;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 27px;
    }

    .dashboard-button {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        background: #0B6687;
        color: white;
        padding: 12px 20px;
        border-radius: 11px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        transition: .2s ease;
    }

    .dashboard-button:hover {
        background: #075B7D;
        transform: translateY(-2px);
    }

    .dashboard-progress {
        height: 12px;
        background: #e2e8f0;
        border-radius: 20px;
        overflow: hidden;
    }

    .dashboard-progress-bar {
        height: 100%;
        background: #0B6687;
        border-radius: 20px;
        transition: width .4s ease;
    }

    .dashboard-label {
        color: #64748b;
        font-size: 13px;
    }

    .dashboard-number {
        color: #0B6687;
        font-size: 34px;
        font-weight: 800;
        margin-top: 8px;
    }
</style>


<!-- HEADER DASHBOARD -->
<div class="dashboard-header mb-7">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5">

        <div>
            <p class="text-blue-100 text-sm font-semibold mb-2">
                SISTEM INFORMASI GEOGRAFIS
            </p>

            <h1 class="text-2xl md:text-3xl font-bold">
                Dashboard SIG Sekolah
            </h1>

            <p class="text-blue-100 mt-3">
                Selamat datang, {{ $user->name }}!
            </p>

            <p class="text-blue-100 text-sm mt-1">
                Kelola dan pantau informasi sekolah melalui satu sistem.
            </p>
        </div>

        <div class="hidden md:flex w-24 h-24 rounded-2xl bg-white/15 border border-white/20 items-center justify-center text-5xl">
            🏫
        </div>

    </div>

</div>


@if ($user->isAdmin())

    <!-- DASHBOARD ADMIN -->
    <div class="mb-6">

        <h2 class="text-xl font-bold text-slate-800">
            Administrator
        </h2>

        <p class="text-sm text-slate-500 mt-1">
            Ringkasan pengelolaan seluruh sistem SIG Sekolah.
        </p>

    </div>


    <!-- STATISTIK PENGGUNA -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-7">

        @foreach ($jumlahPerRole as $role => $jumlah)

            <div class="dashboard-card">

                <div class="flex items-center justify-between gap-4">

                    <div>
                        <p class="dashboard-label">
                            {{ $role }}
                        </p>

                        <h3 class="dashboard-number">
                            {{ $jumlah }}
                        </h3>

                        <p class="text-xs text-slate-400 mt-2">
                            Jumlah akun pengguna
                        </p>
                    </div>

                    <div class="dashboard-icon">
                        👥
                    </div>

                </div>

            </div>

        @endforeach

    </div>


    <!-- KELOLA SEKOLAH -->
    <div class="dashboard-card">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-5">

            <div>
                <h3 class="text-lg font-bold text-slate-800">
                    Pengelolaan Data Sekolah
                </h3>

                <p class="text-sm text-slate-500 mt-2">
                    Kelola informasi sekolah yang terdaftar dalam sistem.
                </p>
            </div>

            <a href="{{ route('dinas.sekolah.index') }}"
               class="dashboard-button">

                🏫 Kelola Data Sekolah

            </a>

        </div>

    </div>


@elseif ($user->isDinas())

    <!-- DASHBOARD DINAS -->
    <div class="mb-6">

        <h2 class="text-xl font-bold text-slate-800">
            Dinas Pendidikan
        </h2>

        <p class="text-sm text-slate-500 mt-1">
            Ringkasan data pendidikan dan sekolah.
        </p>

    </div>


    <!-- STATISTIK DINAS -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-7">

        <div class="dashboard-card">

            <div class="flex items-center justify-between gap-4">

                <div>
                    <p class="dashboard-label">
                        Total Sekolah
                    </p>

                    <h3 class="dashboard-number">
                        {{ $jumlahSekolah }}
                    </h3>

                    <p class="text-xs text-slate-400 mt-2">
                        Sekolah terdaftar dalam sistem
                    </p>
                </div>

                <div class="dashboard-icon">
                    🏫
                </div>

            </div>

        </div>


        <div class="dashboard-card">

            <div class="flex items-center justify-between gap-4">

                <div>
                    <p class="dashboard-label">
                        Sistem Informasi
                    </p>

                    <h3 class="text-2xl font-bold text-[#0B6687] mt-2">
                        Aktif
                    </h3>

                    <p class="text-xs text-slate-400 mt-2">
                        Portal SIG Sekolah
                    </p>
                </div>

                <div class="dashboard-icon">
                    🌐
                </div>

            </div>

        </div>

    </div>


    <!-- PENGELOLAAN DATA -->
    <div class="dashboard-card">

        <h3 class="text-lg font-bold text-slate-800">
            Pengelolaan Data Sekolah
        </h3>

        <p class="text-sm text-slate-500 mt-2 mb-5">
            Lihat, perbarui, dan pantau data sekolah yang tersedia.
        </p>

        <a href="{{ route('dinas.sekolah.index') }}"
           class="dashboard-button">

            📋 Lihat Data Sekolah

        </a>

    </div>


@elseif ($user->isSekolah())

    <!-- DASHBOARD OPERATOR SEKOLAH -->
    <div class="mb-6">

        <h2 class="text-xl font-bold text-slate-800">
            Operator Sekolah
        </h2>

        <p class="text-sm text-slate-500 mt-1">
            Informasi sekolah yang terhubung dengan akun Anda.
        </p>

    </div>


    @if ($sekolah)

        <div class="dashboard-card mb-6">

            <!-- INFORMASI SEKOLAH -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-5">

                <div>

                    <p class="dashboard-label">
                        Sekolah Terhubung
                    </p>

                    <h3 class="text-2xl font-bold text-[#0B6687] mt-2">
                        {{ $sekolah->nama }}
                    </h3>

                    <p class="text-sm text-slate-500 mt-2">
                        NPSN: {{ $sekolah->npsn }}
                    </p>

                </div>

                <div class="dashboard-icon">
                    🏫
                </div>

            </div>


            <!-- KELENGKAPAN PROFIL -->
            <div class="border-t border-slate-100 mt-6 pt-6">

                <div class="flex items-center justify-between gap-3 mb-3">

                    <div>
                        <p class="font-semibold text-slate-800">
                            Kelengkapan Profil
                        </p>

                        <p class="text-xs text-slate-500 mt-1">
                            Persentase kelengkapan informasi sekolah
                        </p>
                    </div>

                    <span class="text-xl font-bold text-[#0B6687]">
                        {{ $kelengkapan }}%
                    </span>

                </div>


                <div class="dashboard-progress">

                    <div
                        class="dashboard-progress-bar"
                        style="width: {{ $kelengkapan }}%">
                    </div>

                </div>

            </div>


            <!-- TOMBOL EDIT -->
            <div class="mt-6">

                <a href="{{ route('sekolah.edit', $sekolah->id) }}"
                   class="dashboard-button">

                    ✏️ Edit Data Sekolah

                </a>

            </div>

        </div>

    @else

        <div class="dashboard-card border-l-4 border-red-500">

            <h3 class="font-bold text-red-700">
                Data Sekolah Belum Terhubung
            </h3>

            <p class="text-sm text-slate-500 mt-2">
                Akun Anda belum terhubung dengan data sekolah.
                Silakan hubungi administrator.
            </p>

        </div>

    @endif

@endif

@endsection