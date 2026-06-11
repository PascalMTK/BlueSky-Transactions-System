@extends('layouts.app')

@section('title', __('app.reports_admin_title'))
@section('page-title', __('app.reports_admin_title'))
@section('page-subtitle', __('app.reports_admin_subtitle'))

@section('content')

<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">📨 {{ __('app.reports_admin_title') }}</div>
            <div class="card-subtitle">{{ $reports->total() }} {{ __('app.report_results') }}</div>
        </div>
    </div>

    @if($reports->isEmpty())
        <div style="padding:60px; text-align:center; color:var(--text-muted);">
            📭 {{ __('app.report_none_yet') }}
        </div>
    @else
        @foreach($reports as $report)
            <div style="padding:16px 20px; border-bottom:1px solid var(--divider); display:flex; align-items:flex-start; gap:14px; {{ $report->status === 'unread' ? 'background:rgba(2,132,199,0.04);' : '' }}">

                {{-- Status dot --}}
                <div style="padding-top:3px; flex-shrink:0;">
                    @if($report->status === 'unread')
                        <span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:#f59e0b; box-shadow:0 0 6px rgba(245,158,11,0.6);"></span>
                    @else
                        <span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:#94a3b8;"></span>
                    @endif
                </div>

                {{-- Content --}}
                <div style="flex:1; min-width:0;">
                    <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap; margin-bottom:4px;">
                        <span style="font-weight:700; font-size:14px;">{{ $report->subject }}</span>
                        @if($report->status === 'unread')
                            <span style="font-size:10px; font-weight:700; background:#fef3c7; color:#92400e; padding:2px 8px; border-radius:20px;">
                                {{ __('app.report_unread') }}
                            </span>
                        @endif
                    </div>
                    <div style="font-size:12px; color:var(--text-muted); margin-bottom:8px;">
                        <strong>{{ $report->agent->name }}</strong>
                        &nbsp;·&nbsp; {{ $report->agent->agent_code }}
                        &nbsp;·&nbsp; {{ $report->created_at->format('d/m/Y H:i') }}
                    </div>
                    <div style="font-size:13px; color:var(--text-secondary); background:var(--bg-input); padding:10px 14px; border-radius:8px; border-left:3px solid var(--sky-primary); white-space:pre-line;">{{ $report->message }}</div>
                </div>

                {{-- Mark read --}}
                @if($report->status === 'unread')
                    <form method="POST" action="{{ route('admin.reports.read', $report) }}" style="flex-shrink:0;">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-sm btn-outline-primary" style="white-space:nowrap;">
                            ✓ {{ __('app.report_mark_read') }}
                        </button>
                    </form>
                @endif
            </div>
        @endforeach

        <div style="padding:16px 20px;">
            {{ $reports->links() }}
        </div>
    @endif
</div>

@endsection
