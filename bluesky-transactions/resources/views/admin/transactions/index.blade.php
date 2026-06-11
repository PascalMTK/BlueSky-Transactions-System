@extends('layouts.app')

@section('title', __('app.all_transactions'))
@section('page-title', __('app.all_transactions'))
@section('page-subtitle', __('app.tx_history_subtitle'))

@section('content')

<div class="stats-grid" style="margin-bottom:20px;">
    <div class="stat-card blue animate-on-scroll">
        <div class="stat-icon blue">📋</div>
        <div class="stat-info">
            <div class="stat-value" data-counter="{{ $totals['count'] }}">0</div>
            <div class="stat-label">{{ __('app.active_filters') }}</div>
        </div>
    </div>
    <div class="stat-card gold animate-on-scroll">
        <div class="stat-icon gold">💰</div>
        <div class="stat-info">
            <div class="stat-value" style="font-size:20px">{{ number_format($totals['amount'], 0, ',', ' ') }}</div>
            <div class="stat-label">{{ __('app.total_volume_lbl') }}</div>
        </div>
    </div>
    <div class="stat-card green animate-on-scroll">
        <div class="stat-icon green">✅</div>
        <div class="stat-info">
            <div class="stat-value" style="font-size:20px">{{ number_format($totals['fees'], 0, ',', ' ') }}</div>
            <div class="stat-label">{{ __('app.total_commissions_lbl') }}</div>
        </div>
    </div>
</div>

<form method="GET" action="{{ route('admin.transactions.index') }}">
<div class="filters-bar">
    <div class="filter-group filter-group-search">
        <label class="filter-label">{{ __('app.search') }}</label>
        <input type="text" name="search" class="filter-control"
               placeholder="{{ __('app.search_tx_placeholder') }}" value="{{ request('search') }}">
    </div>
    <div class="filter-group">
        <label class="filter-label">{{ __('app.origin_country') }}</label>
        <select name="origin_country" class="filter-control">
            <option value="">{{ __('app.all_countries') }}</option>
            @foreach($countries as $c)
                <option value="{{ $c->id }}" {{ request('origin_country') == $c->id ? 'selected' : '' }}>
                    {{ $c->flag_emoji }} {{ $c->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="filter-group">
        <label class="filter-label">{{ __('app.dest_country') }}</label>
        <select name="destination_country" class="filter-control">
            <option value="">{{ __('app.all_countries') }}</option>
            @foreach($countries as $c)
                <option value="{{ $c->id }}" {{ request('destination_country') == $c->id ? 'selected' : '' }}>
                    {{ $c->flag_emoji }} {{ $c->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="filter-group">
        <label class="filter-label">{{ __('app.status') }}</label>
        <select name="status" class="filter-control">
            <option value="">{{ __('app.all_statuses') }}</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>✅ {{ __('app.completed') }}</option>
            <option value="pending"   {{ request('status') == 'pending'   ? 'selected' : '' }}>⏳ {{ __('app.pending') }}</option>
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>❌ {{ __('app.cancelled') }}</option>
        </select>
    </div>
    <div class="filter-group">
        <label class="filter-label">{{ __('app.from_date') }}</label>
        <input type="date" name="date_from" class="filter-control" value="{{ request('date_from') }}">
    </div>
    <div class="filter-group">
        <label class="filter-label">{{ __('app.to_date') }}</label>
        <input type="date" name="date_to" class="filter-control" value="{{ request('date_to') }}">
    </div>
    <div style="display:flex; gap:8px; align-items:flex-end">
        <button type="submit" class="btn btn-primary">🔍</button>
        <a href="{{ route('admin.transactions.index') }}" class="btn btn-secondary">✕</a>
        <a href="{{ route('admin.export.csv', request()->all()) }}" class="btn btn-success" title="{{ __('app.export_csv') }}">📤 CSV</a>
    </div>
</div>
</form>

@php
    $statusLabels = [
        'completed' => __('app.completed'),
        'pending'   => __('app.pending'),
        'cancelled' => __('app.cancelled'),
    ];
@endphp

<div class="table-card animate-on-scroll">
    <div class="table-header">
        <div class="table-title">{{ __('app.tx_history') }}</div>
        <span style="color:var(--text-muted); font-size:13px">{{ $transactions->total() }} {{ __('app.tx_found') }}</span>
    </div>
    <div class="table-scroll">
    <table class="bsky-table">
        <thead>
            <tr>
                <th>{{ __('app.transaction_number') }}</th>
                <th>{{ __('app.sender') }}</th>
                <th class="hide-mobile">{{ __('app.beneficiary') }}</th>
                <th class="hide-mobile">{{ __('app.route') }}</th>
                <th>{{ __('app.amount') }}</th>
                <th class="hide-mobile">{{ __('app.fee') }}</th>
                <th class="hide-mobile">{{ __('app.total') }}</th>
                <th class="hide-mobile">{{ __('app.agent') }}</th>
                <th>{{ __('app.status') }}</th>
                <th>{{ __('app.date') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $tx)
                <tr>
                    <td><span class="tx-number">{{ $tx->transaction_number }}</span></td>
                    <td>
                        <div style="font-weight:600; font-size:13px">{{ $tx->sender_name }}</div>
                        <div style="font-size:11px; color:var(--text-muted)">📞 {{ $tx->sender_phone }}</div>
                    </td>
                    <td class="hide-mobile">
                        @if($tx->receiver_name)
                            <div style="font-size:13px; font-weight:600">{{ $tx->receiver_name }}</div>
                            @if($tx->receiver_phone)
                                <div style="font-size:11px; color:var(--text-muted)">📞 {{ $tx->receiver_phone }}</div>
                            @endif
                        @else
                            <span style="color:var(--text-muted)">—</span>
                        @endif
                    </td>
                    <td class="hide-mobile">
                        <span class="route-pill">
                            {{ $tx->originCountry?->flag_emoji }} {{ $tx->originCountry?->code }}
                            → {{ $tx->destinationCountry?->flag_emoji }} {{ $tx->destinationCountry?->code }}
                        </span>
                    </td>
                    <td class="amount-display amount-primary">
                        {{ number_format($tx->amount, 2, ',', ' ') }}
                        <span style="font-size:10px; color:var(--text-muted); font-weight:500; margin-left:2px;">{{ $tx->currency ?? $tx->originCountry?->currency_code }}</span>
                    </td>
                    <td class="hide-mobile" style="font-size:12px">
                        {{ $tx->fee_percentage }}%<br>
                        <span style="font-weight:700; color:var(--gold)">{{ number_format($tx->fee_amount, 2) }}</span>
                        <span style="font-size:10px; color:var(--text-muted); margin-left:2px;">{{ $tx->currency ?? $tx->originCountry?->currency_code }}</span>
                    </td>
                    <td class="hide-mobile amount-display" style="color:var(--success); font-weight:700">
                        {{ number_format($tx->total_amount, 2, ',', ' ') }}
                        <span style="font-size:10px; color:var(--text-muted); font-weight:500; margin-left:2px;">{{ $tx->currency ?? $tx->originCountry?->currency_code }}</span>
                    </td>
                    <td class="hide-mobile">
                        <div style="font-size:12px; font-weight:600">{{ $tx->agent?->name }}</div>
                        @if($tx->agent?->country)
                            <div style="font-size:11px; color:var(--text-muted)">{{ $tx->agent->country->flag_emoji }}</div>
                        @endif
                    </td>
                    <td><span class="badge badge-{{ $tx->status }}">{{ $statusLabels[$tx->status] ?? ucfirst($tx->status) }}</span></td>
                    <td style="font-size:12px; color:var(--text-muted); white-space:nowrap">
                        {{ $tx->created_at->format('d/m/Y') }}<br>
                        {{ $tx->created_at->format('H:i') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align:center; padding:40px; color:var(--text-muted)">
                        <div style="font-size:40px; margin-bottom:10px">💸</div>
                        <div>{{ __('app.no_transaction') }}</div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
    <div style="padding:14px 20px">{{ $transactions->withQueryString()->links() }}</div>
</div>

@endsection
