<?php

namespace App\Http\Controllers;

use App\Models\AgentReport;
use Illuminate\Http\Request;

class AgentReportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:150',
            'message' => 'required|string|max:2000',
        ]);

        AgentReport::create([
            'agent_id' => auth()->id(),
            'subject'  => $request->subject,
            'message'  => $request->message,
        ]);

        return back()->with('report_success', __('app.report_sent_ok'));
    }

    public function adminIndex()
    {
        $reports = AgentReport::with('agent')
            ->latest()
            ->paginate(20);

        return view('admin.reports.index', compact('reports'));
    }

    public function markRead(AgentReport $report)
    {
        $report->update(['status' => 'read']);
        return back()->with('success', __('app.report_marked_read'));
    }

    public function reply(Request $request, AgentReport $report)
    {
        $request->validate([
            'admin_reply' => 'required|string|max:2000',
        ]);

        $report->update([
            'admin_reply' => $request->admin_reply,
            'replied_at'  => now(),
            'status'      => 'read',
        ]);

        return back()->with('success', __('app.report_reply_sent'));
    }
}
