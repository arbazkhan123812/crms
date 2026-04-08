<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function  __construct()
    {
        $this->middleware('permission:contacts_view',['only' => 'index']);
        $this->middleware('permission:contacts_savecontacts',['only' => 'store']);
        $this->middleware('permission:contacts_deletecontact',['only' => 'destroy']);
    }
    public function index()
    {
        $user = auth()->user();

        $contactsQuery = Contact::with(['account', 'createdBy', 'convertedFromLead', 'contactOwner'])
            ->latest();

        if (!$user->hasRole('Admin')) {
            $contactsQuery->where(function ($query) use ($user) {
                $query->where('created_by', $user->id)
                    ->orWhere('contact_owner', $user->id);
            });
        }

        $contacts = $contactsQuery->get();
        $accounts = Account::orderBy('name')->get();
        $contactOwners = User::orderBy('username')->get();

        return view('Admin.Contacts.index', compact('contacts', 'accounts', 'contactOwners'));
    }

    public function show($id)
    {
        $contact = Contact::with([
            'account.contacts',
            'createdBy',
            'convertedFromLead',
            'contactOwner'
        ])->findOrFail($id);

        return view('Admin.Contacts.show', compact('contact'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'secondary_email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'mobile' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'account_id' => 'nullable|exists:accounts,id',
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
            
            Contact::create($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Contact created successfully!'
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
        $contact = Contact::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'secondary_email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'mobile' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'account_id' => 'nullable|exists:accounts,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $contact->update($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Contact updated successfully!'
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
            $contact = Contact::findOrFail($id);
            $contact->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Contact deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting contact!'
            ]);
        }
    }

    public function edit($id)
    {
        try {
            $contact = Contact::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $contact
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Contact not found!'
            ]);
        }
    }
}
