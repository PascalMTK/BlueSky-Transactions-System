<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('app.create_account') }} — BLUESKY Transactions</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <link href="{{ asset('css/bluesky.css') }}" rel="stylesheet">
    <script>(function(){ const t=localStorage.getItem('bluesky-theme')||'light'; document.documentElement.setAttribute('data-theme',t); })();</script>
    <style>
        /* Background — animated fintech gradient */
        .auth-page {
            background:
                radial-gradient(ellipse at 18% 35%, rgba(14,165,233,0.28) 0%, transparent 52%),
                radial-gradient(ellipse at 82% 68%, rgba(16,185,129,0.18) 0%, transparent 48%),
                radial-gradient(ellipse at 55% 88%, rgba(99,102,241,0.14) 0%, transparent 42%),
                radial-gradient(ellipse at 75% 12%, rgba(14,165,233,0.16) 0%, transparent 40%),
                linear-gradient(145deg, #040e1c 0%, #071b36 38%, #0c2d5c 68%, #030c18 100%);
            background-attachment: fixed;
            animation: bgPulse 14s ease-in-out infinite alternate;
        }
        .auth-page::before {
            background: transparent !important;
            animation: none !important;
        }
        @keyframes bgPulse {
            0%   { filter: brightness(1) saturate(1); }
            50%  { filter: brightness(1.06) saturate(1.15); }
            100% { filter: brightness(0.96) saturate(1.05); }
        }
        /* dot-grid pattern overlay */
        .auth-page::after {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                radial-gradient(circle, rgba(14,165,233,0.22) 1px, transparent 1px);
            background-size: 36px 36px;
            pointer-events: none;
            z-index: 0;
        }
        .auth-container { position: relative; z-index: 1; }

        /* Form side scrollable for long form */
        .auth-form-side {
            justify-content: flex-start !important;
            overflow-y: auto;
            max-height: 100vh;
            padding: 32px 42px !important;
        }

        /* Photo upload */
        .photo-zone {
            border: 2px dashed var(--border);
            border-radius: 14px;
            padding: 20px 16px;
            text-align: center;
            cursor: pointer;
            transition: all 0.25s;
            background: var(--bg-input);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .photo-zone:hover, .photo-zone.dragover {
            border-color: var(--sky-secondary);
            background: rgba(14,165,233,0.06);
        }
        .photo-zone input[type="file"] {
            position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%;
        }
        .photo-avatar {
            width: 64px; height: 64px; border-radius: 50%; flex-shrink: 0;
            background: linear-gradient(135deg, var(--sky-light), #DBEAFE);
            display: flex; align-items: center; justify-content: center; font-size: 28px;
            border: 2.5px solid var(--sky-secondary);
            overflow: hidden;
        }
        .photo-avatar img {
            width: 100%; height: 100%; object-fit: cover; display: none; border-radius: 50%;
        }
        .photo-text-main { font-weight: 700; font-size: 13.5px; color: var(--text-primary); }
        .photo-text-sub  { font-size: 11.5px; color: var(--text-muted); margin-top: 2px; }
        .photo-text-name { font-size: 12px; color: var(--sky-primary); font-weight: 600; margin-top: 4px; display: none; }

        /* Compact form rows */
        .form-row.cols-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .form-group { margin-bottom: 14px; }
        .form-group:last-child { margin-bottom: 0; }

        /* Logo + titre adaptatifs selon l'espace disponible */
        .auth-logo-img {
            width:  clamp(70px, 11vw, 220px) !important;
            height: clamp(70px, 11vw, 220px) !important;
        }
        .auth-logo-name {
            font-size:      clamp(18px, 3vw, 52px) !important;
            letter-spacing: clamp(2px, 0.5vw, 6px) !important;
        }
        .auth-logo-sub {
            font-size:      clamp(8px, 0.85vw, 15px) !important;
            letter-spacing: clamp(3px, 0.7vw, 8px) !important;
        }

        /* Register visual — proportional layout */
        .reg-visual-center {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: clamp(14px, 2.2vh, 28px);
            padding: clamp(12px, 2.5vh, 32px) 28px;
            position: relative;
            z-index: 2;
        }
        /* Text block */
        .reg-text-block { text-align: center; width: 100%; }
        .reg-hero-title {
            font-size: clamp(20px, 2.4vw, 30px);
            font-weight: 800;
            line-height: 1.22;
            letter-spacing: -0.01em;
            background: linear-gradient(135deg, #ffffff 0%, #bae6fd 55%, #6ee7b7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: clamp(6px, 1vh, 10px);
        }
        .reg-hero-desc {
            font-size: clamp(11.5px, 1vw, 13px);
            color: rgba(255,255,255,0.62);
            line-height: 1.6;
            max-width: 300px;
            margin: 0 auto;
        }
        /* Stats row */
        .reg-stats-row {
            display: flex;
            gap: clamp(8px, 1vw, 14px);
            justify-content: center;
        }
        .reg-stat-badge {
            text-align: center;
            padding: clamp(8px, 1.2vh, 12px) clamp(12px, 1.5vw, 20px);
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.14);
            border-radius: 12px;
            backdrop-filter: blur(4px);
            min-width: 68px;
        }
        .reg-stat-num { font-size: clamp(18px, 2vw, 22px); font-weight: 800; line-height: 1; }
        .reg-stat-lbl { font-size: 10px; color: rgba(255,255,255,0.58); margin-top: 3px; letter-spacing: 0.03em; }

        /* SVG illustration container */
        .reg-svg-wrap {
            width: clamp(160px, 22vw, 220px);
            flex-shrink: 0;
        }

        /* Feature list */
        .feat-list {
            display: flex;
            flex-direction: column;
            gap: clamp(6px, 0.9vh, 10px);
            width: 100%;
            max-width: 260px;
        }
        .feat-item {
            display: flex; align-items: center; gap: 10px;
            color: rgba(255,255,255,0.85); font-size: clamp(11.5px, 1vw, 13px);
        }
        .feat-check {
            width: 20px; height: 20px; border-radius: 50%; flex-shrink: 0;
            background: rgba(16,185,129,0.22); border: 1px solid rgba(16,185,129,0.45);
            display: flex; align-items: center; justify-content: center;
            font-size: 10px; color: #6EE7B7;
        }

        /* Pending notice box */
        .pending-notice {
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-left: 3px solid #F59E0B;
            border-radius: 8px;
            padding: 10px 13px;
            font-size: 12px; color: var(--text-secondary);
            margin-bottom: 16px;
        }

        @media (max-width: 768px) {
            .form-row.cols-2 { grid-template-columns: 1fr; }
            .auth-form-side { padding: 24px 22px !important; }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-10px); }
        }
    </style>
</head>
<body>
<div class="auth-page">
    <div class="auth-container" style="max-width:1020px;">

        {{-- ═══════════════ VISUAL SIDE ═══════════════ --}}
        <div class="auth-visual">
            {{-- Logo --}}
            <div class="auth-logo">
                <img src="{{ asset('images/logo.png') }}" alt="Blue Sky" class="auth-logo-img">
                <div>
                    <div class="auth-logo-name">BLUESKY</div>
                    <div class="auth-logo-sub">TRANSACTIONS</div>
                </div>
            </div>

            {{-- Centre --}}
            <div class="reg-visual-center">

                {{-- Text block --}}
                <div class="reg-text-block">
                    <div class="reg-hero-title">{{ __('app.join_network') }}</div>
                    <div class="reg-hero-desc">
                        {{ __('app.hero_desc', ['count' => $activeCountries->count()]) }}
                    </div>
                </div>

                {{-- Stat badges --}}
                @php $locale = app()->getLocale(); @endphp
                <div class="reg-stats-row">
                    <div class="reg-stat-badge">
                        <div class="reg-stat-num" style="color:#38bdf8;">{{ $activeCountries->count() }}+</div>
                        <div class="reg-stat-lbl">{{ $locale === 'fr' ? 'Pays' : 'Countries' }}</div>
                    </div>
                    <div class="reg-stat-badge">
                        <div class="reg-stat-num" style="color:#6ee7b7;">24h</div>
                        <div class="reg-stat-lbl">{{ $locale === 'fr' ? 'Activation' : 'Activation' }}</div>
                    </div>
                    <div class="reg-stat-badge">
                        <div class="reg-stat-num" style="color:#fbbf24;">100%</div>
                        <div class="reg-stat-lbl">{{ $locale === 'fr' ? 'Gratuit' : 'Free' }}</div>
                    </div>
                </div>

                {{-- Transfer illustration SVG --}}
                <div class="reg-svg-wrap" style="animation:float 5s ease-in-out infinite;">
                <svg viewBox="0 0 260 240" xmlns="http://www.w3.org/2000/svg" style="width:100%;height:auto;display:block;">
                  <defs>
                    <radialGradient id="glowBg" cx="50%" cy="50%" r="50%">
                      <stop offset="0%" stop-color="#0ea5e9" stop-opacity="0.18"/>
                      <stop offset="100%" stop-color="#0ea5e9" stop-opacity="0"/>
                    </radialGradient>
                    <linearGradient id="phoneGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                      <stop offset="0%" stop-color="#1e3a5f"/>
                      <stop offset="100%" stop-color="#0c2d5c"/>
                    </linearGradient>
                    <linearGradient id="screenGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                      <stop offset="0%" stop-color="#0ea5e9"/>
                      <stop offset="100%" stop-color="#0369a1"/>
                    </linearGradient>
                    <linearGradient id="btnGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                      <stop offset="0%" stop-color="#10b981"/>
                      <stop offset="100%" stop-color="#059669"/>
                    </linearGradient>
                    <filter id="glow">
                      <feGaussianBlur stdDeviation="3" result="blur"/>
                      <feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge>
                    </filter>
                    <filter id="softglow">
                      <feGaussianBlur stdDeviation="5" result="blur"/>
                      <feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge>
                    </filter>
                  </defs>

                  {{-- Glow background --}}
                  <ellipse cx="130" cy="120" rx="100" ry="90" fill="url(#glowBg)"/>

                  {{-- Africa silhouette (simplified) --}}
                  <path d="M130,28 C118,28 108,32 102,40 C96,48 94,56 92,64 C88,72 80,74 76,82 C72,90 72,100 76,108 C78,114 74,120 72,128 C68,138 66,148 70,158 C74,168 82,172 88,178 C96,186 100,196 108,202 C116,208 126,210 130,210 C134,210 144,208 152,202 C160,196 164,186 172,178 C178,172 186,168 190,158 C194,148 192,138 188,128 C186,120 182,114 184,108 C188,100 188,90 184,82 C180,74 172,72 168,64 C166,56 164,48 158,40 C152,32 142,28 130,28Z"
                        fill="rgba(14,165,233,0.1)" stroke="rgba(14,165,233,0.35)" stroke-width="1.2"/>

                  {{-- Connection dots on Africa --}}
                  <circle cx="108" cy="100" r="4" fill="#0ea5e9" filter="url(#glow)" opacity="0.9">
                    <animate attributeName="opacity" values="0.9;0.4;0.9" dur="2.1s" repeatCount="indefinite"/>
                  </circle>
                  <circle cx="155" cy="118" r="4" fill="#0ea5e9" filter="url(#glow)" opacity="0.9">
                    <animate attributeName="opacity" values="0.9;0.4;0.9" dur="1.8s" begin="0.5s" repeatCount="indefinite"/>
                  </circle>
                  <circle cx="125" cy="145" r="4" fill="#10b981" filter="url(#glow)" opacity="0.9">
                    <animate attributeName="opacity" values="0.9;0.3;0.9" dur="2.4s" begin="0.9s" repeatCount="indefinite"/>
                  </circle>
                  <circle cx="140" cy="80" r="3.5" fill="#38bdf8" filter="url(#glow)" opacity="0.8">
                    <animate attributeName="opacity" values="0.8;0.3;0.8" dur="2.8s" begin="1.2s" repeatCount="indefinite"/>
                  </circle>
                  <circle cx="118" cy="168" r="3" fill="#0ea5e9" filter="url(#glow)" opacity="0.8">
                    <animate attributeName="opacity" values="0.8;0.3;0.8" dur="2s" begin="0.3s" repeatCount="indefinite"/>
                  </circle>

                  {{-- Connection lines --}}
                  <line x1="108" y1="100" x2="155" y2="118" stroke="rgba(14,165,233,0.4)" stroke-width="0.8" stroke-dasharray="4,3">
                    <animate attributeName="stroke-opacity" values="0.4;0.8;0.4" dur="2s" repeatCount="indefinite"/>
                  </line>
                  <line x1="155" y1="118" x2="125" y2="145" stroke="rgba(16,185,129,0.4)" stroke-width="0.8" stroke-dasharray="4,3">
                    <animate attributeName="stroke-opacity" values="0.4;0.8;0.4" dur="2.2s" begin="0.4s" repeatCount="indefinite"/>
                  </line>
                  <line x1="125" y1="145" x2="118" y2="168" stroke="rgba(14,165,233,0.4)" stroke-width="0.8" stroke-dasharray="4,3">
                    <animate attributeName="stroke-opacity" values="0.4;0.8;0.4" dur="1.9s" begin="0.8s" repeatCount="indefinite"/>
                  </line>
                  <line x1="140" y1="80" x2="155" y2="118" stroke="rgba(56,189,248,0.35)" stroke-width="0.8" stroke-dasharray="4,3">
                    <animate attributeName="stroke-opacity" values="0.35;0.75;0.35" dur="2.5s" begin="0.2s" repeatCount="indefinite"/>
                  </line>

                  {{-- Phone body --}}
                  <rect x="90" y="62" width="80" height="138" rx="14" ry="14" fill="url(#phoneGrad)" stroke="rgba(14,165,233,0.6)" stroke-width="1.5" filter="url(#softglow)"/>
                  {{-- Screen --}}
                  <rect x="96" y="72" width="68" height="110" rx="8" ry="8" fill="#0a1628"/>
                  {{-- Screen header --}}
                  <rect x="96" y="72" width="68" height="28" rx="8" ry="0" fill="url(#screenGrad)"/>
                  <rect x="96" y="86" width="68" height="14" rx="0" ry="0" fill="url(#screenGrad)"/>
                  {{-- Notch --}}
                  <rect x="120" y="68" width="20" height="6" rx="3" fill="#0a1628"/>
                  {{-- Screen title --}}
                  <text x="130" y="91" text-anchor="middle" fill="white" font-size="7.5" font-family="Arial,sans-serif" font-weight="bold">TRANSFER</text>

                  {{-- Amount display --}}
                  <rect x="102" y="106" width="56" height="22" rx="5" fill="rgba(14,165,233,0.15)" stroke="rgba(14,165,233,0.3)" stroke-width="0.8"/>
                  <text x="130" y="121" text-anchor="middle" fill="#38bdf8" font-size="11" font-family="Arial,sans-serif" font-weight="bold">500 USD</text>

                  {{-- Arrow icon --}}
                  <text x="130" y="144" text-anchor="middle" fill="rgba(255,255,255,0.5)" font-size="9" font-family="Arial,sans-serif">to · recipient</text>

                  {{-- Send button --}}
                  <rect x="106" y="152" width="48" height="18" rx="9" fill="url(#btnGrad)"/>
                  <text x="130" y="164" text-anchor="middle" fill="white" font-size="8" font-family="Arial,sans-serif" font-weight="bold">SEND →</text>

                  {{-- Bottom bar --}}
                  <rect x="118" y="194" width="24" height="3" rx="1.5" fill="rgba(255,255,255,0.25)"/>

                  {{-- Floating coins --}}
                  <circle cx="72" cy="80" r="11" fill="rgba(251,191,36,0.15)" stroke="rgba(251,191,36,0.6)" stroke-width="1.2" filter="url(#glow)">
                    <animateTransform attributeName="transform" type="translate" values="0,0;0,-5;0,0" dur="3s" repeatCount="indefinite"/>
                  </circle>
                  <text x="72" y="84" text-anchor="middle" fill="#fbbf24" font-size="10" font-family="Arial,sans-serif" font-weight="bold">
                    <animateTransform attributeName="transform" type="translate" values="0,0;0,-5;0,0" dur="3s" repeatCount="indefinite"/>
                    $
                  </text>

                  <circle cx="193" cy="75" r="9" fill="rgba(251,191,36,0.15)" stroke="rgba(251,191,36,0.5)" stroke-width="1.2" filter="url(#glow)">
                    <animateTransform attributeName="transform" type="translate" values="0,0;0,-6;0,0" dur="3.5s" begin="0.7s" repeatCount="indefinite"/>
                  </circle>
                  <text x="193" y="79" text-anchor="middle" fill="#fbbf24" font-size="9" font-family="Arial,sans-serif" font-weight="bold">
                    <animateTransform attributeName="transform" type="translate" values="0,0;0,-6;0,0" dur="3.5s" begin="0.7s" repeatCount="indefinite"/>
                    €
                  </text>

                  <circle cx="56" cy="158" r="8" fill="rgba(16,185,129,0.15)" stroke="rgba(16,185,129,0.55)" stroke-width="1.2" filter="url(#glow)">
                    <animateTransform attributeName="transform" type="translate" values="0,0;0,-4;0,0" dur="2.8s" begin="1.2s" repeatCount="indefinite"/>
                  </circle>
                  <text x="56" y="162" text-anchor="middle" fill="#10b981" font-size="9" font-family="Arial,sans-serif" font-weight="bold">
                    <animateTransform attributeName="transform" type="translate" values="0,0;0,-4;0,0" dur="2.8s" begin="1.2s" repeatCount="indefinite"/>
                    ₣
                  </text>

                  {{-- Speed lines --}}
                  <line x1="38" y1="110" x2="82" y2="118" stroke="rgba(14,165,233,0.3)" stroke-width="1" stroke-dasharray="3,4">
                    <animate attributeName="stroke-dashoffset" values="0;-14" dur="1.2s" repeatCount="indefinite"/>
                  </line>
                  <line x1="42" y1="120" x2="84" y2="124" stroke="rgba(14,165,233,0.2)" stroke-width="0.8" stroke-dasharray="3,4">
                    <animate attributeName="stroke-dashoffset" values="0;-14" dur="1.4s" begin="0.2s" repeatCount="indefinite"/>
                  </line>
                  <line x1="178" y1="115" x2="220" y2="108" stroke="rgba(16,185,129,0.3)" stroke-width="1" stroke-dasharray="3,4">
                    <animate attributeName="stroke-dashoffset" values="0;14" dur="1.3s" repeatCount="indefinite"/>
                  </line>
                </svg>
                </div>

                <div class="feat-list">
                    @foreach([
                        ['app.free_registration', '✓', '#6ee7b7'],
                        ['app.activated_24h',      '✓', '#6ee7b7'],
                        ['app.personal_dashboard', '✓', '#6ee7b7'],
                        ['app.realtime_stats',     '✓', '#6ee7b7'],
                    ] as [$key, $icon, $color])
                    <div class="feat-item">
                        <span class="feat-check">{{ $icon }}</span>
                        <span>{{ __($key) }}</span>
                    </div>
                    @endforeach
                </div>

            </div>

            {{-- Country chips --}}
            @php
            $regLocale = app()->getLocale();
            $regEnNames = ['CD'=>'DR Congo','CG'=>'Congo','CM'=>'Cameroon','CI'=>"Côte d'Ivoire",'SN'=>'Senegal','ML'=>'Mali','BF'=>'Burkina Faso','GN'=>'Guinea','TG'=>'Togo','BJ'=>'Benin','NE'=>'Niger','TD'=>'Chad','GA'=>'Gabon','GQ'=>'Eq. Guinea','CF'=>'C.A.R.','RW'=>'Rwanda','BI'=>'Burundi','KE'=>'Kenya','TZ'=>'Tanzania','UG'=>'Uganda','AO'=>'Angola','MZ'=>'Mozambique','ZM'=>'Zambia','ZW'=>'Zimbabwe','MW'=>'Malawi','ZA'=>'South Africa','MG'=>'Madagascar'];
            $regFrNames = ['CD'=>'RD Congo','CG'=>'Congo','CM'=>'Cameroun','CI'=>"Côte d'Ivoire",'SN'=>'Sénégal','ML'=>'Mali','BF'=>'Burkina Faso','GN'=>'Guinée','TG'=>'Togo','BJ'=>'Bénin','NE'=>'Niger','TD'=>'Tchad','GA'=>'Gabon','GQ'=>'Guinée Éq.','CF'=>'RCA','RW'=>'Rwanda','BI'=>'Burundi','KE'=>'Kenya','TZ'=>'Tanzanie','UG'=>'Ouganda','AO'=>'Angola','MZ'=>'Mozambique','ZM'=>'Zambie','ZW'=>'Zimbabwe','MW'=>'Malawi','ZA'=>'Afrique du Sud','MG'=>'Madagascar'];
            @endphp
            <div class="auth-countries">
                @foreach($activeCountries as $c)
                    @php $chipName = $regLocale === 'fr' ? ($regFrNames[$c->code] ?? $c->name) : ($regEnNames[$c->code] ?? $c->name); @endphp
                    <span class="auth-country-badge">{{ $c->flag_emoji }} {{ $chipName }}</span>
                @endforeach
            </div>
        </div>

        {{-- ═══════════════ FORM SIDE ═══════════════ --}}
        <div class="auth-form-side">

            {{-- Top controls --}}
            <div style="display:flex; justify-content:flex-end; gap:8px; margin-bottom:20px;">
                <div class="lang-switcher">
                    <a href="{{ route('lang.switch', 'fr') }}" class="lang-btn {{ app()->getLocale() === 'fr' ? 'active' : '' }}">🇫🇷 FR</a>
                    <div class="lang-divider"></div>
                    <a href="{{ route('lang.switch', 'en') }}" class="lang-btn {{ app()->getLocale() === 'en' ? 'active' : '' }}">🇬🇧 EN</a>
                </div>
                <button class="theme-toggle" id="themeToggle" onclick="ThemeManager.toggle()">🌙</button>
            </div>

            {{-- Title --}}
            <div class="auth-form-title">{{ __('app.create_account') }} 🚀</div>
            <div class="auth-form-sub">{{ __('app.register_subtitle') }}</div>

            {{-- Errors --}}
            @if($errors->any())
                <div class="alert alert-danger" style="margin-bottom:16px;">
                    @foreach($errors->all() as $error)<div>❌ {{ $error }}</div>@endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register.post') }}" enctype="multipart/form-data">
                @csrf

                {{-- Photo --}}
                <div class="form-group">
                    <label class="form-label">{{ __('app.current_photo') }}</label>
                    <div class="photo-zone" id="photoZone"
                         ondragover="handleDragOver(event)"
                         ondragleave="handleDragLeave(event)"
                         ondrop="handleDrop(event)">
                        <input type="file" name="profile_photo" id="photoInput"
                               accept="image/jpeg,image/png,image/jpg,image/webp"
                               onchange="previewPhoto(this)">
                        <div class="photo-avatar" id="photoAvatar">
                            <span id="photoEmoji">📸</span>
                            <img id="photoPreviewImg" src="" alt="">
                        </div>
                        <div>
                            <div class="photo-text-main">{{ __('app.click_drag_photo') }}</div>
                            <div class="photo-text-sub">{{ __('app.photo_formats') }}</div>
                            <div class="photo-text-name" id="photoName"></div>
                        </div>
                    </div>
                    @error('profile_photo')<div class="invalid-feedback" style="display:block">{{ $message }}</div>@enderror
                </div>

                {{-- Name + Phone --}}
                <div class="form-row cols-2">
                    <div class="form-group">
                        <label class="form-label">{{ __('app.full_name') }} <span class="required">*</span></label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" placeholder="John Doe" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __('app.phone') }} <span class="required">*</span></label>
                        <input type="tel" name="phone"
                               class="form-control @error('phone') is-invalid @enderror"
                               value="{{ old('phone') }}" placeholder="+243 xxx xxx xxx" required>
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Email --}}
                <div class="form-group">
                    <label class="form-label">{{ __('app.email') }} <span class="required">*</span></label>
                    <input type="email" name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" placeholder="your@email.com" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Country --}}
                <div class="form-group">
                    <label class="form-label">{{ __('app.operation_country') }} <span class="required">*</span></label>
                    <select name="country_id"
                            class="form-control @error('country_id') is-invalid @enderror" required>
                        <option value="">{{ __('app.select_country') }}</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                {{ $country->flag_emoji }} {{ $country->name }} ({{ $country->currency_code }})
                            </option>
                        @endforeach
                    </select>
                    @error('country_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Passwords --}}
                <div class="form-row cols-2">
                    <div class="form-group">
                        <label class="form-label">{{ __('app.password') }} <span class="required">*</span></label>
                        <input type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="{{ __('app.password_security') }}" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __('app.confirm_password') }} <span class="required">*</span></label>
                        <input type="password" name="password_confirmation"
                               class="form-control" placeholder="••••••••" required>
                    </div>
                </div>

                {{-- Address + ID --}}
                <div class="form-row cols-2">
                    <div class="form-group">
                        <label class="form-label">{{ __('app.address') }}</label>
                        <input type="text" name="address" class="form-control"
                               value="{{ old('address') }}" placeholder="Rue, Ville, Province">
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __('app.id_number') }}</label>
                        <input type="text" name="id_number" class="form-control"
                               value="{{ old('id_number') }}" placeholder="CNI, Passeport...">
                    </div>
                </div>

                {{-- Pending notice --}}
                <div class="pending-notice">
                    ⚠️ {{ __('app.pending_notice') }}
                </div>

                <button type="submit" class="btn btn-primary btn-xl" style="width:100%; justify-content:center;">
                    🚀 {{ __('app.register_btn') }}
                </button>
            </form>

            <div style="text-align:center; margin-top:14px; font-size:13px; color:var(--text-muted);">
                {{ __('app.already_account') }}
                <a href="{{ route('login') }}" style="color:var(--sky-primary); font-weight:700; text-decoration:none;">
                    {{ __('app.login_btn') }} →
                </a>
            </div>

        </div>{{-- /auth-form-side --}}
    </div>
</div>

<script src="{{ asset('js/bluesky.js') }}"></script>
<script>
function previewPhoto(input) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = (e) => {
        const img  = document.getElementById('photoPreviewImg');
        const emoji = document.getElementById('photoEmoji');
        img.src = e.target.result;
        img.style.display = 'block';
        if (emoji) emoji.style.display = 'none';
        const name = document.getElementById('photoName');
        name.textContent = '✅ ' + file.name;
        name.style.display = 'block';
        document.getElementById('photoZone').style.borderColor = 'var(--sky-secondary)';
    };
    reader.readAsDataURL(file);
}
function handleDragOver(e) {
    e.preventDefault();
    document.getElementById('photoZone').classList.add('dragover');
}
function handleDragLeave() {
    document.getElementById('photoZone').classList.remove('dragover');
}
function handleDrop(e) {
    e.preventDefault();
    document.getElementById('photoZone').classList.remove('dragover');
    const file = e.dataTransfer.files[0];
    if (file && file.type.startsWith('image/')) {
        const input = document.getElementById('photoInput');
        const dt = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;
        previewPhoto(input);
    }
}
</script>
</body>
</html>
