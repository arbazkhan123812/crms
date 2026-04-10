<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\User;
use App\Notifications\RecordAssignedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:accounts_view',['only' => 'index']);
        $this->middleware('permission:accounts_saveaccounts',['only' => 'store']);
        $this->middleware('permission:accounts_deleteaccount',['only' => 'destroy']);
        $this->middleware('permission:accounts_editaccount',['only' => 'update']);
    }
    public function index()
    {
        $user = auth()->user();

        $accountsQuery = Account::with(['contacts', 'createdBy', 'convertedFromLead', 'accountOwner'])
            ->latest();

        if (!$user->hasRole('Admin')) {
            $accountsQuery->where(function ($query) use ($user) {
                $query->where('created_by', $user->id)
                    ->orWhere('account_owner', $user->id);
            });
        }

        $accounts = $accountsQuery->get();
        $accountOwners = User::orderBy('username')->get();
        $parentAccounts = Account::orderBy('name')->get();


        return view('Admin.Accounts.index', compact('accounts', 'accountOwners', 'parentAccounts'));
    }

    public function show($id)
    {
        $account = Account::with([
            'contacts',
            'createdBy',
            'convertedFromLead',
            'accountOwner',
            'parentAccount',
            'notes.createdBy',
            'tasks.assignedTo',
            'calls.calledBy',
            'calls.callOwner',
            'meetings.assignedTo',
        ])->findOrFail($id);

        $users = User::orderBy('username')->get();

        return view('Admin.Accounts.show', compact('account', 'users'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'annual_revenue' => 'nullable|numeric',
            'no_of_employees' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $data = $request->all();
            $data['created_by'] = auth()->id();
            
            $account = Account::create($data);
            $this->notifyAssignedAccountOwner($account->fresh('accountOwner'));
            
            return response()->json([
                'success' => true,
                'message' => 'Account created successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $account = Account::findOrFail($id);
        $previousOwnerId = $account->account_owner;
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'annual_revenue' => 'nullable|numeric',
            'no_of_employees' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $account->update($request->all());
            $this->notifyAssignedAccountOwner($account->fresh('accountOwner'), $previousOwnerId);
            
            return response()->json([
                'success' => true,
                'message' => 'Account updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $account = Account::findOrFail($id);
            
            // Check if account has child accounts
            if ($account->childAccounts()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete account because it has child accounts!'
                ]);
            }
            
            $account->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Account deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting account!'
            ]);
        }
    }

    public function edit($id)
    {
        try {
            $account = Account::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $account
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Account not found!'
            ]);
        }
    }

    protected function notifyAssignedAccountOwner(Account $account, ?int $previousOwnerId = null): void
    {
        if (!$account->account_owner || $account->account_owner === auth()->id() || $account->account_owner === $previousOwnerId) {
            return;
        }

        $assignedUser = User::find($account->account_owner);

        if (!$assignedUser) {
            return;
        }

        $assignedUser->notify(new RecordAssignedNotification(
            'Account',
            $account->id,
            'Account Assigned',
            'A new Account has been assigned to you: ' . ($account->name ?: 'Account #' . $account->id),
            route('admin.account.show', $account->id),
            auth()->id()
        ));
    }
}
