@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

@if ($user->isAdmin())
<style>
    .admin-shell { display: flex; min-height: 100vh; background: #f8fafc; color: #0f172a; }
    .admin-sidebar { position: sticky; top: 0; display: flex; flex: 0 0 232px; flex-direction: column; height: 100vh; padding: 23px 15px 16px; border-right: 1px solid #e8edf5; background: #fff; }
    .admin-brand { display: flex; align-items: center; gap: 10px; padding: 0 8px 26px; color: #0f172a; text-decoration: none; }
    .admin-brand-mark { display: grid; width: 36px; height: 36px; place-items: center; border-radius: 11px; background: #eff6ff; color: #2563eb; }
    .admin-brand strong { display: block; font-size: 13px; letter-spacing: -.03em; }
    .admin-brand small { display: block; margin-top: 3px; color: #94a3b8; font-size: 9px; font-weight: 700; letter-spacing: .1em; }
    .sidebar-label { padding: 0 10px; margin: 0 0 9px; color: #94a3b8; font-size: 9px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; }
    .sidebar-nav { display: grid; gap: 4px; }
    .sidebar-link { display: flex; align-items: center; gap: 11px; min-height: 39px; padding: 0 11px; border-radius: 9px; color: #64748b; font-size: 11px; font-weight: 650; text-decoration: none; transition: .18s ease; }
    .sidebar-link svg { width: 16px; height: 16px; color: #94a3b8; }
    .sidebar-link:hover { background: #f8fafc; color: #0f172a; }
    .sidebar-link.active { background: #eff6ff; color: #2563eb; }
    .sidebar-link.active svg { color: #2563eb; }
    .sidebar-bottom { margin-top: auto; padding-top: 16px; border-top: 1px solid #eef2f7; }
    .admin-user { display: flex; align-items: center; gap: 9px; padding: 12px 7px; }
    .admin-avatar { display: grid; flex: 0 0 34px; width: 34px; height: 34px; place-items: center; border-radius: 11px; background: #dbeafe; color: #1d4ed8; font-size: 12px; font-weight: 800; }
    .admin-user strong { display: block; overflow: hidden; max-width: 135px; color: #334155; font-size: 10px; text-overflow: ellipsis; white-space: nowrap; }
    .admin-user small { display: block; margin-top: 3px; color: #94a3b8; font-size: 9px; }
    .admin-logout { display: flex; width: 100%; align-items: center; gap: 10px; padding: 10px; border: 0; border-radius: 9px; background: transparent; color: #64748b; cursor: pointer; font: inherit; font-size: 11px; text-align: left; }
    .admin-logout:hover { background: #fff1f2; color: #e11d48; }
    .admin-main { flex: 1; min-width: 0; padding: 29px clamp(18px, 3.6vw, 52px) 40px; }
    .admin-topbar { display: flex; justify-content: space-between; align-items: center; gap: 20px; margin-bottom: 30px; }
    .admin-breadcrumb { color: #94a3b8; font-size: 10px; }
    .admin-breadcrumb strong { color: #475569; font-weight: 700; }
    .admin-heading h1 { margin: 0; color: #0f172a; font-size: 24px; letter-spacing: -.05em; }
    .admin-heading p { margin: 7px 0 0; color: #64748b; font-size: 11px; }
    .admin-top-actions { display: flex; align-items: center; gap: 10px; }
    .admin-date { color: #94a3b8; font-size: 10px; }
    .admin-button { display: inline-flex; min-height: 39px; align-items: center; justify-content: center; gap: 8px; padding: 0 13px; border: 1px solid #2563eb; border-radius: 9px; background: #2563eb; color: #fff; cursor: pointer; font: inherit; font-size: 10px; font-weight: 750; text-decoration: none; box-shadow: 0 4px 10px rgba(37, 99, 235, .15); transition: .2s ease; }
    .admin-button:hover { transform: translateY(-1px); background: #1d4ed8; }
    .admin-button svg { width: 15px; height: 15px; }
    .admin-kpis { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 13px; margin-bottom: 26px; }
    .admin-kpi { position: relative; min-width: 0; padding: 17px; overflow: hidden; border: 1px solid #e9edf4; border-radius: 14px; background: #fff; box-shadow: 0 3px 11px rgba(15, 23, 42, .025); }
    .kpi-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; }
    .kpi-label { color: #64748b; font-size: 10px; font-weight: 650; }
    .kpi-icon { display: grid; flex: 0 0 31px; width: 31px; height: 31px; place-items: center; border-radius: 9px; background: #eff6ff; color: #2563eb; }
    .kpi-icon svg { width: 16px; height: 16px; }
    .kpi-number { margin: 14px 0 9px; color: #0f172a; font-size: 25px; font-weight: 800; letter-spacing: -.06em; line-height: 1; }
    .kpi-caption { color: #94a3b8; font-size: 9px; }
    .kpi-caption strong { color: #16a34a; font-weight: 750; }
    .school-card { overflow: hidden; border: 1px solid #e9edf4; border-radius: 14px; background: #fff; box-shadow: 0 3px 11px rgba(15, 23, 42, .025); }
    .school-card-header { display: flex; justify-content: space-between; align-items: center; gap: 16px; padding: 18px 19px; border-bottom: 1px solid #eef2f7; }
    .school-card-header h2 { margin: 0; color: #0f172a; font-size: 13px; letter-spacing: -.02em; }
    .school-card-header p { margin: 5px 0 0; color: #94a3b8; font-size: 10px; }
    .table-tools { display: flex; align-items: center; gap: 8px; }
    .table-search { width: 205px; height: 36px; padding: 0 10px; border: 1px solid #e2e8f0; border-radius: 8px; outline: 0; color: #334155; font: inherit; font-size: 10px; }
    .table-search:focus, .table-select:focus { border-color: #60a5fa; box-shadow: 0 0 0 3px rgba(37, 99, 235, .1); }
    .table-select { height: 36px; padding: 0 9px; border: 1px solid #e2e8f0; border-radius: 8px; outline: 0; background: white; color: #475569; font: inherit; font-size: 10px; }
    .table-submit { height: 36px; padding: 0 11px; border: 0; border-radius: 8px; background: #f1f5f9; color: #475569; cursor: pointer; font: inherit; font-size: 10px; font-weight: 700; }
    .school-table-wrap { overflow-x: auto; }
    .school-table { width: 100%; min-width: 760px; border-collapse: collapse; text-align: left; }
    .school-table th { padding: 11px 15px; background: #fafbfd; color: #94a3b8; font-size: 9px; font-weight: 750; letter-spacing: .06em; text-transform: uppercase; }
    .school-table td { padding: 12px 15px; border-top: 1px solid #f1f4f8; color: #64748b; font-size: 10px; vertical-align: middle; }
    .school-table tbody tr { transition: background .15s ease; }
    .school-table tbody tr:hover { background: #fbfdff; }
    .school-name { display: block; max-width: 250px; overflow: hidden; color: #1e293b; font-size: 10px; font-weight: 750; text-overflow: ellipsis; white-space: nowrap; }
    .school-npsn { display: block; margin-top: 4px; color: #94a3b8; font-size: 9px; }
    .school-level { display: inline-flex; padding: 5px 7px; border-radius: 6px; background: #f1f5f9; color: #475569; font-size: 9px; font-weight: 750; }
    .school-status { display: inline-flex; align-items: center; gap: 5px; padding: 5px 8px; border-radius: 99px; font-size: 9px; font-weight: 750; }
    .school-status:before { width: 5px; height: 5px; border-radius: 50%; background: currentColor; content: ""; }
    .school-status.negeri { background: #eff6ff; color: #2563eb; }
    .school-status.swasta { background: #ecfdf5; color: #059669; }
    .row-action { border: 0; background: transparent; color: #2563eb; cursor: pointer; font: inherit; font-size: 9px; font-weight: 700; text-decoration: none; }
    .row-action:hover { text-decoration: underline; }
    .empty-state { padding: 34px !important; color: #94a3b8 !important; text-align: center; }
    .pagination-wrap { padding: 13px 17px; border-top: 1px solid #eef2f7; }
    @media (max-width: 1000px) { .admin-kpis { grid-template-columns: repeat(2, minmax(0, 1fr)); } .school-card-header { align-items: flex-start; flex-direction: column; } .table-tools { width: 100%; } .table-search { flex: 1; } }
    @media (max-width: 700px) { .admin-sidebar { position: fixed; z-index: 100; right: 0; bottom: 0; left: 0; top: auto; width: 100%; height: auto; min-height: 0; padding: 8px 9px; border-top: 1px solid #e8edf5; border-right: 0; } .admin-brand, .sidebar-label, .sidebar-bottom { display: none; } .sidebar-nav { display: flex; justify-content: space-around; gap: 3px; } .sidebar-link { flex: 1; min-height: 43px; justify-content: center; gap: 5px; padding: 4px; font-size: 8px; flex-direction: column; } .admin-main { padding: 22px 14px 82px; } .admin-topbar { align-items: flex-start; margin-bottom: 21px; } .admin-date { display: none; } .admin-heading h1 { font-size: 21px; } .admin-top-actions { flex-direction: column; align-items: flex-end; } .admin-top-actions .admin-button { min-height: 34px; padding: 0 9px; font-size: 9px; } .admin-kpis { gap: 9px; } .admin-kpi { padding: 12px; } .kpi-number { font-size: 22px; } .kpi-label { font-size: 9px; } .table-tools { flex-wrap: wrap; } .table-search { flex-basis: 100%; } .table-select { flex: 1; } }
    @media (max-width: 420px) { .admin-kpis { grid-template-columns: 1fr 1fr; } .school-card-header { padding: 15px; } }
</style>

<div class="admin-shell">
    <aside class="admin-sidebar">
        <a href="{{ route('peta.index') }}" class="admin-brand">
            <span class="admin-brand-mark">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="m3 6 6-3 6 3 6-3v15l-6 3-6-3-6 3V6Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                    <path d="M9 3v15m6-12v15" stroke="currentColor" stroke-width="1.7"/>
                </svg>
            </span>
            <span><strong>Peta Sekolah</strong><small>ADMIN CONSOLE</small></span>
        </a>

        <p class="sidebar-label">Workspace</p>
        <nav class="sidebar-nav" aria-label="Navigasi dashboard">
            <a href="{{ route('dashboard') }}" class="sidebar-link active">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3" y="3" width="8" height="8" rx="2" stroke="currentColor" stroke-width="1.7"/><rect x="14" y="3" width="7" height="5" rx="2" stroke="currentColor" stroke-width="1.7"/><rect x="14" y="11" width="7" height="10" rx="2" stroke="currentColor" stroke-width="1.7"/><rect x="3" y="14" width="8" height="7" rx="2" stroke="currentColor" stroke-width="1.7"/></svg>
                Ringkasan
            </a>
            <a href="#data-sekolah" class="sidebar-link">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 20V8l8-5 8 5v12H4Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M9 20v-6h6v6M8 9h.01M12 9h.01M16 9h.01" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                Data Sekolah
            </a>
            <a href="{{ route('peta.wilayah') }}" class="sidebar-link">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m3 6 6-3 6 3 6-3v15l-6 3-6-3-6 3V6Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M9 3v15m6-12v15" stroke="currentColor" stroke-width="1.7"/></svg>
                Manajemen Wilayah
            </a>
            <a href="{{ route('peta.index') }}" class="sidebar-link">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 3h7l5 5v13H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M14 3v5h5M9 13h6m-6 4h6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                Laporan &amp; Ekspor
            </a>
            <a href="{{ route('admin.users.index') }}" class="sidebar-link">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="1.7"/><path d="M3 20v-1a6 6 0 0 1 12 0v1H3Zm13-9a3 3 0 1 0 0-6m2 9a5 5 0 0 1 3 4v1h-3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Pengaturan
            </a>
        </nav>

        <div class="sidebar-bottom">
            <div class="admin-user">
                <span class="admin-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                <span><strong>{{ $user->name }}</strong><small>Administrator</small></span>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="admin-logout" type="submit">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M10 17l5-5-5-5m5 5H3m9-9h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Keluar dari akun
                </button>
            </form>
        </div>
    </aside>

    <main class="admin-main">
        <div class="admin-topbar">
            <div class="admin-heading">
                <div class="admin-breadcrumb">Workspace / <strong>Ringkasan</strong></div>
                <h1>Dashboard Admin</h1>
                <p>Ringkasan data sekolah dan pengguna SIG Banjarmasin.</p>
            </div>
            <div class="admin-top-actions">
                <span class="admin-date">{{ now()->translatedFormat('l, d F Y') }}</span>
                <a href="{{ route('admin.users.index') }}" class="admin-button">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="1.8"/><path d="M3 20v-1a6 6 0 0 1 12 0v1H3Zm13-9a3 3 0 1 0 0-6m2 9a5 5 0 0 1 3 4v1h-3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Kelola Pengguna
                </a>
            </div>
        </div>

        <section class="admin-kpis" aria-label="Statistik sekolah">
            <article class="admin-kpi">
                <div class="kpi-top"><span class="kpi-label">Total Sekolah</span><span class="kpi-icon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 20V8l8-5 8 5v12H4Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M9 20v-6h6v6M8 9h.01M12 9h.01M16 9h.01" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span></div>
                <div class="kpi-number">{{ number_format($totalSekolah, 0, ',', '.') }}</div>
                <div class="kpi-caption">Terdaftar di basis data</div>
            </article>
            <article class="admin-kpi">
                <div class="kpi-top"><span class="kpi-label">Sekolah Negeri</span><span class="kpi-icon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 20V8l8-5 8 5v12H4Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M9 20v-6h6v6" stroke="currentColor" stroke-width="1.7"/></svg></span></div>
                <div class="kpi-number">{{ number_format($sekolahNegeri, 0, ',', '.') }}</div>
                <div class="kpi-caption"><strong>{{ $totalSekolah ? round($sekolahNegeri / $totalSekolah * 100) : 0 }}%</strong> dari total sekolah</div>
            </article>
            <article class="admin-kpi">
                <div class="kpi-top"><span class="kpi-label">Sekolah Swasta</span><span class="kpi-icon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 20V8l8-5 8 5v12H4Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M8 10h8m-8 4h8m-8 4h8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span></div>
                <div class="kpi-number">{{ number_format($sekolahSwasta, 0, ',', '.') }}</div>
                <div class="kpi-caption"><strong>{{ $totalSekolah ? round($sekolahSwasta / $totalSekolah * 100) : 0 }}%</strong> dari total sekolah</div>
            </article>
            <article class="admin-kpi">
                <div class="kpi-top"><span class="kpi-label">Kecamatan Terpadat</span><span class="kpi-icon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 21s7-6.2 7-12a7 7 0 1 0-14 0c0 5.8 7 12 7 12Z" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="9" r="2.2" stroke="currentColor" stroke-width="1.7"/></svg></span></div>
                <div class="kpi-number" style="font-size:20px">{{ $kecamatanTerpadat->kecamatan ?? 'Belum ada data' }}</div>
                <div class="kpi-caption">{{ $kecamatanTerpadat ? number_format($kecamatanTerpadat->total, 0, ',', '.') . ' sekolah terdata' : 'Belum ada data wilayah' }}</div>
            </article>
        </section>

        <section class="school-card" id="data-sekolah" aria-labelledby="schoolTableTitle">
            <div class="school-card-header">
                <div>
                    <h2 id="schoolTableTitle">Data Sekolah</h2>
                    <p>Jelajahi informasi sekolah yang terdaftar pada peta publik.</p>
                </div>
                <form class="table-tools" method="GET" action="{{ route('dashboard') }}">
                    <input class="table-search" type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama, NPSN, kecamatan..." aria-label="Cari data sekolah">
                    <select class="table-select" name="status" aria-label="Filter status sekolah">
                        <option value="">Semua status</option>
                        <option value="Negeri" @selected(request('status') === 'Negeri')>Negeri</option>
                        <option value="Swasta" @selected(request('status') === 'Swasta')>Swasta</option>
                    </select>
                    <button class="table-submit" type="submit">Filter</button>
                </form>
            </div>
            <div class="school-table-wrap">
                <table class="school-table">
                    <thead>
                        <tr><th>Sekolah</th><th>Jenjang</th><th>Status</th><th>Kecamatan</th><th>Koordinat</th><th>Aksi</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($sekolahs as $s)
                            <tr>
                                <td><span class="school-name">{{ $s->nama }}</span><span class="school-npsn">NPSN {{ $s->npsn }}</span></td>
                                <td><span class="school-level">{{ $s->jenjang }}</span></td>
                                <td><span class="school-status {{ strtolower($s->status) }}">{{ $s->status }}</span></td>
                                <td>{{ $s->kecamatan }}</td>
                                <td>{{ number_format($s->latitude, 5) }}, {{ number_format($s->longitude, 5) }}</td>
                                <td>
                                    <a class="row-action" href="{{ route('peta.index') }}">Lihat di Peta</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td class="empty-state" colspan="6">Belum ada data sekolah yang sesuai dengan pencarian.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="pagination-wrap">{{ $sekolahs->links() }}</div>
        </section>
    </main>
</div>

@else
<style>
    .dashboard-header {
        background: radial-gradient(circle at 88% 18%, rgba(37, 99, 235, .38), transparent 31%), linear-gradient(130deg, #0f172a, #172554);
        border: 1px solid rgba(148, 163, 184, .15);
        border-radius: 18px;
        padding: 30px;
        color: white;
        box-shadow: 0 12px 30px rgba(15, 23, 42, .12);
    }

    .dashboard-card {
        background: white;
        border: 1px solid #e7edf5;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 5px 18px rgba(15, 23, 42, .035);
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
        background: #eff6ff;
        color: #2563eb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 27px;
    }

    .dashboard-button {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        background: #2563eb;
        color: white;
        padding: 12px 20px;
        border-radius: 11px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        transition: .2s ease;
    }

    .dashboard-button:hover {
        background: #1d4ed8;
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
        background: #2563eb;
        border-radius: 20px;
        transition: width .4s ease;
    }

    .dashboard-label {
        color: #64748b;
        font-size: 13px;
    }

    .dashboard-number {
        color: #1d4ed8;
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

                    <h3 class="text-2xl font-bold text-[#2563EB] mt-2">
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

                    <h3 class="text-2xl font-bold text-[#2563EB] mt-2">
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

                    <span class="text-xl font-bold text-[#2563EB]">
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

@endif

@endsection