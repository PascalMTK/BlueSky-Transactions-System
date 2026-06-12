<?php

namespace App\Http\Controllers;

use App\Mail\AgentApprovedMail;
use App\Models\AgentReport;
use App\Models\Country;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_transactions' => Transaction::count(),
            'total_amount'       => Transaction::where('status', 'completed')->sum('amount'),
            'total_fees'         => Transaction::where('status', 'completed')->sum('fee_amount'),
            'total_agents'       => User::where('role', 'agent')->count(),
            'active_agents'      => User::where('role', 'agent')->where('status', 'active')->count(),
            'pending_agents'     => User::where('role', 'agent')->where('status', 'pending')->count(),
            'countries_active'   => Country::where('is_active', true)->count(),
            'transactions_today' => Transaction::whereDate('created_at', today())->count(),
            'amount_today'       => Transaction::whereDate('created_at', today())->where('status', 'completed')->sum('amount'),
            'transactions_month' => Transaction::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'amount_month'       => Transaction::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->where('status', 'completed')->sum('amount'),
            'tx_completed'       => Transaction::where('status', 'completed')->count(),
            'tx_pending'         => Transaction::where('status', 'pending')->count(),
            'tx_cancelled'       => Transaction::where('status', 'cancelled')->count(),
        ];

        $monthlyData = Transaction::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(amount) as total_amount'),
            DB::raw('SUM(fee_amount) as total_fees')
        )
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('year', 'month')
            ->orderBy('year')->orderBy('month')
            ->get();

        $countryStats = Country::withCount([
            'outgoingTransactions as sent_count'     => fn($q) => $q->where('status', 'completed'),
            'incomingTransactions as received_count' => fn($q) => $q->where('status', 'completed'),
        ])
            ->withSum(['outgoingTransactions as sent_amount'     => fn($q) => $q->where('status', 'completed')], 'amount')
            ->withSum(['incomingTransactions as received_amount' => fn($q) => $q->where('status', 'completed')], 'amount')
            ->where('is_active', true)
            ->get();

        $recentTransactions = Transaction::with(['originCountry', 'destinationCountry', 'agent'])
            ->latest()->limit(10)->get();

        $topAgents = User::where('role', 'agent')
            ->withCount(['transactions as tx_count'  => fn($q) => $q->where('status', 'completed')])
            ->withSum(['transactions as tx_amount'   => fn($q) => $q->where('status', 'completed')], 'amount')
            ->orderByDesc('tx_amount')->limit(5)->get();

        $unreadReports = AgentReport::with('agent')
            ->where('status', 'unread')
            ->latest()->limit(5)->get();
        $unreadReportsCount = AgentReport::where('status', 'unread')->count();

        $topRoute = Transaction::select(
                'origin_country_id', 'destination_country_id',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount) as total_amount')
            )
            ->with(['originCountry', 'destinationCountry'])
            ->where('status', 'completed')
            ->groupBy('origin_country_id', 'destination_country_id')
            ->orderByDesc('count')
            ->first();

        $countries = Country::orderBy('name')->get();

        return view('admin.dashboard', compact(
            'stats', 'monthlyData', 'countryStats', 'recentTransactions', 'topAgents',
            'unreadReports', 'unreadReportsCount', 'topRoute', 'countries'
        ));
    }

    public function agents(Request $request)
    {
        $query = User::with('country')->where('role', 'agent');

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(fn($q2) => $q2->where('name', 'like', "%$q%")
                ->orWhere('email', 'like', "%$q%")
                ->orWhere('agent_code', 'like', "%$q%"));
        }
        if ($request->filled('status'))     { $query->where('status', $request->status); }
        if ($request->filled('country_id')) { $query->where('country_id', $request->country_id); }

        $agents    = $query->withCount('transactions')->latest()->paginate(15);
        $countries = Country::where('is_active', true)->get();

        return view('admin.agents.index', compact('agents', 'countries'));
    }

    public function updateAgentStatus(Request $request, User $agent)
    {
        $request->validate(['status' => 'required|in:active,inactive,pending']);

        $wasNotActive = $agent->status !== 'active';
        $agent->update(['status' => $request->status]);

        if ($request->status === 'active' && $wasNotActive) {
            Mail::to($agent->email)->send(new AgentApprovedMail($agent->load('country')));
        }

        return back()->with('success', 'Agent status updated successfully.');
    }

    public function promoteAgent(User $agent)
    {
        if ($agent->role === 'agent') {
            $agent->update(['role' => 'admin', 'status' => 'active']);
            return back()->with('success', "{$agent->name} has been promoted to administrator.");
        }
        return back()->with('error', 'Action not authorized.');
    }

    public function destroyAgent(User $agent)
    {
        if ($agent->id === auth()->id()) {
            return back()->with('error', __('app.cannot_delete_self'));
        }
        if ($agent->role === 'admin') {
            return back()->with('error', __('app.cannot_delete_admin'));
        }
        if ($agent->transactions()->exists()) {
            return back()->with('error', __('app.cannot_delete_has_tx'));
        }
        $agent->delete();
        return back()->with('success', __('app.agent_deleted_ok'));
    }

    public function transactions(Request $request)
    {
        $query = Transaction::with(['originCountry', 'destinationCountry', 'agent']);

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(fn($q2) => $q2->where('transaction_number', 'like', "%$q%")
                ->orWhere('sender_name', 'like', "%$q%")
                ->orWhere('sender_phone', 'like', "%$q%"));
        }
        if ($request->filled('origin_country'))      { $query->where('origin_country_id', $request->origin_country); }
        if ($request->filled('destination_country')) { $query->where('destination_country_id', $request->destination_country); }
        if ($request->filled('status'))              { $query->where('status', $request->status); }
        if ($request->filled('date_from'))           { $query->whereDate('created_at', '>=', $request->date_from); }
        if ($request->filled('date_to'))             { $query->whereDate('created_at', '<=', $request->date_to); }

        $transactions = $query->latest()->paginate(20);
        $countries    = Country::where('is_active', true)->get();
        $totals = [
            'amount' => (clone $query)->where('status', 'completed')->sum('amount'),
            'fees'   => (clone $query)->where('status', 'completed')->sum('fee_amount'),
            'count'  => $query->getQuery()->getCountForPagination(),
        ];

        return view('admin.transactions.index', compact('transactions', 'countries', 'totals'));
    }

    public function resetSystem()
    {
        Transaction::query()->delete();
        return redirect()->route('admin.dashboard')
            ->with('success', __('app.reset_system_done'));
    }

    public function resetByCountry(Country $country)
    {
        Transaction::where('origin_country_id', $country->id)
            ->orWhere('destination_country_id', $country->id)
            ->delete();

        return redirect()->route('admin.dashboard')
            ->with('success', __('app.reset_by_country_done', ['name' => $country->name]));
    }

    public function statistics()
    {
        $yearlyData = Transaction::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(amount) as total_amount'),
            DB::raw('SUM(fee_amount) as total_fees')
        )
            ->where('status', 'completed')
            ->groupBy('year')->orderBy('year')
            ->get();

        $currentYearMonthly = Transaction::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(amount) as total_amount')
        )
            ->where('status', 'completed')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')->orderBy('month')
            ->get()->keyBy('month');

        return view('admin.statistics', compact('yearlyData', 'currentYearMonthly'));
    }
}
