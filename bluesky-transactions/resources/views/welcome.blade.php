<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BLUESKY Transactions — {{ __('app.welcome') ?? 'Bienvenue' }}</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <link href="{{ asset('css/bluesky.css') }}" rel="stylesheet">
    <script>(function(){ const t=localStorage.getItem('bluesky-theme')||'light'; document.documentElement.setAttribute('data-theme',t); })();</script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        /* ── Animations ── */
        @keyframes meshShift {
            0%   { background-position: 0% 50%, 100% 50%, 50% 0%; }
            33%  { background-position: 40% 80%, 60% 20%, 80% 60%; }
            66%  { background-position: 80% 20%, 20% 80%, 20% 40%; }
            100% { background-position: 0% 50%, 100% 50%, 50% 0%; }
        }
        @keyframes fadeUp   { from { opacity:0; transform:translateY(28px); } to { opacity:1; transform:translateY(0); } }
        @keyframes dashFlow { to   { stroke-dashoffset:-28; } }
        @keyframes dotPulse { 0%,100%{r:3.2;opacity:1;} 50%{r:4.8;opacity:0.6;} }
        @keyframes scrollX  { from { transform:translateX(0); } to { transform:translateX(-50%); } }
        @keyframes livePulse{ 0%,100%{opacity:1;transform:scale(1);} 50%{opacity:0.4;transform:scale(1.5);} }
        @keyframes float    { 0%,100%{transform:translateY(0);} 50%{transform:translateY(-12px);} }

        /* ── Page background ── */
        html, body { min-height: 100vh; font-family: 'Inter', system-ui, sans-serif; }

        body {
            min-height: 100vh;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            overflow-x: hidden;
        }

        /* Dark mode */
        [data-theme="dark"] body {
            background:
                radial-gradient(ellipse 70% 55% at 15% 25%, rgba(14,165,233,0.14) 0%, transparent 55%),
                radial-gradient(ellipse 60% 70% at 88% 78%, rgba(99,102,241,0.11) 0%, transparent 55%),
                radial-gradient(ellipse 80% 45% at 50% 105%, rgba(20,184,166,0.09) 0%, transparent 55%),
                linear-gradient(135deg, #060D1C 0%, #0a1d38 45%, #0b3260 100%);
            background-size: 250% 250%, 250% 250%, 250% 250%, 100% 100%;
            animation: meshShift 20s ease-in-out infinite;
        }

        /* Light mode */
        [data-theme="light"] body {
            background: linear-gradient(145deg, #EFF6FF 0%, #F0F9FF 50%, #E0F2FE 100%);
        }

        /* ── Africa SVG ── */
        .africa-deco {
            position: fixed; bottom: -20px; right: -30px;
            width: 300px; height: 360px; pointer-events: none; z-index: 0;
        }
        [data-theme="light"] .africa-deco { opacity: 0.05; }
        .africa-outline { fill:none; stroke:rgba(255,255,255,0.12); stroke-width:1.2; }
        [data-theme="light"] .africa-outline { stroke:rgba(2,132,199,0.4); }
        .conn-line { stroke:rgba(14,165,233,0.45); stroke-width:1; stroke-dasharray:4 3; animation:dashFlow 3s linear infinite; }
        .conn-line:nth-child(2){animation-delay:-0.6s} .conn-line:nth-child(3){animation-delay:-1.2s}
        .conn-line:nth-child(4){animation-delay:-1.8s} .conn-line:nth-child(5){animation-delay:-2.4s}
        .conn-line:nth-child(6){animation-delay:-0.9s} .conn-line:nth-child(7){animation-delay:-1.5s}
        .country-dot { fill:#38BDF8; filter:drop-shadow(0 0 4px rgba(14,165,233,0.9)); }
        .country-dot.d1{animation:dotPulse 2.6s ease-in-out infinite 0.0s;}
        .country-dot.d2{animation:dotPulse 2.6s ease-in-out infinite 0.4s;}
        .country-dot.d3{animation:dotPulse 2.6s ease-in-out infinite 0.8s;}
        .country-dot.d4{animation:dotPulse 2.6s ease-in-out infinite 1.2s;}
        .country-dot.d5{animation:dotPulse 2.6s ease-in-out infinite 1.6s;}
        .country-dot.d6{animation:dotPulse 2.6s ease-in-out infinite 2.0s;}
        .country-dot.d7{animation:dotPulse 2.6s ease-in-out infinite 2.4s;}
        .country-dot.d8{animation:dotPulse 2.6s ease-in-out infinite 0.2s;}
        .country-label { font-size:6.5px; fill:rgba(255,255,255,0.65); font-family:monospace; font-weight:700; }
        .bg-dot { fill:rgba(255,255,255,0.18); }

        /* ── Theme toggle ── */
        .theme-btn {
            position: fixed; top: 16px; right: 16px; z-index: 10;
            border-radius: 10px; padding: 8px 13px; font-size: 16px;
            cursor: pointer; border: 1px solid; transition: all 0.2s;
        }
        [data-theme="dark"] .theme-btn { background:rgba(255,255,255,0.08); border-color:rgba(255,255,255,0.15); color:white; }
        [data-theme="light"] .theme-btn { background:#F0F9FF; border-color:#BAE6FD; color:#0369A1; }

        /* ── Lang switcher ── */
        .lang-bar {
            position: fixed; top: 16px; left: 16px; z-index: 10;
            display: flex; align-items: center; gap: 4px;
            background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12);
            border-radius: 10px; padding: 4px 6px;
        }
        [data-theme="light"] .lang-bar { background:#F0F9FF; border-color:#BAE6FD; }
        .lang-btn {
            padding: 5px 10px; border-radius: 7px; font-size: 12px; font-weight: 600;
            text-decoration: none; transition: background 0.2s;
        }
        [data-theme="dark"] .lang-btn { color:rgba(255,255,255,0.55); }
        [data-theme="dark"] .lang-btn.active { background:rgba(255,255,255,0.14); color:white; }
        [data-theme="light"] .lang-btn { color:#64748B; }
        [data-theme="light"] .lang-btn.active { background:#DBEAFE; color:#0284C7; }

        /* ── Main card ── */
        .welcome-card {
            position: relative; z-index: 1;
            width: min(540px, 94vw);
            border-radius: 28px;
            padding: 48px 44px 40px;
            animation: fadeUp 0.55s ease both;
            text-align: center;
        }
        [data-theme="dark"] .welcome-card {
            background: rgba(255,255,255,0.055);
            backdrop-filter: blur(22px); -webkit-backdrop-filter: blur(22px);
            border: 1px solid rgba(255,255,255,0.10);
            box-shadow: 0 32px 80px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.08);
        }
        [data-theme="light"] .welcome-card {
            background: white;
            border: 1px solid #BFDBFE;
            box-shadow: 0 20px 60px rgba(2,132,199,0.12), 0 4px 16px rgba(0,0,0,0.06);
        }

        /* ── Logo ── */
        .wl-logo { display:flex; flex-direction:column; align-items:center; gap:14px; margin-bottom:22px; }
        .wl-logo img {
            width: 88px; height: 88px; object-fit: contain; border-radius: 22px;
            filter: drop-shadow(0 6px 24px rgba(2,132,199,0.45));
            animation: float 5s ease-in-out infinite;
        }
        .wl-name { font-size: 28px; font-weight: 900; letter-spacing: 4px; }
        .wl-sub  { font-size: 10px; font-weight: 700; letter-spacing: 3.5px; text-transform: uppercase; margin-top: 2px; }
        [data-theme="dark"] .wl-name { color: white; }
        [data-theme="dark"] .wl-sub  { color: rgba(255,255,255,0.4); }
        [data-theme="light"] .wl-name { color: #0F172A; }
        [data-theme="light"] .wl-sub  { color: #64748B; }

        /* ── Live badge ── */
        .live-badge {
            display: inline-flex; align-items: center; gap: 7px;
            border-radius: 20px; padding: 5px 14px;
            font-size: 11.5px; font-weight: 700; margin-bottom: 18px;
            border: 1px solid;
        }
        .live-dot { width: 7px; height: 7px; border-radius: 50%; animation: livePulse 1.8s infinite; }
        [data-theme="dark"] .live-badge { background:rgba(16,185,129,0.12); border-color:rgba(16,185,129,0.25); color:#10B981; }
        [data-theme="dark"] .live-dot   { background:#10B981; }
        [data-theme="light"] .live-badge{ background:#D1FAE5; border-color:#6EE7B7; color:#059669; }
        [data-theme="light"] .live-dot  { background:#10B981; }

        /* ── Tagline ── */
        .wl-tagline { font-size: 15px; line-height: 1.7; margin-bottom: 26px; }
        [data-theme="dark"] .wl-tagline { color: rgba(255,255,255,0.55); }
        [data-theme="light"] .wl-tagline { color: #475569; }

        /* ── Features grid ── */
        .feat-grid {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 9px; margin-bottom: 28px; text-align: left;
        }
        .feat-item {
            display: flex; align-items: center; gap: 9px;
            border-radius: 11px; padding: 10px 12px;
            font-size: 12.5px; font-weight: 500;
        }
        [data-theme="dark"] .feat-item { background:rgba(255,255,255,0.05); color:rgba(255,255,255,0.8); }
        [data-theme="light"] .feat-item { background:#F8FAFC; color:#334155; border: 1px solid #E2E8F0; }
        .feat-icon {
            width: 28px; height: 28px; border-radius: 8px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center; font-size: 14px;
        }
        [data-theme="dark"] .feat-icon { background:rgba(255,255,255,0.1); }
        [data-theme="light"] .feat-icon { background:#EFF6FF; }

        /* ── Action buttons ── */
        .wl-actions { display: flex; flex-direction: column; gap: 11px; margin-bottom: 22px; }
        .btn-primary-wl {
            display: flex; align-items: center; justify-content: center; gap: 9px;
            width: 100%; padding: 15px 24px; border-radius: 13px; border: none;
            background: linear-gradient(135deg, #0284C7 0%, #0EA5E9 100%);
            color: white; font-size: 15px; font-weight: 800; font-family: inherit;
            cursor: pointer; text-decoration: none;
            box-shadow: 0 8px 28px rgba(2,132,199,0.4);
            transition: all 0.2s;
        }
        .btn-primary-wl:hover { transform: translateY(-2px); box-shadow: 0 12px 36px rgba(2,132,199,0.5); }
        .btn-outline-wl {
            display: flex; align-items: center; justify-content: center; gap: 9px;
            width: 100%; padding: 14px 24px; border-radius: 13px;
            font-size: 15px; font-weight: 700; font-family: inherit;
            cursor: pointer; text-decoration: none; transition: all 0.2s;
            border-width: 1.5px; border-style: solid;
        }
        [data-theme="dark"] .btn-outline-wl {
            background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.18); color: white;
        }
        [data-theme="dark"] .btn-outline-wl:hover { background: rgba(255,255,255,0.12); }
        [data-theme="light"] .btn-outline-wl {
            background: white; border-color: #BFDBFE; color: #0284C7;
        }
        [data-theme="light"] .btn-outline-wl:hover { background: #EFF6FF; }

        /* ── Countries scroll ── */
        .chips-wrap { overflow: hidden; position: relative; margin-bottom: 4px; }
        .chips-wrap::before, .chips-wrap::after {
            content: ''; position: absolute; top: 0; bottom: 0; width: 40px; z-index: 2; pointer-events: none;
        }
        [data-theme="dark"] .chips-wrap::before { left:0; background:linear-gradient(to right, rgba(255,255,255,0.055),transparent); }
        [data-theme="dark"] .chips-wrap::after  { right:0; background:linear-gradient(to left,  rgba(255,255,255,0.055),transparent); }
        [data-theme="light"] .chips-wrap::before{ left:0; background:linear-gradient(to right, white, transparent); }
        [data-theme="light"] .chips-wrap::after { right:0; background:linear-gradient(to left,  white, transparent); }
        .chips-track {
            display: flex; gap: 7px;
            animation: scrollX 30s linear infinite;
            width: max-content;
        }
        .chips-track:hover { animation-play-state: paused; }
        .country-chip {
            display: inline-flex; align-items: center; gap: 6px;
            border-radius: 20px; padding: 5px 12px; font-size: 11.5px; font-weight: 500;
            white-space: nowrap; border: 1px solid;
        }
        [data-theme="dark"] .country-chip { background:rgba(255,255,255,0.07); border-color:rgba(255,255,255,0.11); color:rgba(255,255,255,0.75); }
        [data-theme="light"] .country-chip { background:#F0F9FF; border-color:#BAE6FD; color:#0369A1; }

        /* ── Footer ── */
        .wl-footer { font-size: 11px; margin-top: 20px; }
        [data-theme="dark"] .wl-footer { color: rgba(255,255,255,0.2); }
        [data-theme="light"] .wl-footer { color: #94A3B8; }

        /* ── Responsive ── */
        @media (max-width: 480px) {
            .welcome-card { padding: 34px 22px 28px; border-radius: 22px; }
            .wl-logo img  { width: 72px; height: 72px; }
            .wl-name      { font-size: 24px; }
            .feat-grid    { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

{{-- Lang switcher --}}
<div class="lang-bar">
    <a href="{{ route('lang.switch', 'fr') }}" class="lang-btn {{ app()->getLocale() === 'fr' ? 'active' : '' }}">🇫🇷 FR</a>
    <a href="{{ route('lang.switch', 'en') }}" class="lang-btn {{ app()->getLocale() === 'en' ? 'active' : '' }}">🇬🇧 EN</a>
</div>

{{-- Theme toggle --}}
<button class="theme-btn" onclick="toggleTheme()" id="themeBtn" title="Toggle theme">🌙</button>

{{-- Africa network SVG --}}
<svg class="africa-deco" viewBox="0 0 200 248" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <path class="africa-outline" d="M 82,10 C 96,4 118,5 136,13 C 152,20 163,36 166,55 C 169,72 167,90 164,108 C 161,124 157,138 153,152 C 150,165 146,178 141,191 C 135,207 124,222 112,233 C 104,241 92,245 80,239 C 68,233 59,218 52,202 C 45,187 40,171 38,155 C 36,138 35,122 36,106 C 37,88 42,72 48,58 C 54,44 62,31 70,21 C 75,14 79,11 82,10 Z"/>
    <circle class="bg-dot" cx="58" cy="64" r="1"/><circle class="bg-dot" cx="140" cy="72" r="1"/>
    <circle class="bg-dot" cx="72" cy="112" r="1"/><circle class="bg-dot" cx="155" cy="96" r="1"/>
    <line class="conn-line" x1="88" y1="148" x2="122" y2="182"/>
    <line class="conn-line" x1="122" y1="182" x2="132" y2="196"/>
    <line class="conn-line" x1="132" y1="196" x2="120" y2="220"/>
    <line class="conn-line" x1="120" y1="220" x2="90"  y2="208"/>
    <line class="conn-line" x1="122" y1="182" x2="140" y2="178"/>
    <line class="conn-line" x1="140" y1="178" x2="152" y2="150"/>
    <line class="conn-line" x1="152" y1="150" x2="155" y2="130"/>
    <circle class="country-dot d1" cx="88"  cy="148" r="3.2"/>
    <circle class="country-dot d2" cx="122" cy="182" r="3.2"/>
    <circle class="country-dot d3" cx="152" cy="150" r="3.2"/>
    <circle class="country-dot d4" cx="155" cy="130" r="3.2"/>
    <circle class="country-dot d5" cx="140" cy="178" r="3.2"/>
    <circle class="country-dot d6" cx="132" cy="196" r="3.2"/>
    <circle class="country-dot d7" cx="120" cy="220" r="3.2"/>
    <circle class="country-dot d8" cx="90"  cy="208" r="3.2"/>
    <text class="country-label" x="74" y="146">CD</text>
    <text class="country-label" x="125" y="180">ZM</text>
    <text class="country-label" x="155" y="148">TZ</text>
    <text class="country-label" x="158" y="128">KE</text>
    <text class="country-label" x="143" y="176">MW</text>
    <text class="country-label" x="135" y="194">ZW</text>
    <text class="country-label" x="106" y="231">ZA</text>
    <text class="country-label" x="76"  y="220">NA</text>
</svg>

{{-- Card --}}
<div class="welcome-card">

    {{-- Logo --}}
    <div class="wl-logo">
        <img src="{{ asset('images/logo.png') }}" alt="BLUESKY Logo">
        <div>
            <div class="wl-name">BLUESKY</div>
            <div class="wl-sub">Transactions</div>
        </div>
    </div>

    {{-- Live badge --}}
    <div style="display:flex;justify-content:center;">
        <div class="live-badge">
            <span class="live-dot"></span>
            {{ __('app.system_online') ?? 'Système opérationnel' }}
        </div>
    </div>

    {{-- Tagline --}}
    <p class="wl-tagline">
        {{ __('app.welcome_tagline') ?? 'Plateforme de gestion de transferts d\'argent en Afrique subsaharienne.' }}
    </p>

    {{-- Features --}}
    <div class="feat-grid">
        <div class="feat-item"><div class="feat-icon">📋</div>{{ __('app.feat_record') ?? 'Enregistrement des transferts' }}</div>
        <div class="feat-item"><div class="feat-icon">⚡</div>{{ __('app.feat_realtime') ?? 'Suivi en temps réel' }}</div>
        <div class="feat-item"><div class="feat-icon">📊</div>{{ __('app.feat_reports') ?? 'Rapports & statistiques' }}</div>
        <div class="feat-item"><div class="feat-icon">🛡️</div>{{ __('app.feat_secure') ?? 'Accès sécurisé par rôle' }}</div>
    </div>

    {{-- Buttons --}}
    <div class="wl-actions">
        <a href="{{ route('register') }}" class="btn-primary-wl">
            <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
            {{ __('app.register') ?? 'Créer un compte' }}
        </a>
        <a href="{{ route('login') }}" class="btn-outline-wl">
            <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M15 12H3"/></svg>
            {{ __('app.login_btn') ?? 'Se connecter' }}
        </a>
    </div>

    {{-- Countries scroll --}}
    @if($activeCountries->count())
    <div class="chips-wrap">
        <div class="chips-track">
            @foreach($activeCountries->concat($activeCountries) as $c)
                <span class="country-chip">{{ $c->flag_emoji }} {{ $c->name }}</span>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Footer --}}
    <div class="wl-footer">
        &copy; {{ date('Y') }} BLUESKY Transactions &nbsp;·&nbsp; Laravel 12
    </div>
</div>

<script>
    function toggleTheme() {
        const html = document.documentElement;
        const next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        html.setAttribute('data-theme', next);
        localStorage.setItem('bluesky-theme', next);
        document.getElementById('themeBtn').textContent = next === 'dark' ? '☀️' : '🌙';
    }
    (function(){
        const t = document.documentElement.getAttribute('data-theme') || 'light';
        document.getElementById('themeBtn').textContent = t === 'dark' ? '☀️' : '🌙';
    })();
</script>
</body>
</html>
