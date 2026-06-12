<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('app.transfer_receipt') }} — {{ $transaction->transaction_number }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
            background: #fff;
            color: #1e293b;
            font-size: 12.5px;
            line-height: 1.45;
        }

        /* ══════════════════════════════════════════
           SHELL
        ══════════════════════════════════════════ */
        .receipt {
            max-width: 600px;
            margin: 0 auto;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 6px 32px rgba(2,132,199,0.12), 0 1px 6px rgba(0,0,0,0.07);
            border: 1.5px solid #BAE6FD;
        }

        /* ══════════════════════════════════════════
           HEADER — light sky blue
        ══════════════════════════════════════════ */
        .receipt-header {
            background: linear-gradient(150deg, #EFF6FF 0%, #DBEAFE 55%, #BAE6FD 100%);
            padding: 24px 28px 18px;
            text-align: center;
            position: relative;
            overflow: hidden;
            border-bottom: 3px solid #0284C7;
        }
        .receipt-header::before {
            content: '';
            position: absolute;
            top: -50px; right: -50px;
            width: 160px; height: 160px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(2,132,199,0.12) 0%, transparent 70%);
        }
        .receipt-header::after {
            content: '';
            position: absolute;
            bottom: -60px; left: -40px;
            width: 180px; height: 180px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(2,132,199,0.08) 0%, transparent 70%);
        }
        .logo-wrap {
            position: relative;
            z-index: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 130px;
            height: 130px;
            border-radius: 50%;
            background: rgba(255,255,255,0.85);
            border: 2.5px solid rgba(2,132,199,0.35);
            box-shadow: 0 4px 20px rgba(2,132,199,0.18), inset 0 1px 3px rgba(255,255,255,0.9);
            margin: 0 auto 13px;
        }
        .receipt-header img {
            width: 100px;
            height: 100px;
            object-fit: contain;
            display: block;
        }
        .receipt-brand {
            font-size: 20px;
            font-weight: 900;
            color: #0C4A6E;
            letter-spacing: 2.5px;
            position: relative;
            z-index: 1;
        }
        .receipt-brand span { color: #0284C7; }
        .receipt-subtitle {
            font-size: 10px;
            color: #0369A1;
            margin-top: 4px;
            letter-spacing: 2px;
            text-transform: uppercase;
            position: relative;
            z-index: 1;
            font-weight: 600;
        }
        .header-stripe {
            height: 3px;
            background: linear-gradient(90deg, #0284C7, #38BDF8 40%, #F59E0B 60%, #0284C7);
        }

        /* ══════════════════════════════════════════
           BODY
        ══════════════════════════════════════════ */
        .receipt-body { padding: 18px 24px; background: #fff; }

        .section-title {
            font-size: 8.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.8px;
            color: #94A3B8;
            margin-bottom: 7px;
            display: flex;
            align-items: center;
            gap: 7px;
        }
        .section-title::after { content: ''; flex: 1; height: 1px; background: #E2E8F0; }

        /* ── N° transaction ── */
        .tx-number-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
            padding: 12px 16px;
            background: linear-gradient(135deg, #F0F9FF, #E0F2FE);
            border-radius: 10px;
            border: 1.5px solid #BAE6FD;
            margin-bottom: 14px;
        }
        .tx-number-left { text-align: left; }
        .tx-number-value {
            font-size: 20px;
            font-weight: 900;
            color: #0284C7;
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
        }
        .tx-date { font-size: 10.5px; color: #64748b; margin-top: 2px; }
        .tx-type-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }
        .badge-send       { background: #DBEAFE; color: #1D4ED8; border: 1px solid #BFDBFE; }
        .badge-withdrawal { background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A; }

        /* ── Route ── */
        .route-box {
            display: flex;
            align-items: center;
            background: #F8FAFC;
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 14px;
            border: 1px solid #E2E8F0;
        }
        .route-country { text-align: center; flex: 1; }
        .route-flag    { font-size: 34px; line-height: 1; }
        .route-name    { font-weight: 700; font-size: 12px; color: #0F172A; margin-top: 4px; }
        .route-code    {
            font-size: 9.5px; color: #fff;
            background: #0284C7; padding: 1px 7px;
            border-radius: 8px; display: inline-block;
            margin-top: 3px; font-weight: 600;
        }
        .route-arrow-wrap { display: flex; flex-direction: column; align-items: center; gap: 3px; padding: 0 16px; }
        .route-arrow-line {
            width: 44px; height: 2px;
            background: linear-gradient(90deg, #0284C7, #38BDF8);
            border-radius: 2px; position: relative;
        }
        .route-arrow-line::after {
            content: '';
            position: absolute; right: -6px; top: -4px;
            border-left: 8px solid #38BDF8;
            border-top: 5px solid transparent;
            border-bottom: 5px solid transparent;
        }
        .route-arrow-label { font-size: 8px; color: #0284C7; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; }

        /* ── People ── */
        .people-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 14px;
        }
        .person-box {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-top: 3px solid #0284C7;
            border-radius: 8px;
            padding: 10px 12px;
        }
        .person-box.receiver { border-top-color: #10B981; }
        .person-label {
            font-size: 8px; text-transform: uppercase;
            letter-spacing: 1.2px; color: #94A3B8;
            margin-bottom: 4px; font-weight: 600;
        }
        .person-name  { font-weight: 800; font-size: 13px; color: #0F172A; }
        .person-phone { font-size: 11px; color: #475569; margin-top: 2px; }

        /* ── Amounts ── */
        .amounts-box {
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 14px;
            border: 1.5px solid #BAE6FD;
        }
        .amounts-header {
            background: linear-gradient(90deg, #0284C7, #0EA5E9);
            padding: 7px 14px;
            font-size: 8.5px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1.8px;
            color: rgba(255,255,255,0.9);
        }
        .amount-row {
            display: flex; justify-content: space-between; align-items: center;
            padding: 9px 14px;
            border-bottom: 1px solid #F1F5F9;
            background: #fff;
        }
        .amount-row.total-row {
            background: linear-gradient(135deg, #EFF6FF, #DBEAFE);
            border-bottom: none;
            border-top: 1.5px solid #0284C7;
        }
        .amount-label        { color: #475569; font-size: 12px; }
        .amount-value        { font-family: 'Courier New', monospace; font-weight: 800; font-size: 14px; color: #0F172A; }
        .amount-fee          { color: #B45309; }
        .amount-total        { font-size: 22px !important; color: #0284C7 !important; }
        .amount-label-total  { font-weight: 800; font-size: 13px; color: #0C2D5C; }

        /* ── Meta ── */
        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 8px;
            margin-bottom: 14px;
        }
        .meta-box {
            text-align: center; padding: 9px 6px;
            background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px;
        }
        .meta-label { font-size: 8px; text-transform: uppercase; letter-spacing: 1px; color: #94A3B8; margin-bottom: 4px; font-weight: 600; }
        .meta-value { font-size: 11.5px; font-weight: 700; color: #0F172A; }
        .badge-completed { background: #D1FAE5; color: #065F46; padding: 2px 9px; border-radius: 20px; font-size: 10.5px; font-weight: 700; }
        .badge-pending   { background: #FEF3C7; color: #92400E; padding: 2px 9px; border-radius: 20px; font-size: 10.5px; font-weight: 700; }
        .badge-cancelled { background: #FEE2E2; color: #991B1B; padding: 2px 9px; border-radius: 20px; font-size: 10.5px; font-weight: 700; }

        /* ── Notes ── */
        .notes-box {
            background: #FFFBEB;
            border-left: 3px solid #F59E0B;
            border-radius: 0 7px 7px 0;
            padding: 8px 12px;
            font-size: 11.5px; color: #92400E;
            margin-bottom: 14px;
        }

        /* ── Message client ── */
        .client-message {
            background: linear-gradient(135deg, #F0FDF4, #DCFCE7);
            border: 1.5px solid #86EFAC;
            border-radius: 10px;
            padding: 13px 16px;
            margin-bottom: 14px;
            position: relative;
            overflow: hidden;
        }
        .client-message::before {
            content: '✉';
            position: absolute;
            right: 12px; top: 50%;
            transform: translateY(-50%);
            font-size: 52px;
            opacity: 0.07;
            line-height: 1;
        }
        .client-message-greeting {
            font-size: 10px;
            font-weight: 700;
            color: #15803D;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }
        .client-message-text {
            font-size: 11.5px;
            color: #166534;
            line-height: 1.6;
            font-style: italic;
        }

        /* ── Footer ── */
        .header-stripe-bottom {
            height: 3px;
            background: linear-gradient(90deg, #0284C7, #38BDF8 40%, #F59E0B 60%, #0284C7);
        }
        .receipt-footer {
            background: linear-gradient(135deg, #EFF6FF, #DBEAFE);
            text-align: center;
            padding: 13px 20px 12px;
        }
        .receipt-footer-brand {
            font-size: 12px; font-weight: 900;
            color: #0C4A6E; letter-spacing: 2px;
        }
        .receipt-footer-brand span { color: #0284C7; }
        .footer-flags { font-size: 14px; letter-spacing: 1.5px; margin: 4px 0 2px; }
        .footer-tagline { font-size: 9px; color: #64748B; font-style: italic; }
        .footer-date { font-size: 9px; color: #94A3B8; margin-top: 2px; }

        /* ══════════════════════════════════════════
           SCREEN — wrapper & buttons
        ══════════════════════════════════════════ */
        @media screen {
            body { background: #F1F5F9; padding: 20px 12px 48px; }
            .print-btn-bar {
                max-width: 600px; margin: 0 auto 14px;
                display: flex; gap: 10px; justify-content: flex-end;
                flex-wrap: wrap;
            }
            .btn-print {
                background: linear-gradient(135deg, #0EA5E9, #0284C7);
                color: white; border: none;
                padding: 12px 24px; border-radius: 10px;
                font-size: 14px; font-weight: 700; cursor: pointer;
                display: inline-flex; align-items: center; gap: 7px;
                box-shadow: 0 4px 14px rgba(2,132,199,0.35);
                font-family: inherit;
            }
            .btn-print:hover { background: linear-gradient(135deg, #38BDF8, #0284C7); }
            .btn-back {
                background: #fff; color: #475569;
                border: 1.5px solid #CBD5E1;
                padding: 12px 20px; border-radius: 10px;
                font-size: 14px; font-weight: 600; cursor: pointer;
                text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
            }
            .btn-back:hover { background: #F8FAFC; }
        }

        /* ══════════════════════════════════════════
           MOBILE — adapt layout, keep design
        ══════════════════════════════════════════ */
        @media screen and (max-width: 500px) {
            body { padding: 12px 8px 40px; font-size: 13px; }

            .receipt { border-radius: 16px; }

            /* Header */
            .receipt-header { padding: 20px 18px 16px; }
            .logo-wrap { width: 100px; height: 100px; margin-bottom: 10px; }
            .receipt-header img { width: 76px; height: 76px; }
            .receipt-brand { font-size: 18px; letter-spacing: 2px; }
            .receipt-subtitle { font-size: 9px; }

            /* TX number */
            .tx-number-box { flex-direction: column; align-items: flex-start; gap: 10px; padding: 14px; }
            .tx-number-value { font-size: 17px; letter-spacing: 1px; }
            .tx-date { font-size: 12px; }
            .tx-type-badge { font-size: 12px; padding: 6px 14px; }

            /* Body padding */
            .receipt-body { padding: 16px 14px; }

            /* Section labels */
            .section-title { font-size: 9px; margin-bottom: 9px; }

            /* Route */
            .route-box { padding: 14px 12px; margin-bottom: 14px; }
            .route-flag { font-size: 36px; }
            .route-name { font-size: 13px; }
            .route-code { font-size: 10px; padding: 2px 9px; }
            .route-arrow-wrap { padding: 0 10px; }
            .route-arrow-line { width: 32px; }

            /* People — stack vertically */
            .people-grid { grid-template-columns: 1fr; gap: 10px; }
            .person-box { padding: 12px 14px; }
            .person-label { font-size: 9px; }
            .person-name  { font-size: 16px; }
            .person-phone { font-size: 13px; }

            /* Amounts */
            .amounts-header { padding: 9px 14px; font-size: 9px; }
            .amount-row { padding: 11px 14px; }
            .amount-label { font-size: 13px; }
            .amount-value { font-size: 15px; }
            .amount-total { font-size: 24px !important; }
            .amount-label-total { font-size: 14px; }

            /* Meta — stack 1 col */
            .meta-grid { grid-template-columns: 1fr; gap: 8px; }
            .meta-box { padding: 11px 14px; text-align: left; display: flex; align-items: center; justify-content: space-between; }
            .meta-label { font-size: 9px; margin-bottom: 0; }
            .meta-value { font-size: 13px; }

            /* Notes & message */
            .notes-box { font-size: 13px; padding: 10px 14px; }
            .client-message { padding: 14px; }
            .client-message-greeting { font-size: 10px; }
            .client-message-text { font-size: 13px; }

            /* Footer */
            .receipt-footer { padding: 14px 16px; }
            .receipt-footer-brand { font-size: 13px; }
            .footer-flags { font-size: 16px; margin: 6px 0 4px; }
            .footer-tagline { font-size: 10px; }
            .footer-date { font-size: 10px; }

            /* Action buttons */
            .print-btn-bar { justify-content: stretch; }
            .btn-print, .btn-back { flex: 1; justify-content: center; padding: 14px 16px; font-size: 14px; }
        }

        /* ══════════════════════════════════════════
           PRINT — A4
        ══════════════════════════════════════════ */
        @page { size: A4 portrait; margin: 7mm 9mm; }
        @media print {
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            body { background: white; padding: 0; margin: 0; font-size: 11px; }
            .no-print { display: none !important; }
            .receipt {
                max-width: 100%; border-radius: 8px;
                box-shadow: none; border: 1.5px solid #BAE6FD;
                page-break-inside: avoid;
            }
            .receipt-header     { padding: 16px 22px 13px; }
            .logo-wrap          { width: 100px; height: 100px; margin-bottom: 10px; }
            .receipt-header img { width: 78px; height: 78px; }
            .receipt-brand      { font-size: 16px; }
            .receipt-subtitle   { font-size: 9px; margin-top: 3px; }
            .receipt-body       { padding: 12px 18px; }
            .section-title      { margin-bottom: 5px; font-size: 7.5px; }
            .tx-number-box      { padding: 9px 13px; margin-bottom: 10px; }
            .tx-number-value    { font-size: 17px; }
            .tx-date            { font-size: 9.5px; }
            .route-box          { padding: 9px 13px; margin-bottom: 10px; }
            .route-flag         { font-size: 28px; }
            .route-name         { font-size: 11px; }
            .route-arrow-line   { width: 36px; }
            .people-grid        { gap: 7px; margin-bottom: 10px; }
            .person-box         { padding: 8px 10px; }
            .person-name        { font-size: 12px; }
            .person-phone       { font-size: 10px; }
            .amounts-box        { margin-bottom: 10px; }
            .amounts-header     { padding: 5px 13px; font-size: 7.5px; }
            .amount-row         { padding: 7px 13px; }
            .amount-value       { font-size: 12.5px; }
            .amount-total       { font-size: 18px !important; }
            .amount-label-total { font-size: 12px; }
            .meta-grid          { gap: 6px; margin-bottom: 10px; }
            .meta-box           { padding: 7px 5px; }
            .meta-value         { font-size: 10.5px; }
            .receipt-footer     { padding: 9px 18px 8px; }
            .receipt-footer-brand { font-size: 11px; }
            .footer-flags       { font-size: 13px; margin: 2px 0; }
            .client-message     { padding: 9px 13px; margin-bottom: 10px; }
            .client-message-text { font-size: 10.5px; }
        }
    </style>
</head>
<body>

<div class="print-btn-bar no-print">
    <a href="{{ route('agent.transactions.show', $transaction) }}" class="btn-back">← {{ __('app.back') }}</a>
    <button class="btn-print" onclick="window.print()">🖨️ {{ __('app.print_receipt') }}</button>
</div>

<div class="receipt">

    {{-- HEADER --}}
    <div class="receipt-header">
        <div class="logo-wrap">
            <img src="{{ asset('images/logo.png') }}" alt="BLUESKY">
        </div>
        <div class="receipt-brand">BLUE<span>SKY</span> TRANSACTIONS</div>
        <div class="receipt-subtitle">
            @if(($transaction->transaction_type ?? 'send') === 'withdrawal')
                📥 {{ __('app.type_withdrawal') }} — {{ __('app.transfer_receipt') }}
            @else
                📤 {{ __('app.transfer_receipt') }}
            @endif
        </div>
    </div>
    <div class="header-stripe"></div>

    <div class="receipt-body">

        {{-- N° transaction --}}
        <div class="section-title">{{ __('app.tx_number_label') }}</div>
        <div class="tx-number-box">
            <div class="tx-number-left">
                <div class="tx-number-value">{{ $transaction->transaction_number }}</div>
                <div class="tx-date">📅 {{ $transaction->created_at->format('d/m/Y') }} &nbsp;·&nbsp; 🕐 {{ $transaction->created_at->format('H:i:s') }}</div>
            </div>
            @if(($transaction->transaction_type ?? 'send') === 'withdrawal')
                <span class="tx-type-badge badge-withdrawal">📥 {{ __('app.type_withdrawal') }}</span>
            @else
                <span class="tx-type-badge badge-send">📤 {{ __('app.type_send') }}</span>
            @endif
        </div>

        {{-- Route --}}
        <div class="section-title">{{ __('app.route') }}</div>
        <div class="route-box">
            <div class="route-country">
                <div class="route-flag">{{ $transaction->originCountry?->flag_emoji }}</div>
                <div class="route-name">{{ $transaction->originCountry?->name }}</div>
                <div class="route-code">{{ $transaction->originCountry?->currency_code }}</div>
            </div>
            <div class="route-arrow-wrap">
                <div class="route-arrow-line"></div>
                <div class="route-arrow-label">{{ __('app.receipt_transfer_label') }}</div>
            </div>
            <div class="route-country">
                <div class="route-flag">{{ $transaction->destinationCountry?->flag_emoji }}</div>
                <div class="route-name">{{ $transaction->destinationCountry?->name }}</div>
                <div class="route-code">{{ $transaction->destinationCountry?->currency_code }}</div>
            </div>
        </div>

        {{-- Personnes --}}
        <div class="section-title">{{ __('app.sender') }} / {{ __('app.beneficiary') }}</div>
        <div class="people-grid">
            <div class="person-box">
                <div class="person-label">
                    {{ __('app.sender') }}
                    @if(($transaction->transaction_type ?? 'send') === 'withdrawal')
                        ({{ __('app.optional_for_withdrawal') }})
                    @endif
                </div>
                <div class="person-name">{{ $transaction->sender_name ?: __('app.not_specified') }}</div>
                @if($transaction->sender_phone)
                    <div class="person-phone">📞 {{ $transaction->sender_phone }}</div>
                @endif
            </div>
            <div class="person-box receiver">
                <div class="person-label">
                    @if(($transaction->transaction_type ?? 'send') === 'withdrawal')
                        {{ __('app.withdrawer_info') }}
                    @else
                        {{ __('app.beneficiary') }}
                    @endif
                </div>
                <div class="person-name">{{ $transaction->receiver_name ?: __('app.not_specified') }}</div>
                @if($transaction->receiver_phone)
                    <div class="person-phone">📞 {{ $transaction->receiver_phone }}</div>
                @endif
            </div>
        </div>

        {{-- Montants --}}
        <div class="amounts-box">
            <div class="amounts-header">💰 {{ __('app.transaction_summary') }}</div>
            <div class="amount-row">
                <span class="amount-label">
                    @if(($transaction->transaction_type ?? 'send') === 'withdrawal')
                        {{ __('app.amount_withdrawn') }}
                    @else
                        {{ __('app.amount_sent') }}
                    @endif
                </span>
                <span class="amount-value">{{ number_format($transaction->amount, 2, ',', ' ') }}</span>
            </div>
            <div class="amount-row">
                <span class="amount-label">{{ __('app.fee') }} ({{ $transaction->fee_percentage }}%)</span>
                <span class="amount-value amount-fee">+ {{ number_format($transaction->fee_amount, 2, ',', ' ') }}</span>
            </div>
            <div class="amount-row total-row">
                <span class="amount-label amount-label-total">{{ __('app.client_total') }}</span>
                <span class="amount-value amount-total">{{ number_format($transaction->total_amount, 2, ',', ' ') }}</span>
            </div>
        </div>

        {{-- Méta --}}
        <div class="section-title">{{ __('app.tx_detail_title') }}</div>
        <div class="meta-grid">
            <div class="meta-box">
                <div class="meta-label">{{ __('app.status') }}</div>
                <span class="badge-{{ $transaction->status }}">{{ ucfirst($transaction->status) }}</span>
            </div>
            <div class="meta-box">
                <div class="meta-label">{{ __('app.payment_label') }}</div>
                <div class="meta-value">
                    {{ ['cash'=>'💵 Cash','mobile_money'=>'📱 Mobile','bank'=>'🏦 Bank'][$transaction->payment_method] ?? ucfirst($transaction->payment_method) }}
                </div>
            </div>
            <div class="meta-box">
                <div class="meta-label">{{ __('app.agent') }}</div>
                <div class="meta-value">{{ $transaction->agent?->name }}</div>
            </div>
        </div>

        @if($transaction->notes)
            <div class="notes-box">📝 {{ $transaction->notes }}</div>
        @endif

        {{-- Message client --}}
        <div class="client-message">
            <div class="client-message-greeting">{{ __('app.receipt_client_greeting') }}</div>
            <div class="client-message-text">{{ __('app.receipt_client_message') }}</div>
        </div>

    </div>

    {{-- FOOTER --}}
    <div class="header-stripe-bottom"></div>
    <div class="receipt-footer">
        <div class="receipt-footer-brand">BLUE<span>SKY</span> TRANSACTIONS</div>
        <div class="footer-tagline">{{ __('app.trusted_partner') }}</div>
        <div class="footer-flags">@foreach($activeCountries as $c){{ $c->flag_emoji }} @endforeach</div>
        <div class="footer-date">{{ now()->format('d/m/Y H:i') }}</div>
    </div>

</div>

<script>
    if (window.opener || window.history.length === 1) {
        window.onload = () => setTimeout(() => window.print(), 400);
    }
</script>
</body>
</html>
