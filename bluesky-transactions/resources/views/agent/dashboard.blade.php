@extends('layouts.app')

@section('title', __('app.my_space'))
@section('page-title', __('app.my_space'))
@section('page-subtitle', __('app.hello') . ', ' . auth()->user()->name . ' — ' . (auth()->user()->country?->flag_emoji ?? '') . ' ' . (auth()->user()->country?->name ?? ''))

@push('styles')
<style>
/* ═══════════════════════════════════════════════════
   GRID SYSTEM — proportional, no fixed px widths
   All column tracks use fr + minmax(0,1fr) to prevent
   overflow and ensure true proportional scaling.
═══════════════════════════════════════════════════ */

/* 4-col KPI */
.g-kpi   { display:grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap:clamp(8px,1.4vw,14px); margin-bottom:clamp(10px,1.5vw,16px); }
/* 3-col status */
.g-status{ display:grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap:clamp(8px,1.2vw,12px); margin-bottom:clamp(14px,2vw,22px); }
/* 4-col quick bar */
.g-quick { display:grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap:clamp(7px,1.2vw,12px); margin-bottom:clamp(14px,2vw,20px); }
/* main + sidebar — golden-ratio-ish split */
.g-cols  { display:grid; grid-template-columns: minmax(0, 1.75fr) minmax(0, 1fr); gap:clamp(10px,1.5vw,18px); margin-bottom:clamp(10px,1.5vw,16px); }
/* deep stats: 3-col uniform */
.g-deep  { display:grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap:clamp(8px,1.4vw,14px); margin-bottom:clamp(10px,1.5vw,14px); }
/* deep stats: 2-col */
.g-deep2 { display:grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap:clamp(8px,1.4vw,14px); }

/* ── Hero ── */
.agent-hero {
    position:relative; border-radius:clamp(12px,2vw,20px);
    background:linear-gradient(135deg,#0284C7 0%,#0369A1 50%,#075985 100%);
    background-size:200% 200%; animation:gradientShift 8s ease infinite;
    padding:clamp(16px,3vw,28px) clamp(16px,3.5vw,32px);
    display:flex; align-items:center; justify-content:space-between;
    gap:clamp(12px,2vw,20px); overflow:hidden;
    box-shadow:0 12px 40px rgba(2,132,199,0.35);
    margin-bottom:clamp(14px,2vw,20px);
}
.agent-hero::before {
    content:''; position:absolute; border-radius:50%; pointer-events:none;
    width:280px; height:280px; top:-130px; right:-60px;
    background:rgba(255,255,255,0.06);
}
.hero-left  { display:flex; align-items:center; gap:clamp(12px,1.8vw,18px); position:relative; z-index:1; min-width:0; flex:1; }
.hero-right { display:flex; flex-direction:column; align-items:flex-end; gap:10px; position:relative; z-index:1; flex-shrink:0; }

.hero-avatar {
    width:clamp(48px,7vw,68px); height:clamp(48px,7vw,68px);
    border-radius:clamp(12px,1.5vw,18px);
    background:rgba(255,255,255,0.18); backdrop-filter:blur(8px);
    border:2px solid rgba(255,255,255,0.3);
    display:flex; align-items:center; justify-content:center;
    font-size:clamp(18px,3vw,24px); font-weight:900; color:white;
    flex-shrink:0; box-shadow:0 6px 20px rgba(0,0,0,0.2); overflow:hidden;
}
.hero-avatar img { width:100%; height:100%; object-fit:cover; }
.hero-greeting-lbl { font-size:clamp(9px,1.2vw,11px); color:rgba(255,255,255,0.55); text-transform:uppercase; letter-spacing:2px; margin-bottom:3px; }
.hero-name  { font-size:clamp(16px,3vw,22px); font-weight:900; color:white; line-height:1.15; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.hero-badges{ display:flex; flex-wrap:wrap; gap:5px; margin-top:8px; }
.hero-badge {
    display:inline-flex; align-items:center; gap:5px;
    background:rgba(255,255,255,0.15); backdrop-filter:blur(8px);
    border:1px solid rgba(255,255,255,0.22); padding:3px 10px; border-radius:20px;
    font-size:clamp(10px,1.2vw,12px); color:rgba(255,255,255,0.92); font-weight:500; white-space:nowrap;
}
.hero-badge-green { background:rgba(16,185,129,0.25); border-color:rgba(16,185,129,0.35); }
.hero-cta {
    display:inline-flex; align-items:center; gap:7px;
    background:white; color:var(--sky-primary); font-weight:800; font-size:clamp(12px,1.5vw,14px);
    padding:clamp(8px,1.2vw,11px) clamp(14px,2vw,20px); border-radius:12px;
    text-decoration:none; box-shadow:0 4px 20px rgba(0,0,0,0.2);
    transition:transform 0.2s, box-shadow 0.2s; white-space:nowrap;
}
.hero-cta:hover { transform:translateY(-2px); box-shadow:0 8px 28px rgba(0,0,0,0.25); }
.hero-date-day  { font-size:10px; color:rgba(255,255,255,0.45); text-transform:uppercase; letter-spacing:1px; }
.hero-date-full { font-size:clamp(12px,1.5vw,14px); font-weight:700; color:rgba(255,255,255,0.85); }

/* ── Quick button ── */
.quick-btn {
    display:flex; flex-direction:column; align-items:center; gap:5px;
    background:var(--bg-card); border:1px solid var(--border); border-radius:clamp(10px,1.5vw,14px);
    padding:clamp(10px,1.5vw,14px) clamp(6px,1vw,10px); text-decoration:none;
    transition:all 0.2s; box-shadow:var(--shadow-sm);
}
.quick-btn:hover { border-color:var(--sky-secondary); transform:translateY(-2px); box-shadow:0 6px 20px rgba(14,165,233,0.15); }
.quick-btn-icon  { width:clamp(32px,4.5vw,40px); height:clamp(32px,4.5vw,40px); border-radius:clamp(9px,1.2vw,12px); display:flex; align-items:center; justify-content:center; font-size:clamp(16px,2.2vw,19px); }
.quick-btn-label { font-size:clamp(10px,1.2vw,12px); font-weight:600; color:var(--text-secondary); text-align:center; line-height:1.3; }
.quick-badge     { display:inline-block; background:var(--danger); color:white; border-radius:10px; padding:0 5px; font-size:10px; font-weight:700; line-height:1.5; margin-left:3px; }

/* ── KPI card ── */
.kpi-card {
    background:var(--bg-card); border:1px solid var(--border); border-radius:clamp(12px,1.5vw,16px);
    overflow:hidden; box-shadow:var(--shadow-sm);
    transition:transform 0.2s, box-shadow 0.2s;
    animation:fadeInUp 0.5s ease forwards; opacity:0;
}
.kpi-card:hover { transform:translateY(-3px); box-shadow:var(--shadow); }
.kpi-card:nth-child(1){animation-delay:.05s} .kpi-card:nth-child(2){animation-delay:.12s}
.kpi-card:nth-child(3){animation-delay:.19s} .kpi-card:nth-child(4){animation-delay:.26s}
.kpi-top-bar { height:4px; }
.kpi-body    { padding:clamp(12px,1.8vw,17px) clamp(12px,1.8vw,18px); }
.kpi-label   { font-size:clamp(9px,1.1vw,11px); font-weight:700; letter-spacing:1px; text-transform:uppercase; color:var(--text-muted); margin-bottom:6px; display:flex; align-items:center; gap:5px; }
.kpi-icon    { width:clamp(18px,2vw,22px); height:clamp(18px,2vw,22px); border-radius:6px; display:inline-flex; align-items:center; justify-content:center; font-size:clamp(11px,1.3vw,13px); flex-shrink:0; }
.kpi-value   { font-size:clamp(18px,2.8vw,26px); font-weight:900; color:var(--text-heading); line-height:1; margin-bottom:4px; }
.kpi-sub     { font-size:clamp(10px,1.1vw,12px); color:var(--text-muted); }

/* ── Status mini ── */
.status-mini {
    background:var(--bg-card); border:1px solid var(--border); border-radius:clamp(11px,1.4vw,14px);
    padding:clamp(11px,1.4vw,14px) clamp(10px,1.3vw,14px);
    display:flex; align-items:center; gap:clamp(8px,1.2vw,11px);
    box-shadow:var(--shadow-sm); animation:fadeInUp 0.5s ease forwards; opacity:0;
}
.status-mini:nth-child(1){animation-delay:.30s} .status-mini:nth-child(2){animation-delay:.37s} .status-mini:nth-child(3){animation-delay:.44s}
.status-dot  { width:clamp(32px,4vw,38px); height:clamp(32px,4vw,38px); border-radius:clamp(8px,1.1vw,11px); display:flex; align-items:center; justify-content:center; font-size:clamp(15px,2vw,17px); flex-shrink:0; }
.status-val  { font-size:clamp(18px,2.5vw,22px); font-weight:900; line-height:1; }
.status-lbl  { font-size:clamp(10px,1.1vw,12px); color:var(--text-muted); margin-top:2px; }
.status-lbl a{ font-size:10px; color:var(--sky-primary); text-decoration:none; margin-left:4px; }

/* ── Card header ── */
.dash-card-header  { padding:clamp(12px,1.6vw,16px) clamp(14px,1.8vw,18px) 0; display:flex; align-items:center; justify-content:space-between; margin-bottom:10px; }
.dash-card-title   { font-size:clamp(12.5px,1.5vw,14px); font-weight:800; color:var(--text-heading); }
.dash-card-sub     { font-size:clamp(10px,1.1vw,11.5px); color:var(--text-muted); margin-top:2px; }
.dash-card-link    { font-size:11.5px; color:var(--sky-primary); font-weight:700; text-decoration:none; white-space:nowrap; flex-shrink:0; }
.chart-badge       { font-size:11px; background:rgba(2,132,199,0.1); color:var(--sky-primary); padding:3px 9px; border-radius:20px; font-weight:700; flex-shrink:0; }

/* ── Chart ── */
.chart-wrap       { padding:0 clamp(10px,1.5vw,14px) clamp(12px,1.5vw,16px); }
.chart-canvas-box { position:relative; height:clamp(160px,22vw,215px); }

/* ── TX item ── */
.tx-item      { display:flex; align-items:center; gap:clamp(8px,1.2vw,11px); padding:clamp(10px,1.3vw,13px) clamp(12px,1.5vw,16px); border-bottom:1px solid var(--divider); transition:background 0.15s; }
.tx-item:hover{ background:var(--bg-row-hover); }
.tx-item:last-child{ border-bottom:none; }
.tx-dot       { width:clamp(30px,3.8vw,36px); height:clamp(30px,3.8vw,36px); border-radius:clamp(8px,1vw,10px); display:flex; align-items:center; justify-content:center; font-size:clamp(13px,1.7vw,15px); flex-shrink:0; }
.tx-main      { flex:1; min-width:0; }
.tx-num       { font-size:clamp(9.5px,1.1vw,11px); font-family:monospace; color:var(--sky-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.tx-name      { font-weight:600; font-size:clamp(11.5px,1.3vw,13px); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.tx-route     { font-size:clamp(10px,1.1vw,11px); color:var(--text-muted); margin-top:1px; }
.tx-right     { text-align:right; flex-shrink:0; }
.tx-amount    { font-size:clamp(11.5px,1.4vw,13px); font-weight:800; color:var(--sky-primary); }
.tx-time      { font-size:10px; color:var(--text-muted); margin-bottom:4px; }
.tx-actions   { display:flex; gap:4px; justify-content:flex-end; }
.tx-action-btn{ width:26px; height:26px; border-radius:7px; display:flex; align-items:center; justify-content:center; font-size:12px; text-decoration:none; border:none; cursor:pointer; transition:background 0.15s; }

/* ── Route row ── */
.route-row    { display:flex; align-items:center; gap:clamp(8px,1.2vw,10px); padding:clamp(10px,1.3vw,12px) clamp(12px,1.5vw,16px); border-bottom:1px solid var(--divider); transition:background 0.15s; }
.route-row:last-child{ border-bottom:none; }
.route-row:hover{ background:var(--bg-row-hover); }
.rank-dot     { width:24px; height:24px; border-radius:7px; background:linear-gradient(135deg,var(--sky-primary),var(--sky-secondary)); color:white; font-weight:800; font-size:11px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.route-flags  { font-size:clamp(16px,2.2vw,19px); display:flex; align-items:center; gap:3px; flex-shrink:0; }
.route-main   { flex:1; min-width:0; }
.route-codes  { font-weight:700; font-size:clamp(11.5px,1.4vw,13px); display:flex; align-items:center; gap:5px; }
.route-stats  { font-size:clamp(10px,1.1vw,11px); color:var(--text-muted); margin-top:1px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.route-right  { text-align:right; flex-shrink:0; }
.route-pct    { font-size:clamp(11.5px,1.4vw,13px); font-weight:800; color:var(--sky-primary); }
.mini-bar     { width:44px; height:4px; background:var(--divider); border-radius:4px; overflow:hidden; margin-top:3px; }
.mini-bar-fill{ height:100%; background:linear-gradient(90deg,var(--sky-primary),var(--sky-secondary)); border-radius:4px; }

/* ── Report ── */
.report-row   { padding:clamp(10px,1.3vw,12px) clamp(12px,1.5vw,16px); border-bottom:1px solid var(--divider); display:flex; align-items:flex-start; gap:9px; transition:background 0.15s; }
.report-row:last-child{ border-bottom:none; }
.report-row:hover{ background:var(--bg-row-hover); }
.report-main  { flex:1; min-width:0; }
.report-subject{ font-weight:600; font-size:clamp(11.5px,1.3vw,13px); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.report-meta  { font-size:11px; color:var(--text-muted); margin-top:2px; display:flex; align-items:center; gap:6px; flex-wrap:wrap; }
.reports-col  { display:flex; flex-direction:column; gap:clamp(10px,1.5vw,14px); }

/* ── Empty state ── */
.empty-state      { padding:clamp(24px,4vw,36px) 20px; text-align:center; color:var(--text-muted); }
.empty-state-icon { font-size:clamp(28px,4vw,36px); margin-bottom:10px; }
.empty-state-text { font-size:clamp(11.5px,1.3vw,13px); }

/* ══════════════════════════════════════════════
   DEEP STATS PANEL  (toggle section)
══════════════════════════════════════════════ */
.stats-toggle-row {
    display:flex; align-items:center; justify-content:space-between;
    margin-bottom:clamp(10px,1.5vw,16px);
}
.stats-toggle-btn {
    display:inline-flex; align-items:center; gap:8px;
    background:var(--bg-card); border:1px solid var(--border);
    border-radius:10px; padding:8px 16px;
    font-size:13px; font-weight:700; color:var(--sky-primary);
    cursor:pointer; transition:all 0.2s;
    box-shadow:var(--shadow-sm);
}
.stats-toggle-btn:hover { border-color:var(--sky-secondary); background:rgba(14,165,233,0.06); }
.stats-toggle-icon { transition:transform 0.3s; display:inline-block; }
.stats-toggle-btn.open .stats-toggle-icon { transform:rotate(180deg); }

.deep-stats-panel {
    overflow:hidden;
    max-height:0;
    transition:max-height 0.4s cubic-bezier(0.4,0,0.2,1), opacity 0.3s ease;
    opacity:0;
}
.deep-stats-panel.open {
    max-height:600px;
    opacity:1;
}

/* Deep stat card */
.deep-card {
    background:var(--bg-card); border:1px solid var(--border);
    border-radius:clamp(11px,1.4vw,14px); padding:clamp(13px,1.8vw,18px);
    box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:12px;
    animation:fadeInUp 0.4s ease both;
}
.deep-card-icon {
    width:clamp(36px,4.5vw,44px); height:clamp(36px,4.5vw,44px); border-radius:clamp(9px,1.2vw,12px);
    display:flex; align-items:center; justify-content:center; font-size:clamp(17px,2.2vw,20px);
    flex-shrink:0;
}
.deep-card-val  { font-size:clamp(17px,2.4vw,22px); font-weight:900; color:var(--text-heading); line-height:1; }
.deep-card-lbl  { font-size:clamp(10px,1.1vw,11.5px); color:var(--text-muted); margin-top:3px; font-weight:500; }
.deep-card-sub  { font-size:clamp(9.5px,1vw,11px); color:var(--text-muted); margin-top:2px; }

/* Donut-like type split bar */
.type-split-bar { display:flex; height:8px; border-radius:6px; overflow:hidden; margin-top:10px; gap:2px; }
.type-split-send { background:var(--sky-primary); border-radius:6px 0 0 6px; }
.type-split-wdraw{ background:#8B5CF6; border-radius:0 6px 6px 0; }

/* ═══════════════════════════════════════════════════
   RESPONSIVE
═══════════════════════════════════════════════════ */

/* ≤ 1200px — tablet landscape */
@media (max-width: 1200px) {
    .g-cols { grid-template-columns: minmax(0, 1.5fr) minmax(0, 1fr); }
}

/* ≤ 1024px — tablet (sidebar drawer) */
@media (max-width: 1024px) {
    .g-cols   { grid-template-columns: minmax(0, 1fr); }
    .g-kpi    { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .g-deep   { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .agent-hero { border-radius:16px; }
}

/* ≤ 768px — mobile */
@media (max-width: 768px) {
    /* Hero stacks */
    .agent-hero   { flex-direction:column; align-items:stretch; border-radius:14px; }
    .hero-left    { gap:11px; }
    .hero-right   { flex-direction:row; align-items:center; justify-content:space-between; }
    .hero-date-day{ font-size:9.5px; }

    /* Grids */
    .g-quick  { grid-template-columns:repeat(2, minmax(0, 1fr)); gap:8px; }
    .g-kpi    { grid-template-columns:repeat(2, minmax(0, 1fr)); gap:9px; }
    .g-status { gap:8px; }
    .g-deep   { grid-template-columns:repeat(2, minmax(0, 1fr)); gap:8px; }
    .g-deep2  { grid-template-columns:repeat(2, minmax(0, 1fr)); gap:8px; }
    .g-cols   { gap:12px; }

    /* Deep stats */
    .deep-stats-panel.open { max-height:900px; }
}

/* ≤ 480px — small phones */
@media (max-width: 480px) {
    /* Hero */
    .hero-badge-country { display:none; }
    .hero-right { flex-direction:column; align-items:flex-start; gap:8px; }
    .hero-cta   { width:100%; justify-content:center; }
    .hero-date  { display:none; }

    /* Quick — 2×2, tighter */
    .g-quick  { gap:6px; }

    /* Status — stay 3 cols, ultra compact */
    .g-status { gap:6px; }

    /* KPI */
    .kpi-icon { display:none; }

    /* Deep stats */
    .g-deep   { grid-template-columns:repeat(2, minmax(0, 1fr)); gap:7px; }
    .g-deep2  { grid-template-columns:repeat(2, minmax(0, 1fr)); gap:7px; }

    /* TX actions hidden on tiny screens */
    .tx-actions { display:none; }
    .mini-bar   { display:none; }

    .deep-stats-panel.open { max-height:1200px; }
}

/* ≤ 360px — XS */
@media (max-width: 360px) {
    .g-status { grid-template-columns:minmax(0, 1fr); }
    .g-kpi    { gap:6px; }
    .g-quick  { gap:5px; }
    .kpi-value { font-size:17px; }
}
</style>
@endpush

@php
    use Illuminate\Support\Facades\Storage;
    $photoUrl  = auth()->user()->profile_photo ? Storage::url(auth()->user()->profile_photo) : null;
    $initials  = strtoupper(substr(auth()->user()->name, 0, 2));
    $firstName = explode(' ', auth()->user()->name)[0];
    $diff      = $stats['last_month_amount'] > 0
        ? round((($stats['month_amount'] - $stats['last_month_amount']) / $stats['last_month_amount']) * 100)
        : null;

    // Best month from 6-month data
    $bestMonth = $monthlyData->sortByDesc('total_amount')->first();
    $monthNames = collect(range(1, 12))->map(fn($m) => \Carbon\Carbon::createFromDate(2000, $m, 1)->locale(app()->getLocale())->isoFormat('MMM'))->toArray();

    // Type split percentages
    $typeTotal   = $stats['send_count'] + $stats['withdrawal_count'];
    $sendPct     = $typeTotal > 0 ? round($stats['send_count'] / $typeTotal * 100) : 0;
    $withdrawPct = $typeTotal > 0 ? 100 - $sendPct : 0;
@endphp

@section('content')

{{-- ════════════════════════════
     HERO
═════════════════════════════ --}}
<div class="agent-hero">
    <div class="hero-left">
        <div class="hero-avatar">
            @if($photoUrl)
                <img src="{{ $photoUrl }}" alt="{{ auth()->user()->name }}">
            @else
                {{ $initials }}
            @endif
        </div>
        <div style="min-width:0;">
            <div class="hero-greeting-lbl">{{ __('app.agent_dashboard') }}</div>
            <div class="hero-name">{{ __('app.hello') }}, {{ $firstName }} 👋</div>
            <div class="hero-badges">
                <span class="hero-badge">🔑 {{ auth()->user()->agent_code }}</span>
                @if(auth()->user()->country)
                    <span class="hero-badge hero-badge-country">{{ auth()->user()->country->flag_emoji }} {{ auth()->user()->country->name }}</span>
                @endif
                <span class="hero-badge hero-badge-green">✅ {{ ucfirst(auth()->user()->status) }}</span>
            </div>
        </div>
    </div>
    <div class="hero-right">
        <a href="{{ route('agent.transactions.create') }}" class="hero-cta">➕ {{ __('app.new_transaction') }}</a>
        <div class="hero-date">
            <div class="hero-date-day">{{ now()->format('l') }}</div>
            <div class="hero-date-full">{{ now()->format('d M Y') }}</div>
        </div>
    </div>
</div>

{{-- ════════════════════════════
     QUICK BAR
═════════════════════════════ --}}
<div class="g-quick">
    <a href="{{ route('agent.transactions.create') }}" class="quick-btn">
        <div class="quick-btn-icon" style="background:rgba(2,132,199,0.12);">📤</div>
        <span class="quick-btn-label">{{ __('app.type_send') }}</span>
    </a>
    <a href="{{ route('agent.transactions.index') }}" class="quick-btn">
        <div class="quick-btn-icon" style="background:rgba(16,185,129,0.12);">📋</div>
        <span class="quick-btn-label">{{ __('app.my_transactions') }}</span>
    </a>
    <a href="{{ route('agent.transactions.index', ['status'=>'pending']) }}" class="quick-btn">
        <div class="quick-btn-icon" style="background:rgba(245,158,11,0.12);">⏳</div>
        <span class="quick-btn-label">{{ __('app.pending') }}@if($stats['pending']>0)<span class="quick-badge">{{ $stats['pending'] }}</span>@endif</span>
    </a>
    <a href="{{ route('profile.show') }}" class="quick-btn">
        <div class="quick-btn-icon" style="background:rgba(139,92,246,0.12);">👤</div>
        <span class="quick-btn-label">{{ __('app.my_profile') }}</span>
    </a>
</div>

{{-- ════════════════════════════
     KPI CARDS
═════════════════════════════ --}}
<div class="g-kpi">
    <div class="kpi-card">
        <div class="kpi-top-bar" style="background:linear-gradient(90deg,var(--sky-primary),var(--sky-secondary));"></div>
        <div class="kpi-body">
            <div class="kpi-label"><span class="kpi-icon" style="background:rgba(2,132,199,0.12);">📋</span>{{ __('app.total_transactions') }}</div>
            <div class="kpi-value" data-counter="{{ $stats['total'] }}">{{ number_format($stats['total']) }}</div>
            <div class="kpi-sub">📅 {{ $stats['today'] }} {{ __('app.today') }}</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-top-bar" style="background:linear-gradient(90deg,#F59E0B,#FCD34D);"></div>
        <div class="kpi-body">
            <div class="kpi-label"><span class="kpi-icon" style="background:rgba(245,158,11,0.12);">💰</span>{{ __('app.total_volume') }}</div>
            <div class="kpi-value" style="font-size:clamp(16px,2.2vw,20px);">{{ number_format($stats['total_amount'],0,',',' ') }}</div>
            <div class="kpi-sub">{{ __('app.today') }}: {{ number_format($stats['today_amount'],0,',',' ') }}</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-top-bar" style="background:linear-gradient(90deg,#10B981,#34D399);"></div>
        <div class="kpi-body">
            <div class="kpi-label"><span class="kpi-icon" style="background:rgba(16,185,129,0.12);">✅</span>{{ __('app.total_commissions') }}</div>
            <div class="kpi-value" style="font-size:clamp(16px,2.2vw,20px);">{{ number_format($stats['total_fees'],0,',',' ') }}</div>
            <div class="kpi-sub">@if(auth()->user()->country){{ auth()->user()->country->currency_code }}@endif</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-top-bar" style="background:linear-gradient(90deg,#8B5CF6,#A78BFA);"></div>
        <div class="kpi-body">
            <div class="kpi-label"><span class="kpi-icon" style="background:rgba(139,92,246,0.12);">📆</span>{{ __('app.this_month') }}</div>
            <div class="kpi-value" data-counter="{{ $stats['month'] }}">{{ number_format($stats['month']) }}</div>
            <div class="kpi-sub" style="color:{{ $diff===null?'var(--text-muted)':($diff>=0?'var(--success)':'var(--danger)') }};font-weight:600;">
                @if($diff!==null){{ $diff>=0?'↑':'↓' }} {{ abs($diff) }}% vs last month@else{{ number_format($stats['month_amount'],0,',',' ') }}@endif
            </div>
        </div>
    </div>
</div>

{{-- ════════════════════════════
     STATUS ROW
═════════════════════════════ --}}
<div class="g-status">
    <div class="status-mini">
        <div class="status-dot" style="background:rgba(16,185,129,0.12);">✅</div>
        <div><div class="status-val" style="color:var(--success);">{{ number_format($stats['completed']) }}</div><div class="status-lbl">{{ __('app.completed') }}</div></div>
    </div>
    <div class="status-mini">
        <div class="status-dot" style="background:rgba(245,158,11,0.12);">⏳</div>
        <div>
            <div class="status-val" style="color:#F59E0B;">{{ number_format($stats['pending']) }}</div>
            <div class="status-lbl">{{ __('app.pending') }}@if($stats['pending']>0)<a href="{{ route('agent.transactions.index',['status'=>'pending']) }}">{{ __('app.view') }} →</a>@endif</div>
        </div>
    </div>
    <div class="status-mini">
        <div class="status-dot" style="background:rgba(239,68,68,0.12);">❌</div>
        <div><div class="status-val" style="color:var(--danger);">{{ number_format($stats['cancelled']) }}</div><div class="status-lbl">{{ __('app.cancelled') }}</div></div>
    </div>
</div>

{{-- ════════════════════════════
     DEEP STATS TOGGLE
═════════════════════════════ --}}
<div class="stats-toggle-row">
    <div style="font-size:12px; color:var(--text-muted);">
        {{ __('app.advanced_stats_subtitle') }}
    </div>
    <button class="stats-toggle-btn" id="statsToggleBtn" onclick="toggleDeepStats()">
        <span>{{ __('app.all_my_stats') }}</span>
        <span class="stats-toggle-icon">▼</span>
    </button>
</div>

{{-- ════════════════════════════
     DEEP STATS PANEL
     (données NON présentes dans
     les cartes du dessus)
═════════════════════════════ --}}
<div class="deep-stats-panel" id="deepStatsPanel">

    {{-- Row 1 : taux completion | montant moyen | commission moyenne --}}
    <div class="g-deep" style="margin-bottom:clamp(8px,1.4vw,14px);">

        {{-- Completion rate --}}
        <div class="deep-card">
            <div class="deep-card-icon" style="background:rgba(16,185,129,0.12);">🎯</div>
            <div style="flex:1; min-width:0;">
                <div class="deep-card-val" style="color:var(--success);">{{ $stats['completion_rate'] }}%</div>
                <div class="deep-card-lbl">{{ __('app.completion_rate') }}</div>
                <div class="deep-card-sub">{{ $stats['completed'] }} / {{ $stats['total'] }} tx</div>
            </div>
        </div>

        {{-- Average amount --}}
        <div class="deep-card">
            <div class="deep-card-icon" style="background:rgba(2,132,199,0.12);">📊</div>
            <div style="flex:1; min-width:0;">
                <div class="deep-card-val" style="font-size:clamp(15px,2vw,19px);">{{ number_format($stats['avg_amount'],0,',',' ') }}</div>
                <div class="deep-card-lbl">{{ __('app.avg_amount_tx') }}</div>
                <div class="deep-card-sub">@if(auth()->user()->country){{ auth()->user()->country->currency_code }}@endif</div>
            </div>
        </div>

        {{-- Average fee --}}
        <div class="deep-card">
            <div class="deep-card-icon" style="background:rgba(245,158,11,0.12);">💹</div>
            <div style="flex:1; min-width:0;">
                <div class="deep-card-val" style="color:var(--gold); font-size:clamp(15px,2vw,19px);">{{ number_format($stats['avg_fee'],2,',','.') }}</div>
                <div class="deep-card-lbl">{{ __('app.avg_fee_tx') }}</div>
                <div class="deep-card-sub">@if(auth()->user()->country){{ auth()->user()->country->currency_code }}@endif</div>
            </div>
        </div>

    </div>

    {{-- Row 2 : cette semaine | meilleur mois | répartition envoi/retrait --}}
    <div class="g-deep2" style="margin-bottom:clamp(12px,2vw,22px);">

        {{-- This week --}}
        <div class="deep-card">
            <div class="deep-card-icon" style="background:rgba(139,92,246,0.12);">📅</div>
            <div style="flex:1; min-width:0;">
                <div class="deep-card-val" style="color:#8B5CF6;" data-counter="{{ $stats['week'] }}">0</div>
                <div class="deep-card-lbl">{{ __('app.this_week') }}</div>
                <div class="deep-card-sub">{{ number_format($stats['week_amount'],0,',',' ') }} @if(auth()->user()->country){{ auth()->user()->country->currency_code }}@endif</div>
            </div>
        </div>

        {{-- Best month --}}
        <div class="deep-card">
            <div class="deep-card-icon" style="background:rgba(245,158,11,0.12);">🏆</div>
            <div style="flex:1; min-width:0;">
                @if($bestMonth)
                    <div class="deep-card-val" style="color:var(--gold); font-size:clamp(14px,1.8vw,18px);">
                        {{ $monthNames[$bestMonth->month - 1] }} {{ $bestMonth->year }}
                    </div>
                    <div class="deep-card-lbl">{{ __('app.best_month_6mo') }}</div>
                    <div class="deep-card-sub">{{ number_format($bestMonth->total_amount,0,',',' ') }} · {{ $bestMonth->total }} tx</div>
                @else
                    <div class="deep-card-val" style="color:var(--text-muted);">—</div>
                    <div class="deep-card-lbl">{{ __('app.best_month_lbl') }}</div>
                    <div class="deep-card-sub">{{ __('app.no_data_yet') }}</div>
                @endif
            </div>
        </div>

        {{-- Send vs Withdrawal --}}
        <div class="deep-card" style="flex-direction:column; align-items:stretch; gap:8px;">
            <div style="display:flex; align-items:center; justify-content:space-between;">
                <div style="font-size:clamp(10px,1.1vw,12px); font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:1px;">
                    {{ __('app.sends_vs_withdrawals') }}
                </div>
                <span style="font-size:11px; color:var(--text-muted);">{{ $typeTotal }} tx</span>
            </div>
            <div style="display:flex; justify-content:space-between; align-items:baseline;">
                <div>
                    <div style="font-size:clamp(15px,2vw,19px); font-weight:900; color:var(--sky-primary); line-height:1;">{{ $stats['send_count'] }}</div>
                    <div style="font-size:10px; color:var(--text-muted);">📤 {{ __('app.sends_label') }} <span style="color:var(--sky-primary); font-weight:700;">{{ $sendPct }}%</span></div>
                </div>
                <div style="font-size:18px; color:var(--divider);">|</div>
                <div style="text-align:right;">
                    <div style="font-size:clamp(15px,2vw,19px); font-weight:900; color:#8B5CF6; line-height:1;">{{ $stats['withdrawal_count'] }}</div>
                    <div style="font-size:10px; color:var(--text-muted);">📥 {{ __('app.withdrawals_label') }} <span style="color:#8B5CF6; font-weight:700;">{{ $withdrawPct }}%</span></div>
                </div>
            </div>
            @if($typeTotal > 0)
            <div class="type-split-bar">
                <div class="type-split-send"  style="flex:{{ $sendPct }};"></div>
                <div class="type-split-wdraw" style="flex:{{ $withdrawPct }};"></div>
            </div>
            @endif
        </div>

    </div>
</div>

{{-- ════════════════════════════
     CHART + RECENT TX
═════════════════════════════ --}}
<div class="g-cols">

    <div class="card animate-on-scroll" style="padding:0; overflow:hidden;">
        <div class="dash-card-header">
            <div><div class="dash-card-title">📈 {{ __('app.my_progression') }}</div><div class="dash-card-sub">{{ __('app.monthly_vol_mine') }}</div></div>
            <span class="chart-badge">6 months</span>
        </div>
        <div class="chart-wrap">
            <div class="chart-canvas-box"><canvas id="agentChart"></canvas></div>
        </div>
    </div>

    <div class="card animate-on-scroll" style="padding:0; overflow:hidden;">
        <div class="dash-card-header" style="border-bottom:1px solid var(--divider);">
            <div><div class="dash-card-title">⚡ {{ __('app.recent_transactions') }}</div></div>
            <a href="{{ route('agent.transactions.index') }}" class="dash-card-link">{{ __('app.see_all') }} →</a>
        </div>
        @forelse($recentTransactions->take(6) as $tx)
        <div class="tx-item">
            <div class="tx-dot" style="background:{{ $tx->status==='completed'?'rgba(16,185,129,0.12)':($tx->status==='pending'?'rgba(245,158,11,0.12)':'rgba(239,68,68,0.12)') }};">
                {{ $tx->status==='completed'?'✅':($tx->status==='pending'?'⏳':'❌') }}
            </div>
            <div class="tx-main">
                <div class="tx-num">{{ $tx->transaction_number }}</div>
                <div class="tx-name">{{ $tx->sender_name ?: $tx->receiver_name ?: '—' }}</div>
                <div class="tx-route">{{ $tx->originCountry?->flag_emoji }} {{ $tx->originCountry?->code }} → {{ $tx->destinationCountry?->flag_emoji }} {{ $tx->destinationCountry?->code }}</div>
            </div>
            <div class="tx-right">
                <div class="tx-amount">{{ number_format($tx->amount,0,',',' ') }}</div>
                <div class="tx-time">{{ $tx->created_at->diffForHumans() }}</div>
                <div class="tx-actions">
                    <a href="{{ route('agent.transactions.edit',$tx) }}" class="tx-action-btn"
                       style="background:rgba(2,132,199,0.1);color:var(--sky-primary);"
                       onmouseenter="this.style.background='rgba(2,132,199,0.22)'"
                       onmouseleave="this.style.background='rgba(2,132,199,0.1)'"
                       title="{{ __('app.edit') }}">✏️</a>
                    <form method="POST" action="{{ route('agent.transactions.destroy',$tx) }}" style="display:contents;" data-confirm="{{ __('app.delete_tx_confirm') }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="tx-action-btn"
                                style="background:rgba(239,68,68,0.1);color:var(--danger);"
                                onmouseenter="this.style.background='rgba(239,68,68,0.22)'"
                                onmouseleave="this.style.background='rgba(239,68,68,0.1)'"
                                title="{{ __('app.delete') }}">🗑️</button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="empty-state"><div class="empty-state-icon">💸</div><div class="empty-state-text">{{ __('app.no_tx_yet') }}</div></div>
        @endforelse
    </div>

</div>

{{-- ════════════════════════════
     TOP ROUTES + REPORTS
═════════════════════════════ --}}
<div class="g-cols">

    <div class="card animate-on-scroll" style="padding:0; overflow:hidden;">
        <div class="dash-card-header" style="border-bottom:1px solid var(--divider);">
            <div><div class="dash-card-title">🛣️ {{ __('app.top_routes') }}</div><div class="dash-card-sub">{{ __('app.top_routes_subtitle') }}</div></div>
        </div>
        @forelse($topRoutes as $i => $route)
        @php $pct = $stats['completed']>0 ? round(($route->count/$stats['completed'])*100) : 0; @endphp
        <div class="route-row">
            <div class="rank-dot">{{ $i+1 }}</div>
            <div class="route-flags">{{ $route->originCountry?->flag_emoji }}<span style="font-size:11px;color:var(--sky-primary);">→</span>{{ $route->destinationCountry?->flag_emoji }}</div>
            <div class="route-main">
                <div class="route-codes">
                    <span style="color:var(--text-heading);">{{ $route->originCountry?->code }}</span>
                    <span style="color:var(--sky-primary);font-size:11px;">→</span>
                    <span style="color:var(--text-heading);">{{ $route->destinationCountry?->code }}</span>
                </div>
                <div class="route-stats">{{ number_format($route->count) }} tx · {{ number_format($route->total_amount,0,',',' ') }}</div>
            </div>
            <div class="route-right">
                <div class="route-pct">{{ $pct }}%</div>
                <div class="mini-bar"><div class="mini-bar-fill" style="width:{{ $pct }}%;"></div></div>
            </div>
        </div>
        @empty
        <div class="empty-state"><div class="empty-state-icon">🛣️</div><div class="empty-state-text">{{ __('app.no_tx_yet') }}</div></div>
        @endforelse
    </div>

    <div class="reports-col">

        <div class="card animate-on-scroll" style="padding:0; overflow:hidden;">
            <div class="dash-card-header" style="border-bottom:1px solid var(--divider);">
                <div><div class="dash-card-title">📝 {{ __('app.report_title') }}</div><div class="dash-card-sub">{{ __('app.report_subtitle') }}</div></div>
            </div>
            <div style="padding:clamp(12px,1.6vw,16px) clamp(14px,1.8vw,18px);">
                @if(session('report_success'))
                    <div class="alert alert-success" style="margin-bottom:12px;font-size:13px;">✅ {{ session('report_success') }}</div>
                @endif
                <form method="POST" action="{{ route('agent.reports.store') }}">
                    @csrf
                    <div style="margin-bottom:10px;">
                        <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror"
                               placeholder="{{ __('app.report_subject_placeholder') }}"
                               value="{{ old('subject') }}" required maxlength="150">
                        @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div style="margin-bottom:12px;">
                        <textarea name="message" class="form-control @error('message') is-invalid @enderror"
                                  rows="3" placeholder="{{ __('app.report_message_placeholder') }}"
                                  required maxlength="2000" style="resize:vertical;">{{ old('message') }}</textarea>
                        @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
                        📤 {{ __('app.report_send') }}
                    </button>
                </form>
            </div>
        </div>

        <div class="card animate-on-scroll" style="padding:0; overflow:hidden;">
            <div class="dash-card-header" style="border-bottom:1px solid var(--divider);">
                <div class="dash-card-title">📂 {{ __('app.report_history') }}</div>
            </div>
            @forelse($myReports as $report)
            <div class="report-row" style="flex-direction:column; align-items:stretch; gap:0; padding:0;">

                {{-- Report header --}}
                <div style="display:flex; align-items:flex-start; gap:10px; padding:clamp(10px,1.3vw,13px) clamp(12px,1.5vw,16px);">
                    <span style="font-size:15px; flex-shrink:0; margin-top:1px;">
                        {{ $report->admin_reply ? '💬' : ($report->status==='read' ? '✅' : '🕐') }}
                    </span>
                    <div class="report-main">
                        <div class="report-subject">{{ $report->subject }}</div>
                        <div class="report-meta">
                            <span>{{ $report->created_at->diffForHumans() }}</span>
                            @if($report->admin_reply)
                                <span style="color:var(--success); font-weight:700;">
                                    ✅ {{ __('app.report_replied') }}
                                </span>
                            @else
                                <span style="color:{{ $report->status==='read'?'#22c55e':'#f59e0b' }}; font-weight:600;">
                                    {{ $report->status==='read' ? __('app.report_read') : __('app.report_unread') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Admin reply block --}}
                @if($report->admin_reply)
                <div style="margin:0 clamp(10px,1.5vw,16px) clamp(10px,1.3vw,13px); padding:10px 13px;
                            background:rgba(16,185,129,0.07); border-radius:9px;
                            border-left:3px solid var(--success);">
                    <div style="font-size:10px; font-weight:700; color:var(--success); text-transform:uppercase;
                                letter-spacing:1px; margin-bottom:5px;">
                        🛡️ {{ __('app.report_reply_label') }}
                        @if($report->replied_at)
                            <span style="font-weight:400; color:var(--text-muted); text-transform:none; letter-spacing:0;">
                                — {{ $report->replied_at->format('d/m/Y H:i') }}
                            </span>
                        @endif
                    </div>
                    <div style="font-size:12.5px; color:var(--text-secondary); line-height:1.55; white-space:pre-line;">{{ $report->admin_reply }}</div>
                </div>
                @endif

            </div>
            @empty
            <div class="empty-state" style="padding:22px;"><div class="empty-state-text">{{ __('app.report_none_yet') }}</div></div>
            @endforelse
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
/* ── Deep stats toggle ── */
function toggleDeepStats() {
    const panel = document.getElementById('deepStatsPanel');
    const btn   = document.getElementById('statsToggleBtn');
    const open  = panel.classList.toggle('open');
    btn.classList.toggle('open', open);
    if (open) {
        // trigger data-counter animations for weekly stat
        panel.querySelectorAll('[data-counter]').forEach(el => {
            if (el.dataset.animated) return;
            el.dataset.animated = true;
            const target = parseInt(el.dataset.counter, 10);
            let current  = 0;
            const step   = Math.max(1, Math.ceil(target / 40));
            const timer  = setInterval(() => {
                current = Math.min(current + step, target);
                el.textContent = new Intl.NumberFormat('fr-FR').format(current);
                if (current >= target) clearInterval(timer);
            }, 25);
        });
    }
}

/* ── Chart ── */
@php
    $agentJsMonths = collect(range(1, 12))->map(fn($m) => \Carbon\Carbon::createFromDate(2000, $m, 1)->locale(app()->getLocale())->isoFormat('MMM'))->values()->toArray();
@endphp
const agentMonthly  = @json($monthlyData);
const agentMonthAbb = @json($agentJsMonths);
const isDark = () => document.documentElement.getAttribute('data-theme') === 'dark';
const gridC  = () => isDark() ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.04)';
const tickC  = () => isDark() ? '#64748B' : '#94A3B8';
const ttBg   = () => isDark() ? '#1F2937' : '#ffffff';
const ttTxt  = () => isDark() ? '#F1F5F9' : '#0F172A';
const ttBdr  = () => isDark() ? '#374151' : '#E2E8F0';

const months6 = [], amounts6 = [];
for (let i = 5; i >= 0; i--) {
    const d = new Date(); d.setMonth(d.getMonth() - i);
    const y = d.getFullYear(), m = d.getMonth() + 1;
    months6.push(agentMonthAbb[m - 1]);
    const f = agentMonthly.find(x => x.year == y && x.month == m);
    amounts6.push(f ? parseFloat(f.total_amount) : 0);
}

const agentChart = new Chart(document.getElementById('agentChart'), {
    type: 'bar',
    data: {
        labels: months6,
        datasets: [{
            label: '{{ __("app.monthly_vol_mine") }}',
            data: amounts6,
            backgroundColor: ctx => {
                const g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 210);
                g.addColorStop(0, 'rgba(2,132,199,0.85)');
                g.addColorStop(1, 'rgba(14,165,233,0.3)');
                return g;
            },
            borderColor: '#0284C7', borderWidth: 2,
            borderRadius: 8, borderSkipped: false,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: ttBg(), titleColor: ttTxt(),
                bodyColor: '#0284C7', borderColor: ttBdr(),
                borderWidth: 1, padding: 10, cornerRadius: 10,
                callbacks: { label: ctx => ' ' + new Intl.NumberFormat('fr-FR').format(ctx.raw) }
            }
        },
        scales: {
            y: { beginAtZero: true, grid: { color: gridC() }, ticks: { color: tickC(), maxTicksLimit: 5 } },
            x: { ticks: { color: tickC() }, grid: { display: false } }
        },
        animation: { duration: 700, easing: 'easeOutQuart' }
    }
});

window.blueskyCharts = [agentChart];
requestAnimationFrame(() => agentChart.resize());

new MutationObserver(() => {
    Object.values(agentChart.options.scales || {}).forEach(s => {
        if (s.ticks) s.ticks.color = tickC();
        if (s.grid)  s.grid.color  = gridC();
    });
    const tt = agentChart.options.plugins.tooltip;
    tt.backgroundColor = ttBg(); tt.titleColor = ttTxt(); tt.borderColor = ttBdr();
    agentChart.update('none');
}).observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });
</script>
@endpush
