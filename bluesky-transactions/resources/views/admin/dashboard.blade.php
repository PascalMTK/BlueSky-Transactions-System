@extends('layouts.app')

@section('title', __('app.dashboard'))
@section('page-title', __('app.dashboard'))
@section('page-subtitle', __('app.overview'))

@section('content')

{{-- Stats Cards with animated counters --}}
<div class="stats-grid">
    <div class="stat-card blue animate-on-scroll">
        <div class="stat-icon blue">💸</div>
        <div class="stat-info">
            <div class="stat-value" data-counter="{{ $stats['total_transactions'] }}">{{ number_format($stats['total_transactions']) }}</div>
            <div class="stat-label">{{ __('app.total_transactions') }}</div>
            <div class="stat-change">📅 {{ number_format($stats['transactions_today']) }} {{ __('app.today') }}</div>
        </div>
    </div>
    <div class="stat-card gold animate-on-scroll">
        <div class="stat-icon gold">💰</div>
        <div class="stat-info">
            <div class="stat-value" style="font-size:20px" data-counter="{{ $stats['total_amount'] }}" data-suffix="">
                {{ number_format($stats['total_amount'], 0, ',', ' ') }}
            </div>
            <div class="stat-label">{{ __('app.total_volume') }}</div>
            <div class="stat-change">{{ __('app.this_month') }}: {{ number_format($stats['amount_month'], 0, ',', ' ') }}</div>
        </div>
    </div>
    <div class="stat-card green animate-on-scroll">
        <div class="stat-icon green">✅</div>
        <div class="stat-info">
            <div class="stat-value" style="font-size:20px" data-counter="{{ $stats['total_fees'] }}">
                {{ number_format($stats['total_fees'], 0, ',', ' ') }}
            </div>
            <div class="stat-label">{{ __('app.total_commissions') }}</div>
        </div>
    </div>
    <div class="stat-card purple animate-on-scroll">
        <div class="stat-icon purple">👥</div>
        <div class="stat-info">
            <div class="stat-value" data-counter="{{ $stats['active_agents'] }}">{{ number_format($stats['active_agents']) }}</div>
            <div class="stat-label">{{ __('app.active_agents') }} / {{ $stats['total_agents'] }}</div>
            @if($stats['pending_agents'] > 0)
                <div class="stat-change negative">⚠️ {{ $stats['pending_agents'] }} {{ __('app.waiting') }}</div>
            @endif
        </div>
    </div>
    <div class="stat-card teal animate-on-scroll">
        <div class="stat-icon teal">🌍</div>
        <div class="stat-info">
            <div class="stat-value" data-counter="{{ $stats['countries_active'] }}">{{ number_format($stats['countries_active']) }}</div>
            <div class="stat-label">{{ __('app.operational_countries') }}</div>
            <div class="stat-change">{{ __('app.subsaharan') }}</div>
        </div>
    </div>
    <div class="stat-card red animate-on-scroll">
        <div class="stat-icon red">📆</div>
        <div class="stat-info">
            <div class="stat-value" data-counter="{{ $stats['transactions_month'] }}">{{ number_format($stats['transactions_month']) }}</div>
            <div class="stat-label">{{ __('app.transactions_month') }}</div>
            <div class="stat-change">{{ now()->locale(app()->getLocale())->monthName }} {{ now()->year }}</div>
        </div>
    </div>
</div>

{{-- Alerte rapports non lus --}}
@if($unreadReportsCount > 0)
<div class="animate-on-scroll" style="background:linear-gradient(135deg,#FEF3C7,#FDE68A);border:1.5px solid #F59E0B;border-radius:var(--radius);padding:14px 20px;margin-bottom:18px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
    <div style="display:flex;align-items:center;gap:12px;">
        <div style="width:40px;height:40px;border-radius:10px;background:#F59E0B;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;">📬</div>
        <div>
            <div style="font-weight:800;font-size:14px;color:#92400E;">{{ $unreadReportsCount }} {{ __('app.report_results') }} {{ __('app.report_unread') }}</div>
            <div style="font-size:12px;color:#B45309;margin-top:1px;">
                @foreach($unreadReports->take(2) as $r){{ $r->agent?->name }}: <em>{{ Str::limit($r->subject, 35) }}</em> &nbsp;@endforeach
            </div>
        </div>
    </div>
    <a href="{{ route('admin.reports.index') }}" class="btn btn-sm" style="background:#F59E0B;color:white;font-weight:700;border:none;">{{ __('app.view') }} →</a>
</div>
@endif

{{-- Pending agents alerte --}}
@if($stats['pending_agents'] > 0)
<div class="animate-on-scroll" style="background:linear-gradient(135deg,#EFF6FF,#DBEAFE);border:1.5px solid #0284C7;border-radius:var(--radius);padding:14px 20px;margin-bottom:18px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
    <div style="display:flex;align-items:center;gap:12px;">
        <div style="width:40px;height:40px;border-radius:10px;background:var(--sky-primary);display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;">👤</div>
        <div>
            <div style="font-weight:800;font-size:14px;color:#0C4A6E;">{{ $stats['pending_agents'] }} {{ __('app.waiting') }} — {{ __('app.agent_management') }}</div>
            <div style="font-size:12px;color:#0369A1;margin-top:1px;">{{ __('app.activate') }} / {{ __('app.deactivate') }}</div>
        </div>
    </div>
    <a href="{{ route('admin.agents.index', ['status' => 'pending']) }}" class="btn btn-sm btn-primary" style="font-weight:700;">{{ __('app.manage') }} →</a>
</div>
@endif

{{-- Status breakdown + Aujourd'hui + Top route --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:20px;">
    {{-- Statuts --}}
    <div class="card animate-on-scroll" style="padding:16px;">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted);margin-bottom:12px;">📊 {{ __('app.status') }}</div>
        <div style="display:flex;flex-direction:column;gap:8px;">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <span style="font-size:12px;color:var(--text-secondary)">✅ {{ __('app.completed') }}</span>
                <span style="font-weight:800;color:var(--success);">{{ number_format($stats['tx_completed']) }}</span>
            </div>
            <div style="height:1px;background:var(--divider);"></div>
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <span style="font-size:12px;color:var(--text-secondary)">⏳ {{ __('app.pending') }}</span>
                <span style="font-weight:800;color:#F59E0B;">{{ number_format($stats['tx_pending']) }}</span>
            </div>
            <div style="height:1px;background:var(--divider);"></div>
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <span style="font-size:12px;color:var(--text-secondary)">❌ {{ __('app.cancelled') }}</span>
                <span style="font-weight:800;color:var(--danger);">{{ number_format($stats['tx_cancelled']) }}</span>
            </div>
        </div>
    </div>
    {{-- Aujourd'hui --}}
    <div class="card animate-on-scroll" style="padding:16px;">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted);margin-bottom:12px;">☀️ {{ ucfirst(__('app.today')) }}</div>
        <div style="font-size:28px;font-weight:900;color:var(--sky-primary);line-height:1;">{{ number_format($stats['transactions_today']) }}</div>
        <div style="font-size:11px;color:var(--text-muted);margin-top:3px;margin-bottom:10px;">{{ __('app.total_transactions') }}</div>
        <div style="font-size:15px;font-weight:800;color:var(--text-primary);">{{ number_format($stats['amount_today'], 0, ',', ' ') }}</div>
        <div style="font-size:11px;color:var(--text-muted);margin-top:2px;">{{ __('app.total_volume') }}</div>
    </div>
    {{-- Top route --}}
    <div class="card animate-on-scroll" style="padding:16px;">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted);margin-bottom:12px;">🏆 {{ __('app.top_routes') }}</div>
        @if($topRoute)
            <div style="display:flex;align-items:center;justify-content:center;gap:10px;margin:10px 0;">
                <div style="text-align:center;">
                    <x-flag :code="$topRoute->originCountry?->code" size="lg" style="border-radius:6px; box-shadow:0 2px 8px rgba(0,0,0,0.18);" />
                    <div style="font-size:10px;font-weight:700;color:var(--text-muted);margin-top:4px;">{{ $topRoute->originCountry?->code }}</div>
                </div>
                <div style="color:var(--sky-primary);font-size:20px;font-weight:900;">→</div>
                <div style="text-align:center;">
                    <x-flag :code="$topRoute->destinationCountry?->code" size="lg" style="border-radius:6px; box-shadow:0 2px 8px rgba(0,0,0,0.18);" />
                    <div style="font-size:10px;font-weight:700;color:var(--text-muted);margin-top:4px;">{{ $topRoute->destinationCountry?->code }}</div>
                </div>
            </div>
            <div style="text-align:center;font-size:12px;color:var(--text-muted);">{{ number_format($topRoute->count) }} tx &nbsp;·&nbsp; {{ number_format($topRoute->total_amount, 0, ',', ' ') }}</div>
        @else
            <div style="text-align:center;color:var(--text-muted);font-size:13px;padding:16px 0;">—</div>
        @endif
    </div>
</div>

{{-- Charts --}}
<div class="charts-row">
    <div class="card animate-on-scroll">
        <div class="card-header">
            <div>
                <div class="card-title">📈 {{ __('app.monthly_volume') }}</div>
                <div class="card-subtitle">{{ __('app.last_12_months') }}</div>
            </div>
        </div>
        <div class="card-body" style="padding-bottom:18px;">
            <div style="position:relative; height:240px;">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>
    </div>
    <div class="card animate-on-scroll">
        <div class="card-header">
            <div>
                <div class="card-title">🌍 {{ __('app.country_dist') }}</div>
                <div class="card-subtitle">{{ __('app.sent_transactions') }}</div>
            </div>
        </div>
        <div class="card-body" style="padding-bottom:18px;">
            <div style="position:relative; height:240px;">
                <canvas id="countryChart"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Country Stats Table --}}
<div class="table-card mb-20 animate-on-scroll">
    <div class="table-header">
        <div class="table-title">🗺️ {{ __('app.country_stats') }}</div>
    </div>
    <table class="bsky-table">
        <thead>
            <tr>
                <th>{{ __('app.country') }}</th>
                <th>{{ __('app.sent') }}</th>
                <th>{{ __('app.sent_amount') }}</th>
                <th>{{ __('app.received') }}</th>
                <th>{{ __('app.received_amount') }}</th>
                <th>{{ __('app.balance') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($countryStats as $country)
                <tr>
                    <td>
                        <span style="font-size:22px">{{ $country->flag_emoji }}</span>
                        <span style="font-weight:600; margin-left:8px">{{ $country->name }}</span>
                    </td>
                    <td><span class="badge badge-completed">{{ number_format($country->sent_count ?? 0) }}</span></td>
                    <td class="amount-display amount-primary">{{ number_format($country->sent_amount ?? 0, 0, ',', ' ') }}</td>
                    <td><span class="badge badge-pending">{{ number_format($country->received_count ?? 0) }}</span></td>
                    <td class="amount-display" style="color:var(--success)">{{ number_format($country->received_amount ?? 0, 0, ',', ' ') }}</td>
                    <td>
                        @php $balance = ($country->sent_amount ?? 0) - ($country->received_amount ?? 0); @endphp
                        <span style="color:{{ $balance >= 0 ? 'var(--sky-primary)' : 'var(--danger)' }}; font-weight:700; font-family:monospace;">
                            {{ $balance >= 0 ? '+' : '' }}{{ number_format($balance, 0, ',', ' ') }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center; padding:30px; color:var(--text-muted)">{{ __('app.no_data') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Bottom row --}}
<div style="display:grid; grid-template-columns:2fr 1fr; gap:18px">
    <div class="table-card animate-on-scroll">
        <div class="table-header">
            <div class="table-title">⚡ {{ __('app.recent_transactions') }}</div>
            <a href="{{ route('admin.transactions.index') }}" class="btn btn-sm btn-outline-primary">{{ __('app.see_all') }} →</a>
        </div>
        <table class="bsky-table">
            <thead>
                <tr>
                    <th>{{ __('app.transaction_number') }}</th>
                    <th>{{ __('app.sender') }}</th>
                    <th>{{ __('app.route') }}</th>
                    <th>{{ __('app.amount') }}</th>
                    <th>{{ __('app.date') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentTransactions as $tx)
                    <tr>
                        <td><span class="tx-number">{{ $tx->transaction_number }}</span></td>
                        <td>
                            <div style="font-weight:600">{{ $tx->sender_name }}</div>
                            <div style="font-size:11px; color:var(--text-muted)">{{ $tx->sender_phone }}</div>
                        </td>
                        <td>
                            <span class="route-pill">
                                {{ $tx->originCountry?->flag_emoji }} {{ $tx->originCountry?->code }}
                                → {{ $tx->destinationCountry?->flag_emoji }} {{ $tx->destinationCountry?->code }}
                            </span>
                        </td>
                        <td>
                            <div class="amount-display amount-primary">{{ number_format($tx->amount, 0, ',', ' ') }}</div>
                            <div style="font-size:11px; color:var(--text-muted)">+{{ number_format($tx->fee_amount, 0) }} {{ __('app.fee') }}</div>
                        </td>
                        <td style="color:var(--text-muted); font-size:12px">{{ $tx->created_at->format('d/m H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align:center; padding:30px; color:var(--text-muted)">{{ __('app.no_transaction') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="table-card animate-on-scroll">
        <div class="table-header">
            <div class="table-title">🏆 {{ __('app.top_agents') }}</div>
            <a href="{{ route('admin.agents.index') }}" class="btn btn-sm btn-outline-primary">{{ __('app.manage') }} →</a>
        </div>
        @forelse($topAgents as $index => $agent)
            <div style="padding:12px 18px; border-bottom:1px solid var(--divider); display:flex; align-items:center; gap:10px; transition:background 0.15s;" onmouseenter="this.style.background='var(--bg-row-hover)'" onmouseleave="this.style.background=''">
                <div style="width:28px; height:28px; border-radius:50%; background:linear-gradient(135deg,var(--sky-primary),var(--sky-secondary)); display:flex; align-items:center; justify-content:center; color:white; font-weight:800; font-size:12px; flex-shrink:0;">{{ $index + 1 }}</div>
                <div style="flex:1; min-width:0">
                    <div style="font-weight:700; font-size:13px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $agent->name }}</div>
                    <div style="font-size:11px; color:var(--text-muted)">{{ $agent->country?->flag_emoji }} — {{ number_format($agent->tx_count ?? 0) }} {{ __('app.tx_count') }}</div>
                </div>
                <div class="amount-display amount-primary" style="font-size:12px; flex-shrink:0;">{{ number_format($agent->tx_amount ?? 0, 0, ',', ' ') }}</div>
            </div>
        @empty
            <div style="padding:30px; text-align:center; color:var(--text-muted)">{{ __('app.no_agents') }}</div>
        @endforelse
    </div>
</div>

{{-- DANGER ZONE --}}
<div class="card animate-on-scroll" style="margin-top:24px; border:2px solid rgba(239,68,68,0.22); overflow:hidden;">
    <div class="card-header" style="background:rgba(239,68,68,0.04); border-bottom:1px solid rgba(239,68,68,0.14); padding:14px 22px; display:flex; align-items:center; gap:12px;">
        <div style="width:36px;height:36px;background:rgba(239,68,68,0.1);border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;">⚠️</div>
        <div>
            <div class="card-title" style="color:#EF4444;">{{ __('app.reset_system_title') }}</div>
        </div>
    </div>

    {{-- Row 1: Full system reset --}}
    <div style="padding:16px 22px; display:flex; align-items:center; justify-content:space-between; gap:14px; flex-wrap:wrap;">
        <div>
            <div style="font-size:13px; font-weight:700; color:var(--text-primary); margin-bottom:2px;">{{ __('app.reset_system_btn') }}</div>
            <div style="font-size:12px; color:var(--text-muted);">{{ __('app.reset_system_desc') }}</div>
        </div>
        <button type="button" id="btnResetSystem"
            style="display:inline-flex;align-items:center;gap:7px;padding:9px 20px;border-radius:9px;border:1.5px solid #EF4444;background:transparent;color:#EF4444;font-size:13px;font-weight:700;cursor:pointer;transition:background 0.18s;flex-shrink:0;"
            onmouseenter="this.style.background='rgba(239,68,68,0.07)'"
            onmouseleave="this.style.background='transparent'">
            🗑️ {{ __('app.reset_system_btn') }}
        </button>
    </div>

    {{-- Divider --}}
    <div style="margin:0 22px; border-top:1px dashed rgba(239,68,68,0.2);"></div>

    {{-- Row 2: Reset by country --}}
    <div style="padding:16px 22px; display:flex; align-items:center; justify-content:space-between; gap:14px; flex-wrap:wrap;">
        <div>
            <div style="font-size:13px; font-weight:700; color:var(--text-primary); margin-bottom:2px;">{{ __('app.reset_by_country_section') }}</div>
            <div style="font-size:12px; color:var(--text-muted);">{{ __('app.reset_by_country_desc') }}</div>
        </div>
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; flex-shrink:0;">
            <select id="selectResetCountry"
                style="padding:8px 12px; border-radius:9px; border:1px solid var(--border); background:var(--bg-input); color:var(--text-primary); font-size:13px; min-width:180px; outline:none; cursor:pointer;">
                <option value="">{{ __('app.select_country_to_reset') }}</option>
                @foreach($countries as $c)
                    <option value="{{ $c->id }}"
                        data-name="{{ $c->name }}"
                        data-flag="{{ $c->flag_emoji }}">
                        {{ $c->flag_emoji }} {{ $c->name }}
                    </option>
                @endforeach
            </select>
            <button type="button" id="btnResetCountry" disabled
                style="display:inline-flex;align-items:center;gap:7px;padding:9px 20px;border-radius:9px;border:1.5px solid #EF4444;background:transparent;color:#EF4444;font-size:13px;font-weight:700;cursor:not-allowed;opacity:0.4;transition:background 0.18s,opacity 0.2s;flex-shrink:0;"
                onmouseenter="if(!this.disabled)this.style.background='rgba(239,68,68,0.07)'"
                onmouseleave="this.style.background='transparent'">
                🗑️ {{ __('app.reset_by_country_btn') }}
            </button>
        </div>
    </div>
</div>

{{-- Hidden form: full system reset --}}
<form id="formResetSystem" method="POST" action="{{ route('admin.system.reset') }}" style="display:none;">
    @csrf
    @method('DELETE')
</form>

{{-- Hidden form: per-country reset (action set dynamically) --}}
<form id="formResetCountry" method="POST" action="" data-action-base="{{ url('/admin/system/reset') }}" style="display:none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<script>
const monthlyData  = @json($monthlyData);
const countryStats = @json($countryStats);
@php
    $jsMonths = collect(range(1, 12))->map(fn($m) => \Carbon\Carbon::createFromDate(2000, $m, 1)->locale(app()->getLocale())->isoFormat('MMM'))->toArray();
@endphp
const months = @json($jsMonths);

const isDark    = () => document.documentElement.getAttribute('data-theme') === 'dark';
const gridColor = () => isDark() ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
const tickColor = () => isDark() ? '#64748B' : '#94A3B8';

// Build 12 months data
const labels = [], amounts = [], counts = [];
for (let i = 11; i >= 0; i--) {
    const d = new Date(); d.setMonth(d.getMonth() - i);
    const y = d.getFullYear(), m = d.getMonth() + 1;
    labels.push(months[m-1] + ' ' + String(y).slice(2));
    const f = monthlyData.find(x => x.year == y && x.month == m);
    amounts.push(f ? parseFloat(f.total_amount) : 0);
    counts.push(f ? parseInt(f.total) : 0);
}

const monthlyChart = new Chart(document.getElementById('monthlyChart'), {
    type: 'line',
    data: {
        labels,
        datasets: [{
            label: '{{ __("app.amount") }}',
            data: amounts,
            borderColor: '#0284C7',
            backgroundColor: 'rgba(2,132,199,0.08)',
            fill: true, tension: 0.4,
            pointBackgroundColor: '#0284C7', pointRadius: 4, pointHoverRadius: 7,
        }, {
            label: '{{ __("app.chart_nb_tx_label") }}',
            data: counts,
            borderColor: '#F59E0B',
            backgroundColor: 'transparent',
            tension: 0.4,
            pointBackgroundColor: '#F59E0B', pointRadius: 4,
            yAxisID: 'y1',
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { position: 'top', labels: { color: tickColor(), boxWidth: 12, usePointStyle: true } },
        },
        scales: {
            y:  { beginAtZero: true, grid: { color: gridColor() }, ticks: { color: tickColor(), maxTicksLimit: 6 } },
            y1: { position: 'right', beginAtZero: true, grid: { display: false }, ticks: { color: tickColor() } }
        }
    }
});

const cRaw    = countryStats.filter(c => (parseFloat(c.sent_amount) || 0) > 0);
const cLabels = cRaw.map(c => (c.flag_emoji || '') + ' ' + c.code);
const cData   = cRaw.map(c => parseFloat(c.sent_amount) || 0);
const cColors = ['#0284C7','#F59E0B','#10B981','#8B5CF6','#EF4444','#14B8A6','#EC4899','#6366F1'];

let countryChart;
const countryCanvas = document.getElementById('countryChart');

if (cData.length > 0) {
    countryChart = new Chart(countryCanvas, {
        type: 'doughnut',
        data: {
            labels: cLabels,
            datasets: [{ data: cData, backgroundColor: cColors, hoverOffset: 10, borderWidth: 2, borderColor: 'transparent' }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: { legend: { position: 'bottom', labels: { color: tickColor(), font: { size: 11 }, boxWidth: 10, usePointStyle: true } } }
        }
    });
} else {
    countryCanvas.style.display = 'none';
    countryCanvas.parentElement.insertAdjacentHTML('beforeend',
        `<div style="display:flex;align-items:center;justify-content:center;height:100%;color:var(--text-muted);font-size:13px;flex-direction:column;gap:8px;">
            <span style="font-size:32px;opacity:0.3;">🌍</span>
            <span>{{ __('app.no_data') }}</span>
        </div>`
    );
}

window.blueskyCharts = [monthlyChart, countryChart].filter(Boolean);

// Update charts on theme change
const observer = new MutationObserver(() => {
    window.blueskyCharts.forEach(c => {
        c.options.plugins.legend.labels.color = tickColor();
        if (c.options.scales) Object.values(c.options.scales).forEach(s => {
            if (s.ticks) s.ticks.color = tickColor();
            if (s.grid)  s.grid.color = gridColor();
        });
        c.update('none');
    });
});
observer.observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });

// System reset button
document.getElementById('btnResetSystem').addEventListener('click', async () => {
    const i18n = window.bskyI18n || {};
    const ok = await bskyDangerConfirm({
        title:       i18n.reset_system_confirm_title,
        message:     i18n.reset_system_confirm_msg,
        checkLabel:  i18n.reset_system_confirm_check,
    });
    if (ok) document.getElementById('formResetSystem').submit();
});

// Country reset — enable button only when a country is selected
const selCountry   = document.getElementById('selectResetCountry');
const btnCountry   = document.getElementById('btnResetCountry');
const formCountry  = document.getElementById('formResetCountry');

selCountry.addEventListener('change', () => {
    const has = !!selCountry.value;
    btnCountry.disabled      = !has;
    btnCountry.style.opacity = has ? '1'            : '0.4';
    btnCountry.style.cursor  = has ? 'pointer'      : 'not-allowed';
});

btnCountry.addEventListener('click', async () => {
    const opt  = selCountry.options[selCountry.selectedIndex];
    const name = opt.getAttribute('data-name') || '';
    const flag = opt.getAttribute('data-flag') || '';
    const i18n = window.bskyI18n || {};

    const ok = await bskyDangerConfirm({
        title:       i18n.reset_by_country_confirm_title,
        message:     (i18n.reset_by_country_confirm_msg || '').replace(':name', flag + ' ' + name),
        checkLabel:  i18n.reset_system_confirm_check,
    });
    if (!ok) return;

    formCountry.action = formCountry.dataset.actionBase + '/' + selCountry.value;
    formCountry.submit();
});
</script>
@endpush
