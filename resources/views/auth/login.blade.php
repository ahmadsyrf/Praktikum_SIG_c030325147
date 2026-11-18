<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0f172a">
    <title>Masuk · Peta Sekolah Banjarmasin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; }
        html, body { min-height: 100%; margin: 0; }
        body { min-height: 100vh; color: #0f172a; background: #fff; font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        .login-shell { display: grid; min-height: 100vh; grid-template-columns: 1fr 1fr; }
        .login-side { display: flex; align-items: center; justify-content: center; padding: 48px; }
        .login-content { width: min(100%, 410px); }
        .brand { display: flex; align-items: center; gap: 12px; margin-bottom: 65px; color: #0f172a; text-decoration: none; }
        .brand-mark { display: grid; width: 43px; height: 43px; place-items: center; border-radius: 14px; background: #eff6ff; color: #2563eb; }
        .brand-title { display: block; font-size: 15px; font-weight: 800; letter-spacing: -.03em; }
        .brand-caption { display: block; margin-top: 3px; color: #64748b; font-size: 10px; font-weight: 650; letter-spacing: .1em; }
        .welcome-tag { display: inline-flex; align-items: center; gap: 7px; padding: 7px 10px; border: 1px solid #dbeafe; border-radius: 99px; background: #eff6ff; color: #2563eb; font-size: 10px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
        .welcome-tag span { width: 6px; height: 6px; border-radius: 50%; background: #3b82f6; }
        h1 { margin: 17px 0 8px; color: #0f172a; font-size: clamp(29px, 3vw, 36px); letter-spacing: -.055em; line-height: 1.15; }
        .intro { margin: 0 0 30px; color: #64748b; font-size: 14px; line-height: 1.7; }
        .field { margin-top: 19px; }
        .field label { display: block; margin-bottom: 8px; color: #334155; font-size: 12px; font-weight: 700; }
        .input-shell { position: relative; }
        .input-shell svg { position: absolute; top: 14px; left: 14px; color: #94a3b8; }
        .input-shell input { width: 100%; height: 48px; padding: 0 15px 0 43px; border: 1px solid #e2e8f0; border-radius: 12px; outline: 0; background: rgba(248, 250, 252, .75); color: #0f172a; font: inherit; font-size: 13px; transition: border-color .2s ease, box-shadow .2s ease, background .2s ease; }
        .input-shell input::placeholder { color: #a1aab8; }
        .input-shell input:focus { border-color: #60a5fa; background: #fff; box-shadow: 0 0 0 4px rgba(37, 99, 235, .1); }
        .remember-row { display: flex; justify-content: space-between; align-items: center; margin: 17px 0 22px; }
        .remember { display: inline-flex; align-items: center; gap: 8px; color: #475569; font-size: 12px; cursor: pointer; }
        .remember input { width: 15px; height: 15px; margin: 0; accent-color: #2563eb; }
        .submit-button { display: flex; width: 100%; min-height: 48px; justify-content: center; align-items: center; gap: 9px; border: 0; border-radius: 12px; background: #2563eb; color: #fff; cursor: pointer; font: inherit; font-size: 13px; font-weight: 750; box-shadow: 0 8px 18px rgba(37, 99, 235, .2); transition: transform .2s ease, background .2s ease, box-shadow .2s ease; }
        .submit-button:hover { transform: translateY(-1px); background: #1d4ed8; box-shadow: 0 11px 22px rgba(37, 99, 235, .25); }
        .submit-button:active { transform: translateY(0); }
        .guest-link { display: flex; justify-content: center; align-items: center; gap: 8px; width: 100%; min-height: 46px; margin-top: 11px; border: 1px solid #e2e8f0; border-radius: 12px; color: #475569; font-size: 12px; font-weight: 650; text-decoration: none; transition: .2s ease; }
        .guest-link:hover { border-color: #bfdbfe; background: #f8fbff; color: #2563eb; }
        .secure-note { display: flex; justify-content: center; align-items: center; gap: 6px; margin: 24px 0 0; color: #94a3b8; font-size: 10px; }
        .error-box { margin: 18px 0 0; padding: 12px 14px; border: 1px solid #fecaca; border-radius: 11px; background: #fef2f2; color: #b91c1c; font-size: 12px; line-height: 1.6; }
        .visual-side { position: relative; display: flex; min-height: 100vh; align-items: flex-end; overflow: hidden; padding: 58px; background: #0f172a; color: #fff; isolation: isolate; }
        .visual-side:before { position: absolute; z-index: -1; inset: 0; background: radial-gradient(ellipse at 68% 28%, rgba(37, 99, 235, .25), transparent 45%), linear-gradient(155deg, #0f172a 20%, #111f3d 100%); content: ""; }
        .map-art { position: absolute; inset: 0; z-index: -1; width: 100%; height: 100%; opacity: .9; }
        .visual-grid { position: absolute; inset: 0; z-index: -1; background-image: linear-gradient(rgba(148, 163, 184, .045) 1px, transparent 1px), linear-gradient(90deg, rgba(148, 163, 184, .045) 1px, transparent 1px); background-size: 44px 44px; mask-image: linear-gradient(to bottom, black, transparent 95%); }
        .visual-copy { width: min(100%, 580px); margin: 0 auto; }
        .visual-kicker { display: inline-flex; align-items: center; gap: 8px; margin-bottom: 16px; color: #93c5fd; font-size: 10px; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; }
        .visual-kicker:before { width: 20px; height: 1px; background: #60a5fa; content: ""; }
        .visual-copy h2 { max-width: 520px; margin: 0; font-size: clamp(30px, 4vw, 48px); font-weight: 720; letter-spacing: -.055em; line-height: 1.12; }
        .visual-copy > p { max-width: 460px; margin: 16px 0 30px; color: #a8b7ce; font-size: 13px; line-height: 1.8; }
        .map-stats { display: flex; gap: 34px; padding-top: 23px; border-top: 1px solid rgba(148, 163, 184, .2); }
        .map-stat strong { display: block; color: #fff; font-size: 22px; letter-spacing: -.04em; }
        .map-stat span { display: block; margin-top: 5px; color: #94a3b8; font-size: 10px; }
        .map-coordinates { position: absolute; top: 31px; right: 35px; color: rgba(191, 219, 254, .55); font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 9px; letter-spacing: .08em; }
        @media (max-width: 800px) {
            .login-shell { grid-template-columns: 1fr; }
            .login-side { min-height: 100vh; padding: 30px 24px; }
            .login-content { max-width: 420px; }
            .brand { margin-bottom: 48px; }
            .visual-side { display: none; }
        }
        @media (max-width: 420px) {
            .login-side { padding: 24px 20px; }
            .brand { margin-bottom: 42px; }
            h1 { font-size: 31px; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *:before, *:after { scroll-behavior: auto !important; transition-duration: .01ms !important; }
        }
    </style>
</head>
<body>
    <main class="login-shell">
        <section class="login-side">
            <div class="login-content">
                <a class="brand" href="{{ route('peta.index') }}">
                    <span class="brand-mark" aria-hidden="true">
                        <svg width="23" height="23" viewBox="0 0 24 24" fill="none">
                            <path d="m3 6 6-3 6 3 6-3v15l-6 3-6-3-6 3V6Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                            <path d="M9 3v15m6-12v15" stroke="currentColor" stroke-width="1.7"/>
                            <circle cx="15" cy="8" r="2" fill="currentColor"/>
                        </svg>
                    </span>
                    <span><span class="brand-title">Peta Sekolah Banjarmasin</span><span class="brand-caption">SISTEM INFORMASI GEOGRAFIS</span></span>
                </a>

                <span class="welcome-tag"><span></span> Portal administrator</span>
                <h1>Selamat datang<br>kembali.</h1>
                <p class="intro">Masuk untuk mengelola data sekolah dan melihat perkembangan pendidikan di Banjarmasin.</p>

                @if ($errors->any())
                    <div class="error-box" role="alert">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('login.process') }}" method="POST">
                    @csrf
                    <div class="field">
                        <label for="email">Alamat email</label>
                        <div class="input-shell">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <rect x="3" y="5" width="18" height="14" rx="3" stroke="currentColor" stroke-width="1.7"/>
                                <path d="m4 7 8 6 8-6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" autocomplete="email" required autofocus>
                        </div>
                    </div>
                    <div class="field">
                        <label for="password">Kata sandi</label>
                        <div class="input-shell">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <rect x="4" y="10" width="16" height="11" rx="3" stroke="currentColor" stroke-width="1.7"/>
                                <path d="M8 10V7a4 4 0 1 1 8 0v3m-4 5v2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                            </svg>
                            <input type="password" id="password" name="password" placeholder="Masukkan kata sandi" autocomplete="current-password" required>
                        </div>
                    </div>
                    <div class="remember-row">
                        <label class="remember" for="remember">
                            <input type="checkbox" id="remember" name="remember" value="1" @checked(old('remember'))>
                            Ingat saya
                        </label>
                    </div>
                    <button class="submit-button" type="submit">
                        Masuk ke Dashboard
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </form>

                <a class="guest-link" href="{{ route('peta.index') }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M3 6 9 3l6 3 6-3v15l-6 3-6-3-6 3V6Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                        <path d="M9 3v15m6-12v15" stroke="currentColor" stroke-width="1.7"/>
                    </svg>
                    Masuk sebagai Tamu · Lihat Peta Publik
                </a>
                <p class="secure-note">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 3 5 6v5c0 5 3 8 7 10 4-2 7-5 7-10V6l-7-3Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                        <path d="m9 12 2 2 4-4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Akses aman untuk pengelola data
                </p>
            </div>
        </section>

        <aside class="visual-side" aria-label="Visual peta dan statistik sekolah Banjarmasin">
            <div class="visual-grid"></div>
            <span class="map-coordinates">3°19' S&nbsp;&nbsp; 114°35' E</span>
            <svg class="map-art" viewBox="0 0 900 900" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
                <defs>
                    <pattern id="map-grid" width="34" height="34" patternUnits="userSpaceOnUse">
                        <path d="M34 0H0V34" fill="none" stroke="#64748b" stroke-opacity=".13" stroke-width="1"/>
                    </pattern>
                    <filter id="pin-glow" x="-150%" y="-150%" width="400%" height="400%">
                        <feGaussianBlur stdDeviation="7" result="blur"/>
                        <feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge>
                    </filter>
                    <linearGradient id="river" x1="0" x2="1" y1="0" y2="1">
                        <stop offset="0" stop-color="#2563eb" stop-opacity=".04"/>
                        <stop offset=".5" stop-color="#3b82f6" stop-opacity=".48"/>
                        <stop offset="1" stop-color="#06b6d4" stop-opacity=".16"/>
                    </linearGradient>
                </defs>
                <rect width="900" height="900" fill="url(#map-grid)"/>
                <g fill="none" stroke="#64748b" stroke-opacity=".2">
                    <path d="M-30 170 188 130l132 54 134-43 162 56 220-59"/>
                    <path d="M-50 275 161 240l163 48 162-56 162 65 270-47"/>
                    <path d="M-40 390 163 355l183 48 158-42 170 68 242-35"/>
                    <path d="M-40 510 168 480l177 52 156-37 185 72 240-30"/>
                    <path d="M-40 640 174 601l176 61 167-47 194 74 220-39"/>
                    <path d="M135 -20 168 180l-20 174 42 160-21 190 36 220"/>
                    <path d="M280 -20 307 176l-23 180 53 162-27 188 36 220"/>
                    <path d="M450 -20 475 175l-20 190 48 159-30 187 35 230"/>
                    <path d="M620 -20 646 178l-28 181 59 164-33 185 41 230"/>
                    <path d="M790 -20 805 185l-19 180 58 165-24 190 42 230"/>
                </g>
                <path d="M-30 458c140-85 203 51 324-18s165-121 275-48 200 65 360-38l20 99c-144 98-250 30-364 71s-160 104-285 42S121 541-18 621Z" fill="url(#river)" stroke="#60a5fa" stroke-opacity=".6" stroke-width="3"/>
                <path d="M82 756c87-73 115-133 162-225s124-91 166-198 95-123 170-168 131-74 203-152" fill="none" stroke="#38bdf8" stroke-opacity=".36" stroke-width="2" stroke-dasharray="5 9"/>
                <g filter="url(#pin-glow)">
                    <g fill="#60a5fa" stroke="#dbeafe" stroke-width="2">
                        <circle cx="214" cy="328" r="6"/><circle cx="346" cy="244" r="6"/>
                        <circle cx="410" cy="430" r="7"/><circle cx="562" cy="326" r="6"/>
                        <circle cx="625" cy="512" r="7"/><circle cx="294" cy="584" r="6"/>
                        <circle cx="742" cy="410" r="6"/><circle cx="500" cy="650" r="6"/>
                    </g>
                    <g fill="#2563eb" stroke="#bfdbfe" stroke-width="2">
                        <circle cx="475" cy="300" r="10"/><circle cx="356" cy="468" r="9"/>
                        <circle cx="676" cy="620" r="9"/>
                    </g>
                    <g fill="#fff">
                        <circle cx="475" cy="300" r="3"/><circle cx="356" cy="468" r="3"/>
                        <circle cx="676" cy="620" r="3"/>
                    </g>
                </g>
                <circle cx="475" cy="300" r="28" fill="none" stroke="#60a5fa" stroke-opacity=".25"/>
                <circle cx="475" cy="300" r="46" fill="none" stroke="#60a5fa" stroke-opacity=".12"/>
            </svg>
            <div class="visual-copy">
                <div class="visual-kicker">Spatial intelligence · Kalimantan Selatan</div>
                <h2>Setiap sekolah,<br>terhubung dalam satu peta.</h2>
                <p>Jelajahi persebaran sekolah Banjarmasin melalui data spasial yang mudah dipahami dan selalu siap digunakan.</p>
                <div class="map-stats">
                    <div class="map-stat"><strong>500+</strong><span>Sekolah terdata</span></div>
                    <div class="map-stat"><strong>5</strong><span>Kecamatan terintegrasi</span></div>
                </div>
            </div>
        </aside>
    </main>
</body>
</html>
