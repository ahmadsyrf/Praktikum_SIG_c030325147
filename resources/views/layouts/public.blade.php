
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Peta Sekolah Banjarmasin') - SIG Sekolah</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet"
          href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <style>
        * {
            box-sizing: border-box;
        }

        html, body {
            width: 100%;
            min-height: 100%;
            margin: 0;
            padding: 0;
            font-family: system-ui, sans-serif;
            overflow-x: hidden;
            overflow-y: auto;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            color: #0f172a;
            background: #f8fafc;
        }

        body.map-tool {
            min-height: 100dvh;
            overflow: hidden;
        }

        body.map-tool > main {
            position: fixed;
            inset: 0;
            width: 100%;
            height: 100vh;
            height: 100dvh;
            min-height: 0;
            overflow: hidden;
        }

        body.map-tool #peta,
        body.map-tool .peta {
            position: fixed !important;
            z-index: 1;
            inset: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            height: 100dvh !important;
            min-height: 0 !important;
            max-height: none !important;
            margin: 0 !important;
            border: 0 !important;
            border-radius: 0 !important;
        }

        body.map-tool .toolbar {
            position: fixed;
            z-index: 1100;
            top: 104px;
            left: 24px;
            display: flex;
            width: max-content;
            max-width: calc(100vw - 48px);
            align-items: center;
            gap: 9px;
            margin: 0;
            padding: 11px 13px;
            border: 1px solid rgba(226, 232, 240, .9);
            border-radius: 14px;
            background: rgba(255, 255, 255, .94);
            box-shadow: 0 12px 30px rgba(15, 23, 42, .13);
            backdrop-filter: blur(16px);
        }

        body.map-tool .toolbar label {
            display: flex;
            align-items: center;
            gap: 7px;
            color: #475569;
            font-size: 11px;
            font-weight: 700;
        }

        body.map-tool .toolbar input,
        body.map-tool .toolbar select {
            min-height: 36px;
            padding: 7px 10px;
            border: 1px solid #e2e8f0;
            border-radius: 9px;
            outline: 0;
            background: #fff;
            color: #334155;
            font: inherit;
            font-size: 11px;
        }

        body.map-tool .toolbar input:focus,
        body.map-tool .toolbar select:focus {
            border-color: #60a5fa;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .1);
        }

        body.map-tool .toolbar button {
            min-height: 36px;
            padding: 0 12px;
            border: 0;
            border-radius: 9px;
            background: #2563eb;
            color: #fff;
            cursor: pointer;
            font: inherit;
            font-size: 11px;
            font-weight: 700;
        }

        body.map-tool .toolbar button:hover {
            background: #1d4ed8;
        }

        body.map-tool .toolbar #jumlah {
            color: #2563eb;
            font-size: 11px;
            font-weight: 750;
            white-space: nowrap;
        }

        body.map-tool .panel {
            position: fixed !important;
            z-index: 1050;
            top: 104px !important;
            right: 24px !important;
            bottom: auto !important;
            left: auto !important;
            width: min(340px, calc(100vw - 48px)) !important;
            max-height: calc(100dvh - 128px) !important;
            overflow: auto;
            border: 1px solid rgba(226, 232, 240, .9);
            border-radius: 14px;
            background: rgba(255, 255, 255, .95);
            box-shadow: 0 12px 30px rgba(15, 23, 42, .14);
            backdrop-filter: blur(16px);
        }

        body.map-tool .panel[style*="bottom"] {
            top: auto !important;
            right: 24px !important;
            bottom: 24px !important;
            left: auto !important;
            width: auto !important;
            max-width: calc(100vw - 48px);
        }

        body.map-tool #hasil.panel {
            top: 104px !important;
            right: 24px !important;
            width: min(340px, calc(100vw - 48px)) !important;
        }

        body.map-tool .leaflet-control-zoom {
            position: fixed !important;
            z-index: 1100;
            top: auto !important;
            right: auto !important;
            bottom: 24px !important;
            left: 16px !important;
            margin: 0 !important;
            overflow: hidden;
            border: 1px solid #e2e8f0 !important;
            border-radius: 11px !important;
            box-shadow: 0 5px 16px rgba(15, 23, 42, .14);
        }

        body.map-tool .leaflet-control-zoom a {
            color: #334155 !important;
        }

        body.map-tool .leaflet-control-attribution {
            margin: 0 7px 5px 0 !important;
            border-radius: 6px;
            background: rgba(255, 255, 255, .8);
            font-size: 9px !important;
        }

        .bar {
            position: fixed;
            top: 18px;
            left: 50%;
            transform: translateX(-50%);
            width: min(1440px, calc(100% - 48px));
            min-height: 68px;
            display: flex;
            align-items: center;
            gap: 24px;
            padding: 10px 16px 10px 20px;
            background: rgba(255, 255, 255, .88);
            border: 1px solid rgba(226, 232, 240, .9);
            border-radius: 18px;
            color: #0f172a;
            box-shadow: 0 12px 35px rgba(15, 23, 42, .12);
            backdrop-filter: blur(18px);
            z-index: 2000;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 11px;
            white-space: nowrap;
            color: #0f172a;
            text-decoration: none;
            font-size: 15px;
            letter-spacing: -.02em;
        }

        .menu {
            display: flex;
            gap: 3px;
            overflow-x: auto;
            flex: 1;
            scrollbar-width: none;
        }

        .menu::-webkit-scrollbar {
            display: none;
        }

        .menu a {
            color: #64748b;
            text-decoration: none;
            padding: 9px 11px;
            border-radius: 10px;
            white-space: nowrap;
            font-size: 13px;
            font-weight: 600;
            transition: background .2s ease, color .2s ease;
        }

        .menu a:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        .menu a.aktif {
            background: #eff6ff;
            color: #2563eb;
        }

        .masuk {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            padding: 0 15px;
            border-radius: 11px;
            background: #2563eb;
            color: #fff;
            white-space: nowrap;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 5px 12px rgba(37, 99, 235, .2);
            transition: background .2s ease, transform .2s ease;
        }

        .masuk:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }

        main {
            flex: 1 0 auto;
            width: 100%;
            min-height: calc(100vh - 56px);
            position: relative;
            display: block;
            overflow: visible;
            padding: 0;
            margin: 0;
        }

        main > * {
            max-width: none;
        }

        .toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            padding: 10px 16px;
            font-size: 14px;
            background: #f3f4f6;
            border-bottom: 1px solid #d1d5db;
        }

        .toolbar input,
        .toolbar select,
        .toolbar button {
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 14px;
        }

        .toolbar button {
            background: #1e3a8a;
            color: #fff;
            border-color: #1e3a8a;
            cursor: pointer;
        }

        .peta {
            width: 100%;
            min-height: 300px;
        }

        .panel {
            position: absolute;
            z-index: 1000;
            background: #fff;
            border-radius: 8px;
            padding: 12px 14px;
            font-size: 14px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .2);
        }

        @media (max-width: 768px) {
            .bar {
                top: 10px;
                width: calc(100% - 20px);
                min-height: 58px;
                gap: 8px;
                padding: 7px 9px;
                border-radius: 15px;
            }

            .brand {
                font-size: 12px;
            }

            .menu a {
                padding: 8px;
                font-size: 11px;
            }

            .masuk {
                min-height: 38px;
                padding: 0 10px;
                font-size: 11px;
            }

            body.map-tool .toolbar {
                top: 80px;
                right: 12px;
                left: 12px;
                width: auto;
                max-width: none;
                flex-wrap: wrap;
                gap: 7px;
                padding: 9px;
            }

            body.map-tool .toolbar input,
            body.map-tool .toolbar select {
                min-width: 0;
                max-width: 100%;
            }

            body.map-tool .panel {
                top: auto !important;
                right: 12px !important;
                bottom: 12px !important;
                left: 12px !important;
                width: auto !important;
                max-width: none;
                max-height: 34dvh !important;
            }

            body.map-tool .panel[style*="top: 12px"] {
                top: 80px !important;
                right: 12px !important;
                bottom: auto !important;
                left: 12px !important;
                width: auto !important;
                max-height: calc(100dvh - 100px) !important;
            }

            body.map-tool .panel[style*="bottom"] {
                top: auto !important;
                right: 12px !important;
                bottom: 12px !important;
                left: 12px !important;
            }

            body.map-tool #hasil.panel {
                top: auto !important;
                right: 12px !important;
                bottom: 12px !important;
                left: 12px !important;
                width: min(340px, calc(100vw - 24px)) !important;
                max-height: 34dvh !important;
            }

            body.map-tool .leaflet-control-zoom {
                bottom: 16px !important;
                left: 12px !important;
            }
        }
    </style>

    @stack('styles')
</head>

<body class="{{ request()->routeIs('peta.index') ? 'map-home' : 'map-tool' }}">

    <!-- NAVBAR -->
    <header class="bar">

        <a class="brand" href="{{ route('peta.index') }}">
            <span style="display:grid;place-items:center;width:38px;height:38px;border-radius:12px;background:#eff6ff;color:#2563eb;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="m3 6 6-3 6 3 6-3v15l-6 3-6-3-6 3V6Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                    <path d="M9 3v15m6-12v15" stroke="currentColor" stroke-width="1.7"/>
                    <circle cx="15" cy="8" r="2" fill="currentColor"/>
                </svg>
            </span>
            <span>
                <strong style="display:block;">Peta Sekolah</strong>
                <small style="display:block;margin-top:2px;color:#64748b;font-size:10px;font-weight:500;">BANJARMASIN · GIS</small>
            </span>
        </a>

        @php
            $menu = [
                'peta.index'   => 'Peta',
                'peta.klaster' => 'Klaster',
                'peta.panas'   => 'Peta Panas',
                'peta.wilayah' => 'Wilayah',
                'peta.rute'    => 'Rute',
                'peta.alamat'  => 'Cek Lokasi',
            ];
        @endphp

        <nav class="menu">
            @foreach ($menu as $nama => $label)
                @if (Route::has($nama))
                    <a href="{{ route($nama) }}"
                       @class(['aktif' => request()->routeIs($nama)])>
                        {{ $label }}
                    </a>
                @endif
            @endforeach
        </nav>

        @auth
            <a class="masuk" href="{{ route('dashboard') }}">
                Dashboard
            </a>
        @else
            <a class="masuk" href="{{ route('login') }}">
                Masuk
            </a>
        @endauth

    </header>

    <!-- KONTEN HALAMAN -->
    <main>
        @yield('content')
    </main>

    <!-- LEAFLET -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        window.PETA_URL = {
            data: "{{ route('peta.data') }}",
            wilayah: "{{ route('peta.data.wilayah') }}",
        };
    </script>

    <script src="{{ asset('js/peta.js') }}"></script>

    @stack('scripts')

</body>
</html>
