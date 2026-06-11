@extends('layouts.app')

@section('title', $mode === 'create' ? __('app.add_country') : __('app.edit_country'))
@section('page-title', $mode === 'create' ? __('app.add_country') : __('app.edit_country'))
@section('page-subtitle', $mode === 'create' ? __('app.country_form_subtitle_create') : __('app.country_form_subtitle_edit'))

@push('styles')
<style>
    .field-preview {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 56px; height: 56px;
        border-radius: 14px;
        background: var(--sky-light);
        border: 2px solid rgba(14,165,233,0.2);
        font-size: 30px;
        flex-shrink: 0;
        transition: all 0.2s;
    }
    .form-section {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 20px;
    }
    .form-section-title {
        font-size: 12px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin-bottom: 18px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--divider);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .toggle-wrap {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 18px;
        background: var(--bg-input);
        border-radius: 12px;
        border: 1px solid var(--border);
        cursor: pointer;
        transition: border-color 0.2s;
    }
    .toggle-wrap:hover { border-color: var(--sky-primary); }
    .toggle-track {
        position: relative;
        width: 46px; height: 26px;
        border-radius: 13px;
        transition: background 0.3s;
        flex-shrink: 0;
    }
    .toggle-thumb {
        position: absolute;
        top: 3px; left: 3px;
        width: 20px; height: 20px;
        background: white;
        border-radius: 50%;
        box-shadow: 0 1px 4px rgba(0,0,0,0.2);
        transition: transform 0.3s;
    }
    .country-preview-card {
        background: linear-gradient(135deg, var(--sky-light), rgba(14,165,233,0.05));
        border: 1px solid rgba(14,165,233,0.2);
        border-radius: 16px;
        padding: 20px;
        text-align: center;
        position: sticky;
        top: 20px;
    }
    .country-form-grid {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: 20px;
        align-items: start;
    }
    @media (max-width: 860px) {
        .country-form-grid {
            grid-template-columns: 1fr;
        }
        .country-preview-card { position: static; }
    }
</style>
@endpush

@section('content')

@if($errors->any())
    <div class="alert alert-danger" style="margin-bottom:20px;">
        @foreach($errors->all() as $e)
            <div style="display:flex; align-items:center; gap:6px;">❌ {{ $e }}</div>
        @endforeach
    </div>
@endif

<form method="POST"
      action="{{ $mode === 'create' ? route('admin.countries.store') : route('admin.countries.update', $country) }}"
      id="countryForm">
    @csrf
    @if($mode === 'edit') @method('PUT') @endif

    <div class="country-form-grid">

        {{-- LEFT: form fields --}}
        <div>

            {{-- Section 1: Identity --}}
            <div class="form-section animate-on-scroll">
                <div class="form-section-title">🗺️ {{ app()->getLocale() === 'fr' ? 'Identité du pays' : 'Country identity' }}</div>

                <div style="display:flex; gap:16px; align-items:flex-start; margin-bottom:16px;">
                    {{-- Flag preview --}}
                    <div style="padding-top:22px;">
                        <div class="field-preview" id="flagPreview">
                            {{ old('flag_emoji', $country->flag_emoji) ?: '🏳' }}
                        </div>
                    </div>
                    {{-- Name + flag input --}}
                    <div style="flex:1;">
                        <div class="form-row cols-2">
                            <div class="form-group">
                                <label class="form-label">{{ __('app.country') }} <span class="required">*</span></label>
                                <input type="text" name="name" id="countryName"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $country->name) }}"
                                       placeholder="{{ app()->getLocale() === 'fr' ? 'Ex: Cameroun' : 'E.g. Cameroon' }}"
                                       required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">{{ __('app.flag_emoji') }} <span class="required">*</span></label>
                                <input type="text" name="flag_emoji" id="flagInput"
                                       class="form-control @error('flag_emoji') is-invalid @enderror"
                                       value="{{ old('flag_emoji', $country->flag_emoji) }}"
                                       placeholder="🇨🇲" maxlength="10"
                                       style="font-size:20px; text-align:center; letter-spacing:3px;"
                                       oninput="updateFlagPreview(this.value)" required>
                                <div style="font-size:10px;color:var(--text-muted);margin-top:3px;">
                                    {{ app()->getLocale() === 'fr' ? 'Copier-coller l\'emoji' : 'Copy-paste the flag emoji' }}
                                </div>
                                @error('flag_emoji')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-row cols-2">
                    <div class="form-group">
                        <label class="form-label">
                            {{ __('app.country_code') }} <span class="required">*</span>
                            <span style="font-size:10px;color:var(--text-muted);font-weight:400;"> ISO 3166-1 alpha-2</span>
                        </label>
                        <input type="text" name="code" id="countryCode"
                               class="form-control @error('code') is-invalid @enderror"
                               value="{{ old('code', $country->code) }}"
                               placeholder="CM" maxlength="2"
                               style="text-transform:uppercase; letter-spacing:4px; font-weight:800; font-size:18px; text-align:center;"
                               oninput="this.value=this.value.toUpperCase()" required>
                        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __('app.phone_code') }} <span class="required">*</span></label>
                        <input type="text" name="phone_code" id="phoneCode"
                               class="form-control @error('phone_code') is-invalid @enderror"
                               value="{{ old('phone_code', $country->phone_code) }}"
                               placeholder="+237" maxlength="10" required>
                        @error('phone_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- Section 2: Currency --}}
            <div class="form-section animate-on-scroll">
                <div class="form-section-title">💱 {{ app()->getLocale() === 'fr' ? 'Devise' : 'Currency' }}</div>

                <div class="form-row cols-2">
                    <div class="form-group">
                        <label class="form-label">
                            {{ __('app.currency_code_lbl') }} <span class="required">*</span>
                            <span style="font-size:10px;color:var(--text-muted);font-weight:400;"> ISO 4217</span>
                        </label>
                        <input type="text" name="currency_code" id="currencyCode"
                               class="form-control @error('currency_code') is-invalid @enderror"
                               value="{{ old('currency_code', $country->currency_code) }}"
                               placeholder="XAF" maxlength="5"
                               style="text-transform:uppercase; letter-spacing:3px; font-weight:700; font-size:16px; text-align:center;"
                               oninput="this.value=this.value.toUpperCase(); updatePreview()" required>
                        @error('currency_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">{{ __('app.currency_name_lbl') }} <span class="required">*</span></label>
                        <input type="text" name="currency_name" id="currencyName"
                               class="form-control @error('currency_name') is-invalid @enderror"
                               value="{{ old('currency_name', $country->currency_name) }}"
                               placeholder="{{ app()->getLocale() === 'fr' ? 'Franc CFA BEAC' : 'CFA Franc BEAC' }}"
                               oninput="updatePreview()" required>
                        @error('currency_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- Section 3: Fees & Status --}}
            <div class="form-section animate-on-scroll">
                <div class="form-section-title">⚙️ {{ app()->getLocale() === 'fr' ? 'Paramètres' : 'Settings' }}</div>

                <div class="form-group" style="max-width:220px; margin-bottom:20px;">
                    <label class="form-label">{{ __('app.default_fee') }} (%) <span class="required">*</span></label>
                    <div style="position:relative;">
                        <input type="number" name="default_fee_percentage"
                               class="form-control @error('default_fee_percentage') is-invalid @enderror"
                               value="{{ old('default_fee_percentage', $country->default_fee_percentage ?? 3.0) }}"
                               placeholder="3.00" step="0.01" min="0" max="100"
                               style="padding-right:36px;" required>
                        <span style="position:absolute; right:12px; top:50%; transform:translateY(-50%); color:var(--sky-primary); font-weight:700;">%</span>
                    </div>
                    @error('default_fee_percentage')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Active toggle --}}
                <div class="toggle-wrap" id="toggleWrap" onclick="toggleActive()">
                    <input type="hidden" name="is_active" value="0" id="isActiveHidden">
                    <input type="checkbox" name="is_active" value="1" id="isActiveCheck"
                           {{ old('is_active', $country->is_active ?? true) ? 'checked' : '' }}
                           style="display:none;">
                    <div class="toggle-track" id="toggleTrack"
                         style="background: {{ old('is_active', $country->is_active ?? true) ? 'var(--sky-primary)' : 'var(--border)' }};">
                        <div class="toggle-thumb" id="toggleThumb"
                             style="transform: {{ old('is_active', $country->is_active ?? true) ? 'translateX(20px)' : 'translateX(0)' }};"></div>
                    </div>
                    <div>
                        <div style="font-weight:700; font-size:14px; color:var(--text-primary);" id="toggleLabel">
                            {{ old('is_active', $country->is_active ?? true) ? __('app.active') : __('app.disabled') }}
                        </div>
                        <div style="font-size:11.5px; color:var(--text-muted); margin-top:1px;">
                            {{ __('app.country_active_hint') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div style="display:flex; gap:12px; justify-content:flex-end;">
                <a href="{{ route('admin.countries.index') }}" class="btn btn-secondary btn-lg">
                    ← {{ __('app.cancel') }}
                </a>
                <button type="submit" class="btn btn-primary btn-lg">
                    {{ $mode === 'create' ? '✅ ' . __('app.add_country') : '💾 ' . __('app.save_changes') }}
                </button>
            </div>
        </div>

        {{-- RIGHT: live preview card --}}
        <div>
            <div class="country-preview-card animate-on-scroll">
                <div style="font-size:10px; font-weight:700; color:var(--sky-primary); text-transform:uppercase; letter-spacing:1.5px; margin-bottom:16px;">
                    {{ app()->getLocale() === 'fr' ? 'Aperçu' : 'Preview' }}
                </div>

                <div style="width:64px; height:64px; border-radius:16px; background:white;
                            border:2px solid rgba(14,165,233,0.2); display:flex; align-items:center;
                            justify-content:center; font-size:36px; margin:0 auto 12px;">
                    <span id="previewFlag">{{ old('flag_emoji', $country->flag_emoji) ?: '🏳' }}</span>
                </div>

                <div style="font-size:18px; font-weight:800; color:var(--text-heading); margin-bottom:4px;" id="previewName">
                    {{ old('name', $country->name) ?: '—' }}
                </div>
                <div style="margin-bottom:16px;">
                    <span style="font-size:11px; font-weight:700; letter-spacing:2px; padding:3px 10px; border-radius:6px;
                                 background:var(--sky-light); color:var(--sky-primary);" id="previewCode">
                        {{ old('code', $country->code) ?: '???' }}
                    </span>
                </div>

                <div style="display:flex; flex-direction:column; gap:8px; font-size:12px; text-align:left; padding:12px; background:var(--bg-input); border-radius:10px;">
                    <div style="display:flex; justify-content:space-between;">
                        <span style="color:var(--text-muted);">{{ __('app.currency') }}</span>
                        <span style="font-weight:700;" id="previewCurrency">
                            {{ old('currency_code', $country->currency_code) ?: '—' }}
                        </span>
                    </div>
                    <div style="display:flex; justify-content:space-between;">
                        <span style="color:var(--text-muted);">{{ __('app.phone_code') }}</span>
                        <span style="font-weight:600;" id="previewPhone">
                            {{ old('phone_code', $country->phone_code) ?: '—' }}
                        </span>
                    </div>
                </div>

                <div style="margin-top:12px; font-size:11px; color:var(--text-muted); font-style:italic; line-height:1.5;">
                    {{ __('app.country_active_hint') }}
                </div>
            </div>
        </div>

    </div>
</form>

<script>
function updateFlagPreview(val) {
    document.getElementById('flagPreview').textContent = val || '🏳';
    document.getElementById('previewFlag').textContent = val || '🏳';
}

function updatePreview() {
    const name  = document.getElementById('countryName')?.value;
    const code  = document.getElementById('countryCode')?.value;
    const cur   = document.getElementById('currencyCode')?.value;
    const phone = document.getElementById('phoneCode')?.value;
    if (name)  document.getElementById('previewName').textContent  = name;
    if (code)  document.getElementById('previewCode').textContent  = code || '???';
    if (cur)   document.getElementById('previewCurrency').textContent = cur;
    if (phone) document.getElementById('previewPhone').textContent = phone;
}

// Wire all inputs to live preview
['countryName','countryCode','currencyCode','phoneCode'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('input', updatePreview);
});

function toggleActive() {
    const cb    = document.getElementById('isActiveCheck');
    const track = document.getElementById('toggleTrack');
    const thumb = document.getElementById('toggleThumb');
    const label = document.getElementById('toggleLabel');
    cb.checked = !cb.checked;
    track.style.background = cb.checked ? 'var(--sky-primary)' : 'var(--border)';
    thumb.style.transform  = cb.checked ? 'translateX(20px)' : 'translateX(0)';
    label.textContent = cb.checked ? '{{ __('app.active') }}' : '{{ __('app.disabled') }}';
    document.getElementById('isActiveHidden').disabled = cb.checked;
}
</script>
@endsection
