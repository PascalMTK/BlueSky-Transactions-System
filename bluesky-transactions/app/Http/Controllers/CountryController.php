<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index()
    {
        $countries = Country::withCount(['agents', 'outgoingTransactions'])
            ->orderBy('name')
            ->get();

        return view('admin.countries.index', compact('countries'));
    }

    public function create()
    {
        return view('admin.countries.form', ['country' => new Country(), 'mode' => 'create']);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                   => 'required|string|max:100',
            'code'                   => 'required|string|size:2|unique:countries,code',
            'currency_code'          => 'required|string|max:5',
            'currency_name'          => 'required|string|max:80',
            'flag_emoji'             => 'nullable|string|max:10',
            'phone_code'             => 'required|string|max:10',
            'default_fee_percentage' => 'required|numeric|min:0|max:100',
            'is_active'              => 'boolean',
        ]);

        $data['code']          = strtoupper($data['code']);
        $data['currency_code'] = strtoupper($data['currency_code']);
        $data['is_active']     = $request->boolean('is_active', true);
        $data['flag_emoji']    = $data['flag_emoji'] ?: Country::codeToEmoji($data['code']);

        Country::create($data);

        return redirect()->route('admin.countries.index')
            ->with('success', __('app.country_created_ok'));
    }

    public function edit(Country $country)
    {
        return view('admin.countries.form', compact('country') + ['mode' => 'edit']);
    }

    public function update(Request $request, Country $country)
    {
        $data = $request->validate([
            'name'                   => 'required|string|max:100',
            'code'                   => 'required|string|size:2|unique:countries,code,' . $country->id,
            'currency_code'          => 'required|string|max:5',
            'currency_name'          => 'required|string|max:80',
            'flag_emoji'             => 'nullable|string|max:10',
            'phone_code'             => 'required|string|max:10',
            'default_fee_percentage' => 'required|numeric|min:0|max:100',
            'is_active'              => 'boolean',
        ]);

        $data['code']          = strtoupper($data['code']);
        $data['currency_code'] = strtoupper($data['currency_code']);
        $data['is_active']     = $request->boolean('is_active', false);
        $data['flag_emoji']    = $data['flag_emoji'] ?: Country::codeToEmoji($data['code']);

        $country->update($data);

        return redirect()->route('admin.countries.index')
            ->with('success', __('app.country_updated_ok'));
    }

    public function toggle(Country $country)
    {
        $country->update(['is_active' => !$country->is_active]);

        return back()->with('success',
            $country->is_active ? __('app.country_activated') : __('app.country_deactivated')
        );
    }

    public function destroy(Country $country)
    {
        if ($country->outgoingTransactions()->exists() || $country->agents()->exists()) {
            return back()->with('error', __('app.country_cannot_delete'));
        }

        $country->delete();

        return redirect()->route('admin.countries.index')
            ->with('success', __('app.country_deleted_ok'));
    }
}
