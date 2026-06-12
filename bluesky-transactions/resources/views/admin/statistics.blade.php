@extends('layouts.app')

@section('title', __('app.statistics'))
@section('page-title', __('app.statistics'))
@section('page-subtitle', __('app.stats_subtitle'))

@section('content')

<div class="charts-row" style="margin-bottom:24px;">
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">📊 {{ __('app.yearly_growth') }}</div>
                <div class="card-subtitle">{{ __('app.volume_count_year') }}</div>
            </div>
        </div>
        <div class="card-body">
            <canvas id="yearlyChart" height="90"></canvas>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">📅 {{ __('app.monthly_progression') }}</div>
                <div class="card-subtitle">{{ __('app.monthly_detail') }}</div>
            </div>
        </div>
        <div class="card-body">
            <canvas id="currentYearChart" height="160"></canvas>
        </div>
    </div>
</div>

<div class="table-card mb-20">
    <div class="table-header">
        <div class="table-title">📈 {{ __('app.yearly_summary') }}</div>
    </div>
    <table class="bsky-table">
        <thead>
            <tr>
                <th>{{ __('app.year') }}</th>
                <th>{{ __('app.transactions_lbl') }}</th>
                <th>{{ __('app.total_volume_lbl') }}</th>
                <th>{{ __('app.commissions_lbl') }}</th>
                <th>{{ __('app.growth') }}</th>
            </tr>
        </thead>
        <tbody>
            @php $prevAmount = 0; @endphp
            @forelse($yearlyData as $year)
                @php $growth = $prevAmount > 0 ? (($year->total_amount - $prevAmount) / $prevAmount * 100) : 0; @endphp
                <tr>
                    <td style="font-weight:800; font-size:22px; color:var(--sky-primary)">{{ $year->year }}</td>
                    <td style="font-weight:700">{{ number_format($year->total) }}</td>
                    <td class="amount-display amount-primary" style="font-size:16px">{{ number_format($year->total_amount, 0, ',', ' ') }}</td>
                    <td class="amount-display" style="color:var(--success)">{{ number_format($year->total_fees, 0, ',', ' ') }}</td>
                    <td>
                        @if($prevAmount > 0)
                            <span style="font-weight:700; color:{{ $growth >= 0 ? 'var(--success)' : 'var(--danger)' }}">
                                {{ $growth >= 0 ? '📈 +' : '📉 ' }}{{ number_format($growth, 1) }}%
                            </span>
                            <div class="progress">
                                <div class="progress-bar" style="width:{{ min(abs($growth), 100) }}%; background:{{ $growth >= 0 ? 'linear-gradient(90deg,#10B981,#34D399)' : 'linear-gradient(90deg,#EF4444,#F87171)' }}"></div>
                            </div>
                        @else
                            <span style="color:var(--text-muted); font-size:12px">{{ __('app.first_year') }}</span>
                        @endif
                    </td>
                </tr>
                @php $prevAmount = $year->total_amount; @endphp
            @empty
                <tr><td colspan="5" style="text-align:center; padding:40px; color:var(--text-muted)">{{ __('app.no_data') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="table-card">
    <div class="table-header">
        <div class="table-title">📅 {{ __('app.monthly_detail') }}</div>
    </div>
    <table class="bsky-table">
        <thead>
            <tr>
                <th>{{ __('app.month') }}</th>
                <th>{{ __('app.transactions_lbl') }}</th>
                <th>{{ __('app.volume') }}</th>
                <th>{{ __('app.progression') }}</th>
            </tr>
        </thead>
        <tbody>
            @php
                $months  = collect(range(1, 12))->map(fn($m) => \Carbon\Carbon::createFromDate(2000, $m, 1)->locale(app()->getLocale())->monthName)->toArray();
                $maxAmt  = $currentYearMonthly->max('total_amount') ?: 1;
            @endphp
            @foreach($months as $i => $monthName)
                @php $month = $currentYearMonthly->get($i + 1); @endphp
                <tr>
                    <td style="font-weight:600; color:{{ ($i+1) == now()->month ? 'var(--sky-primary)' : 'inherit' }}">
                        {{ $monthName }}
                        @if(($i+1) == now()->month)
                            <span style="font-size:11px; background:var(--sky-light); color:var(--sky-dark); padding:2px 7px; border-radius:4px; margin-left:6px">{{ __('app.current') }}</span>
                        @endif
                    </td>
                    <td>{{ $month ? number_format($month->total) : '—' }}</td>
                    <td class="amount-display amount-primary">{{ $month ? number_format($month->total_amount, 0, ',', ' ') : '—' }}</td>
                    <td style="width:200px">
                        @if($month)
                            <div class="progress">
                                <div class="progress-bar" style="width:{{ ($month->total_amount / $maxAmt * 100) }}%"></div>
                            </div>
                            <div style="font-size:11px; color:var(--text-muted); margin-top:3px">
                                {{ number_format($month->total_amount / $maxAmt * 100, 1) }}% {{ __('app.max_percent') }}
                            </div>
                        @else
                            <span style="color:var(--text-muted); font-size:12px">{{ __('app.no_data') }}</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection

@push('scripts')
<script>
const yearlyData        = @json($yearlyData);
const currentYearMonthly = @json($currentYearMonthly);
const isDark = () => document.documentElement.getAttribute('data-theme') === 'dark';
const gridC  = () => isDark() ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
const tickC  = () => isDark() ? '#64748B' : '#94A3B8';

const yChart = new Chart(document.getElementById('yearlyChart'), {
    type: 'bar',
    data: {
        labels: yearlyData.map(y => y.year),
        datasets: [{
            label: '{{ __("app.total_volume_lbl") }}',
            data: yearlyData.map(y => parseFloat(y.total_amount)),
            backgroundColor: 'rgba(2,132,199,0.75)',
            borderColor: '#0284C7', borderWidth: 2, borderRadius: 8,
        }, {
            label: '{{ __("app.commissions_lbl") }}',
            data: yearlyData.map(y => parseFloat(y.total_fees)),
            backgroundColor: 'rgba(245,158,11,0.75)',
            borderColor: '#F59E0B', borderWidth: 2, borderRadius: 8,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top', labels: { color: tickC(), usePointStyle: true } } },
        scales: {
            y: { beginAtZero: true, grid: { color: gridC() }, ticks: { color: tickC() } },
            x: { ticks: { color: tickC() } }
        }
    }
});

@php
    $jsMonths12 = collect(range(1, 12))->map(fn($m) => \Carbon\Carbon::createFromDate(2000, $m, 1)->locale(app()->getLocale())->isoFormat('MMM'))->toArray();
@endphp
const months12 = @json($jsMonths12);
const monthAmounts = months12.map((_, i) => {
    const m = i + 1;
    const f = Object.values(currentYearMonthly).find(d => parseInt(d.month) === m);
    return f ? parseFloat(f.total_amount) : 0;
});

const mChart = new Chart(document.getElementById('currentYearChart'), {
    type: 'line',
    data: {
        labels: months12,
        datasets: [{
            label: '{{ __("app.volume") }}',
            data: monthAmounts,
            borderColor: '#10B981',
            backgroundColor: 'rgba(16,185,129,0.08)',
            fill: true, tension: 0.4,
            pointBackgroundColor: '#10B981', pointRadius: 4,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: gridC() }, ticks: { color: tickC() } },
            x: { ticks: { color: tickC() } }
        }
    }
});

window.blueskyCharts = [yChart, mChart];
new MutationObserver(() => {
    [yChart, mChart].forEach(c => {
        c.options.plugins.legend.labels.color = tickC();
        Object.values(c.options.scales||{}).forEach(s => {
            if (s.ticks) s.ticks.color = tickC();
            if (s.grid)  s.grid.color  = gridC();
        });
        c.update('none');
    });
}).observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });
</script>
@endpush
