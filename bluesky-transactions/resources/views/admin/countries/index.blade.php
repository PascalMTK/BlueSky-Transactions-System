@extends('layouts.app')

@section('page-title', __('app.operating_countries'))
@section('page-subtitle', __('app.countries_subtitle'))

@section('content')

{{-- Stats --}}
<div class="stats-grid">
    <div class="stat-card blue animate-on-scroll">
        <div class="stat-icon blue">🌍</div>
        <div class="stat-info">
            <div class="stat-value" data-counter="{{ $countries->count() }}">0</div>
            <div class="stat-label">{{ __('app.total_countries') }}</div>
        </div>
    </div>
    <div class="stat-card green animate-on-scroll">
        <div class="stat-icon green">✅</div>
        <div class="stat-info">
            <div class="stat-value" data-counter="{{ $countries->where('is_active', true)->count() }}">0</div>
            <div class="stat-label">{{ __('app.active') }}</div>
        </div>
    </div>
    <div class="stat-card gold animate-on-scroll">
        <div class="stat-icon gold">💱</div>
        <div class="stat-info">
            <div class="stat-value" data-counter="{{ $countries->pluck('currency_code')->unique()->count() }}">0</div>
            <div class="stat-label">{{ __('app.currencies') }}</div>
        </div>
    </div>
    <div class="stat-card red animate-on-scroll">
        <div class="stat-icon red">🚫</div>
        <div class="stat-info">
            <div class="stat-value" data-counter="{{ $countries->where('is_active', false)->count() }}">0</div>
            <div class="stat-label">{{ __('app.disabled') }}</div>
        </div>
    </div>
</div>

{{-- Action bar --}}
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <p style="font-size:13px; color:var(--text-muted); margin:0;">
        <span style="font-weight:700; color:var(--text-primary);">{{ $countries->count() }}</span>
        {{ __('app.registered_countries_lbl') }}
        &nbsp;·&nbsp;
        <span style="color:var(--success); font-weight:600;">{{ $countries->where('is_active', true)->count() }}</span>
        {{ __('app.status_active') }}
    </p>
    <a href="{{ route('admin.countries.create') }}" class="btn btn-primary btn-lg">
        ＋ {{ __('app.add_country') }}
    </a>
</div>

{{-- Cards grid --}}
<div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(268px, 1fr)); gap:18px;">
    @forelse($countries as $country)

    <div class="card animate-on-scroll"
         style="padding:0; overflow:hidden; {{ !$country->is_active ? 'opacity:0.65;' : '' }}">

        {{-- Card top band --}}
        <div style="height:5px; background:{{ $country->is_active
            ? 'linear-gradient(90deg, var(--sky-primary), var(--sky-secondary))'
            : 'var(--border)' }};"></div>

        <div style="padding:18px 18px 16px;">

            {{-- Header row: flag + name + status toggle --}}
            <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:14px;">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="width:48px; height:48px; border-radius:12px;
                                background:var(--sky-light); display:flex; align-items:center;
                                justify-content:center; overflow:hidden; flex-shrink:0;
                                border:1px solid rgba(14,165,233,0.15);">
                        <x-flag :code="$country->code" size="48" style="border-radius:0; width:100%; height:100%; object-fit:cover;" />
                    </div>
                    <div>
                        <div style="font-size:14.5px; font-weight:800; color:var(--text-heading); line-height:1.2;">
                            {{ $country->name }}
                        </div>
                        <div style="display:flex; align-items:center; gap:6px; margin-top:4px;">
                            <span class="badge" style="background:var(--sky-light); color:var(--sky-primary);
                                          font-size:10px; letter-spacing:1.5px; font-weight:800;">
                                {{ $country->code }}
                            </span>
                            <span style="font-size:11px; color:var(--text-muted);">{{ $country->phone_code }}</span>
                        </div>
                    </div>
                </div>

                {{-- Toggle active/inactive --}}
                <form method="POST" action="{{ route('admin.countries.toggle', $country) }}">
                    @csrf @method('PATCH')
                    <button type="submit"
                        title="{{ $country->is_active ? __('app.deactivate') : __('app.activate') }}"
                        class="badge {{ $country->is_active ? 'badge-active' : 'badge-inactive' }}"
                        style="border:none; cursor:pointer; font-size:11px; white-space:nowrap;">
                        {{ $country->is_active ? '● '.__('app.status_active') : '○ '.__('app.disabled') }}
                    </button>
                </form>
            </div>

            {{-- Details --}}
            <div style="background:var(--bg-input); border-radius:10px; padding:10px 12px;
                        display:flex; flex-direction:column; gap:7px; margin-bottom:14px;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-size:11px; color:var(--text-muted);">{{ __('app.currency') }}</span>
                    <span style="font-size:12.5px; font-weight:700; color:var(--text-primary);">
                        {{ $country->currency_code }}
                        <span style="font-weight:400; color:var(--text-muted); font-size:11px;">
                            {{ $country->currency_name }}
                        </span>
                    </span>
                </div>
                <div style="height:1px; background:var(--divider);"></div>
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-size:11px; color:var(--text-muted);">{{ __('app.default_fee') }}</span>
                    <span style="font-size:13px; font-weight:800; color:var(--sky-primary);">
                        {{ $country->default_fee_percentage }}%
                    </span>
                </div>
            </div>

            {{-- Counters --}}
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:14px;">
                <div style="text-align:center; padding:9px; background:var(--bg-input); border-radius:9px;
                            border:1px solid var(--border);">
                    <div style="font-size:20px; font-weight:900; color:var(--sky-primary); line-height:1;">
                        {{ $country->agents_count }}
                    </div>
                    <div style="font-size:10px; color:var(--text-muted); margin-top:2px;">
                        {{ __('app.agent') }}s
                    </div>
                </div>
                <div style="text-align:center; padding:9px; background:var(--bg-input); border-radius:9px;
                            border:1px solid var(--border);">
                    <div style="font-size:20px; font-weight:900; color:var(--success); line-height:1;">
                        {{ $country->outgoing_transactions_count }}
                    </div>
                    <div style="font-size:10px; color:var(--text-muted); margin-top:2px;">{{ __('app.tx_count') }}</div>
                </div>
            </div>

            {{-- Actions --}}
            <div style="display:flex; gap:8px;">
                <a href="{{ route('admin.countries.edit', $country) }}"
                   class="btn btn-secondary btn-sm" style="flex:1; justify-content:center;">
                    ✏️ {{ __('app.edit') }}
                </a>
                @if($country->agents_count === 0 && $country->outgoing_transactions_count === 0)
                <form method="POST" action="{{ route('admin.countries.destroy', $country) }}"
                      onsubmit="return confirm('{{ addslashes(__('app.delete_confirm')) }}')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" title="{{ __('app.delete') }}">
                        🗑
                    </button>
                </form>
                @endif
            </div>

        </div>
    </div>

    @empty
    <div style="grid-column:1/-1;">
        <div class="card" style="text-align:center; padding:56px 24px;">
            <div style="font-size:52px; margin-bottom:14px;">🌍</div>
            <div style="font-size:17px; font-weight:700; color:var(--text-heading); margin-bottom:6px;">
                {{ __('app.no_data') }}
            </div>
            <p style="color:var(--text-muted); font-size:13px; margin-bottom:20px;">
                {{ __('app.add_first_country') }}
            </p>
            <a href="{{ route('admin.countries.create') }}" class="btn btn-primary btn-lg">
                ＋ {{ __('app.add_country') }}
            </a>
        </div>
    </div>
    @endforelse
</div>

@endsection
