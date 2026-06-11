@extends('layouts.app')

@section('title', 'Transaction ' . $transaction->transaction_number)
@section('page-title', __('app.tx_detail_title'))
@section('page-subtitle', $transaction->transaction_number)

@section('content')

<div style="max-width:680px; margin:0 auto;">

    {{-- Barre d'actions principale : navigation + actions sûres uniquement --}}
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; flex-wrap:wrap; gap:10px;">
        <a href="{{ url()->previous() }}" class="btn btn-outline-primary">← {{ __('app.back') }}</a>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="{{ route('agent.transactions.print', $transaction) }}" target="_blank" class="btn btn-secondary">🖨️ {{ __('app.print_receipt') }}</a>
            <a href="{{ route('agent.transactions.edit', $transaction) }}" class="btn btn-primary">✏️ {{ __('app.edit') }}</a>
        </div>
    </div>

    <div class="card animate-on-scroll" id="receipt">
        {{-- Receipt header --}}
        <div id="receipt-header" style="background:linear-gradient(135deg,var(--sky-primary),var(--sky-deeper)); padding:26px 28px; text-align:center;">
            <img id="receipt-logo" src="{{ asset('images/logo.png') }}" alt="Blue Sky" style="width:90px; height:90px; object-fit:contain; filter:brightness(0) invert(1); margin-bottom:8px;">
            <div id="receipt-brand" style="font-size:22px; font-weight:900; color:white; letter-spacing:2px">BLUESKY TRANSACTIONS</div>
            <div id="receipt-subtitle" style="font-size:11px; color:rgba(255,255,255,0.7); margin-top:4px; letter-spacing:2px">
                @if(($transaction->transaction_type ?? 'send') === 'withdrawal')
                    📥 {{ strtoupper(__('app.type_withdrawal')) }} — {{ __('app.transfer_receipt') }}
                @else
                    📤 {{ __('app.transfer_receipt') }}
                @endif
            </div>
        </div>

        <div class="card-body">
            {{-- Transaction number + type badge --}}
            <div style="text-align:center; margin-bottom:26px; padding:16px; background:var(--bg-input); border-radius:10px; border:1px dashed var(--sky-secondary);">
                <div style="font-size:11px; color:var(--text-muted); text-transform:uppercase; letter-spacing:1.5px">{{ __('app.tx_number_label') }}</div>
                <div style="font-size:24px; font-weight:900; color:var(--sky-primary); font-family:monospace; letter-spacing:2px">{{ $transaction->transaction_number }}</div>
                <div style="font-size:12px; color:var(--text-muted); margin-top:4px">{{ $transaction->created_at->format('d/m/Y') }} &nbsp;·&nbsp; {{ $transaction->created_at->format('H:i:s') }}</div>
                <div style="margin-top:10px;">
                    @if(($transaction->transaction_type ?? 'send') === 'withdrawal')
                        <span style="display:inline-flex; align-items:center; gap:5px; background:rgba(251,191,36,0.15); color:#d97706; border:1px solid rgba(251,191,36,0.4); border-radius:8px; padding:5px 14px; font-size:12px; font-weight:700;">
                            📥 {{ __('app.type_withdrawal') }}
                        </span>
                    @else
                        <span style="display:inline-flex; align-items:center; gap:5px; background:rgba(14,165,233,0.12); color:var(--sky-primary); border:1px solid rgba(14,165,233,0.3); border-radius:8px; padding:5px 14px; font-size:12px; font-weight:700;">
                            📤 {{ __('app.type_send') }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Route --}}
            <div style="display:flex; align-items:center; justify-content:center; gap:20px; margin-bottom:26px; padding:18px; background:var(--bg-input); border-radius:12px;">
                <div style="text-align:center">
                    <div style="font-size:40px">{{ $transaction->originCountry?->flag_emoji }}</div>
                    <div style="font-weight:700; color:var(--text-heading)">{{ $transaction->originCountry?->name }}</div>
                    <div style="font-size:12px; color:var(--text-muted)">{{ $transaction->currency ?? $transaction->originCountry?->currency_code }}</div>
                </div>
                <div style="font-size:30px; color:var(--sky-primary); font-weight:900">→</div>
                <div style="text-align:center">
                    <div style="font-size:40px">{{ $transaction->destinationCountry?->flag_emoji }}</div>
                    <div style="font-weight:700; color:var(--text-heading)">{{ $transaction->destinationCountry?->name }}</div>
                    <div style="font-size:12px; color:var(--text-muted)">{{ $transaction->destinationCountry?->currency_code }}</div>
                </div>
            </div>

            {{-- People --}}
            <div class="rg-2" style="margin-bottom:22px;">
                <div style="padding:14px; background:var(--bg-input); border-radius:10px;">
                    <div style="font-size:10px; color:var(--text-muted); text-transform:uppercase; letter-spacing:1.5px; margin-bottom:5px">
                        {{ __('app.sender') }}
                        @if(($transaction->transaction_type ?? 'send') === 'withdrawal')
                            <span style="font-size:10px; color:#d97706;">({{ __('app.optional_for_withdrawal') }})</span>
                        @endif
                    </div>
                    <div style="font-weight:700; color:var(--text-heading)">
                        {{ $transaction->sender_name ?: __('app.not_specified') }}
                    </div>
                    @if($transaction->sender_phone)
                        <div style="font-size:13px; color:var(--text-secondary)">📞 {{ $transaction->sender_phone }}</div>
                    @endif
                </div>
                <div style="padding:14px; background:var(--bg-input); border-radius:10px;">
                    <div style="font-size:10px; color:var(--text-muted); text-transform:uppercase; letter-spacing:1.5px; margin-bottom:5px">
                        @if(($transaction->transaction_type ?? 'send') === 'withdrawal')
                            {{ __('app.withdrawer_info') }}
                        @else
                            {{ __('app.beneficiary') }}
                        @endif
                    </div>
                    <div style="font-weight:700; color:var(--text-heading)">
                        {{ $transaction->receiver_name ?? __('app.not_specified') }}
                    </div>
                    @if($transaction->receiver_phone)
                        <div style="font-size:13px; color:var(--text-secondary)">📞 {{ $transaction->receiver_phone }}</div>
                    @endif
                </div>
            </div>

            {{-- Amounts --}}
            <div style="background:var(--bg-input); border:1px solid var(--border); border-radius:12px; padding:18px; margin-bottom:22px;">
                <div style="display:flex; justify-content:space-between; margin-bottom:12px; padding-bottom:12px; border-bottom:1px dashed var(--border);">
                    <span style="color:var(--text-secondary)">
                        @if(($transaction->transaction_type ?? 'send') === 'withdrawal')
                            {{ __('app.amount_withdrawn') }}
                        @else
                            {{ __('app.amount_sent') }}
                        @endif
                    </span>
                    <span class="amount-display" style="font-size:18px; color:var(--text-heading)">
                        {{ number_format($transaction->amount, 2, ',', ' ') }}
                        <span style="font-size:12px; color:var(--text-muted); font-weight:500; margin-left:3px;">{{ $transaction->currency ?? $transaction->originCountry?->currency_code }}</span>
                    </span>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom:12px; padding-bottom:12px; border-bottom:1px dashed var(--border);">
                    <span style="color:var(--text-secondary)">{{ __('app.fee') }} ({{ $transaction->fee_percentage }}%)</span>
                    <span class="amount-display" style="font-size:18px; color:var(--gold)">
                        {{ number_format($transaction->fee_amount, 2, ',', ' ') }}
                        <span style="font-size:12px; color:var(--text-muted); font-weight:500; margin-left:3px;">{{ $transaction->currency ?? $transaction->originCountry?->currency_code }}</span>
                    </span>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <span style="font-weight:700; font-size:15px; color:var(--text-heading)">{{ __('app.client_total') }}</span>
                    <span class="amount-display" style="font-size:24px; color:var(--sky-primary); font-weight:900">
                        {{ number_format($transaction->total_amount, 2, ',', ' ') }}
                        <span style="font-size:14px; color:var(--text-muted); font-weight:600; margin-left:4px;">{{ $transaction->currency ?? $transaction->originCountry?->currency_code }}</span>
                    </span>
                </div>
            </div>

            {{-- Meta --}}
            <div class="rg-3" style="margin-bottom:18px;">
                <div style="text-align:center; padding:12px; background:var(--bg-input); border-radius:8px;">
                    <div style="font-size:10px; color:var(--text-muted); margin-bottom:4px; text-transform:uppercase; letter-spacing:1px">{{ __('app.status') }}</div>
                    <span class="badge badge-{{ $transaction->status }}" style="font-size:11px">{{ ucfirst($transaction->status) }}</span>
                </div>
                <div style="text-align:center; padding:12px; background:var(--bg-input); border-radius:8px;">
                    <div style="font-size:10px; color:var(--text-muted); margin-bottom:4px; text-transform:uppercase; letter-spacing:1px">{{ __('app.payment_label') }}</div>
                    <div style="font-size:12px; font-weight:600; color:var(--text-primary)">
                        {{ ['cash'=>'💵 Cash','mobile_money'=>'📱 Mobile','bank'=>'🏦 Bank'][$transaction->payment_method] ?? ucfirst($transaction->payment_method) }}
                    </div>
                </div>
                <div style="text-align:center; padding:12px; background:var(--bg-input); border-radius:8px;">
                    <div style="font-size:10px; color:var(--text-muted); margin-bottom:4px; text-transform:uppercase; letter-spacing:1px">{{ __('app.agent') }}</div>
                    <div style="font-size:12px; font-weight:600; color:var(--text-primary)">{{ $transaction->agent?->name }}</div>
                </div>
            </div>

            @if($transaction->notes)
                <div style="padding:12px 14px; background:var(--bg-input); border:1px solid var(--border); border-radius:8px; font-size:13px; color:var(--text-secondary); margin-bottom:18px;">
                    📝 {{ $transaction->notes }}
                </div>
            @endif

            <div style="text-align:center; margin-top:22px; padding-top:14px; border-top:1px solid var(--divider); color:var(--text-muted); font-size:11px;">
                BLUESKY TRANSACTIONS — {{ __('app.trusted_partner') }}<br>
                @foreach($activeCountries as $c){{ $c->flag_emoji }} @endforeach
            </div>
        </div>
    </div>

    {{-- ⚠️ Zone danger — clairement séparée du reçu --}}
    <div style="margin-top:28px; border:1.5px solid rgba(239,68,68,0.3); border-radius:var(--radius); padding:18px 22px; background:rgba(239,68,68,0.04);">
        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:14px;">
            <div>
                <div style="font-size:13px; font-weight:700; color:var(--danger); margin-bottom:3px;">⚠️ {{ __('app.danger_zone') }}</div>
                <div style="font-size:12px; color:var(--text-muted);">{{ __('app.delete_tx_warning') }}</div>
            </div>
            <form method="POST" action="{{ route('agent.transactions.destroy', $transaction) }}"
                  data-confirm="{{ __('app.delete_tx_confirm') }}">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger">🗑️ {{ __('app.delete_this_tx') }}</button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
@media print {
    /* ── Hide navigation & action buttons ── */
    .topbar, .sidebar, .sidebar-overlay,
    .page-header, .breadcrumb { display: none !important; }
    .main-wrapper  { margin-left: 0 !important; }
    .content-area  { padding: 0 !important; }
    body           { background: white !important; }

    /* Hide the action buttons row above the receipt */
    #receipt ~ * , #receipt + * { display: none !important; }
    div:has(> .btn) { display: none !important; }

    /* ── Receipt card ── */
    #receipt {
        box-shadow: none !important;
        border: 1.5px solid #CBD5E1 !important;
        border-radius: 8px !important;
        max-width: 100% !important;
        page-break-inside: avoid;
    }

    /* ── Header: fond blanc + bordure bleue — logo visible ── */
    #receipt-header {
        background: #EFF6FF !important;
        border-bottom: 3px solid #0284C7 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    /* Logo: afficher les vraies couleurs (retirer le filtre blanc) */
    #receipt-logo {
        filter: none !important;
        width: 72px !important;
        height: 72px !important;
    }

    /* Texte du header: bleu foncé au lieu de blanc */
    #receipt-brand    { color: #0C4A6E !important; }
    #receipt-subtitle { color: #0369A1 !important; opacity: 1 !important; }

    /* ── Montants et couleurs ── */
    .amount-display        { color: #0284C7 !important; }
    .badge-completed       { background: #D1FAE5 !important; color: #065F46 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .badge-pending         { background: #FEF3C7 !important; color: #92400E !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .badge-cancelled       { background: #FEE2E2 !important; color: #991B1B !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }

    /* ── Backgrounds des sections ── */
    [style*="background:var(--bg-input)"],
    [style*="background: var(--bg-input)"] {
        background: #F8FAFC !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    /* Bordures dashed */
    [style*="border:1px dashed"] { border-color: #94A3B8 !important; }

    /* ── Forcer l'impression des couleurs (Chrome/Edge/Firefox) ── */
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
}
</style>
@endpush
