<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function exportCsv(Request $request)
    {
        $query = Transaction::with(['originCountry', 'destinationCountry', 'agent']);

        if (!auth()->user()->isAdmin()) {
            $query->where('agent_id', auth()->id());
        }

        if ($request->filled('date_from'))           { $query->whereDate('created_at', '>=', $request->date_from); }
        if ($request->filled('date_to'))             { $query->whereDate('created_at', '<=', $request->date_to); }
        if ($request->filled('origin_country'))      { $query->where('origin_country_id', $request->origin_country); }
        if ($request->filled('destination_country')) { $query->where('destination_country_id', $request->destination_country); }

        $transactions = $query->latest()->get();
        $filename     = 'bluesky_transactions_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($transactions) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM for Excel

            fputcsv($file, [
                'Transaction #', 'Date', 'Sender Name', 'Sender Phone',
                'Receiver Name', 'Receiver Phone', 'Amount', 'Fee %',
                'Fee Amount', 'Total', 'Origin Country', 'Destination Country',
                'Route Code', 'Payment Method', 'Status', 'Agent',
            ], ';');

            foreach ($transactions as $tx) {
                fputcsv($file, [
                    $tx->transaction_number,
                    $tx->created_at->format('d/m/Y H:i'),
                    $tx->sender_name,
                    $tx->sender_phone,
                    $tx->receiver_name  ?? '-',
                    $tx->receiver_phone ?? '-',
                    number_format($tx->amount, 2),
                    $tx->fee_percentage . '%',
                    number_format($tx->fee_amount, 2),
                    number_format($tx->total_amount, 2),
                    $tx->originCountry?->name      ?? '-',
                    $tx->destinationCountry?->name ?? '-',
                    $tx->route_code,
                    ucfirst(str_replace('_', ' ', $tx->payment_method)),
                    ucfirst($tx->status),
                    $tx->agent?->name ?? '-',
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
