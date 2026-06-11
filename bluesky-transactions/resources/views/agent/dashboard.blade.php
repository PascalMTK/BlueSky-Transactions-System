@extends('layouts.app')

@section('title', __('app.my_space'))
@section('page-title', __('app.my_space'))
@section('page-subtitle', __('app.hello') . ', ' . auth()->user()->name . ' — ' . (auth()->user()->country?->flag_emoji ?? '') . ' ' . (auth()->user()->country?->name ?? ''))

@section('content')

{{-- Welcome banner --}}
<div class="welcome-banner" style="background:linear-gradient(135deg,var(--sky-primary),var(--sky-deeper)); border-radius:var(--radius); padding:22px 26px; margin-bottom:22px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:14px; animation:fadeInUp 0.5s ease;">
    <div>
        <div style="font-size:12px; color:rgba(255,255,255,0.65); margin-bottom:4px; text-transform:uppercase; letter-spacing:1.5px">{{ __('app.agent_dashboard') }}</div>
        <div style="font-size:22px; font-weight:800; color:white">{{ __('app.hello') }}, {{ explode(' ', auth()->user()->name)[0] }} 👋</div>
        <div style="font-size:13px; color:rgba(255,255,255,0.75); margin-top:4px">
            Code:
            <span style="font-family:monospace; background:rgba(255,255,255,0.15); padding:2px 8px; border-radius:5px;">{{ auth()->user()->agent_code }}</span>
            @if(auth()->user()->country)
                &nbsp;|&nbsp; {{ auth()->user()->country->flag_emoji }} {{ auth()->user()->country->name }}
            @endif
        </div>
    </div>
    <a href="{{ route('agent.transactions.create') }}" class="btn btn-xl" style="background:white; color:var(--sky-primary); font-weight:800; box-shadow:0 4px 16px rgba(0,0,0,0.2);">
        ➕ {{ __('app.new_transaction') }}
    </a>
</div>

{{-- Stats --}}
<div class="stats-grid">
    <div class="stat-card blue animate-on-scroll">
        <div class="stat-icon blue">📋</div>
        <div class="stat-info">
            <div class="stat-value" data-counter="{{ $stats['total'] }}">0</div>
            <div class="stat-label">{{ __('app.total_transactions') }}</div>
            <div class="stat-change">📅 {{ $stats['today'] }} {{ __('app.today') }} · {{ number_format($stats['today_amount'], 0, ',', ' ') }}</div>
        </div>
    </div>
    <div class="stat-card gold animate-on-scroll">
        <div class="stat-icon gold">💰</div>
        <div class="stat-info">
            <div class="stat-value" style="font-size:20px">{{ number_format($stats['total_amount'], 0, ',', ' ') }}</div>
            <div class="stat-label">{{ __('app.total_volume') }}</div>
        </div>
    </div>
    <div class="stat-card green animate-on-scroll">
        <div class="stat-icon green">✅</div>
        <div class="stat-info">
            <div class="stat-value" style="font-size:20px">{{ number_format($stats['total_fees'], 0, ',', ' ') }}</div>
            <div class="stat-label">{{ __('app.total_commissions') }}</div>
        </div>
    </div>
    <div class="stat-card purple animate-on-scroll">
        <div class="stat-icon purple">📆</div>
        <div class="stat-info">
            <div class="stat-value" data-counter="{{ $stats['month'] }}">0</div>
            <div class="stat-label">{{ __('app.this_month') }}</div>
            @php
                $diff = $stats['last_month_amount'] > 0
                    ? round((($stats['month_amount'] - $stats['last_month_amount']) / $stats['last_month_amount']) * 100)
                    : null;
            @endphp
            <div class="stat-change" style="color:{{ $diff === null ? 'var(--text-muted)' : ($diff >= 0 ? 'var(--success)' : 'var(--danger)') }}">
                {{ $diff !== null ? ($diff >= 0 ? '↑' : '↓') . abs($diff) . '% vs last month' : number_format($stats['month_amount'], 0, ',', ' ') }}
            </div>
        </div>
    </div>
</div>

{{-- Status breakdown --}}
<div style="display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-top:14px;">
    <div class="stat-card animate-on-scroll" style="padding:14px 16px; flex-direction:row; align-items:center; gap:12px;">
        <div style="width:38px;height:38px;border-radius:10px;background:rgba(16,185,129,0.12);display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;">✅</div>
        <div>
            <div style="font-size:22px;font-weight:800;color:var(--success)">{{ number_format($stats['completed']) }}</div>
            <div style="font-size:11px;color:var(--text-muted);margin-top:1px;">{{ __('app.completed') }}</div>
        </div>
    </div>
    <div class="stat-card animate-on-scroll" style="padding:14px 16px; flex-direction:row; align-items:center; gap:12px;">
        <div style="width:38px;height:38px;border-radius:10px;background:rgba(245,158,11,0.12);display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;">⏳</div>
        <div>
            <div style="font-size:22px;font-weight:800;color:#F59E0B">{{ number_format($stats['pending']) }}</div>
            <div style="font-size:11px;color:var(--text-muted);margin-top:1px;">{{ __('app.pending') }}</div>
            @if($stats['pending'] > 0)
                <a href="{{ route('agent.transactions.index', ['status' => 'pending']) }}" style="font-size:10px;color:var(--sky-primary);text-decoration:none;">{{ __('app.view') }} →</a>
            @endif
        </div>
    </div>
    <div class="stat-card animate-on-scroll" style="padding:14px 16px; flex-direction:row; align-items:center; gap:12px;">
        <div style="width:38px;height:38px;border-radius:10px;background:rgba(239,68,68,0.12);display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;">❌</div>
        <div>
            <div style="font-size:22px;font-weight:800;color:var(--danger)">{{ number_format($stats['cancelled']) }}</div>
            <div style="font-size:11px;color:var(--text-muted);margin-top:1px;">{{ __('app.cancelled') }}</div>
        </div>
    </div>
</div>

<div class="charts-row" style="margin-top:22px;">
    <div class="card animate-on-scroll">
        <div class="card-header">
            <div>
                <div class="card-title">📈 {{ __('app.my_progression') }}</div>
                <div class="card-subtitle">{{ __('app.monthly_vol_mine') }}</div>
            </div>
        </div>
        <div class="card-body" style="padding-bottom:18px;">
            <div style="position:relative; height:240px;">
                <canvas id="agentChart"></canvas>
            </div>
        </div>
    </div>

    <div class="card animate-on-scroll">
        <div class="card-header">
            <div class="card-title">⚡ {{ __('app.recent_transactions') }}</div>
        </div>
        <div>
            @forelse($recentTransactions->take(6) as $tx)
                <div style="padding:11px 16px; border-bottom:1px solid var(--divider); display:flex; align-items:center; gap:10px; transition:background 0.15s;" onmouseenter="this.style.background='var(--bg-row-hover)'" onmouseleave="this.style.background=''">
                    <div style="flex:1; min-width:0;">
                        <div style="font-size:11px; font-family:monospace; color:var(--sky-primary)">{{ $tx->transaction_number }}</div>
                        <div style="font-weight:600; font-size:13px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            {{ $tx->sender_name ?: $tx->receiver_name ?: '—' }}
                        </div>
                        <div style="font-size:11px; color:var(--text-muted);">
                            {{ $tx->originCountry?->flag_emoji }} {{ $tx->originCountry?->code }}
                            → {{ $tx->destinationCountry?->flag_emoji }} {{ $tx->destinationCountry?->code }}
                            &nbsp;·&nbsp; <span class="badge badge-{{ $tx->status }}" style="font-size:10px; padding:1px 6px;">{{ ucfirst($tx->status) }}</span>
                        </div>
                    </div>
                    <div style="text-align:right; flex-shrink:0;">
                        <div class="amount-display amount-primary" style="font-size:13px;">{{ number_format($tx->amount, 0, ',', ' ') }}</div>
                        <div style="font-size:10px; color:var(--text-muted); margin-bottom:4px;">{{ $tx->created_at->diffForHumans() }}</div>
                        <div style="display:flex; gap:4px; justify-content:flex-end;">
                            <a href="{{ route('agent.transactions.edit', $tx) }}"
                               class="btn btn-primary"
                               style="padding:3px 8px; font-size:11px; line-height:1.4;"
                               title="{{ __('app.edit') }}">✏️</a>
                            <form method="POST" action="{{ route('agent.transactions.destroy', $tx) }}"
                                  style="display:inline;" data-confirm="{{ __('app.delete_tx_confirm') }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger"
                                        style="padding:3px 8px; font-size:11px; line-height:1.4;"
                                        title="{{ __('app.delete') }}">🗑️</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div style="padding:40px; text-align:center; color:var(--text-muted);">{{ __('app.no_tx_yet') }}</div>
            @endforelse
            <div style="padding:12px 18px; text-align:center">
                <a href="{{ route('agent.transactions.index') }}" class="btn btn-sm btn-outline-primary">{{ __('app.see_all') }} →</a>
            </div>
        </div>
    </div>
</div>

{{-- Top Routes + Rapport --}}
<div class="rg-2" style="margin-top:22px;">

    {{-- Top routes --}}
    <div class="card animate-on-scroll">
        <div class="card-header">
            <div>
                <div class="card-title">🛣️ {{ __('app.top_routes') }}</div>
                <div class="card-subtitle">{{ __('app.top_routes_subtitle') }}</div>
            </div>
        </div>
        @forelse($topRoutes as $i => $route)
            <div style="padding:13px 18px; border-bottom:1px solid var(--divider); display:flex; align-items:center; gap:12px;">
                <div style="width:26px;height:26px;border-radius:8px;background:linear-gradient(135deg,var(--sky-primary),var(--sky-secondary));color:white;font-weight:800;font-size:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">{{ $i+1 }}</div>
                <div style="flex:1;min-width:0;">
                    <div style="font-weight:700;font-size:13px;display:flex;align-items:center;gap:6px;">
                        {{ $route->originCountry?->flag_emoji }} <span style="font-size:11px;color:var(--text-muted)">{{ $route->originCountry?->code }}</span>
                        <span style="color:var(--sky-primary)">→</span>
                        {{ $route->destinationCountry?->flag_emoji }} <span style="font-size:11px;color:var(--text-muted)">{{ $route->destinationCountry?->code }}</span>
                    </div>
                    <div style="font-size:11px;color:var(--text-muted);margin-top:2px;">{{ number_format($route->count) }} tx · {{ number_format($route->total_amount, 0, ',', ' ') }}</div>
                </div>
                <div style="flex-shrink:0;">
                    @php $pct = $stats['completed'] > 0 ? round(($route->count / $stats['completed']) * 100) : 0; @endphp
                    <div style="font-size:12px;font-weight:700;color:var(--sky-primary)">{{ $pct }}%</div>
                    <div style="width:48px;height:4px;background:var(--divider);border-radius:4px;margin-top:3px;overflow:hidden;">
                        <div style="width:{{ $pct }}%;height:100%;background:linear-gradient(90deg,var(--sky-primary),var(--sky-secondary));border-radius:4px;"></div>
                    </div>
                </div>
            </div>
        @empty
            <div style="padding:36px;text-align:center;color:var(--text-muted);font-size:13px;">{{ __('app.no_tx_yet') }}</div>
        @endforelse
    </div>

</div>

{{-- Rapport à l'administrateur --}}
<div class="rg-2" style="margin-top:22px;">

    {{-- Formulaire d'envoi --}}
    <div class="card animate-on-scroll">
        <div class="card-header">
            <div>
                <div class="card-title">📝 {{ __('app.report_title') }}</div>
                <div class="card-subtitle">{{ __('app.report_subtitle') }}</div>
            </div>
        </div>
        <div class="card-body" style="padding:18px 20px;">
            @if(session('report_success'))
                <div class="alert alert-success" style="margin-bottom:14px;">✅ {{ session('report_success') }}</div>
            @endif
            <form method="POST" action="{{ route('agent.reports.store') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">{{ __('app.report_subject') }}</label>
                    <input type="text" name="subject"
                           class="form-control @error('subject') is-invalid @enderror"
                           placeholder="{{ __('app.report_subject_placeholder') }}"
                           value="{{ old('subject') }}" required maxlength="150">
                    @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('app.report_message') }}</label>
                    <textarea name="message"
                              class="form-control @error('message') is-invalid @enderror"
                              rows="5"
                              placeholder="{{ __('app.report_message_placeholder') }}"
                              required maxlength="2000"
                              style="resize:vertical;">{{ old('message') }}</textarea>
                    @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">
                    📤 {{ __('app.report_send') }}
                </button>
            </form>
        </div>
    </div>

    {{-- Mes rapports précédents --}}
    <div class="card animate-on-scroll">
        <div class="card-header">
            <div class="card-title">📂 {{ __('app.report_history') }}</div>
        </div>
        @forelse($myReports as $report)
            <div style="padding:12px 18px; border-bottom:1px solid var(--divider); display:flex; align-items:flex-start; gap:10px;">
                <span style="font-size:18px; flex-shrink:0;">{{ $report->status === 'read' ? '✅' : '🕐' }}</span>
                <div style="flex:1; min-width:0;">
                    <div style="font-weight:600; font-size:13px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        {{ $report->subject }}
                    </div>
                    <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">
                        {{ $report->created_at->diffForHumans() }}
                        &nbsp;·&nbsp;
                        <span style="color:{{ $report->status === 'read' ? '#22c55e' : '#f59e0b' }}; font-weight:600;">
                            {{ $report->status === 'read' ? __('app.report_read') : __('app.report_unread') }}
                        </span>
                    </div>
                </div>
            </div>
        @empty
            <div style="padding:36px; text-align:center; color:var(--text-muted); font-size:13px;">
                {{ __('app.report_none_yet') }}
            </div>
        @endforelse
    </div>
</div>

@endsection

@push('scripts')
<script>
const agentMonthly = @json($monthlyData);
const isDark = () => document.documentElement.getAttribute('data-theme') === 'dark';
const gridC  = () => isDark() ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
const tickC  = () => isDark() ? '#64748B' : '#94A3B8';

const months6 = [], amounts6 = [];
for (let i = 5; i >= 0; i--) {
    const d = new Date(); d.setMonth(d.getMonth() - i);
    const y = d.getFullYear(), m = d.getMonth() + 1;
    months6.push(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'][m-1]);
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
            backgroundColor: 'rgba(2,132,199,0.75)',
            borderColor: '#0284C7', borderWidth: 2, borderRadius: 8,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: gridC() },
                ticks: { color: tickC(), maxTicksLimit: 5 }
            },
            x: { ticks: { color: tickC() }, grid: { display: false } }
        },
        animation: { duration: 600, easing: 'easeOutQuart' }
    }
});

window.blueskyCharts = [agentChart];

// Force resize once the DOM is fully painted
requestAnimationFrame(() => agentChart.resize());

new MutationObserver(() => {
    Object.values(agentChart.options.scales||{}).forEach(s => {
        if (s.ticks) s.ticks.color = tickC();
        if (s.grid)  s.grid.color  = gridC();
    });
    agentChart.update('none');
}).observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });
</script>
@endpush
