<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('app.transfer_receipt') }} — {{ $transaction->transaction_number }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
            background: #F8FAFC;
            color: #1E293B;
            font-size: 14px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        /* ── Page wrapper ── */
        @media screen {
            .page-wrap { min-height: 100vh; padding: 16px 12px 48px; }
        }

        /* ── Action bar ── */
        .action-bar {
            max-width: 520px;
            margin: 0 auto 14px;
            display: flex;
            gap: 10px;
        }
        .btn-back {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 11px 18px; border-radius: 10px;
            background: #fff; border: 1.5px solid #CBD5E1;
            color: #475569; font-size: 14px; font-weight: 600;
            text-decoration: none; cursor: pointer;
            transition: background 0.15s;
        }
        .btn-back:hover { background: #F1F5F9; }
        .btn-print {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 11px 22px; border-radius: 10px;
            background: #0284C7; border: none;
            color: #fff; font-size: 14px; font-weight: 700;
            cursor: pointer; font-family: inherit;
            transition: background 0.15s;
        }
        .btn-print:hover { background: #0369A1; }

        /* ══════════════════════════════════
           CARD
        ══════════════════════════════════ */
        .receipt {
            max-width: 520px;
            margin: 0 auto;
            background: #fff;
            border-radius: 18px;
            overflow: hidden;
            border: 1.5px solid #E2E8F0;
            box-shadow: 0 4px 24px rgba(0,0,0,0.07), 0 1px 4px rgba(0,0,0,0.04);
        }

        /* ── Top accent bar ── */
        .top-bar {
            height: 5px;
            background: linear-gradient(90deg, #0284C7 0%, #38BDF8 50%, #0EA5E9 100%);
        }

        /* ── Header ── */
        .r-header {
            padding: 24px 24px 20px;
            text-align: center;
            border-bottom: 1px solid #F1F5F9;
        }
        .r-logo {
            width: 72px; height: 72px;
            object-fit: contain;
            border-radius: 16px;
            margin-bottom: 10px;
            display: block;
            margin-left: auto; margin-right: auto;
        }
        .r-brand {
            font-size: 20px; font-weight: 900;
            color: #0C4A6E; letter-spacing: 2px;
        }
        .r-brand-accent { color: #0284C7; }
        .r-subtitle {
            font-size: 11px; font-weight: 600;
            color: #64748B; letter-spacing: 1.5px;
            text-transform: uppercase; margin-top: 3px;
        }

        /* ── TX number pill ── */
        .tx-pill {
            margin: 16px 20px 0;
            background: #EFF6FF;
            border: 1.5px solid #BFDBFE;
            border-radius: 12px;
            padding: 14px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
        }
        .tx-num {
            font-size: 17px; font-weight: 900;
            color: #0284C7; letter-spacing: 2px;
            font-family: 'Courier New', monospace;
        }
        .tx-date { font-size: 12px; color: #64748B; margin-top: 2px; }
        .type-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 6px 14px; border-radius: 20px;
            font-size: 12px; font-weight: 700;
        }
        .badge-send       { background: #DBEAFE; color: #1D4ED8; }
        .badge-withdrawal { background: #FEF3C7; color: #92400E; }

        /* ── Section ── */
        .r-body { padding: 20px; display: flex; flex-direction: column; gap: 18px; }

        .sec-label {
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1.5px;
            color: #94A3B8; margin-bottom: 8px;
        }

        /* ── Route ── */
        .route-row {
            display: flex; align-items: center;
            background: #F8FAFC; border: 1px solid #E2E8F0;
            border-radius: 12px; padding: 14px 16px;
            gap: 8px;
        }
        .r-country { flex: 1; text-align: center; }
        .r-flag    { font-size: 38px; line-height: 1; }
        .r-cname   { font-size: 13px; font-weight: 700; color: #0F172A; margin-top: 5px; }
        .r-ccode   {
            display: inline-block; margin-top: 4px;
            background: #0284C7; color: #fff;
            font-size: 10px; font-weight: 700;
            padding: 2px 9px; border-radius: 20px;
        }
        .arrow-col { display: flex; flex-direction: column; align-items: center; gap: 2px; }
        .arrow-line {
            width: 36px; height: 2px;
            background: linear-gradient(90deg, #0284C7, #38BDF8);
            border-radius: 2px; position: relative;
        }
        .arrow-line::after {
            content: '';
            position: absolute; right: -6px; top: -4px;
            border-left: 8px solid #38BDF8;
            border-top: 5px solid transparent;
            border-bottom: 5px solid transparent;
        }
        .arrow-label { font-size: 9px; color: #0284C7; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase; }

        /* ── People ── */
        .people-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        @media (max-width: 400px) { .people-row { grid-template-columns: 1fr; } }
        .person-card {
            background: #F8FAFC; border: 1px solid #E2E8F0;
            border-top: 3px solid #0284C7;
            border-radius: 10px; padding: 12px 14px;
        }
        .person-card.receiver { border-top-color: #10B981; }
        .p-role  { font-size: 9.5px; text-transform: uppercase; letter-spacing: 1px; color: #94A3B8; font-weight: 700; margin-bottom: 5px; }
        .p-name  { font-size: 15px; font-weight: 800; color: #0F172A; }
        .p-phone { font-size: 13px; color: #475569; margin-top: 3px; }

        /* ── Amounts ── */
        .amounts-card {
            border: 1.5px solid #E2E8F0;
            border-radius: 12px; overflow: hidden;
        }
        .amounts-head {
            background: #0284C7;
            padding: 10px 16px;
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1.5px;
            color: rgba(255,255,255,0.95);
        }
        .a-row {
            display: flex; justify-content: space-between; align-items: center;
            padding: 12px 16px; border-bottom: 1px solid #F1F5F9;
            background: #fff;
        }
        .a-row:last-child { border-bottom: none; }
        .a-row.total {
            background: #EFF6FF;
            border-top: 2px solid #0284C7;
        }
        .a-label       { font-size: 14px; color: #475569; }
        .a-label.bold  { font-size: 15px; font-weight: 800; color: #0C2D5C; }
        .a-val         { font-size: 16px; font-weight: 800; font-family: 'Courier New', monospace; color: #0F172A; }
        .a-val.fee     { color: #B45309; }
        .a-val.total-v { font-size: 24px !important; color: #0284C7 !important; }

        /* ── Meta ── */
        .meta-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 8px;
        }
        @media (max-width: 400px) { .meta-row { grid-template-columns: 1fr; } }
        .meta-card {
            text-align: center; padding: 12px 8px;
            background: #F8FAFC; border: 1px solid #E2E8F0;
            border-radius: 10px;
        }
        .m-label { font-size: 9.5px; text-transform: uppercase; letter-spacing: 1px; color: #94A3B8; font-weight: 700; margin-bottom: 5px; }
        .m-val   { font-size: 13px; font-weight: 700; color: #0F172A; }
        .st-badge { display: inline-block; padding: 3px 11px; border-radius: 20px; font-size: 11.5px; font-weight: 700; }
        .st-completed { background: #D1FAE5; color: #065F46; }
        .st-pending   { background: #FEF3C7; color: #92400E; }
        .st-cancelled { background: #FEE2E2; color: #991B1B; }

        /* ── Notes ── */
        .notes-box {
            background: #FFFBEB;
            border-left: 3px solid #F59E0B;
            border-radius: 0 8px 8px 0;
            padding: 10px 14px;
            font-size: 13px; color: #92400E;
        }

        /* ── Client message ── */
        .msg-box {
            background: #F0FDF4;
            border: 1.5px solid #86EFAC;
            border-radius: 12px; padding: 14px 16px;
        }
        .msg-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #15803D; margin-bottom: 5px; }
        .msg-text  { font-size: 13px; color: #166534; line-height: 1.65; font-style: italic; }

        /* ── Footer ── */
        .r-footer {
            background: #F8FAFC;
            border-top: 1px solid #E2E8F0;
            text-align: center; padding: 16px 20px;
        }
        .r-footer-brand {
            font-size: 13px; font-weight: 900;
            color: #0C4A6E; letter-spacing: 2px;
        }
        .r-footer-flags { font-size: 15px; margin: 6px 0 4px; letter-spacing: 1px; }
        .r-footer-tagline { font-size: 10px; color: #94A3B8; font-style: italic; }
        .r-footer-date    { font-size: 10px; color: #CBD5E1; margin-top: 3px; }

        /* ── Bottom accent bar ── */
        .bot-bar {
            height: 4px;
            background: linear-gradient(90deg, #0284C7, #38BDF8 50%, #0EA5E9);
        }

        /* ══════════════════════════════════
           PRINT
        ══════════════════════════════════ */
        @page { size: A4 portrait; margin: 8mm 10mm; }
        @media print {
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            body { background: #fff; font-size: 12px; }
            .no-print { display: none !important; }
            .receipt { box-shadow: none; border-radius: 10px; max-width: 100%; page-break-inside: avoid; }
            .r-logo  { width: 56px; height: 56px; }
            .r-brand { font-size: 17px; }
            .tx-pill { margin: 12px 16px 0; padding: 11px 14px; }
            .tx-num  { font-size: 15px; }
            .r-body  { padding: 14px 16px; gap: 13px; }
            .r-flag  { font-size: 30px; }
            .a-val.total-v { font-size: 20px !important; }
            .r-header { padding: 18px 18px 14px; }
            .amounts-head { padding: 7px 14px; }
            .a-row { padding: 9px 14px; }
            .meta-card { padding: 9px 6px; }
        }
    </style>
</head>
<body>
<div class="page-wrap">

    {{-- Action bar --}}
    <div class="action-bar no-print">
        <a href="{{ route('agent.transactions.show', $transaction) }}" class="btn-back">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
            {{ __('app.back') }}
        </a>
        <button class="btn-print" onclick="window.print()">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            {{ __('app.print_receipt') }}
        </button>
    </div>

    <div class="receipt">
        <div class="top-bar"></div>

        {{-- Header --}}
        <div class="r-header">
            <img class="r-logo" src="{{ asset('images/logo.png') }}" alt="BLUESKY">
            <div class="r-brand">BLUE<span class="r-brand-accent">SKY</span> TRANSACTIONS</div>
            <div class="r-subtitle">
                @if(($transaction->transaction_type ?? 'send') === 'withdrawal')
                    📥 {{ __('app.type_withdrawal') }}
                @else
                    📤 {{ __('app.transfer_receipt') }}
                @endif
            </div>
        </div>

        {{-- TX number --}}
        <div class="tx-pill">
            <div>
                <div class="tx-num">{{ $transaction->transaction_number }}</div>
                <div class="tx-date">{{ $transaction->created_at->format('d/m/Y') }} · {{ $transaction->created_at->format('H:i') }}</div>
            </div>
            @if(($transaction->transaction_type ?? 'send') === 'withdrawal')
                <span class="type-badge badge-withdrawal">📥 {{ __('app.type_withdrawal') }}</span>
            @else
                <span class="type-badge badge-send">📤 {{ __('app.type_send') }}</span>
            @endif
        </div>

        <div class="r-body">

            {{-- Route --}}
            <div>
                <div class="sec-label">{{ __('app.route') }}</div>
                <div class="route-row">
                    <div class="r-country">
                        <div class="r-flag">{{ $transaction->originCountry?->flag_emoji }}</div>
                        <div class="r-cname">{{ $transaction->originCountry?->name }}</div>
                        <span class="r-ccode">{{ $transaction->originCountry?->currency_code }}</span>
                    </div>
                    <div class="arrow-col">
                        <div class="arrow-line"></div>
                        <div class="arrow-label">→</div>
                    </div>
                    <div class="r-country">
                        <div class="r-flag">{{ $transaction->destinationCountry?->flag_emoji }}</div>
                        <div class="r-cname">{{ $transaction->destinationCountry?->name }}</div>
                        <span class="r-ccode">{{ $transaction->destinationCountry?->currency_code }}</span>
                    </div>
                </div>
            </div>

            {{-- People --}}
            <div>
                <div class="sec-label">{{ __('app.sender') }} / {{ __('app.beneficiary') }}</div>
                <div class="people-row">
                    <div class="person-card">
                        <div class="p-role">
                            {{ __('app.sender') }}
                            @if(($transaction->transaction_type ?? 'send') === 'withdrawal')
                                ({{ __('app.optional_for_withdrawal') }})
                            @endif
                        </div>
                        <div class="p-name">{{ $transaction->sender_name ?: __('app.not_specified') }}</div>
                        @if($transaction->sender_phone)
                            <div class="p-phone">📞 {{ $transaction->sender_phone }}</div>
                        @endif
                    </div>
                    <div class="person-card receiver">
                        <div class="p-role">
                            @if(($transaction->transaction_type ?? 'send') === 'withdrawal')
                                {{ __('app.withdrawer_info') }}
                            @else
                                {{ __('app.beneficiary') }}
                            @endif
                        </div>
                        <div class="p-name">{{ $transaction->receiver_name ?: __('app.not_specified') }}</div>
                        @if($transaction->receiver_phone)
                            <div class="p-phone">📞 {{ $transaction->receiver_phone }}</div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Amounts --}}
            <div>
                <div class="amounts-card">
                    <div class="amounts-head">💰 {{ __('app.transaction_summary') }}</div>
                    <div class="a-row">
                        <span class="a-label">
                            @if(($transaction->transaction_type ?? 'send') === 'withdrawal')
                                {{ __('app.amount_withdrawn') }}
                            @else
                                {{ __('app.amount_sent') }}
                            @endif
                        </span>
                        <span class="a-val">{{ number_format($transaction->amount, 2, ',', ' ') }}</span>
                    </div>
                    <div class="a-row">
                        <span class="a-label">{{ __('app.fee') }} ({{ $transaction->fee_percentage }}%)</span>
                        <span class="a-val fee">+ {{ number_format($transaction->fee_amount, 2, ',', ' ') }}</span>
                    </div>
                    <div class="a-row total">
                        <span class="a-label bold">{{ __('app.client_total') }}</span>
                        <span class="a-val total-v">{{ number_format($transaction->total_amount, 2, ',', ' ') }}</span>
                    </div>
                </div>
            </div>

            {{-- Meta --}}
            <div>
                <div class="sec-label">{{ __('app.tx_detail_title') }}</div>
                <div class="meta-row">
                    <div class="meta-card">
                        <div class="m-label">{{ __('app.status') }}</div>
                        <span class="st-badge st-{{ $transaction->status }}">{{ ucfirst($transaction->status) }}</span>
                    </div>
                    <div class="meta-card">
                        <div class="m-label">{{ __('app.payment_label') }}</div>
                        <div class="m-val">
                            {{ ['cash'=>'💵 Cash','mobile_money'=>'📱 Mobile','bank'=>'🏦 Bank'][$transaction->payment_method] ?? ucfirst($transaction->payment_method) }}
                        </div>
                    </div>
                    <div class="meta-card">
                        <div class="m-label">{{ __('app.agent') }}</div>
                        <div class="m-val">{{ $transaction->agent?->name }}</div>
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            @if($transaction->notes)
                <div class="notes-box">📝 {{ $transaction->notes }}</div>
            @endif

            {{-- Client message --}}
            <div class="msg-box">
                <div class="msg-title">{{ __('app.receipt_client_greeting') }}</div>
                <div class="msg-text">{{ __('app.receipt_client_message') }}</div>
            </div>

        </div>

        {{-- Footer --}}
        <div class="r-footer">
            <div class="r-footer-brand">BLUE<span style="color:#0284C7">SKY</span> TRANSACTIONS</div>
            <div class="r-footer-flags">@foreach($activeCountries as $c){{ $c->flag_emoji }} @endforeach</div>
            <div class="r-footer-tagline">{{ __('app.trusted_partner') }}</div>
            <div class="r-footer-date">{{ now()->format('d/m/Y H:i') }}</div>
        </div>
        <div class="bot-bar"></div>
    </div>

</div>
<script>
    if (window.opener || window.history.length === 1) {
        window.onload = () => setTimeout(() => window.print(), 400);
    }
</script>
</body>
</html>
