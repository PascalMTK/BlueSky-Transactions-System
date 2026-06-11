@extends('layouts.app')

@section('title', __('app.edit_transaction') . ' ' . $transaction->transaction_number)
@section('page-title', __('app.edit_transaction'))
@section('page-subtitle', $transaction->transaction_number)

@section('content')

<div style="max-width:900px; margin:0 auto;">
    <div class="card animate-on-scroll">
        <div class="card-header-wrap">
            <div class="card-header-info">
                <div class="card-title">✏️ {{ __('app.edit_transaction') }}</div>
                <div class="card-subtitle" style="font-family:monospace; color:var(--sky-primary)">{{ $transaction->transaction_number }}</div>
            </div>
            {{-- Live preview --}}
            <div class="card-preview-box">
                <div style="font-size:11px; color:var(--text-muted); text-transform:uppercase; letter-spacing:1px; margin-bottom:4px">{{ __('app.client_total') }}</div>
                <div id="totalPreview" class="amount-display" style="font-size:28px; color:var(--sky-primary)">—</div>
                <div style="font-size:12px; color:var(--text-muted); margin-top:2px">
                    {{ __('app.incl_fee') }} <span id="feePreview" style="color:var(--gold); font-weight:700">0</span> {{ __('app.fee') }}
                </div>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">✅ {{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)<div>❌ {{ $error }}</div>@endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('agent.transactions.update', $transaction) }}" id="txForm">
                @csrf
                @method('PUT')

                {{-- Toggle Envoi / Retrait --}}
                <div style="margin-bottom:24px;">
                    <div style="font-size:12px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:1.5px; margin-bottom:12px; padding-bottom:8px; border-bottom:2px solid var(--divider);">
                        🔄 {{ __('app.transaction_type_lbl') }}
                    </div>
                    <div class="tx-type-grid">
                        <label style="flex:1; cursor:pointer;">
                            <input type="radio" name="transaction_type" value="send" id="typeSend"
                                   {{ old('transaction_type', $transaction->transaction_type ?? 'send') == 'send' ? 'checked' : '' }}
                                   onchange="switchTxType()" style="display:none;">
                            <div class="tx-type-btn" id="btnSend" style="text-align:center; padding:14px; border:2px solid var(--border); border-radius:12px; background:var(--bg-input); color:var(--text-secondary); font-weight:700; transition:all 0.2s;">
                                <div style="font-size:24px; margin-bottom:4px">📤</div>
                                <div>{{ __('app.type_send') }}</div>
                                <div style="font-size:11px; font-weight:400; opacity:0.75; margin-top:2px">{{ __('app.type_send_desc') }}</div>
                            </div>
                        </label>
                        <label style="flex:1; cursor:pointer;">
                            <input type="radio" name="transaction_type" value="withdrawal" id="typeWithdrawal"
                                   {{ old('transaction_type', $transaction->transaction_type ?? 'send') == 'withdrawal' ? 'checked' : '' }}
                                   onchange="switchTxType()" style="display:none;">
                            <div class="tx-type-btn" id="btnWithdrawal" style="text-align:center; padding:14px; border:2px solid var(--border); border-radius:12px; background:var(--bg-input); color:var(--text-secondary); font-weight:700; transition:all 0.2s;">
                                <div style="font-size:24px; margin-bottom:4px">📥</div>
                                <div>{{ __('app.type_withdrawal') }}</div>
                                <div style="font-size:11px; font-weight:400; opacity:0.75; margin-top:2px">{{ __('app.type_withdrawal_desc') }}</div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Section Expéditeur --}}
                <div id="senderSection" style="margin-bottom:22px;">
                    <div style="font-size:12px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:1.5px; margin-bottom:14px; padding-bottom:8px; border-bottom:2px solid var(--divider);">
                        👤 {{ __('app.sender_info') }}
                    </div>
                    <div class="form-row cols-2">
                        <div class="form-group">
                            <label class="form-label">{{ __('app.sender_name') }} <span class="required" id="senderNameRequired">*</span></label>
                            <input type="text" name="sender_name" id="senderNameInput"
                                   class="form-control @error('sender_name') is-invalid @enderror"
                                   value="{{ old('sender_name', $transaction->sender_name) }}" placeholder="John Doe">
                            @error('sender_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('app.sender_phone') }} <span class="required" id="senderPhoneRequired">*</span></label>
                            <input type="tel" name="sender_phone" id="senderPhoneInput"
                                   class="form-control @error('sender_phone') is-invalid @enderror"
                                   value="{{ old('sender_phone', $transaction->sender_phone) }}" placeholder="+243 xxx xxx xxx">
                            @error('sender_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                {{-- Section Bénéficiaire --}}
                <div id="receiverSection" style="margin-bottom:22px;">
                    <div style="font-size:12px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:1.5px; margin-bottom:14px; padding-bottom:8px; border-bottom:2px solid var(--divider);">
                        <span id="receiverSectionTitle">👥 {{ __('app.receiver_info') }}</span>
                    </div>
                    <div class="form-row cols-2">
                        <div class="form-group">
                            <label class="form-label">{{ __('app.receiver_name') }} <span class="required" id="receiverNameRequired" style="display:none">*</span></label>
                            <input type="text" name="receiver_name" id="receiverNameInput"
                                   class="form-control @error('receiver_name') is-invalid @enderror"
                                   value="{{ old('receiver_name', $transaction->receiver_name) }}" placeholder="Jane Mwamba">
                            @error('receiver_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('app.receiver_phone') }} <span class="required" id="receiverPhoneRequired" style="display:none">*</span></label>
                            <input type="tel" name="receiver_phone" id="receiverPhoneInput"
                                   class="form-control @error('receiver_phone') is-invalid @enderror"
                                   value="{{ old('receiver_phone', $transaction->receiver_phone) }}" placeholder="+260 xxx xxx xxx">
                            @error('receiver_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                {{-- Transfer details --}}
                <div style="margin-bottom:22px;">
                    <div style="font-size:12px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:1.5px; margin-bottom:14px; padding-bottom:8px; border-bottom:2px solid var(--divider);">
                        💰 {{ __('app.transfer_details') }}
                    </div>
                    <div class="form-row cols-2">
                        <div class="form-group">
                            <label class="form-label">{{ __('app.origin_country_lbl') }} <span class="required">*</span></label>
                            <select name="origin_country_id" id="originCountry"
                                    class="form-control @error('origin_country_id') is-invalid @enderror"
                                    required onchange="updateFee()">
                                <option value="">— Select —</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}"
                                            data-fee="{{ $country->default_fee_percentage }}"
                                            {{ old('origin_country_id', $transaction->origin_country_id) == $country->id ? 'selected' : '' }}>
                                        {{ $country->flag_emoji }} {{ $country->name }} ({{ $country->currency_code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('origin_country_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('app.dest_country_lbl') }} <span class="required">*</span></label>
                            <select name="destination_country_id"
                                    class="form-control @error('destination_country_id') is-invalid @enderror" required>
                                <option value="">— Select —</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}"
                                            {{ old('destination_country_id', $transaction->destination_country_id) == $country->id ? 'selected' : '' }}>
                                        {{ $country->flag_emoji }} {{ $country->name }} ({{ $country->currency_code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('destination_country_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="form-row cols-2" style="margin-bottom:0;">
                        <div class="form-group">
                            <label class="form-label" id="amountLabel">{{ __('app.amount_to_send') }} <span class="required">*</span></label>
                            <input type="number" name="amount" id="amount"
                                   class="form-control @error('amount') is-invalid @enderror"
                                   value="{{ old('amount', $transaction->amount) }}" placeholder="0.00"
                                   step="0.01" min="1" required oninput="calculateFee()">
                            @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('app.currency') }} <span class="required">*</span></label>
                            @php
                                $oldCurrency = old('currency', $transaction->currency ?? $transaction->originCountry?->currency_code);
                                $isOther = $oldCurrency && !array_key_exists($oldCurrency, $currencies);
                            @endphp
                            <select name="_currency_select" id="currencySelect"
                                    class="form-control @error('currency') is-invalid @enderror"
                                    onchange="onCurrencyChange()">
                                @foreach($currencies as $code => $name)
                                    <option value="{{ $code }}" {{ (!$isOther && $oldCurrency == $code) ? 'selected' : '' }}>
                                        {{ $code }} — {{ $name }}
                                    </option>
                                @endforeach
                                <option value="__other__" {{ $isOther ? 'selected' : '' }}>
                                    ✏️ {{ app()->getLocale() === 'fr' ? 'Autre — saisir manuellement' : 'Other — enter manually' }}
                                </option>
                            </select>
                            <input type="text" name="currency" id="currencyCustom"
                                   class="form-control" style="margin-top:6px; display:{{ $isOther ? 'block' : 'none' }};"
                                   placeholder="{{ app()->getLocale() === 'fr' ? 'Ex: CAD, CHF, MAD...' : 'e.g. CAD, CHF, MAD...' }}"
                                   value="{{ $isOther ? $oldCurrency : '' }}"
                                   maxlength="10" {{ $isOther ? 'required' : '' }}
                                   oninput="this.value=this.value.toUpperCase(); calculateFee()">
                            @error('currency')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="form-row cols-2">
                        <div class="form-group">
                            <label class="form-label">{{ __('app.fee_percentage') }} <span class="required">*</span></label>
                            <input type="number" name="fee_percentage" id="feePercent"
                                   class="form-control @error('fee_percentage') is-invalid @enderror"
                                   value="{{ old('fee_percentage', $transaction->fee_percentage) }}"
                                   placeholder="3.00" step="0.01" min="0" max="100" required oninput="calculateFee()">
                            @error('fee_percentage')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('app.payment_method') }} <span class="required">*</span></label>
                            <select name="payment_method" class="form-control" required>
                                <option value="cash"         {{ old('payment_method', $transaction->payment_method) == 'cash'         ? 'selected' : '' }}>💵 {{ __('app.cash') }}</option>
                                <option value="mobile_money" {{ old('payment_method', $transaction->payment_method) == 'mobile_money' ? 'selected' : '' }}>📱 {{ __('app.mobile_money') }}</option>
                                <option value="bank"         {{ old('payment_method', $transaction->payment_method) == 'bank'         ? 'selected' : '' }}>🏦 {{ __('app.bank') }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Statut (édition uniquement) --}}
                <div class="form-group" style="margin-bottom:22px;">
                    <label class="form-label">{{ __('app.status') }} <span class="required">*</span></label>
                    <select name="status" class="form-control" required>
                        <option value="completed" {{ old('status', $transaction->status) == 'completed' ? 'selected' : '' }}>✅ {{ __('app.completed') }}</option>
                        <option value="pending"   {{ old('status', $transaction->status) == 'pending'   ? 'selected' : '' }}>⏳ {{ __('app.pending') }}</option>
                        <option value="cancelled" {{ old('status', $transaction->status) == 'cancelled' ? 'selected' : '' }}>❌ {{ __('app.cancelled') }}</option>
                    </select>
                </div>

                {{-- Notes --}}
                <div class="form-group">
                    <label class="form-label">{{ __('app.notes') }}</label>
                    <textarea name="notes" class="form-control" rows="3"
                              placeholder="{{ __('app.notes_placeholder') }}">{{ old('notes', $transaction->notes) }}</textarea>
                </div>

                {{-- Summary box --}}
                <div id="summaryBox" style="display:none; background:var(--bg-input); border:1px solid var(--border); border-radius:12px; padding:20px; margin-bottom:22px; transition:all 0.3s;">
                    <div style="font-size:13px; font-weight:700; color:var(--sky-primary); margin-bottom:14px">📋 {{ __('app.transaction_summary') }}</div>
                    <div class="summary-inner-grid">
                        <div>
                            <div style="font-size:11px; color:var(--text-muted); text-transform:uppercase; letter-spacing:1px" id="sumAmountLabel">{{ __('app.amount_sent') }}</div>
                            <div id="sumAmount" class="amount-display" style="font-size:22px; color:var(--text-heading)">—</div>
                        </div>
                        <div>
                            <div style="font-size:11px; color:var(--text-muted); text-transform:uppercase; letter-spacing:1px">{{ __('app.fee') }}</div>
                            <div id="sumFee" class="amount-display" style="font-size:22px; color:var(--gold)">—</div>
                            <div id="sumFeePercent" style="font-size:12px; color:var(--text-muted)"></div>
                        </div>
                        <div>
                            <div style="font-size:11px; color:var(--text-muted); text-transform:uppercase; letter-spacing:1px">{{ __('app.client_total') }}</div>
                            <div id="sumTotal" class="amount-display" style="font-size:26px; color:var(--sky-primary); font-weight:900">—</div>
                        </div>
                    </div>
                </div>

                <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; padding-top:8px; border-top:1px solid var(--divider); margin-top:8px;">
                    {{-- Côté gauche : annuler sans sauvegarder --}}
                    <a href="{{ route('agent.dashboard') }}"
                       class="btn btn-secondary btn-lg"
                       style="gap:8px;">
                        ✕ {{ __('app.cancel_no_save') }}
                    </a>
                    {{-- Côté droit : sauvegarder les changements --}}
                    <button type="submit" class="btn btn-primary btn-lg" id="submitBtn"
                            style="min-width:200px; justify-content:center;">
                        💾 {{ __('app.save_changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const LABELS = {
    send: {
        receiverTitle: '👥 ' + @json(__('app.receiver_info')),
        amountLabel:   @json(__('app.amount_to_send')),
        sumAmountLbl:  @json(__('app.amount_sent')),
    },
    withdrawal: {
        receiverTitle: '👥 ' + @json(__('app.withdrawer_info')),
        amountLabel:   @json(__('app.amount_to_withdraw')),
        sumAmountLbl:  @json(__('app.amount_withdrawn')),
    }
};

function switchTxType() {
    const isWithdrawal = document.getElementById('typeWithdrawal').checked;
    const type = isWithdrawal ? 'withdrawal' : 'send';
    const lbl = LABELS[type];

    const btnSend = document.getElementById('btnSend');
    const btnWd   = document.getElementById('btnWithdrawal');
    if (isWithdrawal) {
        btnSend.style.background = 'var(--bg-input)';
        btnSend.style.color      = 'var(--text-secondary)';
        btnSend.style.borderColor= 'var(--border)';
        btnWd.style.background   = 'var(--sky-primary)';
        btnWd.style.color        = 'white';
        btnWd.style.borderColor  = 'var(--sky-primary)';
    } else {
        btnSend.style.background  = 'var(--sky-primary)';
        btnSend.style.color       = 'white';
        btnSend.style.borderColor = 'var(--sky-primary)';
        btnWd.style.background    = 'var(--bg-input)';
        btnWd.style.color         = 'var(--text-secondary)';
        btnWd.style.borderColor   = 'var(--border)';
    }

    document.getElementById('senderNameInput').required  = !isWithdrawal;
    document.getElementById('senderPhoneInput').required = !isWithdrawal;
    document.getElementById('senderNameRequired').style.display  = isWithdrawal ? 'none' : '';
    document.getElementById('senderPhoneRequired').style.display = isWithdrawal ? 'none' : '';

    document.getElementById('receiverNameInput').required  = isWithdrawal;
    document.getElementById('receiverPhoneInput').required = isWithdrawal;
    document.getElementById('receiverNameRequired').style.display  = isWithdrawal ? '' : 'none';
    document.getElementById('receiverPhoneRequired').style.display = isWithdrawal ? '' : 'none';

    document.getElementById('receiverSectionTitle').textContent = lbl.receiverTitle;
    document.getElementById('amountLabel').innerHTML = lbl.amountLabel + ' <span class="required">*</span>';
    document.getElementById('sumAmountLabel').textContent = lbl.sumAmountLbl;
}

function onCurrencyChange() {
    const sel    = document.getElementById('currencySelect');
    const custom = document.getElementById('currencyCustom');
    const isOther = sel.value === '__other__';
    custom.style.display = isOther ? 'block' : 'none';
    custom.required      = isOther;
    if (!isOther) { custom.value = sel.value; } // sync hidden field
    calculateFee();
}

function getCurrency() {
    const sel = document.getElementById('currencySelect');
    if (sel.value === '__other__') {
        return document.getElementById('currencyCustom').value.toUpperCase();
    }
    return sel.value;
}

function updateFee() {
    const sel = document.getElementById('originCountry');
    const opt = sel.options[sel.selectedIndex];
    if (opt?.dataset?.fee) { document.getElementById('feePercent').value = opt.dataset.fee; }
    if (opt?.dataset?.currency) {
        const curSel = document.getElementById('currencySelect');
        for (let i = 0; i < curSel.options.length; i++) {
            if (curSel.options[i].value === opt.dataset.currency) {
                curSel.selectedIndex = i;
                onCurrencyChange();
                break;
            }
        }
    }
    calculateFee();
}

function calculateFee() {
    const amount = parseFloat(document.getElementById('amount').value) || 0;
    const feeP   = parseFloat(document.getElementById('feePercent').value) || 0;
    const cur    = getCurrency();
    const fmt    = n => new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2 }).format(n) + (cur ? ' ' + cur : '');
    if (amount > 0) {
        const fee   = Math.round(amount * feeP / 100 * 100) / 100;
        const total = amount + fee;
        document.getElementById('totalPreview').textContent = fmt(total);
        document.getElementById('feePreview').textContent   = fmt(fee);
        document.getElementById('sumAmount').textContent    = fmt(amount);
        document.getElementById('sumFee').textContent       = fmt(fee);
        document.getElementById('sumFeePercent').textContent= feeP + '%';
        document.getElementById('sumTotal').textContent     = fmt(total);
        document.getElementById('summaryBox').style.display = 'block';
    } else {
        document.getElementById('summaryBox').style.display = 'none';
        document.getElementById('totalPreview').textContent = '—';
        document.getElementById('feePreview').textContent   = '0';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    switchTxType();
    onCurrencyChange(); // init hidden field & "Autre" state
    calculateFee();
});
</script>
@endpush
