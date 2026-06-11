<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('app.login_btn') }} — BLUESKY Transactions</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <link href="{{ asset('css/bluesky.css') }}" rel="stylesheet">
    <script>
        (function(){
            const t = localStorage.getItem('bluesky-theme') || 'light';
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>
    <style>
        /* ── Base overrides ── */
        .auth-form-side {
            background: var(--bg-card);
            transition: background 0.3s;
            position: relative;
            overflow-x: hidden;
            overflow-y: auto;           /* scrollable on short screens */
        }
        [data-theme="dark"] .auth-container { border: 1px solid #1F2937; }

        /* ── Animated mesh gradient ── */
        @keyframes meshShift {
            0%   { background-position: 0% 50%,   100% 50%,   50%  0%; }
            33%  { background-position: 40% 80%,   60% 20%,   80% 60%; }
            66%  { background-position: 80% 20%,   20% 80%,   20% 40%; }
            100% { background-position: 0% 50%,   100% 50%,   50%  0%; }
        }
        .auth-page {
            background:
                radial-gradient(ellipse 70% 55% at 15% 25%, rgba(14,165,233,0.14) 0%, transparent 55%),
                radial-gradient(ellipse 60% 70% at 88% 78%, rgba(99,102,241,0.11) 0%, transparent 55%),
                radial-gradient(ellipse 80% 45% at 50% 105%, rgba(20,184,166,0.09) 0%, transparent 55%),
                linear-gradient(135deg, #060D1C 0%, #0a1d38 45%, #0b3260 100%);
            background-size: 250% 250%, 250% 250%, 250% 250%, 100% 100%;
            animation: meshShift 20s ease-in-out infinite;
        }
        [data-theme="dark"] .auth-page {
            background:
                radial-gradient(ellipse 70% 55% at 15% 25%, rgba(14,165,233,0.10) 0%, transparent 55%),
                radial-gradient(ellipse 60% 70% at 88% 78%, rgba(99,102,241,0.08) 0%, transparent 55%),
                linear-gradient(135deg, #030810 0%, #070F1E 45%, #050D1A 100%);
            background-size: 250% 250%, 250% 250%, 100% 100%;
            animation: meshShift 20s ease-in-out infinite;
        }

        /* ── Image gradient mask ── */
        .auth-img-masked {
            -webkit-mask-image: linear-gradient(to bottom, transparent 0%, black 20%, black 76%, transparent 100%);
            mask-image:         linear-gradient(to bottom, transparent 0%, black 20%, black 76%, transparent 100%);
        }

        /* ── Africa network SVG ── */
        .africa-map { position:absolute; bottom:-10px; right:-18px; width:210px; height:260px; pointer-events:none; }
        .africa-outline { fill:none; stroke:rgba(255,255,255,0.12); stroke-width:1.2; }
        .conn-line { stroke:rgba(14,165,233,0.45); stroke-width:1; stroke-dasharray:4 3; }
        @keyframes dashFlow { to { stroke-dashoffset:-28; } }
        .conn-line { animation: dashFlow 3s linear infinite; }
        .conn-line:nth-child(2) { animation-delay:-0.6s; }
        .conn-line:nth-child(3) { animation-delay:-1.2s; }
        .conn-line:nth-child(4) { animation-delay:-1.8s; }
        .conn-line:nth-child(5) { animation-delay:-2.4s; }
        .conn-line:nth-child(6) { animation-delay:-0.9s; }
        .conn-line:nth-child(7) { animation-delay:-1.5s; }
        @keyframes dotPulse { 0%,100%{r:3.2;opacity:1;} 50%{r:4.8;opacity:0.6;} }
        .country-dot { fill:#38BDF8; filter:drop-shadow(0 0 4px rgba(14,165,233,0.9)); }
        .country-dot.d1{animation:dotPulse 2.6s ease-in-out infinite 0.0s;}
        .country-dot.d2{animation:dotPulse 2.6s ease-in-out infinite 0.4s;}
        .country-dot.d3{animation:dotPulse 2.6s ease-in-out infinite 0.8s;}
        .country-dot.d4{animation:dotPulse 2.6s ease-in-out infinite 1.2s;}
        .country-dot.d5{animation:dotPulse 2.6s ease-in-out infinite 1.6s;}
        .country-dot.d6{animation:dotPulse 2.6s ease-in-out infinite 2.0s;}
        .country-dot.d7{animation:dotPulse 2.6s ease-in-out infinite 2.4s;}
        .country-dot.d8{animation:dotPulse 2.6s ease-in-out infinite 0.2s;}
        .country-label { font-size:6.5px; fill:rgba(255,255,255,0.65); font-family:monospace; font-weight:700; letter-spacing:0.5px; }
        .bg-dot { fill:rgba(255,255,255,0.18); }

        /* ── Glassmorphism feature card ── */
        .feature-card-glass {
            background: rgba(255,255,255,0.07);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.10);
            border-radius: 14px;
            padding: 16px 18px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.22), inset 0 1px 0 rgba(255,255,255,0.08);
        }

        /* ── Country chips scroll ── */
        .chips-scroll-wrap { overflow:hidden; position:relative; }
        .chips-scroll-track { display:flex; gap:7px; width:max-content; animation:chipsScroll 22s linear infinite; }
        .chips-scroll-track:hover { animation-play-state:paused; }
        @keyframes chipsScroll { 0%{transform:translateX(0);} 100%{transform:translateX(-50%);} }

        /* ══════════════════════════════════════════════════
           FLOATING LABEL INPUTS  — robust implementation
           Height: 58px, padding-top: 22px so text sits low,
           icon always at vertical center (29px from top).
           Label rests at center, floats to top-left on focus.
        ══════════════════════════════════════════════════ */
        .fi-wrap {
            position: relative;
            margin-bottom: 0;   /* spacing handled by .form-group */
        }

        /* The actual input */
        .fi-input {
            width: 100%;
            height: 58px;
            padding: 22px 14px 6px 44px !important;   /* top | right | bottom | left */
            font-size: 14px !important;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            background: var(--bg-input);
            color: var(--text-primary);
            font-family: inherit;
            box-sizing: border-box;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            /* Override bluesky.css .form-control shorthand padding */
        }
        /* Extra right room for password toggle */
        .fi-input.fi-has-toggle { padding-right: 46px !important; }

        .fi-input:focus {
            border-color: var(--sky-primary);
            box-shadow: 0 0 0 3px rgba(2,132,199,0.12);
            background: var(--bg-card);
        }
        .fi-input.is-invalid { border-color: var(--danger); }

        /* Left icon — always vertically centered in the 58px box */
        .fi-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 16px;
            line-height: 1;
            pointer-events: none;
            z-index: 1;
        }

        /* The floating label */
        .fi-label {
            position: absolute;
            left: 44px;
            top: 20px;               /* resting = ~center of 58px with 14px text */
            font-size: 14px;
            line-height: 1;
            color: var(--text-muted);
            pointer-events: none;
            transition: top 0.18s ease, font-size 0.18s ease, color 0.18s ease, font-weight 0.18s ease, letter-spacing 0.18s ease;
            white-space: nowrap;
        }

        /* Floated state: input has value OR is focused OR autofilled */
        .fi-input:focus        ~ .fi-label,
        .fi-input.has-value    ~ .fi-label,
        .fi-input:-webkit-autofill ~ .fi-label {
            top: 6px;
            font-size: 9.5px;
            color: var(--sky-primary);
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        /* Password toggle button */
        .fi-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            line-height: 1;
            padding: 4px;
            color: var(--text-muted);
            transition: transform 0.22s ease, opacity 0.18s;
            z-index: 2;
        }
        .fi-toggle:hover { opacity: 0.75; transform: translateY(-50%) scale(1.15); }

        /* Autofill: prevent browser yellow tint killing the label animation */
        .fi-input:-webkit-autofill {
            -webkit-box-shadow: 0 0 0 100px var(--bg-input) inset !important;
            -webkit-text-fill-color: var(--text-primary) !important;
            transition: background-color 5000s ease-in-out 0s;
        }
        .fi-input:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0 100px var(--bg-card) inset !important;
        }

        /* ── Magnetic button ── */
        #loginBtn {
            transition: transform 0.12s ease, box-shadow 0.3s ease, background 0.2s ease !important;
            will-change: transform;
        }
        #loginBtn:hover {
            box-shadow: 0 0 35px rgba(2,132,199,0.55), 0 8px 24px rgba(2,132,199,0.3) !important;
        }

        /* ── Top strip ── */
        .auth-top-strip {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--divider);
            position: relative;
            z-index: 1;
        }
        .ssl-badge {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 11px;
            font-weight: 700;
            color: #0369A1;
            background: rgba(2,132,199,0.07);
            padding: 6px 12px;
            border-radius: 20px;
            border: 1px solid rgba(2,132,199,0.18);
            white-space: nowrap;
        }
        .ssl-badge-text { display: inline; }   /* hidden on very small screens */

        /* ── Security notice ── */
        .security-notice { margin-top: 20px; padding: 14px 16px; background: #F0F9FF; border-radius: 12px; border: 1px solid #BAE6FD; transition: background 0.3s, border-color 0.3s; }
        .security-notice .sec-header { display:flex; align-items:center; gap:8px; margin-bottom:8px; }
        .security-notice .sec-title  { font-size:12px; font-weight:700; color:#0369A1; transition:color 0.3s; }
        .security-notice .sec-text   { font-size:12px; color:#475569; line-height:1.6; margin:0 0 10px; transition:color 0.3s; }
        .security-notice .sec-btn    { display:inline-flex; align-items:center; gap:5px; font-size:11px; font-weight:600; color:#0284C7; text-decoration:none; padding:5px 12px; border:1px solid #BAE6FD; border-radius:20px; background:#fff; transition:background 0.2s,border-color 0.3s,color 0.3s; }
        .security-notice .sec-btn:hover { background:#E0F2FE; }
        [data-theme="dark"] .security-notice          { background:#0C1A2E; border-color:#1E3A5F; }
        [data-theme="dark"] .security-notice .sec-title{ color:#38BDF8; }
        [data-theme="dark"] .security-notice .sec-text { color:#94A3B8; }
        [data-theme="dark"] .security-notice .sec-btn  { background:#0F2133; border-color:#1E3A5F; color:#38BDF8; }
        [data-theme="dark"] .security-notice .sec-btn:hover { background:#1a3a5c; }
        [data-theme="dark"] .ssl-badge { color:#38BDF8; background:rgba(14,165,233,0.10); border-color:rgba(14,165,233,0.25); }

        /* Autofill detection keyframes (animationstart trick) */
        @keyframes onAutoFillStart  { from {} to {} }
        @keyframes onAutoFillCancel { from {} to {} }
        .fi-input:-webkit-autofill { animation-name: onAutoFillStart;  animation-duration: 0.001s; }
        .fi-input:not(:-webkit-autofill) { animation-name: onAutoFillCancel; animation-duration: 0.001s; }

        /* ══════════════════════════════════════════════════
           RESPONSIVE OVERRIDES
        ══════════════════════════════════════════════════ */
        /* Tablet (≤1024px): single column, left panel hidden (handled by bluesky.css).
           Make the form-side fill viewport height properly. */
        @media (max-width: 1024px) {
            .auth-form-side {
                padding: 36px 48px;
                min-height: 100vh;
                justify-content: flex-start;
            }
        }

        /* Mobile (≤768px) */
        @media (max-width: 768px) {
            .auth-form-side { padding: 28px 24px; }
            .auth-top-strip { margin-bottom: 20px; }
        }

        /* Mobile (≤768px) — iOS prevents zoom below 16px */
        @media (max-width: 768px) {
            .fi-input { font-size: 16px !important; }
        }

        /* Small mobile (≤480px) */
        @media (max-width: 480px) {
            .auth-top-strip { margin-bottom: 16px; padding-bottom: 12px; }
            .ssl-badge-text { display: none; }
            .ssl-badge { padding: 6px 10px; gap: 0; }
            .auth-form-title { font-size: 21px !important; }
            .auth-form-sub  { font-size: 12.5px; margin-bottom: 20px; }
            .security-notice { padding: 12px 13px; }
            .fi-input { height: 54px !important; font-size: 16px !important; }
            .fi-label { top: 18px; font-size: 15px; }
            .fi-input:focus ~ .fi-label,
            .fi-input.has-value ~ .fi-label,
            .fi-input:-webkit-autofill ~ .fi-label { top: 5px; font-size: 9px; }
        }
    </style>
</head>
<body>

<!-- Animated background particles -->
<div style="position:fixed;inset:0;overflow:hidden;pointer-events:none;z-index:0;" aria-hidden="true">
    @for($i = 0; $i < 5; $i++)
        <div style="position:absolute;width:{{ rand(180,360) }}px;height:{{ rand(180,360) }}px;border-radius:50%;background:rgba(14,165,233,{{ 0.015 + ($i * 0.008) }});top:{{ rand(0,100) }}%;left:{{ rand(0,100) }}%;animation:float {{ 5 + $i }}s ease-in-out infinite;animation-delay:{{ $i * 0.7 }}s;"></div>
    @endfor
    <div style="position:absolute;top:3%;left:2%;animation:float 16s ease-in-out infinite;">
        <span style="display:block;font-size:220px;opacity:0.018;transform:rotate(-12deg);line-height:1;filter:grayscale(40%);">🌍</span>
    </div>
    <div style="position:absolute;bottom:4%;right:3%;animation:float 13s ease-in-out infinite 5s;">
        <span style="display:block;font-size:190px;opacity:0.015;transform:rotate(16deg);line-height:1;filter:grayscale(40%);">🌍</span>
    </div>
</div>

<div class="auth-page">
    <div class="auth-container">

        <!-- ===== LEFT VISUAL ===== -->
        <div class="auth-visual">

            <!-- ── Flag filigree watermarks ── -->
            <div aria-hidden="true" style="position:absolute;inset:0;overflow:hidden;pointer-events:none;user-select:none;z-index:0;">
                <span style="position:absolute;top:2%;right:-2%;font-size:112px;opacity:0.08;transform:rotate(18deg);display:block;line-height:1;">🇨🇩</span>
                <span style="position:absolute;top:43%;right:0%;font-size:98px;opacity:0.06;transform:rotate(9deg);display:block;line-height:1;">🇰🇪</span>
                <span style="position:absolute;top:28%;left:18%;font-size:66px;opacity:0.04;transform:rotate(-42deg);display:block;line-height:1;">🇲🇼</span>
                <div style="position:absolute;top:16%;left:-4%;animation:float 7s ease-in-out infinite;">
                    <span style="display:block;font-size:84px;opacity:0.05;transform:rotate(-26deg);line-height:1;">🇿🇲</span>
                </div>
                <div style="position:absolute;top:61%;left:2%;animation:float 9s ease-in-out infinite 1.5s;">
                    <span style="display:block;font-size:80px;opacity:0.05;transform:rotate(-17deg);line-height:1;">🇹🇿</span>
                </div>
            </div>

            <!-- ── Africa network map SVG ── -->
            <svg class="africa-map" viewBox="0 0 200 248" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <!-- Continent outline -->
                <path class="africa-outline" d="M 82,10 C 96,4 118,5 136,13 C 152,20 163,36 166,55 C 169,72 167,90 164,108 C 161,124 157,138 153,152 C 150,165 146,178 141,191 C 135,207 124,222 112,233 C 104,241 92,245 80,239 C 68,233 59,218 52,202 C 45,187 40,171 38,155 C 36,138 35,122 36,106 C 37,88 42,72 48,58 C 54,44 62,31 70,21 C 75,14 79,11 82,10 Z"/>
                <!-- Scattered background dots -->
                <circle class="bg-dot" cx="58"  cy="64"  r="1"/>
                <circle class="bg-dot" cx="140" cy="72"  r="1"/>
                <circle class="bg-dot" cx="72"  cy="112" r="1"/>
                <circle class="bg-dot" cx="155" cy="96"  r="1"/>
                <circle class="bg-dot" cx="95"  cy="170" r="1"/>
                <circle class="bg-dot" cx="50"  cy="140" r="1"/>
                <circle class="bg-dot" cx="160" cy="138" r="1"/>
                <circle class="bg-dot" cx="110" cy="100" r="1"/>
                <circle class="bg-dot" cx="66"  cy="92"  r="0.8"/>
                <circle class="bg-dot" cx="132" cy="118" r="0.8"/>
                <circle class="bg-dot" cx="78"  cy="198" r="0.8"/>
                <circle class="bg-dot" cx="148" cy="168" r="0.8"/>
                <!-- Connection lines CD→ZM, ZM→ZW, ZW→ZA, ZA→NA, ZM→MW, MW→TZ, TZ→KE, KE→TZ -->
                <line class="conn-line" x1="88" y1="148" x2="122" y2="182"/>
                <line class="conn-line" x1="122" y1="182" x2="132" y2="196"/>
                <line class="conn-line" x1="132" y1="196" x2="120" y2="220"/>
                <line class="conn-line" x1="120" y1="220" x2="90"  y2="208"/>
                <line class="conn-line" x1="122" y1="182" x2="140" y2="178"/>
                <line class="conn-line" x1="140" y1="178" x2="152" y2="150"/>
                <line class="conn-line" x1="152" y1="150" x2="155" y2="130"/>
                <line class="conn-line" x1="88"  y1="148" x2="122" y2="182"/>
                <!-- Country nodes — CD, ZM, TZ, KE, MW, ZW, ZA, NA -->
                <circle class="country-dot d1" cx="88"  cy="148" r="3.2"/>
                <circle class="country-dot d2" cx="122" cy="182" r="3.2"/>
                <circle class="country-dot d3" cx="152" cy="150" r="3.2"/>
                <circle class="country-dot d4" cx="155" cy="130" r="3.2"/>
                <circle class="country-dot d5" cx="140" cy="178" r="3.2"/>
                <circle class="country-dot d6" cx="132" cy="196" r="3.2"/>
                <circle class="country-dot d7" cx="120" cy="220" r="3.2"/>
                <circle class="country-dot d8" cx="90"  cy="208" r="3.2"/>
                <!-- Country labels -->
                <text class="country-label" x="74"  y="146">CD</text>
                <text class="country-label" x="125" y="180">ZM</text>
                <text class="country-label" x="155" y="148">TZ</text>
                <text class="country-label" x="158" y="128">KE</text>
                <text class="country-label" x="143" y="176">MW</text>
                <text class="country-label" x="135" y="194">ZW</text>
                <text class="country-label" x="106" y="231">ZA</text>
                <text class="country-label" x="76"  y="220">NA</text>
            </svg>

            <!-- Logo -->
            <div class="auth-logo" style="flex-direction:column;align-items:center;text-align:center;gap:18px;margin-bottom:56px;position:relative;z-index:1;">
                <img src="{{ asset('images/logo.png') }}" alt="Blue Sky" class="auth-logo-img">
                <div>
                    <div class="auth-logo-name">BLUESKY</div>
                    <div class="auth-logo-sub">TRANSACTIONS</div>
                </div>
            </div>

            <!-- Desk photo with gradient mask -->
            <div class="auth-img-masked" style="margin:0 -38px;flex:1;min-height:190px;position:relative;z-index:1;">
                <img src="{{ asset('images/blog-2.jpg') }}"
                     alt="Agent at work"
                     style="width:100%;height:100%;object-fit:cover;object-position:center;display:block;border-radius:16px;opacity:0.88;">
                <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(7,89,133,0.65) 0%,transparent 55%);border-radius:16px;"></div>
            </div>

            <!-- Feature highlights — glassmorphic card -->
            <div class="feature-card-glass" style="position:relative;z-index:1;margin-top:14px;">
                <div style="display:flex;flex-direction:column;gap:9px;">
                    @foreach([
                        ['📋', 'feat_record',    []],
                        ['⚡', 'feat_realtime',  []],
                        ['📊', 'feat_reports',   []],
                        ['🌍', 'feat_network',   ['count' => $activeCountries->count()]],
                        ['🛡️', 'feat_secure',   []],
                    ] as [$icon, $key, $params])
                    <div style="display:flex;align-items:center;gap:10px;font-size:12.5px;color:rgba(255,255,255,0.92);">
                        <span style="width:26px;height:26px;border-radius:7px;background:rgba(255,255,255,0.13);display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0;">{{ $icon }}</span>
                        {{ __('app.'.$key, $params) }}
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Countries — infinite horizontal scroll -->
            @php
            $loginLocale = app()->getLocale();
            $loginFrNames = [
                'CM' => 'Cameroun',       'CI' => "Côte d'Ivoire",  'SN' => 'Sénégal',
                'ML' => 'Mali',           'BF' => 'Burkina Faso',   'GN' => 'Guinée',
                'CD' => 'Congo RDC',      'CG' => 'Congo',          'GA' => 'Gabon',
                'TG' => 'Togo',           'BJ' => 'Bénin',          'NE' => 'Niger',
                'TD' => 'Tchad',          'CF' => 'Centrafrique',   'GQ' => 'Guinée Éq.',
                'NG' => 'Nigéria',        'GH' => 'Ghana',          'MA' => 'Maroc',
                'DZ' => 'Algérie',        'TN' => 'Tunisie',        'EG' => 'Égypte',
                'MR' => 'Mauritanie',     'GM' => 'Gambie',         'GW' => 'Guinée-Bissau',
                'SL' => 'Sierra Leone',   'LR' => 'Libéria',        'CV' => 'Cap-Vert',
                'KE' => 'Kenya',          'TZ' => 'Tanzanie',       'UG' => 'Ouganda',
                'RW' => 'Rwanda',         'BI' => 'Burundi',        'ET' => 'Éthiopie',
                'SD' => 'Soudan',         'SS' => 'Soudan du Sud',  'MZ' => 'Mozambique',
                'AO' => 'Angola',         'ZM' => 'Zambie',         'ZW' => 'Zimbabwe',
                'MG' => 'Madagascar',     'MU' => 'Maurice',        'SC' => 'Seychelles',
                'ST' => 'São Tomé-et-Príncipe',
            ];
            $loginEnNames = [
                'CM' => 'Cameroon',       'CI' => 'Ivory Coast',    'SN' => 'Senegal',
                'ML' => 'Mali',           'BF' => 'Burkina Faso',   'GN' => 'Guinea',
                'CD' => 'DR Congo',       'CG' => 'Congo',          'GA' => 'Gabon',
                'TG' => 'Togo',           'BJ' => 'Benin',          'NE' => 'Niger',
                'TD' => 'Chad',           'CF' => 'Cent. Africa',   'GQ' => 'Eq. Guinea',
                'NG' => 'Nigeria',        'GH' => 'Ghana',          'MA' => 'Morocco',
                'DZ' => 'Algeria',        'TN' => 'Tunisia',        'EG' => 'Egypt',
                'MR' => 'Mauritania',     'GM' => 'Gambia',         'GW' => 'Guinea-Bissau',
                'SL' => 'Sierra Leone',   'LR' => 'Liberia',        'CV' => 'Cape Verde',
                'KE' => 'Kenya',          'TZ' => 'Tanzania',       'UG' => 'Uganda',
                'RW' => 'Rwanda',         'BI' => 'Burundi',        'ET' => 'Ethiopia',
                'SD' => 'Sudan',          'SS' => 'South Sudan',    'MZ' => 'Mozambique',
                'AO' => 'Angola',         'ZM' => 'Zambia',         'ZW' => 'Zimbabwe',
                'MG' => 'Madagascar',     'MU' => 'Mauritius',      'SC' => 'Seychelles',
                'ST' => 'São Tomé & Príncipe',
            ];
            @endphp
            <div style="position:relative;z-index:1;">
                <div style="font-size:10px;color:rgba(255,255,255,0.38);margin-bottom:8px;letter-spacing:1.5px;text-transform:uppercase;">
                    {{ __('app.countries_covered') }}
                </div>
                <div class="chips-scroll-wrap">
                    <div class="chips-scroll-track">
                        @foreach($activeCountries->concat($activeCountries) as $country)
                            @php
                                $chipName = $loginLocale === 'fr'
                                    ? ($loginFrNames[$country->code] ?? $country->name)
                                    : ($loginEnNames[$country->code] ?? $country->name);
                            @endphp
                            <span class="auth-country-badge" style="white-space:nowrap;">{{ $country->flag_emoji }} {{ $chipName }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== RIGHT FORM ===== -->
        <div class="auth-form-side">

            <!-- Watermarks (form side) -->
            <div aria-hidden="true" style="position:absolute;inset:0;overflow:hidden;pointer-events:none;user-select:none;">
                <span style="position:absolute;top:-18px;right:-22px;font-size:155px;opacity:0.032;transform:rotate(22deg);display:block;line-height:1;filter:grayscale(30%);">🌍</span>
                <span style="position:absolute;bottom:-22px;left:-22px;font-size:138px;opacity:0.032;transform:rotate(-14deg);display:block;line-height:1;filter:grayscale(30%);">🇨🇩</span>
                <span style="position:absolute;top:38%;right:-16px;font-size:96px;opacity:0.022;transform:rotate(30deg);display:block;line-height:1;">🇰🇪</span>
            </div>

            <!-- Top strip -->
            <div class="auth-top-strip">
                <div class="ssl-badge">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    <span class="ssl-badge-text">{{ __('app.ssl_secured') }}</span>
                </div>
                <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
                    <div class="lang-switcher">
                        <a href="{{ route('lang.switch', 'fr') }}" class="lang-btn {{ app()->getLocale() === 'fr' ? 'active' : '' }}">🇫🇷 FR</a>
                        <div class="lang-divider"></div>
                        <a href="{{ route('lang.switch', 'en') }}" class="lang-btn {{ app()->getLocale() === 'en' ? 'active' : '' }}">🇬🇧 EN</a>
                    </div>
                    <button class="theme-toggle" id="themeToggle" onclick="ThemeManager.toggle()">🌙</button>
                </div>
            </div>

            <!-- Mobile logo -->
            <div class="auth-mobile-logo" style="display:none;flex-direction:column;align-items:center;margin-bottom:28px;text-align:center;">
                <img src="{{ asset('images/logo.png') }}" alt="Blue Sky" style="width:110px;height:110px;object-fit:contain;margin-bottom:10px;">
                <div style="font-size:22px;font-weight:900;color:var(--sky-primary);letter-spacing:2px;">BLUESKY</div>
                <div style="font-size:10px;color:var(--text-muted);letter-spacing:2.5px;text-transform:uppercase;">TRANSACTIONS</div>
            </div>

            <div class="auth-form-title" style="position:relative;z-index:1;">{{ __('app.welcome_back') }} 👋</div>
            <div class="auth-form-sub" style="position:relative;z-index:1;">{{ __('app.sign_in_subtitle') }}</div>

            @if(session('success'))
                <div class="alert alert-success" style="position:relative;z-index:1;">✅ {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger" style="position:relative;z-index:1;">❌ {{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" id="loginForm" style="position:relative;z-index:1;">
                @csrf

                <!-- Email -->
                <div class="form-group">
                    <div class="fi-wrap">
                        <span class="fi-icon">📧</span>
                        <input type="email" name="email" id="loginEmail"
                               class="fi-input @error('email') is-invalid @enderror"
                               value="{{ old('email') }}"
                               placeholder=" "
                               autocomplete="email" required>
                        <label class="fi-label" for="loginEmail">{{ __('app.email') }}</label>
                    </div>
                    @error('email')<div class="invalid-feedback" style="font-size:12px;color:var(--danger);margin-top:4px;">{{ $message }}</div>@enderror
                </div>

                <!-- Password -->
                <div class="form-group">
                    <div class="fi-wrap">
                        <span class="fi-icon">🔒</span>
                        <input type="password" name="password" id="pwdInput"
                               class="fi-input fi-has-toggle @error('password') is-invalid @enderror"
                               placeholder=" " required>
                        <label class="fi-label" for="pwdInput">{{ __('app.password') }}</label>
                        <button type="button" class="fi-toggle" onclick="togglePwd()" id="pwdEye" title="Afficher/masquer">👁️</button>
                    </div>
                    @error('password')<div class="invalid-feedback" style="font-size:12px;color:var(--danger);margin-top:4px;">{{ $message }}</div>@enderror
                </div>

                <div style="margin-bottom:22px;">
                    <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer;color:var(--text-secondary);">
                        <input type="checkbox" name="remember" style="accent-color:var(--sky-primary);width:15px;height:15px;">
                        {{ __('app.remember_me') }}
                    </label>
                </div>

                <!-- Magnetic submit button -->
                <button type="submit" class="btn btn-primary btn-xl" style="width:100%;justify-content:center;" id="loginBtn">
                    <span id="loginBtnText">🔐 {{ __('app.login_btn') }}</span>
                    <span id="loginBtnSpinner" style="display:none;">⏳</span>
                </button>
            </form>

            <div style="text-align:center;margin-top:22px;font-size:13px;color:var(--text-muted);position:relative;z-index:1;">
                {{ __('app.no_account') }}
                <a href="{{ route('register') }}" style="color:var(--sky-primary);font-weight:700;text-decoration:none;">
                    {{ __('app.create_agent') }} →
                </a>
            </div>

            <!-- Access notice -->
            <div class="security-notice" style="position:relative;z-index:1;">
                <div class="sec-header">
                    <span style="font-size:18px;">🔑</span>
                    <span class="sec-title">{{ __('app.access_reserved_title') }}</span>
                </div>
                <p class="sec-text" style="margin-bottom:10px;">{{ __('app.access_reserved_desc') }}</p>
                <a href="{{ route('register') }}" class="sec-btn">✍️ {{ __('app.access_register_cta') }}</a>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/bluesky.js') }}"></script>
<script>
/* ── Password toggle ── */
function togglePwd() {
    const i = document.getElementById('pwdInput');
    const e = document.getElementById('pwdEye');
    const show = i.type === 'password';
    i.type = show ? 'text' : 'password';
    e.style.transform = 'translateY(-50%) scale(1.25) rotate(12deg)';
    setTimeout(() => {
        e.textContent = show ? '🙈' : '👁️';
        e.style.transform = 'translateY(-50%)';
    }, 150);
}

/* ── Floating labels: has-value class ── */
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.fi-input').forEach(input => {
        const check = () => {
            if (input.value.trim()) input.classList.add('has-value');
            else input.classList.remove('has-value');
        };
        check(); // handle pre-filled (old('email'))
        input.addEventListener('input',  check);
        input.addEventListener('change', check);
        // Handle browser autofill (animationstart trick)
        input.addEventListener('animationstart', (e) => {
            if (e.animationName === 'onAutoFillStart') input.classList.add('has-value');
            if (e.animationName === 'onAutoFillCancel') check();
        });
    });
});

/* ── Magnetic button ── */
const loginBtn = document.getElementById('loginBtn');
if (loginBtn) {
    loginBtn.addEventListener('mousemove', (e) => {
        const rect = loginBtn.getBoundingClientRect();
        const x = (e.clientX - rect.left - rect.width  / 2) * 0.10;
        const y = (e.clientY - rect.top  - rect.height / 2) * 0.14;
        loginBtn.style.transform = `translate(${x}px, ${y}px)`;
    });
    loginBtn.addEventListener('mouseleave', () => {
        loginBtn.style.transform = '';
    });
}

/* ── Submit spinner ── */
document.getElementById('loginForm').addEventListener('submit', () => {
    document.getElementById('loginBtnText').style.display = 'none';
    document.getElementById('loginBtnSpinner').style.display = 'inline';
    loginBtn.disabled = true;
    loginBtn.style.opacity = '0.8';
});
</script>
</body>
</html>
