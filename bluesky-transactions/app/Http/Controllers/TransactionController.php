<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['originCountry', 'destinationCountry'])
            ->where('agent_id', auth()->id());

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(fn($q2) => $q2->where('transaction_number', 'like', "%$q%")
                ->orWhere('sender_name', 'like', "%$q%")
                ->orWhere('sender_phone', 'like', "%$q%")
                ->orWhere('receiver_name', 'like', "%$q%"));
        }
        if ($request->filled('status'))           { $query->where('status', $request->status); }
        if ($request->filled('transaction_type')) { $query->where('transaction_type', $request->transaction_type); }
        if ($request->filled('date_from'))        { $query->whereDate('created_at', '>=', $request->date_from); }
        if ($request->filled('date_to'))          { $query->whereDate('created_at', '<=', $request->date_to); }

        // Filtre par pays (origine ou destination)
        if ($request->filled('country_id')) {
            $cid = $request->country_id;
            $query->where(fn($q2) => $q2->where('origin_country_id', $cid)
                ->orWhere('destination_country_id', $cid));
        }

        $transactions = $query->latest()->paginate(15);

        $stats = [
            'total'        => Transaction::where('agent_id', auth()->id())->count(),
            'today'        => Transaction::where('agent_id', auth()->id())->whereDate('created_at', today())->count(),
            'month_amount' => Transaction::where('agent_id', auth()->id())
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->where('status', 'completed')->sum('amount'),
        ];

        $countries = Country::where('is_active', true)->orderBy('name')->get();

        return view('agent.transactions.index', compact('transactions', 'stats', 'countries'));
    }

    public function create()
    {
        $countries    = Country::where('is_active', true)->orderBy('name')->get();
        $agentCountry = auth()->user()->country;
        $currencies   = $this->buildCurrencies();
        return view('agent.transactions.create', compact('countries', 'agentCountry', 'currencies'));
    }

    public function store(Request $request)
    {
        $isWithdrawal = $request->transaction_type === 'withdrawal';

        $rules = [
            'transaction_type'      => 'required|in:send,withdrawal',
            'amount'                => 'required|numeric|min:1',
            'fee_percentage'        => 'required|numeric|min:0|max:100',
            'origin_country_id'     => 'required|exists:countries,id',
            'destination_country_id'=> 'required|exists:countries,id|different:origin_country_id',
            'payment_method'        => 'required|in:cash,mobile_money,bank',
            'currency'              => 'required|string|max:10',
            'notes'                 => 'nullable|string|max:500',
        ];

        if ($isWithdrawal) {
            // Retrait : le bénéficiaire vient retirer — ses infos sont obligatoires
            $rules['receiver_name']  = 'required|string|max:255';
            $rules['receiver_phone'] = 'required|string|max:25';
            $rules['sender_name']    = 'nullable|string|max:255';
            $rules['sender_phone']   = 'nullable|string|max:25';
        } else {
            // Envoi : l'expéditeur est obligatoire
            $rules['sender_name']    = 'required|string|max:255';
            $rules['sender_phone']   = 'required|string|max:25';
            $rules['receiver_name']  = 'nullable|string|max:255';
            $rules['receiver_phone'] = 'nullable|string|max:25';
        }

        $request->validate($rules);

        $feeAmount   = round($request->amount * ($request->fee_percentage / 100), 2);
        $totalAmount = $request->amount + $feeAmount;

        Transaction::create([
            'transaction_number'     => Transaction::generateTransactionNumber(),
            'transaction_type'       => $request->transaction_type,
            'sender_name'            => $request->sender_name ?? '',
            'sender_phone'           => $request->sender_phone ?? '',
            'receiver_name'          => $request->receiver_name,
            'receiver_phone'         => $request->receiver_phone,
            'amount'                 => $request->amount,
            'fee_percentage'         => $request->fee_percentage,
            'fee_amount'             => $feeAmount,
            'total_amount'           => $totalAmount,
            'currency'               => strtoupper($request->currency),
            'origin_country_id'      => $request->origin_country_id,
            'destination_country_id' => $request->destination_country_id,
            'agent_id'               => auth()->id(),
            'status'                 => 'completed',
            'payment_method'         => $request->payment_method,
            'notes'                  => $request->notes,
            'sent_at'                => now(),
        ]);

        return redirect()->route('agent.transactions.index')
            ->with('success', __('app.tx_saved_ok'));
    }

    public function show(Transaction $transaction)
    {
        if (!auth()->user()->isAdmin() && $transaction->agent_id !== auth()->id()) {
            abort(403);
        }
        $transaction->load(['originCountry', 'destinationCountry', 'agent']);
        return view('agent.transactions.show', compact('transaction'));
    }

    public function printReceipt(Transaction $transaction)
    {
        if (!auth()->user()->isAdmin() && $transaction->agent_id !== auth()->id()) {
            abort(403);
        }
        $transaction->load(['originCountry', 'destinationCountry', 'agent']);
        return view('agent.transactions.print', compact('transaction'));
    }

    public function edit(Transaction $transaction)
    {
        if (!auth()->user()->isAdmin() && $transaction->agent_id !== auth()->id()) {
            abort(403);
        }
        $countries    = Country::where('is_active', true)->orderBy('name')->get();
        $agentCountry = auth()->user()->country;
        $currencies   = $this->buildCurrencies();
        return view('agent.transactions.edit', compact('transaction', 'countries', 'agentCountry', 'currencies'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        if (!auth()->user()->isAdmin() && $transaction->agent_id !== auth()->id()) {
            abort(403);
        }

        $isWithdrawal = $request->transaction_type === 'withdrawal';

        $rules = [
            'transaction_type'      => 'required|in:send,withdrawal',
            'amount'                => 'required|numeric|min:1',
            'fee_percentage'        => 'required|numeric|min:0|max:100',
            'origin_country_id'     => 'required|exists:countries,id',
            'destination_country_id'=> 'required|exists:countries,id|different:origin_country_id',
            'payment_method'        => 'required|in:cash,mobile_money,bank',
            'currency'              => 'required|string|max:10',
            'status'                => 'required|in:pending,completed,cancelled',
            'notes'                 => 'nullable|string|max:500',
        ];

        if ($isWithdrawal) {
            $rules['receiver_name']  = 'required|string|max:255';
            $rules['receiver_phone'] = 'required|string|max:25';
            $rules['sender_name']    = 'nullable|string|max:255';
            $rules['sender_phone']   = 'nullable|string|max:25';
        } else {
            $rules['sender_name']    = 'required|string|max:255';
            $rules['sender_phone']   = 'required|string|max:25';
            $rules['receiver_name']  = 'nullable|string|max:255';
            $rules['receiver_phone'] = 'nullable|string|max:25';
        }

        $request->validate($rules);

        $feeAmount   = round($request->amount * ($request->fee_percentage / 100), 2);
        $totalAmount = $request->amount + $feeAmount;

        $transaction->update([
            'transaction_type'       => $request->transaction_type,
            'sender_name'            => $request->sender_name ?? '',
            'sender_phone'           => $request->sender_phone ?? '',
            'receiver_name'          => $request->receiver_name,
            'receiver_phone'         => $request->receiver_phone,
            'amount'                 => $request->amount,
            'fee_percentage'         => $request->fee_percentage,
            'fee_amount'             => $feeAmount,
            'total_amount'           => $totalAmount,
            'currency'               => strtoupper($request->currency),
            'origin_country_id'      => $request->origin_country_id,
            'destination_country_id' => $request->destination_country_id,
            'status'                 => $request->status,
            'payment_method'         => $request->payment_method,
            'notes'                  => $request->notes,
        ]);

        return redirect()->route('agent.transactions.show', $transaction)
            ->with('success', __('app.tx_updated_ok'));
    }

    public function destroy(Transaction $transaction)
    {
        if (!auth()->user()->isAdmin() && $transaction->agent_id !== auth()->id()) {
            abort(403);
        }

        $transaction->delete();

        return redirect()->route('agent.transactions.index')
            ->with('success', __('app.tx_deleted_ok'));
    }

    public function getFeeForCountry(Country $country)
    {
        return response()->json(['fee_percentage' => $country->default_fee_percentage]);
    }

    /**
     * Build the full currency list from all countries in DB + international config.
     * Automatically adapts when new countries are added.
     * Returns ['CODE' => 'Name'] sorted by code.
     */
    private function buildCurrencies(): array
    {
        $fromDb = Country::select('currency_code', 'currency_name')
            ->distinct()
            ->orderBy('currency_code')
            ->pluck('currency_name', 'currency_code')
            ->toArray();

        $international = config('currencies', []);

        return collect(array_merge($fromDb, $international))
            ->sortKeys()
            ->toArray();
    }
}
