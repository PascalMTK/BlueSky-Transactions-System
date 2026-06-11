@extends('layouts.app')

@section('title', 'My Profile')
@section('page-title', 'My Profile')
@section('page-subtitle', 'Manage your personal information and settings')

@push('styles')
<style>
/* Profile specific styles */
.profile-hero {
    background: linear-gradient(135deg, var(--sky-primary), var(--sky-deeper));
    border-radius: var(--radius);
    padding: 32px 36px;
    display: flex;
    align-items: center;
    gap: 28px;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
    animation: fadeInUp 0.5s ease;
}
.profile-hero::before {
    content: '';
    position: absolute; top: -60px; right: -60px;
    width: 220px; height: 220px; border-radius: 50%;
    background: rgba(255,255,255,0.05);
}

.profile-avatar-wrap {
    position: relative; flex-shrink: 0;
}
.profile-avatar-img {
    width: 100px; height: 100px; border-radius: 50%;
    object-fit: cover;
    border: 4px solid rgba(255,255,255,0.35);
    box-shadow: 0 6px 24px rgba(0,0,0,0.3);
}
.profile-avatar-placeholder {
    width: 100px; height: 100px; border-radius: 50%;
    background: rgba(255,255,255,0.2);
    display: flex; align-items: center; justify-content: center;
    font-size: 38px; font-weight: 800; color: white;
    border: 4px solid rgba(255,255,255,0.35);
    box-shadow: 0 6px 24px rgba(0,0,0,0.3);
    backdrop-filter: blur(4px);
}
.profile-avatar-badge {
    position: absolute; bottom: 2px; right: 2px;
    width: 28px; height: 28px; border-radius: 50%;
    background: var(--success); border: 3px solid white;
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; cursor: pointer;
    transition: transform 0.2s;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
}
.profile-avatar-badge:hover { transform: scale(1.15); }

/* Photo upload in card */
.photo-drop-zone {
    border: 2px dashed var(--border);
    border-radius: 14px;
    padding: 32px 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: var(--bg-input);
    position: relative;
}
.photo-drop-zone:hover, .photo-drop-zone.over {
    border-color: var(--sky-secondary);
    background: rgba(14,165,233,0.06);
}
.photo-drop-zone input[type=file] {
    position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%;
}
.current-photo {
    width: 90px; height: 90px; border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--sky-secondary);
    box-shadow: 0 4px 16px rgba(14,165,233,0.25);
    margin: 0 auto 12px;
    display: block;
}
.current-initials {
    width: 90px; height: 90px; border-radius: 50%;
    background: linear-gradient(135deg, var(--sky-primary), var(--sky-secondary));
    display: flex; align-items: center; justify-content: center;
    font-size: 30px; font-weight: 800; color: white;
    margin: 0 auto 12px;
}

/* Tabs */
.profile-tabs {
    display: flex; gap: 4px;
    background: var(--bg-input); border-radius: 12px;
    padding: 5px; margin-bottom: 22px;
    border: 1px solid var(--border);
}
.profile-tab {
    flex: 1; padding: 10px 14px; border-radius: 9px;
    font-size: 13px; font-weight: 600; cursor: pointer;
    border: none; background: transparent;
    color: var(--text-muted); transition: all 0.2s;
    display: flex; align-items: center; justify-content: center; gap: 6px;
    font-family: inherit;
}
.profile-tab:hover { color: var(--text-primary); background: var(--bg-card); }
.profile-tab.active {
    background: var(--bg-card);
    color: var(--sky-primary);
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.tab-panel { display: none; }
.tab-panel.active { display: block; animation: fadeInUp 0.3s ease; }
</style>
@endpush

@section('content')

@php
    use Illuminate\Support\Facades\Storage;
    $photoUrl = $user->profile_photo ? Storage::url($user->profile_photo) : null;
    $initials = strtoupper(substr($user->name, 0, 2));
@endphp

{{-- ===== PROFILE HERO ===== --}}
<div class="profile-hero">
    <div class="profile-avatar-wrap">
        @if($photoUrl)
            <img src="{{ $photoUrl }}" alt="Profile" class="profile-avatar-img" id="heroPhoto">
        @else
            <div class="profile-avatar-placeholder" id="heroPhoto">{{ $initials }}</div>
        @endif
        <label for="heroPhotoInput" class="profile-avatar-badge" title="Change photo">📷</label>
        <input type="file" id="heroPhotoInput" accept="image/*" style="display:none" onchange="quickPhotoUpload(this)">
    </div>

    <div style="flex:1; min-width:0; position:relative; z-index:1;">
        <div style="font-size:26px; font-weight:900; color:white; margin-bottom:4px;">{{ $user->name }}</div>
        <div style="display:flex; flex-wrap:wrap; gap:10px; margin-top:6px;">
            <span style="background:rgba(255,255,255,0.15); backdrop-filter:blur(8px); padding:4px 12px; border-radius:20px; font-size:12px; color:rgba(255,255,255,0.9); border:1px solid rgba(255,255,255,0.2);">
                {{ $user->isAdmin() ? '🛡️ Administrator' : '🏢 Agent' }}
            </span>
            @if($user->country)
                <span style="background:rgba(255,255,255,0.15); backdrop-filter:blur(8px); padding:4px 12px; border-radius:20px; font-size:12px; color:rgba(255,255,255,0.9); border:1px solid rgba(255,255,255,0.2);">
                    {{ $user->country->flag_emoji }} {{ $user->country->name }}
                </span>
            @endif
            <span style="background:rgba(255,255,255,0.15); backdrop-filter:blur(8px); padding:4px 12px; border-radius:20px; font-size:12px; color:rgba(255,255,255,0.9); border:1px solid rgba(255,255,255,0.2);">
                🔑 {{ $user->agent_code }}
            </span>
            <span style="background:rgba({{ $user->status === 'active' ? '16,185,129' : '239,68,68' }},0.25); backdrop-filter:blur(8px); padding:4px 12px; border-radius:20px; font-size:12px; color:rgba(255,255,255,0.95); border:1px solid rgba(255,255,255,0.15);">
                {{ $user->status === 'active' ? '✅ Active' : '⏳ ' . ucfirst($user->status) }}
            </span>
        </div>
        <div style="font-size:13px; color:rgba(255,255,255,0.6); margin-top:10px;">
            📧 {{ $user->email }}
            @if($user->phone) &nbsp;|&nbsp; 📞 {{ $user->phone }} @endif
        </div>
    </div>

    <div style="text-align:right; position:relative; z-index:1;">
        <div style="font-size:11px; color:rgba(255,255,255,0.5); text-transform:uppercase; letter-spacing:1px; margin-bottom:4px;">Member since</div>
        <div style="font-size:18px; font-weight:800; color:white;">{{ $user->created_at->format('M Y') }}</div>
        <div style="font-size:12px; color:rgba(255,255,255,0.5); margin-top:2px;">{{ $user->created_at->diffForHumans() }}</div>
    </div>
</div>

{{-- ===== TABS ===== --}}
<div class="profile-tabs">
    <button class="profile-tab active" onclick="switchTab('info', this)">
        ✏️ Edit Info
    </button>
    <button class="profile-tab" onclick="switchTab('photo', this)">
        📸 Change Photo
    </button>
    <button class="profile-tab" onclick="switchTab('password', this)">
        🔒 Change Password
    </button>
</div>

{{-- ===== TAB: EDIT INFO ===== --}}
<div id="tab-info" class="tab-panel active">
    <div class="card animate-on-scroll">
        <div class="card-header">
            <div>
                <div class="card-title">✏️ Personal Information</div>
                <div class="card-subtitle">Update your name, country and contact details</div>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf @method('PUT')

                <div class="form-row cols-2">
                    <div class="form-group">
                        <label class="form-label">Full Name <span class="required">*</span></label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email address</label>
                        <input type="email" class="form-control"
                               value="{{ $user->email }}" disabled
                               style="opacity:0.6; cursor:not-allowed;">
                        <div class="form-text">Email cannot be changed. Contact an administrator.</div>
                    </div>
                </div>

                <div class="form-row cols-2">
                    <div class="form-group">
                        <label class="form-label">Phone Number <span class="required">*</span></label>
                        <input type="tel" name="phone"
                               class="form-control @error('phone') is-invalid @enderror"
                               value="{{ old('phone', $user->phone) }}"
                               placeholder="+243 xxx xxx xxx" required>
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Operating Country <span class="required">*</span></label>
                        <select name="country_id"
                                class="form-control @error('country_id') is-invalid @enderror" required>
                            <option value="">— Select country —</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}"
                                        {{ old('country_id', $user->country_id) == $country->id ? 'selected' : '' }}>
                                    {{ $country->flag_emoji }} {{ $country->name }}
                                    ({{ $country->currency_code }} — {{ $country->phone_code }})
                                </option>
                            @endforeach
                        </select>
                        @error('country_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-row cols-2">
                    <div class="form-group">
                        <label class="form-label">Physical Address</label>
                        <input type="text" name="address"
                               class="form-control"
                               value="{{ old('address', $user->address) }}"
                               placeholder="Street, City, Province">
                    </div>
                    <div class="form-group">
                        <label class="form-label">ID Number</label>
                        <input type="text" name="id_number"
                               class="form-control"
                               value="{{ old('id_number', $user->id_number) }}"
                               placeholder="National ID, Passport...">
                    </div>
                </div>

                {{-- Country details preview --}}
                <div id="countryPreview" style="display:{{ $user->country ? 'block' : 'none' }}; background:var(--bg-input); border:1px solid var(--border); border-radius:12px; padding:16px 20px; margin-bottom:20px; transition:all 0.3s;">
                    <div style="font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:1.5px; margin-bottom:12px;">Selected Country Details</div>
                    <div style="display:flex; flex-wrap:wrap; gap:16px;">
                        <div style="text-align:center;">
                            <div id="previewFlag" style="font-size:36px; line-height:1;">{{ $user->country?->flag_emoji ?? '' }}</div>
                            <div id="previewName" style="font-size:13px; font-weight:700; color:var(--text-heading); margin-top:4px;">{{ $user->country?->name ?? '' }}</div>
                        </div>
                        <div style="display:flex; flex-direction:column; gap:8px; flex:1;">
                            <div style="display:flex; gap:20px; flex-wrap:wrap;">
                                <div>
                                    <div style="font-size:10px; color:var(--text-muted); text-transform:uppercase; letter-spacing:1px;">Currency</div>
                                    <div id="previewCurrency" style="font-weight:700; color:var(--sky-primary);">{{ $user->country?->currency_code ?? '' }} — {{ $user->country?->currency_name ?? '' }}</div>
                                </div>
                                <div>
                                    <div style="font-size:10px; color:var(--text-muted); text-transform:uppercase; letter-spacing:1px;">Phone Code</div>
                                    <div id="previewPhone" style="font-weight:700; color:var(--success);">{{ $user->country?->phone_code ?? '' }}</div>
                                </div>
                                <div>
                                    <div style="font-size:10px; color:var(--text-muted); text-transform:uppercase; letter-spacing:1px;">Default Fee</div>
                                    <div id="previewFee" style="font-weight:700; color:var(--gold);">{{ $user->country?->default_fee_percentage ?? '' }}%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="display:flex; justify-content:flex-end; gap:12px;">
                    <a href="{{ route('agent.dashboard') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-lg">
                        ✅ Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== TAB: CHANGE PHOTO ===== --}}
<div id="tab-photo" class="tab-panel">
    <div class="card animate-on-scroll" style="max-width:600px; margin:0 auto;">
        <div class="card-header">
            <div>
                <div class="card-title">📸 Profile Photo</div>
                <div class="card-subtitle">Upload a new profile picture</div>
            </div>
        </div>
        <div class="card-body">
            {{-- Current photo --}}
            <div style="text-align:center; margin-bottom:24px;">
                <div style="font-size:12px; color:var(--text-muted); text-transform:uppercase; letter-spacing:1px; margin-bottom:12px;">Current Photo</div>
                @if($photoUrl)
                    <img src="{{ $photoUrl }}" alt="Current"
                         style="width:110px; height:110px; border-radius:50%; object-fit:cover; border:4px solid var(--sky-secondary); box-shadow:0 6px 24px rgba(14,165,233,0.25);"
                         id="currentPhotoDisplay">
                @else
                    <div style="width:110px; height:110px; border-radius:50%; background:linear-gradient(135deg,var(--sky-primary),var(--sky-secondary)); display:flex; align-items:center; justify-content:center; font-size:38px; font-weight:800; color:white; border:4px solid var(--sky-secondary); box-shadow:0 6px 24px rgba(14,165,233,0.25); margin:0 auto;" id="currentPhotoDisplay">
                        {{ $initials }}
                    </div>
                @endif
            </div>

            <form method="POST" action="{{ route('profile.photo') }}" enctype="multipart/form-data" id="photoForm">
                @csrf

                <div class="photo-drop-zone" id="photoDropZone"
                     ondragover="photoDragOver(event)"
                     ondragleave="photoDragLeave(event)"
                     ondrop="photoDrop(event)">

                    <input type="file" name="photo" id="photoFileInput"
                           accept="image/jpeg,image/png,image/jpg,image/webp"
                           onchange="previewNewPhoto(this)">

                    <img id="newPhotoPreview" src="" alt="Preview"
                         style="display:none; width:100px; height:100px; border-radius:50%; object-fit:cover; border:3px solid var(--sky-secondary); margin:0 auto 14px; box-shadow:0 4px 16px rgba(14,165,233,0.25);">

                    <div style="font-size:36px; margin-bottom:10px;" id="dropIcon">📷</div>
                    <div style="font-weight:700; font-size:15px; color:var(--text-primary);">
                        Click to select or drag & drop
                    </div>
                    <div style="font-size:12px; color:var(--text-muted); margin-top:5px;">
                        JPEG, PNG, WebP — max 3 MB
                    </div>
                    <div id="newPhotoName" style="font-size:13px; color:var(--sky-primary); font-weight:700; margin-top:8px; display:none;"></div>
                </div>

                @error('photo')<div class="invalid-feedback" style="display:block; margin-top:8px;">{{ $message }}</div>@enderror

                <div style="margin-top:20px; display:flex; justify-content:center;">
                    <button type="submit" class="btn btn-primary btn-lg" id="uploadBtn" style="display:none;">
                        📤 Upload Photo
                    </button>
                </div>
            </form>

            <div style="margin-top:20px; padding:12px 16px; background:var(--bg-input); border-radius:10px; border:1px solid var(--border); font-size:12.5px; color:var(--text-secondary);">
                💡 <strong>Tip:</strong> Use a clear face photo with good lighting. Square format works best (e.g., 400×400 px).
            </div>
        </div>
    </div>
</div>

{{-- ===== TAB: CHANGE PASSWORD ===== --}}
<div id="tab-password" class="tab-panel">
    <div class="card animate-on-scroll" style="max-width:500px; margin:0 auto;">
        <div class="card-header">
            <div>
                <div class="card-title">🔒 Change Password</div>
                <div class="card-subtitle">Set a new secure password</div>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('profile.password') }}">
                @csrf @method('PUT')

                <div class="form-group">
                    <label class="form-label">Current Password <span class="required">*</span></label>
                    <div style="position:relative;">
                        <input type="password" name="current_password" id="currentPwd"
                               class="form-control @error('current_password') is-invalid @enderror"
                               placeholder="Your current password" required>
                        <button type="button" onclick="toggleField('currentPwd','eyeC')"
                                style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:16px;color:var(--text-muted);" id="eyeC">👁️</button>
                    </div>
                    @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">New Password <span class="required">*</span></label>
                    <div style="position:relative;">
                        <input type="password" name="password" id="newPwd"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Min. 8 characters" required
                               oninput="checkStrength(this.value)">
                        <button type="button" onclick="toggleField('newPwd','eyeN')"
                                style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:16px;color:var(--text-muted);" id="eyeN">👁️</button>
                    </div>
                    <!-- Password strength bar -->
                    <div style="margin-top:8px;">
                        <div class="progress">
                            <div id="strengthBar" class="progress-bar" style="width:0%; transition:width 0.4s, background 0.4s;"></div>
                        </div>
                        <div id="strengthLabel" style="font-size:11px; color:var(--text-muted); margin-top:4px;"></div>
                    </div>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Confirm New Password <span class="required">*</span></label>
                    <input type="password" name="password_confirmation"
                           class="form-control" placeholder="Repeat new password" required>
                </div>

                <div style="padding:12px 16px; background:var(--bg-input); border-radius:10px; border:1px solid var(--border); margin-bottom:20px; font-size:12.5px; color:var(--text-secondary);">
                    🛡️ Use at least 8 characters with a mix of letters, numbers and symbols.
                </div>

                <button type="submit" class="btn btn-primary btn-lg" style="width:100%; justify-content:center;">
                    🔒 Update Password
                </button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Country data for preview
const countriesData = @json($countries->keyBy('id'));

document.querySelector('select[name="country_id"]')?.addEventListener('change', function() {
    const id = this.value;
    const c  = countriesData[id];
    if (c) {
        document.getElementById('previewFlag').textContent    = c.flag_emoji;
        document.getElementById('previewName').textContent    = c.name;
        document.getElementById('previewCurrency').textContent= c.currency_code + ' — ' + c.currency_name;
        document.getElementById('previewPhone').textContent   = c.phone_code;
        document.getElementById('previewFee').textContent     = c.default_fee_percentage + '%';
        document.getElementById('countryPreview').style.display = 'block';
    } else {
        document.getElementById('countryPreview').style.display = 'none';
    }
});

// Tab switching
function switchTab(tab, btn) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.profile-tab').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
    btn.classList.add('active');
}

// Password strength
function checkStrength(val) {
    let score = 0;
    if (val.length >= 8) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    const bar    = document.getElementById('strengthBar');
    const label  = document.getElementById('strengthLabel');
    const levels = [
        { pct: 0,   color: 'transparent', text: '' },
        { pct: 25,  color: '#EF4444',     text: '⚠️ Too weak' },
        { pct: 50,  color: '#F59E0B',     text: '🟡 Fair' },
        { pct: 75,  color: '#0EA5E9',     text: '🔵 Good' },
        { pct: 100, color: '#10B981',     text: '✅ Strong' },
    ];
    const l = levels[score] || levels[0];
    bar.style.width      = l.pct + '%';
    bar.style.background = l.color;
    label.textContent    = l.text;
}

// Show/hide password
function toggleField(id, eyeId) {
    const f = document.getElementById(id);
    const e = document.getElementById(eyeId);
    f.type = f.type === 'password' ? 'text' : 'password';
    e.textContent = f.type === 'text' ? '🙈' : '👁️';
}

// Photo preview (new upload)
function previewNewPhoto(input) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = (e) => {
        const preview = document.getElementById('newPhotoPreview');
        preview.src = e.target.result;
        preview.style.display = 'block';
        document.getElementById('dropIcon').style.display = 'none';
        document.getElementById('newPhotoName').textContent = '📎 ' + file.name;
        document.getElementById('newPhotoName').style.display = 'block';
        document.getElementById('uploadBtn').style.display = 'flex';
        document.getElementById('photoDropZone').style.borderColor = 'var(--sky-secondary)';
    };
    reader.readAsDataURL(file);
}
function photoDragOver(e)  { e.preventDefault(); document.getElementById('photoDropZone').classList.add('over'); }
function photoDragLeave(e) { document.getElementById('photoDropZone').classList.remove('over'); }
function photoDrop(e) {
    e.preventDefault();
    document.getElementById('photoDropZone').classList.remove('over');
    const file = e.dataTransfer.files[0];
    if (file && file.type.startsWith('image/')) {
        const inp = document.getElementById('photoFileInput');
        const dt  = new DataTransfer();
        dt.items.add(file);
        inp.files = dt.files;
        previewNewPhoto(inp);
    }
}

// Quick photo upload from hero badge
function quickPhotoUpload(input) {
    const file = input.files[0];
    if (!file) return;
    const form = new FormData();
    form.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    form.append('photo', file);
    fetch('{{ route("profile.photo") }}', { method: 'POST', body: form })
        .then(r => r.redirected ? window.location.reload() : r.text())
        .then(() => window.location.reload());
}
</script>
@endpush
