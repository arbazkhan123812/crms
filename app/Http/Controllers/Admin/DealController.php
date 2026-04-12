<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DealController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $dealsQuery = Deal::with(['owner', 'account', 'contact'])->latest();

        if (!$user->hasRole('Admin')) {
            $dealsQuery->where(function ($query) use ($user) {
                $query->where('created_by', $user->id)
                    ->orWhere('deal_owner', $user->id);
            });
        }

        $deals = $dealsQuery->get();
        $stages = Deal::STAGES;

        return view('Admin.Deals.index', compact('deals', 'stages'));
    }

    public function create()
    {
        $owners = User::orderBy('username')->get();
        $accounts = Account::orderBy('name')->get();
        $contacts = Contact::orderBy('first_name')->orderBy('last_name')->get();
        $stages = Deal::STAGES;
        $sources = Deal::SOURCES;

        return view('Admin.Deals.create', compact('owners', 'accounts', 'contacts', 'stages', 'sources'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'deal_owner' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'account_id' => 'required|exists:accounts,id',
            'stage' => ['required', Rule::in(Deal::STAGES)],
            'probability' => 'required|integer|min:0|max:100',
            'deal_source' => 'required|string|max:255',
            'contact_id' => 'required|exists:contacts,id',
            'closing_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Deal::create([
            'deal_owner' => $request->deal_owner,
            'amount' => $request->amount,
            'account_id' => $request->account_id,
            'stage' => $request->stage,
            'probability' => $request->probability,
            'deal_source' => $request->deal_source,
            'contact_id' => $request->contact_id,
            'closing_date' => $request->closing_date,
            'description' => $request->description,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.deals.index')->with('success', 'Deal created successfully.');
    }

    public function updateStage(Request $request, $id)
    {
        $deal = Deal::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'stage' => ['required', Rule::in(Deal::STAGES)],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $deal->update([
            'stage' => $request->stage,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Deal stage updated successfully.',
        ]);
    }
}
