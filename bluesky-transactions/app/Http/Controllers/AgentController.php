<?php

namespace App\Http\Controllers;

use App\Models\AgentReport;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class AgentController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();

        $base = fn() => Transaction::where('agent_id', $user->id);

        $lastMonthAmount = $base()
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at',  now()->subMonth()->year)
            ->where('status', 'completed')->sum('amount');

        $stats = [
            'total'            => $base()->count(),
            'total_amount'     => $base()->where('status', 'completed')->sum('amount'),
            'total_fees'       => $base()->where('status', 'completed')->sum('fee_amount'),
            'today'            => $base()->whereDate('created_at', today())->count(),
            'today_amount'     => $base()->whereDate('created_at', today())->where('status', 'completed')->sum('amount'),
            'month'            => $base()->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'month_amount'     => $base()->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->where('status', 'completed')->sum('amount'),
            'last_month_amount'=> $lastMonthAmount,
            'completed'        => $base()->where('status', 'completed')->count(),
            'pending'          => $base()->where('status', 'pending')->count(),
            'cancelled'        => $base()->where('status', 'cancelled')->count(),
        ];

        // Monthly progression for agent (last 6 months)
        $monthlyData = Transaction::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(amount) as total_amount')
        )
            ->where('agent_id', $user->id)
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $recentTransactions = Transaction::with(['originCountry', 'destinationCountry'])
            ->where('agent_id', $user->id)
            ->latest()
            ->limit(8)
            ->get();

        $myReports = AgentReport::where('agent_id', $user->id)->latest()->limit(5)->get();

        $topRoutes = Transaction::select(
                'origin_country_id', 'destination_country_id',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount) as total_amount')
            )
            ->with(['originCountry', 'destinationCountry'])
            ->where('agent_id', $user->id)
            ->where('status', 'completed')
            ->groupBy('origin_country_id', 'destination_country_id')
            ->orderByDesc('count')
            ->limit(3)
            ->get();

        return view('agent.dashboard', compact('stats', 'monthlyData', 'recentTransactions', 'myReports', 'topRoutes'));
    }
}
