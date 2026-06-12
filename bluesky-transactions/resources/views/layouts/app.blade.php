<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('app.dashboard')) — BLUESKY Transactions</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <link href="{{ asset('css/bluesky.css') }}" rel="stylesheet">
    <script>
        (function(){
            const t = localStorage.getItem('bluesky-theme') || 'light';
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>
    <script>
        window.bskyI18n = {
            confirm_ok:                      '{{ __("app.confirm_ok") }}',
            confirm_cancel:                  '{{ __("app.confirm_cancel") }}',
            reset_system_confirm_title:      '{{ __("app.reset_system_confirm_title") }}',
            reset_system_confirm_msg:        '{{ __("app.reset_system_confirm_msg") }}',
            reset_system_confirm_check:      '{{ __("app.reset_system_confirm_check") }}',
            reset_by_country_confirm_title:  '{{ __("app.reset_by_country_confirm_title") }}',
            reset_by_country_confirm_msg:    '{{ __("app.reset_by_country_confirm_msg") }}',
        };
    </script>
    @stack('styles')
</head>
<body>

@php
    use Illuminate\Support\Facades\Storage;
    $user     = auth()->user();
    $photoUrl = $user->profile_photo ? Storage::url($user->profile_photo) : null;
    $initials = strtoupper(substr($user->name, 0, 2));
@endphp

<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<!-- ===== SIDEBAR ===== -->
<aside class="sidebar" id="sidebar">

    <!-- Brand -->
    <a href="{{ $user->isAdmin() ? route('admin.dashboard') : route('agent.dashboard') }}" class="sidebar-brand">
        <img src="{{ asset('images/logo.png') }}" alt="Blue Sky Logo" class="brand-logo-img">
        <div class="brand-text">
            <div class="brand-name">BLUESKY</div>
            <div class="brand-tagline">{{ __('app.money_transfer') }}</div>
        </div>
    </a>

    <!-- Profile mini card -->
    <a href="{{ route('profile.show') }}" style="display:flex;align-items:center;gap:12px;padding:14px 16px;margin:8px 10px;border-radius:12px;background:rgba(255,255,255,0.06);text-decoration:none;transition:background 0.2s;border:1px solid rgba(255,255,255,0.07);">
        @if($photoUrl)
            <img src="{{ $photoUrl }}" alt="{{ __('app.my_profile') }}"
                 style="width:40px;height:40px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,0.3);flex-shrink:0;">
        @else
            <div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,var(--sky-primary),var(--sky-secondary));display:flex;align-items:center;justify-content:center;color:white;font-weight:800;font-size:15px;flex-shrink:0;border:2px solid rgba(255,255,255,0.2);">
                {{ $initials }}
            </div>
        @endif
        <div style="flex:1;min-width:0;">
            <div style="font-size:13px;font-weight:700;color:rgba(255,255,255,0.9);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ Str::limit($user->name, 18) }}</div>
            <div style="font-size:11px;color:rgba(255,255,255,0.45);margin-top:1px;">
                {{ $user->isAdmin() ? '🛡️ '.__('app.admin_panel') : '🏢 '.__('app.agent_panel') }}
                @if($user->country) — <x-flag :code="$user->country->code" size="xs" style="border-radius:2px;" /> @endif
            </div>
        </div>
        <span style="font-size:14px;color:rgba(255,255,255,0.35);">✏️</span>
    </a>

    <!-- Navigation -->
    <div class="nav-section">
        @if($user->isAdmin())
            <div class="nav-section-title">{{ __('app.administration') }}</div>
            <div class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="nav-icon">📊</span> {{ __('app.dashboard') }}
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('admin.transactions.index') }}" class="nav-link {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}">
                    <span class="nav-icon">💸</span> {{ __('app.all_transactions') }}
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('admin.agents.index') }}" class="nav-link {{ request()->routeIs('admin.agents.*') ? 'active' : '' }}">
                    <span class="nav-icon">👥</span> {{ __('app.agent_management') }}
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                    <span class="nav-icon">📨</span> {{ __('app.reports_admin_title') }}
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('admin.countries.index') }}" class="nav-link {{ request()->routeIs('admin.countries.*') ? 'active' : '' }}">
                    <span class="nav-icon">🌍</span> {{ __('app.operating_countries') }}
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('admin.export.csv') }}" class="nav-link">
                    <span class="nav-icon">📤</span> {{ __('app.export_csv') }}
                </a>
            </div>
            <div class="nav-section-title" style="margin-top:14px">{{ __('app.agent_panel') }}</div>
        @endif

        <div class="nav-item">
            <a href="{{ route('agent.dashboard') }}" class="nav-link {{ request()->routeIs('agent.dashboard') ? 'active' : '' }}">
                <span class="nav-icon">🏠</span> {{ __('app.my_space') }}
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('agent.transactions.create') }}" class="nav-link {{ request()->routeIs('agent.transactions.create') ? 'active' : '' }}">
                <span class="nav-icon">➕</span> {{ __('app.new_transaction') }}
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('agent.transactions.index') }}" class="nav-link {{ request()->routeIs('agent.transactions.index') ? 'active' : '' }}">
                <span class="nav-icon">📋</span> {{ __('app.my_transactions') }}
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('profile.show') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <span class="nav-icon">👤</span> {{ __('app.my_profile') }}
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('agent.export.csv') }}" class="nav-link">
                <span class="nav-icon">📥</span> {{ __('app.my_data') }}
            </a>
        </div>
    </div>

    <!-- Countries flags -->
    <div class="countries-panel">
        <div class="countries-title">{{ __('app.operating_countries') }}</div>
        <div class="countries-grid">
            @foreach($activeCountries as $c)
                <x-flag :code="$c->code" size="sm" :alt="$c->name" style="border-radius:3px;" />
            @endforeach
        </div>
    </div>

    <!-- Logout -->
    <div style="padding:10px 10px 14px;">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-link" style="width:100%;background:none;border:none;cursor:pointer;color:rgba(255,120,120,0.8);">
                <span class="nav-icon">🚪</span> {{ __('app.logout') }}
            </button>
        </form>
    </div>
</aside>

<!-- ===== MAIN WRAPPER ===== -->
<div class="main-wrapper">
    <header class="topbar" id="mainTopbar">
        <div class="topbar-left">
            <button class="menu-toggle" onclick="toggleSidebar()" aria-label="Menu">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>
            <div>
                <div class="topbar-title">@yield('page-title', __('app.dashboard'))</div>
                <div class="topbar-subtitle">@yield('page-subtitle', 'BLUESKY Transactions')</div>
            </div>
        </div>

        <div class="topbar-right">
            <div class="topbar-date">🕐 <span id="liveClock">--:--:--</span></div>

            <button class="theme-toggle" id="themeToggle" onclick="ThemeManager.toggle()" aria-label="Toggle theme">🌙</button>

            <div class="lang-switcher">
                <a href="{{ route('lang.switch', 'en') }}" class="lang-btn {{ app()->getLocale() === 'en' ? 'active' : '' }}">🇬🇧 EN</a>
                <div class="lang-divider"></div>
                <a href="{{ route('lang.switch', 'fr') }}" class="lang-btn {{ app()->getLocale() === 'fr' ? 'active' : '' }}">🇫🇷 FR</a>
            </div>

            <!-- User menu -->
            <div class="dropdown">
                <div class="user-menu" onclick="toggleDropdown(this)">
                    @if($photoUrl)
                        <img src="{{ $photoUrl }}" alt="{{ $user->name }}"
                             style="width:34px;height:34px;border-radius:9px;object-fit:cover;border:2px solid var(--sky-light);flex-shrink:0;">
                    @else
                        <div class="user-avatar">{{ $initials }}</div>
                    @endif
                    <div>
                        <div class="user-info-name">{{ Str::limit($user->name, 16) }}</div>
                        <div class="user-info-role">
                            {{ $user->isAdmin() ? '🛡️ '.__('app.admin_panel') : '🏢 '.__('app.agent_panel') }}
                            @if($user->country) — <x-flag :code="$user->country->code" size="xs" style="border-radius:2px;" /> @endif
                        </div>
                    </div>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color:var(--text-muted)">
                        <polyline points="6,9 12,15 18,9"/>
                    </svg>
                </div>
                <div class="dropdown-menu" id="userDropdown">
                    @if($user->agent_code)
                        <div class="dropdown-item" style="cursor:default;color:var(--text-muted);font-size:11px;pointer-events:none;">
                            🔑 {{ $user->agent_code }}
                        </div>
                        <div class="dropdown-divider"></div>
                    @endif
                    <a href="{{ route('profile.show') }}" class="dropdown-item">👤 {{ __('app.my_profile') }}</a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item danger">🚪 {{ __('app.logout') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Flash alerts -->
    <div class="alerts-wrapper">
        @if(session('success'))
            <div class="alert alert-success" style="margin-top:14px">✅ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger" style="margin-top:14px">❌ {{ session('error') }}</div>
        @endif
        @if(session('warning'))
            <div class="alert alert-warning" style="margin-top:14px">⚠️ {{ session('warning') }}</div>
        @endif
    </div>

    <div class="content-area page-enter">@yield('content')</div>
</div>

{{-- ===== MOBILE BOTTOM NAV ===== --}}
<nav class="mobile-bottom-nav" aria-label="Navigation mobile">
    @php
        $adminLinks = $user->isAdmin() ? [
            ['route' => 'admin.dashboard',          'icon' => '📊', 'label' => __('app.dashboard'),       'match' => 'admin.dashboard'],
            ['route' => 'agent.transactions.create','icon' => '➕', 'label' => __('app.new'),             'match' => 'agent.transactions.create'],
            ['route' => 'agent.transactions.index', 'icon' => '📋', 'label' => __('app.transactions'),    'match' => 'agent.transactions.index'],
            ['route' => 'admin.agents.index',       'icon' => '👥', 'label' => __('app.agents'),          'match' => 'admin.agents.*'],
            ['route' => 'profile.show',             'icon' => '👤', 'label' => __('app.my_profile'),      'match' => 'profile.*'],
        ] : [
            ['route' => 'agent.dashboard',          'icon' => '🏠', 'label' => __('app.my_space'),        'match' => 'agent.dashboard'],
            ['route' => 'agent.transactions.create','icon' => '➕', 'label' => __('app.new'),             'match' => 'agent.transactions.create'],
            ['route' => 'agent.transactions.index', 'icon' => '📋', 'label' => __('app.transactions'),    'match' => 'agent.transactions.index'],
            ['route' => 'profile.show',             'icon' => '👤', 'label' => __('app.my_profile'),      'match' => 'profile.*'],
        ];
        $links = $user->isAdmin() ? $adminLinks : $adminLinks;
    @endphp
    <div class="mobile-nav-inner" style="grid-template-columns: repeat({{ $user->isAdmin() ? 5 : 4 }}, 1fr);">
        @foreach($links as $link)
            <a href="{{ route($link['route']) }}"
               class="mobile-nav-item {{ request()->routeIs($link['match']) ? 'active' : '' }}">
                <span class="mobile-nav-icon">{{ $link['icon'] }}</span>
                <span class="mobile-nav-label">{{ $link['label'] }}</span>
            </a>
        @endforeach
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="{{ asset('js/bluesky.js') }}"></script>
@stack('scripts')
</body>
</html>
