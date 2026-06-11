<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BLUE SKY MONEY TRANSFERT</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
            background:
                linear-gradient(150deg, rgba(7,27,54,0.55) 0%, rgba(12,45,92,0.5) 35%, rgba(3,105,161,0.45) 70%, rgba(14,165,233,0.4) 100%),
                url('{{ asset('images/blog-4.jpg') }}') center/cover no-repeat fixed;
            color: #fff;
            position: relative;
        }

        /* ============================================================
           FILIGRANE
        ============================================================ */
        .watermark {
            position: fixed;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }
        .watermark svg {
            width: clamp(340px, 90vw, 1100px);
            opacity: 0.055;
            transform: rotate(-8deg);
        }

        /* ============================================================
           PARTICULES
        ============================================================ */
        .particles {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }
        .particle {
            position: absolute;
            border-radius: 50%;
            background: rgba(186, 230, 253, 0.15);
            animation: float-up linear infinite;
        }
        @keyframes float-up {
            0%   { transform: translateY(110vh) scale(0.8); opacity: 0; }
            10%  { opacity: 1; }
            90%  { opacity: 0.5; }
            100% { transform: translateY(-15vh) scale(1.2); opacity: 0; }
        }

        /* ============================================================
           ANIMATIONS
        ============================================================ */
        @keyframes pulse-ring {
            0%, 100% { box-shadow: 0 0 0 0 rgba(14,165,233,0.45), 0 0 40px rgba(14,165,233,0.25); }
            50%       { box-shadow: 0 0 0 12px rgba(14,165,233,0), 0 0 70px rgba(14,165,233,0.45); }
        }
        @keyframes fade-down {
            from { opacity: 0; transform: translateY(-24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes fade-up {
            from { opacity: 0; transform: translateY(32px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes pop-in {
            from { opacity: 0; transform: scale(0.75); }
            to   { opacity: 1; transform: scale(1); }
        }

        /* ============================================================
           NAV HEADER
        ============================================================ */
        .nav-header {
            position: relative;
            z-index: 20;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 32px;
            animation: fade-down 0.55s ease both;
            gap: 12px;
        }
        .nav-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .nav-brand img {
            width: 36px;
            height: 36px;
            object-fit: contain;
        }
        .nav-brand-text {
            font-size: 14px;
            font-weight: 800;
            color: #fff;
            letter-spacing: 0.04em;
            line-height: 1.1;
        }
        .nav-brand-sub {
            font-size: 9px;
            font-weight: 500;
            color: rgba(186,230,253,0.7);
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }
        .nav-links {
            display: flex;
            gap: 10px;
            flex-shrink: 0;
        }
        .nav-links a {
            text-decoration: none;
            color: rgba(255,255,255,0.82);
            font-size: 13px;
            font-weight: 500;
            padding: 8px 20px;
            border-radius: 50px;
            border: 1px solid rgba(255,255,255,0.22);
            transition: all 0.22s;
            backdrop-filter: blur(8px);
            background: rgba(255,255,255,0.07);
            white-space: nowrap;
        }
        .nav-links a:hover {
            background: rgba(255,255,255,0.16);
            border-color: rgba(255,255,255,0.45);
            color: #fff;
        }
        .nav-links a.nav-cta {
            background: #0EA5E9;
            border-color: #0EA5E9;
            color: #fff;
            font-weight: 600;
        }
        .nav-links a.nav-cta:hover {
            background: #0284C7;
            border-color: #0284C7;
        }

        /* ============================================================
           MAIN
        ============================================================ */
        .main-content {
            position: relative;
            z-index: 5;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - 76px);
            padding: 24px 20px 56px;
            text-align: center;
        }

        /* ============================================================
           LOGO
        ============================================================ */
        .logo-wrap {
            margin-bottom: 28px;
            animation: fade-down 0.65s ease 0.08s both;
        }
        .logo-ring {
            width: 230px;
            height: 230px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(14,165,233,0.22), rgba(3,105,161,0.12));
            border: 3px solid rgba(14,165,233,0.55);
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse-ring 3.2s ease-in-out infinite;
            backdrop-filter: blur(14px);
            margin: 0 auto;
        }
        .logo-img {
            width: 170px;
            height: 170px;
            object-fit: contain;
            filter: drop-shadow(0 0 20px rgba(14,165,233,0.6));
        }

        /* ============================================================
           HERO TITLE
        ============================================================ */
        .hero-title {
            animation: fade-up 0.7s ease 0.18s both;
            margin-bottom: 14px;
            line-height: 1;
        }
        .title-blue-sky {
            display: block;
            font-size: clamp(3.6rem, 12vw, 9rem);
            font-weight: 900;
            letter-spacing: 0.035em;
            background: linear-gradient(135deg, #ffffff 0%, #BAE6FD 45%, #38BDF8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .title-money {
            display: block;
            font-size: clamp(1.2rem, 5vw, 3.2rem);
            font-weight: 700;
            letter-spacing: clamp(0.12em, 1.8vw, 0.26em);
            color: #BAE6FD;
            margin-top: 10px;
            text-transform: uppercase;
        }

        /* ============================================================
           SUBTITLE
        ============================================================ */
        .hero-sub {
            font-size: clamp(13px, 2.2vw, 17px);
            color: rgba(186,230,253,0.78);
            max-width: 520px;
            line-height: 1.75;
            margin-bottom: 30px;
            animation: fade-up 0.7s ease 0.3s both;
            padding: 0 8px;
        }

        /* ============================================================
           PAYS
        ============================================================ */
        .countries-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 7px;
            margin-bottom: 36px;
            max-width: 640px;
            padding: 0 8px;
            animation: fade-up 0.7s ease 0.42s both;
        }
        .country-badge {
            font-size: clamp(11px, 1.8vw, 13px);
            padding: 5px 13px;
            border-radius: 20px;
            background: rgba(255,255,255,0.09);
            border: 1px solid rgba(255,255,255,0.18);
            backdrop-filter: blur(6px);
            font-weight: 500;
            animation: pop-in 0.4s ease both;
            white-space: nowrap;
        }
        .badge-name {
            display: inline-block;
            transition: opacity 0.28s ease, transform 0.28s ease;
        }
        .badge-name.lang-out {
            opacity: 0;
            transform: translateY(-5px);
        }
        .badge-name.lang-in {
            animation: lang-pop 0.28s ease both;
        }
        @keyframes lang-pop {
            from { opacity: 0; transform: translateY(5px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ============================================================
           BOUTONS CTA
        ============================================================ */
        .cta-group {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            justify-content: center;
            animation: fade-up 0.7s ease 0.54s both;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            font-weight: 700;
            font-size: clamp(13px, 2vw, 15px);
            padding: 13px 32px;
            border-radius: 50px;
            transition: all 0.25s;
            letter-spacing: 0.025em;
        }
        .btn-solid {
            background: linear-gradient(135deg, #0EA5E9, #0284C7);
            color: #fff;
            box-shadow: 0 5px 22px rgba(14,165,233,0.42);
        }
        .btn-solid:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 32px rgba(14,165,233,0.6);
        }
        .btn-ghost {
            background: rgba(255,255,255,0.09);
            color: #fff;
            border: 2px solid rgba(255,255,255,0.3);
            backdrop-filter: blur(10px);
        }
        .btn-ghost:hover {
            background: rgba(255,255,255,0.18);
            border-color: rgba(255,255,255,0.55);
            transform: translateY(-3px);
        }

        /* ============================================================
           STATS
        ============================================================ */
        .stats-section {
            width: 100%;
            max-width: 640px;
            margin-top: 48px;
            padding-top: 36px;
            border-top: 1px solid rgba(255,255,255,0.12);
            animation: fade-up 0.7s ease 0.68s both;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
        }
        .stat-card {
            text-align: center;
            padding: 14px 8px;
            border-radius: 14px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            backdrop-filter: blur(8px);
        }
        .stat-value {
            font-size: clamp(1.4rem, 3.5vw, 2rem);
            font-weight: 800;
            color: #38BDF8;
            line-height: 1;
        }
        .stat-label {
            font-size: clamp(9px, 1.4vw, 11px);
            color: rgba(186,230,253,0.65);
            margin-top: 5px;
            letter-spacing: 0.07em;
            text-transform: uppercase;
        }

        /* ============================================================
           FOOTER
        ============================================================ */
        .page-footer {
            position: relative;
            z-index: 5;
            text-align: center;
            padding: 16px 20px 24px;
            font-size: 11px;
            color: rgba(186,230,253,0.4);
            letter-spacing: 0.05em;
        }

        /* ============================================================
           RESPONSIVE — TABLET (max 768px)
        ============================================================ */
        @media (max-width: 768px) {
            .nav-header {
                padding: 14px 20px;
            }
            .nav-brand-text { font-size: 13px; }
            .nav-links a { padding: 7px 16px; font-size: 12px; }

            .logo-ring { width: 190px; height: 190px; }
            .logo-img  { width: 138px; height: 138px; }

            .stats-grid { grid-template-columns: repeat(2, 1fr); }

            .main-content { padding: 16px 16px 48px; }
        }

        /* ============================================================
           RESPONSIVE — MOBILE (max 480px)
        ============================================================ */
        @media (max-width: 480px) {
            .nav-header {
                padding: 12px 16px;
                flex-wrap: wrap;
                gap: 10px;
            }
            .nav-brand-text { font-size: 12px; }
            .nav-brand img  { width: 30px; height: 30px; }
            .nav-links { gap: 8px; }
            .nav-links a { padding: 6px 14px; font-size: 12px; }

            .logo-ring { width: 160px; height: 160px; }
            .logo-img  { width: 116px; height: 116px; }
            .logo-wrap { margin-bottom: 22px; }

            .hero-title { margin-bottom: 12px; }
            .title-money { margin-top: 5px; letter-spacing: 0.08em; }

            .hero-sub { margin-bottom: 22px; }

            .countries-row { gap: 6px; margin-bottom: 26px; }
            .country-badge  { font-size: 11px; padding: 4px 10px; }

            .cta-group { flex-direction: column; align-items: center; gap: 11px; width: 100%; }
            .btn        { width: 100%; max-width: 320px; justify-content: center; padding: 13px 24px; }

            .stats-section { margin-top: 32px; padding-top: 26px; }
            .stats-grid    { grid-template-columns: repeat(2, 1fr); gap: 8px; }
            .stat-card     { padding: 12px 6px; }

            .main-content { min-height: auto; padding: 12px 14px 40px; }
        }

        /* ============================================================
           TRES PETIT ECRAN (max 360px)
        ============================================================ */
        @media (max-width: 360px) {
            .logo-ring { width: 138px; height: 138px; }
            .logo-img  { width: 100px; height: 100px; }
            .countries-row { max-width: 100%; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
</head>
<body>

    <!-- FILIGRANE BLUE SKY -->
    <div class="watermark" aria-hidden="true">
        <svg viewBox="0 0 900 420" fill="none" xmlns="http://www.w3.org/2000/svg">
            <text x="50%" y="54%" dominant-baseline="middle" text-anchor="middle"
                  font-family="Inter, Arial Black, sans-serif"
                  font-size="310" font-weight="900" letter-spacing="4" fill="white">BS</text>
            <circle cx="450" cy="210" r="340" stroke="white" stroke-width="2"  fill="none" opacity="0.5"/>
            <circle cx="450" cy="210" r="270" stroke="white" stroke-width="1"  fill="none" opacity="0.3"/>
            <circle cx="450" cy="210" r="200" stroke="white" stroke-width="1"  fill="none" opacity="0.2"/>
            <!-- nuage gauche -->
            <ellipse cx="115" cy="85"  rx="78" ry="37" stroke="white" stroke-width="2" fill="none" opacity="0.45"/>
            <ellipse cx="172" cy="73"  rx="52" ry="28" stroke="white" stroke-width="2" fill="none" opacity="0.45"/>
            <!-- nuage droit -->
            <ellipse cx="786" cy="335" rx="86" ry="42" stroke="white" stroke-width="2" fill="none" opacity="0.45"/>
            <ellipse cx="848" cy="321" rx="58" ry="30" stroke="white" stroke-width="2" fill="none" opacity="0.45"/>
            <!-- étoiles -->
            <circle cx="58"  cy="310" r="3"   fill="white" opacity="0.45"/>
            <circle cx="96"  cy="266" r="2"   fill="white" opacity="0.3"/>
            <circle cx="848" cy="76"  r="3"   fill="white" opacity="0.45"/>
            <circle cx="808" cy="124" r="2"   fill="white" opacity="0.3"/>
            <circle cx="450" cy="28"  r="2.5" fill="white" opacity="0.38"/>
            <circle cx="395" cy="388" r="2"   fill="white" opacity="0.3"/>
            <!-- lignes horizon -->
            <line x1="0"   y1="168" x2="190" y2="168" stroke="white" stroke-width="1" opacity="0.12"/>
            <line x1="710" y1="252" x2="900" y2="252" stroke="white" stroke-width="1" opacity="0.12"/>
        </svg>
    </div>

    <!-- PARTICULES -->
    <div class="particles" aria-hidden="true">
        @for($i = 0; $i < 12; $i++)
            <div class="particle" style="
                width:  {{ 6 + ($i * 9) % 28 }}px;
                height: {{ 6 + ($i * 9) % 28 }}px;
                left:   {{ ($i * 83) % 100 }}%;
                animation-duration: {{ 11 + ($i * 2) % 12 }}s;
                animation-delay:    -{{ ($i * 1.7) % 10 }}s;
            "></div>
        @endfor
    </div>

    <!-- NAVIGATION -->
    <header class="nav-header">
        <a href="#" class="nav-brand">
            <img src="{{ asset('images/logo.png') }}" alt="Blue Sky">
            <div>
                <div class="nav-brand-text">BLUE SKY</div>
                <div class="nav-brand-sub">Money Transfer</div>
            </div>
        </a>

        @if (Route::has('login'))
            <nav class="nav-links">
                @auth
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                @else
                    <a href="{{ route('login') }}">Sign In</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="nav-cta">Create Account</a>
                    @endif
                @endauth
            </nav>
        @endif
    </header>

    <!-- CONTENU PRINCIPAL -->
    <main class="main-content">

        <!-- Logo agrandi avec halo -->
        <div class="logo-wrap">
            <div class="logo-ring">
                <img src="{{ asset('images/logo.png') }}" alt="Blue Sky Logo" class="logo-img">
            </div>
        </div>

        <!-- Titre principal GRAND -->
        <div class="hero-title">
            <span class="title-blue-sky">BLUE SKY</span>
            <span class="title-money">Money Transfert</span>
        </div>

        <!-- Subtitle -->
        <p class="hero-sub">
            Fast, secure and reliable money transfers across Africa.
            Your money arrives at its destination within minutes.
        </p>

        <!-- Countries covered — driven by DB via $activeCountries (AppServiceProvider) -->
        @php
        $locale = app()->getLocale();
        $frNames = [
            'CM' => 'Cameroun',         'CI' => "Côte d'Ivoire",   'SN' => 'Sénégal',
            'ML' => 'Mali',             'BF' => 'Burkina Faso',    'GN' => 'Guinée',
            'CD' => 'Congo RDC',        'CG' => 'Congo',           'GA' => 'Gabon',
            'TG' => 'Togo',             'BJ' => 'Bénin',           'NE' => 'Niger',
            'TD' => 'Tchad',            'CF' => 'Centrafrique',    'GQ' => 'Guinée Éq.',
            'NG' => 'Nigéria',          'GH' => 'Ghana',           'MA' => 'Maroc',
            'DZ' => 'Algérie',          'TN' => 'Tunisie',         'EG' => 'Égypte',
            'MR' => 'Mauritanie',       'GM' => 'Gambie',          'GW' => 'Guinée-Bissau',
            'SL' => 'Sierra Leone',     'LR' => 'Libéria',         'CV' => 'Cap-Vert',
            'KE' => 'Kenya',            'TZ' => 'Tanzanie',        'UG' => 'Ouganda',
            'RW' => 'Rwanda',           'BI' => 'Burundi',         'ET' => 'Éthiopie',
            'SD' => 'Soudan',           'SS' => 'Soudan du Sud',   'MZ' => 'Mozambique',
            'AO' => 'Angola',           'ZM' => 'Zambie',          'ZW' => 'Zimbabwe',
            'MG' => 'Madagascar',       'MU' => 'Maurice',         'SC' => 'Seychelles',
            'ST' => 'São Tomé-et-Príncipe',
        ];
        $enNames = [
            'CM' => 'Cameroon',         'CI' => 'Ivory Coast',     'SN' => 'Senegal',
            'ML' => 'Mali',             'BF' => 'Burkina Faso',    'GN' => 'Guinea',
            'CD' => 'DR Congo',         'CG' => 'Congo',           'GA' => 'Gabon',
            'TG' => 'Togo',             'BJ' => 'Benin',           'NE' => 'Niger',
            'TD' => 'Chad',             'CF' => 'Cent. Africa',    'GQ' => 'Eq. Guinea',
            'NG' => 'Nigeria',          'GH' => 'Ghana',           'MA' => 'Morocco',
            'DZ' => 'Algeria',          'TN' => 'Tunisia',         'EG' => 'Egypt',
            'MR' => 'Mauritania',       'GM' => 'Gambia',          'GW' => 'Guinea-Bissau',
            'SL' => 'Sierra Leone',     'LR' => 'Liberia',         'CV' => 'Cape Verde',
            'KE' => 'Kenya',            'TZ' => 'Tanzania',        'UG' => 'Uganda',
            'RW' => 'Rwanda',           'BI' => 'Burundi',         'ET' => 'Ethiopia',
            'SD' => 'Sudan',            'SS' => 'South Sudan',     'MZ' => 'Mozambique',
            'AO' => 'Angola',           'ZM' => 'Zambia',          'ZW' => 'Zimbabwe',
            'MG' => 'Madagascar',       'MU' => 'Mauritius',       'SC' => 'Seychelles',
            'ST' => 'São Tomé & Príncipe',
        ];
        @endphp
        <div class="countries-row" id="countries-row">
            @foreach($activeCountries as $c)
                @php
                    $nameEn = $enNames[$c->code] ?? $c->name;
                    $nameFr = $frNames[$c->code] ?? $c->name;
                @endphp
                <span class="country-badge"
                      data-en="{{ $nameEn }}"
                      data-fr="{{ $nameFr }}">
                    {{ $c->flag_emoji }}&nbsp;<span class="badge-name">{{ $locale === 'fr' ? $nameFr : $nameEn }}</span>
                </span>
            @endforeach
        </div>

        <!-- Action buttons -->
        <div class="cta-group">
            @auth
                <a href="{{ url('/dashboard') }}" class="btn btn-solid">
                    🚀 Go to Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-solid">
                    🔐 Sign In
                </a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-ghost">
                        ✨ Create Account
                    </a>
                @endif
            @endauth
        </div>

        <!-- Stats -->
        <div class="stats-section">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">{{ $activeCountries->count() }}+</div>
                    <div class="stat-label">{{ $locale === 'fr' ? 'Pays' : 'Countries' }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">24/7</div>
                    <div class="stat-label">Availability</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">100%</div>
                    <div class="stat-label">Secure</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">&lt;5min</div>
                    <div class="stat-label">Transfer time</div>
                </div>
            </div>
        </div>

    </main>

    <footer class="page-footer">
        &copy; {{ date('Y') }} Blue Sky Money Transfer — All rights reserved
    </footer>

<script>
(function () {
    const badges = document.querySelectorAll('#countries-row .country-badge');
    if (!badges.length) return;

    // Start from current app locale so the cycle is coherent
    let showFr = {{ app()->getLocale() === 'fr' ? 'true' : 'false' }};

    function switchLang() {
        showFr = !showFr;
        badges.forEach(b => {
            const nameEl = b.querySelector('.badge-name');
            // fade out
            nameEl.classList.remove('lang-in');
            nameEl.classList.add('lang-out');
            setTimeout(() => {
                nameEl.textContent = showFr ? b.dataset.fr : b.dataset.en;
                nameEl.classList.remove('lang-out');
                nameEl.classList.add('lang-in');
                // remove animation class after it completes
                setTimeout(() => nameEl.classList.remove('lang-in'), 300);
            }, 280);
        });
    }

    // Start cycling: first switch after 3s, then every 3s
    setInterval(switchLang, 3000);
})();
</script>
</body>
</html>
