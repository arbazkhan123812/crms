<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LeadController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:leads_view', ['only' => ['index']]);
        $this->middleware('permission:leads_save', ['only' => ['save']]);
        $this->middleware('permission:leads_delete', ['only' => ['delete']]);
    }

    public function index()
    {
        $query = Lead::with('owner');

        if (!Auth::user()->hasRole('Admin')) {
            $query->where('created_by', Auth::id());
        }

        $leads = $query->latest()->get();
        $users = User::all();

        $leadSources = ['Website', 'Referral', 'Email Campaign', 'Social Media', 'Advertisement', 'Trade Show', 'Cold Call', 'Other'];
        $leadStatuses = ['New', 'Contacted', 'Qualified', 'Lost', 'Cancelled', 'Junk'];

        return view('Admin.Leads.index', compact('leads', 'users', 'leadSources', 'leadStatuses'));
    }

    public function save(Request $request)
    {
        $id = $request->input('id');

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:leads,email' . ($id ? ',' . $id : ''),
            'lead_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $data = $request->only([
                'lead_owner',
                'first_name',
                'last_name',
                'title',
                'phone',
                'mobile',
                'lead_source',
                'annual_revenue',
                'company',
                'email',
                'website',
                'lead_status',
                'no_of_employees',
                'street',
                'state',
                'country',
                'city',
                'zip_code',
                'description'
            ]);

            if ($request->hasFile('lead_image')) {
                $image = $request->file('lead_image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads/leads'), $imageName);
                $data['lead_image'] = 'uploads/leads/' . $imageName;
            }

            if ($id) {
                $lead = Lead::findOrFail($id);
                if ($request->hasFile('lead_image') && $lead->lead_image && file_exists(public_path($lead->lead_image))) {
                    unlink(public_path($lead->lead_image));
                }
                $lead->update($data);
                return response()->json(['success' => true, 'message' => 'Lead updated successfully!']);
            } else {
                $data['created_by'] = Auth::user()->id;
                Lead::create($data);
                return response()->json(['success' => true, 'message' => 'Lead created successfully!']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        try {
            $lead = Lead::findOrFail($id);
            if ($lead->lead_image && file_exists(public_path($lead->lead_image))) {
                unlink(public_path($lead->lead_image));
            }
            $lead->delete();
            return response()->json(['success' => true, 'message' => 'Lead deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting lead!']);
        }
    }

    public function get($id)
    {
        try {
            $lead = Lead::with('owner')->findOrFail($id);
            return response()->json(['success' => true, 'data' => $lead]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lead not found!']);
        }
    }

    // Add Note Method
    public function addNote(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'lead_id' => 'required|exists:leads,id',
                'note' => 'required|string|min:2',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ]);
            }

            $lead = Lead::findOrFail($request->lead_id);
            $note = $lead->addNote($request->note, $request->type);

            return response()->json([
                'success' => true,
                'message' => 'Note added successfully!',
                'data' => $note
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
}
