@extends('layouts.app')

@section('title', __('app.my_transactions'))
@section('page-title', __('app.my_transactions'))
@section('page-subtitle', __('app.tx_history_subtitle_mine'))

@section('content')

@php
    $statusLabels = [
        'completed' => __('app.completed'),
        'pending'   => __('app.pending'),
        'cancelled' => __('app.cancelled'),
    ];
    $paymentLabels = [
        'cash'         => '💵 ' . __('app.cash'),
        'mobile_money' => '📱 ' . __('app.mobile_money'),
        'bank'         => '🏦 ' . __('app.bank'),
    ];
@endphp

<div class="stats-grid" style="margin-bottom:20px;">
    <div class="stat-card blue animate-on-scroll">
        <div class="stat-icon blue">📋</div>
        <div class="stat-info">
            <div class="stat-value" data-counter="{{ $stats['total'] }}">0</div>
            <div class="stat-label">{{ __('app.total_transactions') }}</div>
        </div>
    </div>
    <div class="stat-card gold animate-on-scroll">
        <div class="stat-icon gold">📅</div>
        <div class="stat-info">
            <div class="stat-value" data-counter="{{ $stats['today'] }}">0</div>
            <div class="stat-label">{{ ucfirst(__('app.today')) }}</div>
        </div>
    </div>
    <div class="stat-card green animate-on-scroll">
        <div class="stat-icon green">💰</div>
        <div class="stat-info">
            <div class="stat-value" style="font-size:18px">{{ number_format($stats['month_amount'], 0, ',', ' ') }}</div>
            <div class="stat-label">{{ __('app.volume_month') }}</div>
        </div>
    </div>
</div>

<form method="GET" action="{{ route('agent.transactions.index') }}">
<div class="filters-bar">
    <div class="filter-group filter-group-search">
        <label class="filter-label">{{ __('app.search') }}</label>
        <input type="text" name="search" class="filter-control"
               placeholder="{{ __('app.search_tx_placeholder_mine') }}" value="{{ request('search') }}">
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
        <label class="filter-label">{{ __('app.transaction_type_lbl') }}</label>
        <select name="transaction_type" class="filter-control">
            <option value="">{{ __('app.all_types') }}</option>
            <option value="send"       {{ request('transaction_type') == 'send'       ? 'selected' : '' }}>📤 {{ __('app.type_send') }}</option>
            <option value="withdrawal" {{ request('transaction_type') == 'withdrawal' ? 'selected' : '' }}>📥 {{ __('app.type_withdrawal') }}</option>
        </select>
    </div>
    <div class="filter-group">
        <label class="filter-label">{{ __('app.country') }}</label>
        <select name="country_id" class="filter-control">
            <option value="">{{ __('app.all_countries') }}</option>
            @foreach($countries as $country)
                <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>
                    {{ $country->flag_emoji }} {{ $country->name }}
                </option>
            @endforeach
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
        <a href="{{ route('agent.transactions.index') }}" class="btn btn-secondary">✕</a>
        <a href="{{ route('agent.export.csv', request()->all()) }}" class="btn btn-success">📥 CSV</a>
    </div>
</div>
</form>

<div style="display:flex; justify-content:flex-end; margin-bottom:14px;">
    <a href="{{ route('agent.transactions.create') }}" class="btn btn-primary">
        ➕ {{ __('app.new_transaction') }}
    </a>
</div>

<div class="table-card animate-on-scroll">
    <div class="table-header">
        <div class="table-title">{{ __('app.tx_history_mine') }}</div>
        <span style="color:var(--text-muted); font-size:13px">{{ $transactions->total() }} {{ __('app.results_found') }}</span>
    </div>
    <div class="table-scroll">
    <table class="bsky-table">
        <thead>
            <tr>
                <th>{{ __('app.transaction_number') }}</th>
                <th>{{ __('app.transaction_type_lbl') }}</th>
                <th>{{ __('app.sender') }}</th>
                <th>{{ __('app.beneficiary') }}</th>
                <th class="hide-mobile">{{ __('app.route') }}</th>
                <th>{{ __('app.amount') }}</th>
                <th class="hide-mobile">{{ __('app.fee') }}</th>
                <th class="hide-mobile">{{ __('app.total') }}</th>
                <th class="hide-mobile">{{ __('app.payment_method') }}</th>
                <th>{{ __('app.status') }}</th>
                <th>{{ __('app.date') }}</th>
                <th>{{ __('app.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $tx)
                <tr>
                    <td>
                        <a href="{{ route('agent.transactions.show', $tx) }}" class="tx-number" style="text-decoration:none; color:var(--sky-primary)">
                            {{ $tx->transaction_number }}
                        </a>
                    </td>
                    <td>
                        @if(($tx->transaction_type ?? 'send') === 'withdrawal')
                            <span style="display:inline-flex; align-items:center; gap:4px; background:rgba(251,191,36,0.15); color:#d97706; border:1px solid rgba(251,191,36,0.3); border-radius:6px; padding:3px 8px; font-size:11px; font-weight:700;">
                                📥 {{ __('app.type_withdrawal') }}
                            </span>
                        @else
                            <span style="display:inline-flex; align-items:center; gap:4px; background:rgba(14,165,233,0.12); color:var(--sky-primary); border:1px solid rgba(14,165,233,0.25); border-radius:6px; padding:3px 8px; font-size:11px; font-weight:700;">
                                📤 {{ __('app.type_send') }}
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($tx->sender_name)
                            <div style="font-weight:600">{{ $tx->sender_name }}</div>
                            <div style="font-size:11px; color:var(--text-muted)">📞 {{ $tx->sender_phone }}</div>
                        @else
                            <span style="color:var(--text-muted); font-size:12px">—</span>
                        @endif
                    </td>
                    <td>
                        @if($tx->receiver_name)
                            <div style="font-size:13px">{{ $tx->receiver_name }}</div>
                            @if($tx->receiver_phone)
                                <div style="font-size:11px; color:var(--text-muted)">📞 {{ $tx->receiver_phone }}</div>
                            @endif
                        @else
                            <span style="color:var(--text-muted); font-size:12px">—</span>
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
                    <td class="hide-mobile">
                        <span style="color:var(--gold); font-weight:700; font-family:monospace">{{ number_format($tx->fee_amount, 2) }}</span>
                        <span style="font-size:10px; color:var(--text-muted); margin-left:2px;">{{ $tx->currency ?? $tx->originCountry?->currency_code }}</span>
                        <div style="font-size:11px; color:var(--text-muted)">{{ $tx->fee_percentage }}%</div>
                    </td>
                    <td class="hide-mobile amount-display" style="color:var(--success); font-weight:700">
                        {{ number_format($tx->total_amount, 2, ',', ' ') }}
                        <span style="font-size:10px; color:var(--text-muted); font-weight:500; margin-left:2px;">{{ $tx->currency ?? $tx->originCountry?->currency_code }}</span>
                    </td>
                    <td class="hide-mobile" style="font-size:12px; color:var(--text-secondary)">
                        {{ $paymentLabels[$tx->payment_method] ?? ucfirst($tx->payment_method) }}
                    </td>
                    <td><span class="badge badge-{{ $tx->status }}">{{ $statusLabels[$tx->status] ?? ucfirst($tx->status) }}</span></td>
                    <td style="font-size:12px; color:var(--text-muted); white-space:nowrap">
                        {{ $tx->created_at->format('d/m/Y') }}<br>{{ $tx->created_at->format('H:i') }}
                    </td>
                    <td style="white-space:nowrap">
                        <a href="{{ route('agent.transactions.show', $tx) }}"
                           class="btn btn-secondary" style="padding:5px 10px; font-size:12px;" title="{{ __('app.view') }}">
                            👁
                        </a>
                        <a href="{{ route('agent.transactions.edit', $tx) }}"
                           class="btn btn-primary" style="padding:5px 10px; font-size:12px;" title="{{ __('app.edit') }}">
                            ✏️
                        </a>
                        <form method="POST" action="{{ route('agent.transactions.destroy', $tx) }}"
                              style="display:inline" data-confirm="{{ __('app.delete_tx_confirm') }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger" style="padding:5px 10px; font-size:12px;" title="{{ __('app.delete') }}">
                                🗑️
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" style="text-align:center; padding:50px; color:var(--text-muted)">
                        <div style="font-size:50px; margin-bottom:12px">💸</div>
                        <div style="font-size:16px; font-weight:600; margin-bottom:8px">{{ __('app.no_transaction') }}</div>
                        <div style="font-size:13px">{{ __('app.tx_start') }}</div>
                        <div style="margin-top:16px">
                            <a href="{{ route('agent.transactions.create') }}" class="btn btn-primary">➕ {{ __('app.new_transaction') }}</a>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
    <div style="padding:14px 20px">{{ $transactions->withQueryString()->links() }}</div>
</div>

@endsection
