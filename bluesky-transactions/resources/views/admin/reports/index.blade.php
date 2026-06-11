@extends('layouts.app')

@section('title', __('app.reports_admin_title'))
@section('page-title', __('app.reports_admin_title'))
@section('page-subtitle', __('app.reports_admin_subtitle'))

@push('styles')
<style>
.report-card {
    border-bottom: 1px solid var(--divider);
    padding: 20px 22px;
    transition: background 0.15s;
}
.report-card:last-child { border-bottom: none; }
.report-card.unread { background: rgba(2,132,199,0.03); }

.report-bubble {
    font-size: 13px;
    color: var(--text-secondary);
    background: var(--bg-input);
    padding: 11px 15px;
    border-radius: 10px;
    border-left: 3px solid var(--sky-primary);
    white-space: pre-line;
    line-height: 1.6;
}

.reply-bubble {
    font-size: 13px;
    color: var(--text-secondary);
    background: rgba(16,185,129,0.07);
    padding: 11px 15px;
    border-radius: 10px;
    border-left: 3px solid var(--success);
    white-space: pre-line;
    line-height: 1.6;
}

.reply-form-wrap {
    margin-top: 12px;
    border-top: 1px dashed var(--divider);
    padding-top: 12px;
}

.reply-toggle-btn {
    display: inline-flex; align-items: center; gap: 6px;
    background: var(--bg-card); border: 1px solid var(--border);
    border-radius: 8px; padding: 6px 14px;
    font-size: 12px; font-weight: 700; color: var(--sky-primary);
    cursor: pointer; transition: all 0.2s;
}
.reply-toggle-btn:hover { border-color: var(--sky-secondary); background: rgba(14,165,233,0.07); }

.reply-form { display: none; margin-top: 10px; }
.reply-form.open { display: block; }
.reply-form textarea {
    width: 100%; resize: vertical;
    border-radius: 9px; padding: 10px 13px;
    font-size: 13px; line-height: 1.55;
    border: 1px solid var(--border);
    background: var(--bg-input);
    color: var(--text-primary);
    font-family: inherit;
    min-height: 90px;
    outline: none;
    transition: border 0.2s;
}
.reply-form textarea:focus { border-color: var(--sky-secondary); }
.reply-edit-btn {
    font-size: 11px; color: var(--sky-primary); background: none;
    border: none; cursor: pointer; font-weight: 600; text-decoration: underline;
    padding: 0; margin-left: 8px;
}
</style>
@endpush

@section('content')

<div class="card" style="padding:0; overflow:hidden;">
    <div class="card-header" style="padding:14px 22px;">
        <div>
            <div class="card-title">📨 {{ __('app.reports_admin_title') }}</div>
            <div class="card-subtitle">{{ $reports->total() }} {{ __('app.report_results') }}</div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin:0 22px 14px; font-size:13px;">✅ {{ session('success') }}</div>
    @endif

    @if($reports->isEmpty())
        <div style="padding:60px; text-align:center; color:var(--text-muted);">
            📭 {{ __('app.report_none_yet') }}
        </div>
    @else
        @foreach($reports as $report)
        <div class="report-card {{ $report->status === 'unread' ? 'unread' : '' }}">
            <div style="display:flex; align-items:flex-start; gap:14px;">

                {{-- Status dot --}}
                <div style="padding-top:4px; flex-shrink:0;">
                    @if($report->admin_reply)
                        <span title="{{ __('app.report_replied') }}" style="display:inline-block; width:10px; height:10px; border-radius:50%; background:var(--success); box-shadow:0 0 6px rgba(16,185,129,0.5);"></span>
                    @elseif($report->status === 'unread')
                        <span title="{{ __('app.report_unread') }}" style="display:inline-block; width:10px; height:10px; border-radius:50%; background:#f59e0b; box-shadow:0 0 6px rgba(245,158,11,0.6);"></span>
                    @else
                        <span title="{{ __('app.report_read') }}" style="display:inline-block; width:10px; height:10px; border-radius:50%; background:#94a3b8;"></span>
                    @endif
                </div>

                {{-- Content --}}
                <div style="flex:1; min-width:0;">

                    {{-- Header --}}
                    <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:5px;">
                        <span style="font-weight:700; font-size:14px;">{{ $report->subject }}</span>
                        @if($report->admin_reply)
                            <span style="font-size:10px; font-weight:700; background:rgba(16,185,129,0.12); color:var(--success); padding:2px 8px; border-radius:20px; border:1px solid rgba(16,185,129,0.25);">
                                ✅ {{ __('app.report_replied') }}
                            </span>
                        @elseif($report->status === 'unread')
                            <span style="font-size:10px; font-weight:700; background:#fef3c7; color:#92400e; padding:2px 8px; border-radius:20px;">
                                {{ __('app.report_unread') }}
                            </span>
                        @endif
                    </div>

                    {{-- Meta --}}
                    <div style="font-size:12px; color:var(--text-muted); margin-bottom:10px; display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                        <strong style="color:var(--text-secondary);">{{ $report->agent?->name }}</strong>
                        <span>·</span>
                        <span>{{ $report->agent?->agent_code }}</span>
                        @if($report->agent?->country)
                            <span>· {{ $report->agent->country->flag_emoji }}</span>
                        @endif
                        <span>·</span>
                        <span>{{ $report->created_at->format('d/m/Y H:i') }}</span>
                    </div>

                    {{-- Agent message --}}
                    <div class="report-bubble">{{ $report->message }}</div>

                    {{-- Existing reply --}}
                    @if($report->admin_reply)
                        <div style="margin-top:12px;">
                            <div style="font-size:11px; font-weight:700; color:var(--success); text-transform:uppercase; letter-spacing:1px; margin-bottom:6px;">
                                🛡️ {{ __('app.report_reply_label') }}
                                @if($report->replied_at)
                                    <span style="font-weight:400; color:var(--text-muted); text-transform:none; letter-spacing:0;"> — {{ $report->replied_at->format('d/m/Y H:i') }}</span>
                                @endif
                                <button class="reply-edit-btn" onclick="toggleReplyForm({{ $report->id }}, true)">✏️ {{ __('app.edit') }}</button>
                            </div>
                            <div class="reply-bubble" id="replyDisplay_{{ $report->id }}">{{ $report->admin_reply }}</div>
                        </div>
                    @endif

                    {{-- Reply form --}}
                    <div class="reply-form-wrap">
                        @if(!$report->admin_reply)
                            <button class="reply-toggle-btn" onclick="toggleReplyForm({{ $report->id }})">
                                💬 {{ __('app.report_reply') }}
                            </button>
                        @endif

                        <div class="reply-form" id="replyForm_{{ $report->id }}">
                            <form method="POST" action="{{ route('admin.reports.reply', $report) }}">
                                @csrf @method('PATCH')
                                <textarea
                                    name="admin_reply"
                                    placeholder="{{ __('app.report_reply_placeholder') }}"
                                    required
                                    maxlength="2000"
                                >{{ $report->admin_reply }}</textarea>
                                <div style="display:flex; gap:8px; margin-top:8px; align-items:center;">
                                    <button type="submit" class="btn btn-primary" style="font-size:12px; padding:7px 16px;">
                                        📤 {{ __('app.report_reply_send') }}
                                    </button>
                                    <button type="button" class="btn btn-secondary" style="font-size:12px; padding:7px 14px;"
                                        onclick="toggleReplyForm({{ $report->id }}, false)">
                                        {{ __('app.cancel') }}
                                    </button>
                                    <span style="font-size:11px; color:var(--text-muted); margin-left:4px;">
                                        {{ __('app.agent') }}: <strong>{{ $report->agent?->name }}</strong>
                                    </span>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>

                {{-- Actions (mark read) --}}
                @if($report->status === 'unread')
                    <form method="POST" action="{{ route('admin.reports.read', $report) }}" style="flex-shrink:0;">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-sm btn-outline-primary" style="white-space:nowrap; font-size:11px;">
                            ✓ {{ __('app.report_mark_read') }}
                        </button>
                    </form>
                @endif

            </div>
        </div>
        @endforeach

        <div style="padding:16px 20px;">
            {{ $reports->links() }}
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
function toggleReplyForm(id, forceOpen) {
    const form    = document.getElementById('replyForm_' + id);
    const display = document.getElementById('replyDisplay_' + id);
    if (forceOpen === true) {
        form.classList.add('open');
        if (display) display.style.display = 'none';
        form.querySelector('textarea').focus();
    } else if (forceOpen === false) {
        form.classList.remove('open');
        if (display) display.style.display = '';
    } else {
        form.classList.toggle('open');
        if (form.classList.contains('open')) {
            form.querySelector('textarea').focus();
        }
    }
}
</script>
@endpush
