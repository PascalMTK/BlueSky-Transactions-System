@extends('layouts.app')

@section('title', __('app.my_space'))
@section('page-title', __('app.my_space'))
@section('page-subtitle', __('app.hello') . ', ' . auth()->user()->name . ' — ' . (auth()->user()->country?->flag_emoji ?? '') . ' ' . (auth()->user()->country?->name ?? ''))

@section('content')

{{-- Welcome banner --}}
<div class="welcome-banner" style="background:linear-gradient(135deg,var(--sky-primary),var(--sky-deeper)); border-radius:var(--radius); padding:22px 26px; margin-bottom:24px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:14px; animation:fadeInUp 0.5s ease;">
    <div>
        <div style="font-size:11px; color:rgba(255,255,255,0.6); text-transform:uppercase; letter-spacing:1.5px; margin-bottom:4px;">{{ __('app.agent_dashboard') }}</div>
        <div style="font-size:22px; font-weight:800; color:white;">{{ __('app.hello') }}, {{ explode(' ', auth()->user()->name)[0] }} 👋</div>
        <div style="font-size:13px; color:rgba(255,255,255,0.75); margin-top:6px; display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
            <span style="font-family:monospace; background:rgba(255,255,255,0.15); padding:3px 10px; border-radius:6px; letter-spacing:1px;">{{ auth()->user()->agent_code }}</span>
            @if(auth()->user()->country)
                <span>{{ auth()->user()->country->flag_emoji }} {{ auth()->user()->country->name }}</span>
            @endif
        </div>
    </div>
    <a href="{{ route('agent.transactions.create') }}" class="btn btn-xl" style="background:white; color:var(--sky-primary); font-weight:800; box-shadow:0 4px 16px rgba(0,0,0,0.2);">
        ➕ {{ __('app.new_transaction') }}
    </a>
</div>

{{-- Main stats --}}
<div class="stats-grid">
    <div class="stat-card blue animate-on-scroll">
        <div class="stat-icon blue">📋</div>
        <div class="stat-info">
            <div class="stat-value" data-counter="{{ $stats['total'] }}">0</div>
            <div class="stat-label">{{ __('app.total_transactions') }}</div>
            <div class="stat-change">📅 {{ $stats['today'] }} {{ __('app.today') }}</div>
        </div>
    </div>
    <div class="stat-card gold animate-on-scroll">
        <div class="stat-icon gold">💰</div>
        <div class="stat-info">
            <div class="stat-value" style="font-size:19px;">{{ number_format($stats['total_amount'], 0, ',', ' ') }}</div>
            <div class="stat-label">{{ __('app.total_volume') }}</div>
            <div class="stat-change" style="color:var(--text-muted);">{{ number_format($stats['today_amount'], 0, ',', ' ') }} {{ __('app.today') }}</div>
        </div>
    </div>
    <div class="stat-card green animate-on-scroll">
        <div class="stat-icon green">💵</div>
        <div class="stat-info">
            <div class="stat-value" style="font-size:19px;">{{ number_format($stats['total_fees'], 0, ',', ' ') }}</div>
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
            <div class="stat-change {{ $diff !== null && $diff < 0 ? 'negative' : '' }}" style="{{ $diff === null ? 'color:var(--text-muted)' : '' }}">
                {{ $diff !== null ? ($diff >= 0 ? '↑' : '↓') . abs($diff) . '% vs last month' : number_format($stats['month_amount'], 0, ',', ' ') }}
            </div>
        </div>
    </div>
</div>

{{-- Status breakdown --}}
<div style="display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin-bottom:24px;">
    <div class="stat-card green animate-on-scroll">
        <div class="stat-icon green" style="width:42px;height:42px;font-size:20px;border-radius:11px;flex-shrink:0;">✅</div>
        <div class="stat-info">
            <div class="stat-value" style="color:var(--success);">{{ number_format($stats['completed']) }}</div>
            <div class="stat-label">{{ __('app.completed') }}</div>
        </div>
    </div>
    <div class="stat-card animate-on-scroll">
        <div class="stat-icon gold" style="width:42px;height:42px;font-size:20px;border-radius:11px;flex-shrink:0;">⏳</div>
        <div class="stat-info">
            <div class="stat-value" style="color:#F59E0B;">{{ number_format($stats['pending']) }}</div>
            <div class="stat-label">{{ __('app.pending') }}</div>
            @if($stats['pending'] > 0)
                <a href="{{ route('agent.transactions.index', ['status' => 'pending']) }}" style="font-size:11px;color:var(--sky-primary);text-decoration:none;font-weight:600;">{{ __('app.view') }} →</a>
            @endif
        </div>
    </div>
    <div class="stat-card animate-on-scroll">
        <div class="stat-icon red" style="width:42px;height:42px;font-size:20px;border-radius:11px;flex-shrink:0;">❌</div>
        <div class="stat-info">
            <div class="stat-value" style="color:var(--danger);">{{ number_format($stats['cancelled']) }}</div>
            <div class="stat-label">{{ __('app.cancelled') }}</div>
        </div>
    </div>
</div>

{{-- Chart + Recent Transactions --}}
<div class="charts-row" style="margin-bottom:24px;">

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

    <div class="card animate-on-scroll" style="display:flex;flex-direction:column;">
        <div class="card-header">
            <div class="card-title">⚡ {{ __('app.recent_transactions') }}</div>
        </div>
        <div style="flex:1;">
            @forelse($recentTransactions->take(6) as $tx)
                <div style="padding:11px 16px; border-bottom:1px solid var(--divider); display:flex; align-items:center; gap:10px; transition:background 0.15s;" onmouseenter="this.style.background='var(--bg-row-hover)'" onmouseleave="this.style.background=''">
                    <div style="flex:1; min-width:0;">
                        <div style="font-size:11px; font-family:monospace; color:var(--sky-primary); margin-bottom:2px;">{{ $tx->transaction_number }}</div>
                        <div style="font-weight:600; font-size:13px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            {{ $tx->sender_name ?: $tx->receiver_name ?: '—' }}
                        </div>
                        <div style="font-size:11px; color:var(--text-muted); margin-top:2px; display:flex; align-items:center; gap:4px; flex-wrap:wrap;">
                            {{ $tx->originCountry?->flag_emoji }} {{ $tx->originCountry?->code }}
                            <span style="color:var(--sky-primary);">→</span>
                            {{ $tx->destinationCountry?->flag_emoji }} {{ $tx->destinationCountry?->code }}
                            <span class="badge badge-{{ $tx->status }}" style="font-size:10px; padding:1px 6px;">{{ ucfirst($tx->status) }}</span>
                        </div>
                    </div>
                    <div style="text-align:right; flex-shrink:0;">
                        <div class="amount-display amount-primary" style="font-size:13px;">{{ number_format($tx->amount, 0, ',', ' ') }}</div>
                        <div style="font-size:10px; color:var(--text-muted); margin-bottom:5px;">{{ $tx->created_at->diffForHumans() }}</div>
                        <div style="display:flex; gap:4px; justify-content:flex-end;">
                            <a href="{{ route('agent.transactions.edit', $tx) }}"
                               class="btn btn-primary btn-sm"
                               title="{{ __('app.edit') }}">✏️</a>
                            <form method="POST" action="{{ route('agent.transactions.destroy', $tx) }}"
                                  style="display:inline;" data-confirm="{{ __('app.delete_tx_confirm') }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"
                                        title="{{ __('app.delete') }}">🗑️</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div style="padding:40px; text-align:center; color:var(--text-muted);">{{ __('app.no_tx_yet') }}</div>
            @endforelse
        </div>
        <div style="padding:12px 18px; border-top:1px solid var(--divider); text-align:center;">
            <a href="{{ route('agent.transactions.index') }}" class="btn btn-sm btn-outline-primary">{{ __('app.see_all') }} →</a>
        </div>
    </div>

</div>

{{-- Top Routes + Support Ticket --}}
<div class="rg-2" style="margin-bottom:24px;">

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
                <div style="width:28px;height:28px;border-radius:8px;background:linear-gradient(135deg,var(--sky-primary),var(--sky-secondary));color:white;font-weight:800;font-size:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">{{ $i + 1 }}</div>
                <div style="flex:1;min-width:0;">
                    <div style="font-weight:700;font-size:13px;display:flex;align-items:center;gap:5px;">
                        {{ $route->originCountry?->flag_emoji }}
                        <span style="font-size:11px;color:var(--text-muted);">{{ $route->originCountry?->code }}</span>
                        <span style="color:var(--sky-primary);font-weight:900;">→</span>
                        {{ $route->destinationCountry?->flag_emoji }}
                        <span style="font-size:11px;color:var(--text-muted);">{{ $route->destinationCountry?->code }}</span>
                    </div>
                    <div style="font-size:11px;color:var(--text-muted);margin-top:2px;">
                        {{ number_format($route->count) }} tx · {{ number_format($route->total_amount, 0, ',', ' ') }}
                    </div>
                </div>
                <div style="text-align:right;flex-shrink:0;">
                    @php $pct = $stats['completed'] > 0 ? round(($route->count / $stats['completed']) * 100) : 0; @endphp
                    <div style="font-size:12px;font-weight:700;color:var(--sky-primary);margin-bottom:4px;">{{ $pct }}%</div>
                    <div class="progress" style="width:56px;">
                        <div class="progress-bar" style="width:{{ $pct }}%;"></div>
                    </div>
                </div>
            </div>
        @empty
            <div style="padding:36px;text-align:center;color:var(--text-muted);font-size:13px;">{{ __('app.no_tx_yet') }}</div>
        @endforelse
    </div>

    {{-- Support ticket column --}}
    <div style="display:flex;flex-direction:column;gap:14px;">

        {{-- Send a ticket --}}
        <div class="card animate-on-scroll">
            <div class="card-header">
                <div>
                    <div class="card-title">📝 {{ __('app.report_title') }}</div>
                    <div class="card-subtitle">{{ __('app.report_subtitle') }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('agent.reports.store') }}">
                @csrf
                <div class="card-body" style="padding-bottom:14px;">
                    @if(session('report_success'))
                        <div class="alert alert-success">✅ {{ session('report_success') }}</div>
                    @endif
                    <div class="form-group">
                        <label class="form-label">{{ __('app.report_subject') }}</label>
                        <input type="text" name="subject"
                               class="form-control @error('subject') is-invalid @enderror"
                               placeholder="{{ __('app.report_subject_placeholder') }}"
                               value="{{ old('subject') }}" required maxlength="150">
                        @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">{{ __('app.report_message') }}</label>
                        <textarea name="message"
                                  class="form-control @error('message') is-invalid @enderror"
                                  rows="4"
                                  placeholder="{{ __('app.report_message_placeholder') }}"
                                  required maxlength="2000"
                                  style="resize:vertical;">{{ old('message') }}</textarea>
                        @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div style="padding:12px 22px; border-top:1px solid var(--divider);">
                    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
                        📤 {{ __('app.report_send') }}
                    </button>
                </div>
            </form>
        </div>

        {{-- Ticket history --}}
        <div class="card animate-on-scroll">
            <div class="card-header">
                <div class="card-title">📂 {{ __('app.report_history') }}</div>
            </div>
            @forelse($myReports->take(4) as $report)
                <div style="padding:11px 18px; border-bottom:1px solid var(--divider); display:flex; align-items:flex-start; gap:10px;">
                    <span style="font-size:16px;flex-shrink:0;margin-top:1px;">{{ $report->status === 'read' ? '✅' : '🕐' }}</span>
                    <div style="flex:1;min-width:0;">
                        <div style="font-weight:600;font-size:13px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $report->subject }}</div>
                        <div style="font-size:11px;color:var(--text-muted);margin-top:2px;display:flex;gap:6px;align-items:center;flex-wrap:wrap;">
                            <span>{{ $report->created_at->diffForHumans() }}</span>
                            <span style="color:{{ $report->status === 'read' ? '#22c55e' : '#f59e0b' }};font-weight:600;">
                                {{ $report->status === 'read' ? __('app.report_read') : __('app.report_unread') }}
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div style="padding:28px;text-align:center;color:var(--text-muted);font-size:13px;">{{ __('app.report_none_yet') }}</div>
            @endforelse
        </div>

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
