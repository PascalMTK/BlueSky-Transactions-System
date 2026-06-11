@extends('layouts.app')

@section('title', __('app.agent_management'))
@section('page-title', __('app.agent_management'))
@section('page-subtitle', __('app.agent_monitoring'))

@section('content')

<div class="stats-grid" style="margin-bottom:20px;">
    <div class="stat-card blue animate-on-scroll">
        <div class="stat-icon blue">👥</div>
        <div class="stat-info">
            <div class="stat-value" data-counter="{{ $agents->total() }}">0</div>
            <div class="stat-label">{{ __('app.total_agents') }}</div>
        </div>
    </div>
    <div class="stat-card green animate-on-scroll">
        <div class="stat-icon green">✅</div>
        <div class="stat-info">
            <div class="stat-value" data-counter="{{ $agents->getCollection()->where('status','active')->count() }}">0</div>
            <div class="stat-label">{{ __('app.active') }}</div>
        </div>
    </div>
    <div class="stat-card gold animate-on-scroll">
        <div class="stat-icon gold">⏳</div>
        <div class="stat-info">
            <div class="stat-value" data-counter="{{ $agents->getCollection()->where('status','pending')->count() }}">0</div>
            <div class="stat-label">{{ __('app.waiting') }}</div>
        </div>
    </div>
    <div class="stat-card red animate-on-scroll">
        <div class="stat-icon red">🚫</div>
        <div class="stat-info">
            <div class="stat-value" data-counter="{{ $agents->getCollection()->where('status','inactive')->count() }}">0</div>
            <div class="stat-label">{{ __('app.disabled') }}</div>
        </div>
    </div>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('admin.agents.index') }}">
<div class="filters-bar">
    <div class="filter-group filter-group-search">
        <label class="filter-label">{{ __('app.search') }}</label>
        <input type="text" name="search" class="filter-control"
               placeholder="{{ __('app.search_agent') }}"
               value="{{ request('search') }}">
    </div>
    <div class="filter-group">
        <label class="filter-label">{{ __('app.status') }}</label>
        <select name="status" class="filter-control">
            <option value="">{{ __('app.all_statuses') }}</option>
            <option value="active"   {{ request('status') == 'active'   ? 'selected' : '' }}>✅ {{ __('app.status_active') }}</option>
            <option value="pending"  {{ request('status') == 'pending'  ? 'selected' : '' }}>⏳ {{ __('app.pending') }}</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>🚫 {{ __('app.status_inactive') }}</option>
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
    <div style="display:flex; gap:8px; align-items:flex-end">
        <button type="submit" class="btn btn-primary">🔍 {{ __('app.filter') }}</button>
        <a href="{{ route('admin.agents.index') }}" class="btn btn-secondary">✕ {{ __('app.reset') }}</a>
    </div>
</div>
</form>

{{-- Table --}}
<div class="table-card animate-on-scroll">
    <div class="table-header">
        <div class="table-title">{{ __('app.agent_list') }}</div>
        <span style="color:var(--text-muted); font-size:13px">{{ $agents->total() }} {{ __('app.agents_found') }}</span>
    </div>
    <div class="table-scroll">
    <table class="bsky-table">
        <thead>
            <tr>
                <th>{{ __('app.agent') }}</th>
                <th class="hide-mobile">{{ __('app.code') }}</th>
                <th>{{ __('app.country') }}</th>
                <th class="hide-mobile">{{ __('app.contact') }}</th>
                <th class="hide-mobile">{{ __('app.tx_count') }}</th>
                <th>{{ __('app.status') }}</th>
                <th class="hide-mobile">{{ __('app.registration') }}</th>
                <th>{{ __('app.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($agents as $agent)
                <tr>
                    <td>
                        <div style="display:flex; align-items:center; gap:10px">
                            <div style="width:36px; height:36px; border-radius:9px; background:linear-gradient(135deg,var(--sky-primary),var(--sky-secondary)); display:flex; align-items:center; justify-content:center; color:white; font-weight:800; font-size:13px; flex-shrink:0;">
                                {{ strtoupper(substr($agent->name, 0, 2)) }}
                            </div>
                            <div>
                                <div style="font-weight:700; font-size:13px">{{ $agent->name }}</div>
                                <div style="font-size:11px; color:var(--text-muted)">{{ $agent->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="hide-mobile"><span class="tx-number">{{ $agent->agent_code ?? '—' }}</span></td>
                    <td>
                        @if($agent->country)
                            <span style="font-size:18px">{{ $agent->country->flag_emoji }}</span>
                            <span style="font-size:11px; color:var(--text-muted); margin-left:4px">{{ $agent->country->code }}</span>
                        @else
                            <span style="color:var(--text-muted)">—</span>
                        @endif
                    </td>
                    <td class="hide-mobile">
                        <div style="font-size:13px">{{ $agent->phone ?? '—' }}</div>
                        @if($agent->address)
                            <div style="font-size:11px; color:var(--text-muted)">{{ Str::limit($agent->address, 28) }}</div>
                        @endif
                    </td>
                    <td class="hide-mobile">
                        <span style="font-weight:700; color:var(--sky-primary)">{{ number_format($agent->transactions_count) }}</span>
                        <span style="font-size:11px; color:var(--text-muted)"> {{ __('app.tx_count') }}</span>
                    </td>
                    <td>
                        <div style="display:flex; flex-direction:column; gap:4px">
                            <span class="badge badge-{{ $agent->status }}">
                                @if($agent->status === 'active') ✅ {{ __('app.status_active') }}
                                @elseif($agent->status === 'pending') ⏳ {{ __('app.pending') }}
                                @else 🚫 {{ __('app.status_inactive') }} @endif
                            </span>
                            <span class="badge badge-{{ $agent->role }}">
                                {{ $agent->role === 'admin' ? '🛡️ '.__('app.role_admin_lbl') : '🏢 '.__('app.role_agent_lbl') }}
                            </span>
                        </div>
                    </td>
                    <td class="hide-mobile" style="color:var(--text-muted); font-size:12px">{{ $agent->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div style="display:flex; flex-wrap:wrap; gap:5px">
                            @if($agent->status !== 'active')
                                <form method="POST" action="{{ route('admin.agents.status', $agent) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="active">
                                    <button type="submit" class="btn btn-sm btn-success" title="{{ __('app.activate') }}">✅</button>
                                </form>
                            @endif
                            @if($agent->status !== 'inactive')
                                <form method="POST" action="{{ route('admin.agents.status', $agent) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="inactive">
                                    <button type="submit" class="btn btn-sm btn-secondary" title="{{ __('app.deactivate') }}">🚫</button>
                                </form>
                            @endif
                            @if($agent->role !== 'admin')
                                <form method="POST" action="{{ route('admin.agents.promote', $agent) }}"
                                      data-confirm="{{ __('app.promote_confirm', ['name' => $agent->name]) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-warning" title="{{ __('app.promote_admin') }}">🛡️</button>
                                </form>
                            @endif
                            @if($agent->transactions_count == 0)
                                <form method="POST" action="{{ route('admin.agents.destroy', $agent) }}"
                                      data-confirm="{{ __('app.delete_confirm') }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="{{ __('app.delete') }}">🗑️</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center; padding:40px; color:var(--text-muted)">
                        <div style="font-size:40px; margin-bottom:10px">👥</div>
                        <div>{{ __('app.no_agents') }}</div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
    <div style="padding:14px 20px">{{ $agents->withQueryString()->links() }}</div>
</div>

@endsection
