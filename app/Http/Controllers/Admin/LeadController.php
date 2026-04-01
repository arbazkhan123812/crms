<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $query = Lead::with(['owner', 'user']);

        // Agar user Super Admin nahi hai, to sirf uski apni leads filter karo
        if (!Auth::user()->hasRole('Admin')) {
            $query->where('created_by', Auth::id());
        }

        $leads = $query->latest()->get();
        $users = User::all();


        // Define dropdown options
        $leadSources = ['Website', 'Referral', 'Email Campaign', 'Social Media', 'Advertisement', 'Trade Show', 'Cold Call', 'Other'];
        $leadStatuses = ['New', 'Contacted', 'Qualified', 'Lost', 'Cancelled', 'Junk'];

        return view('Admin.Leads.index', compact('leads', 'users', 'leadSources', 'leadStatuses'));
    }

    public function save(Request $request)
    {
        try {
            $id = $request->input('id');

            $data = [
                'lead_owner' => $request->input('lead_owner'),
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'title' => $request->input('title'),
                'phone' => $request->input('phone'),
                'mobile' => $request->input('mobile'),
                'lead_source' => $request->input('lead_source'),
                'annual_revenue' => $request->input('annual_revenue'),
                'company' => $request->input('company'),
                'email' => $request->input('email'),
                'website' => $request->input('website'),
                'lead_status' => $request->input('lead_status'),
                'no_of_employees' => $request->input('no_of_employees'),
                'street' => $request->input('street'),
                'state' => $request->input('state'),
                'country' => $request->input('country'),
                'city' => $request->input('city'),
                'zip_code' => $request->input('zip_code'),
                'description' => $request->input('description'),
                'created_by' =>  Auth::user()->id,
            ];

            // Handle image upload
            if ($request->hasFile('lead_image')) {
                $image = $request->file('lead_image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads/leads'), $imageName);
                $data['lead_image'] = 'uploads/leads/' . $imageName;
            }

            if ($id) {
                $lead = Lead::findOrFail($id);

                // Delete old image if new one is uploaded
                if ($request->hasFile('lead_image') && $lead->lead_image && file_exists(public_path($lead->lead_image))) {
                    unlink(public_path($lead->lead_image));
                }
                $data = [
                    'lead_owner' => $request->input('lead_owner'),
                    'first_name' => $request->input('first_name'),
                    'last_name' => $request->input('last_name'),
                    'title' => $request->input('title'),
                    'phone' => $request->input('phone'),
                    'mobile' => $request->input('mobile'),
                    'lead_source' => $request->input('lead_source'),
                    'annual_revenue' => $request->input('annual_revenue'),
                    'company' => $request->input('company'),
                    'email' => $request->input('email'),
                    'website' => $request->input('website'),
                    'lead_status' => $request->input('lead_status'),
                    'no_of_employees' => $request->input('no_of_employees'),
                    'street' => $request->input('street'),
                    'state' => $request->input('state'),
                    'country' => $request->input('country'),
                    'city' => $request->input('city'),
                    'zip_code' => $request->input('zip_code'),
                    'description' => $request->input('description'),
                ];

                $data['updated_by'] = Auth::user()->id;
                if ($lead->created_by == Auth::user()->id || Auth::user()->hasRole('Admin')) {

                    $lead->update($data);
                    return response()->json(['success' => true, 'message' => 'Lead updated successfully!']);
                } else {
                    return response()->json(['success' => false, 'message' => 'You can only edit your own made leads!']);
                }
            } else {
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

            // Delete lead image if exists  
            if ($lead->lead_image && file_exists(public_path($lead->lead_image))) {
                unlink(public_path($lead->lead_image));
            }

            if ($lead->created_by == Auth::user()->id || Auth::user()->hasRole('Admin')) {

                $lead->delete();
                return response()->json(['success' => true, 'message' => 'Lead deleted successfully!']);
            } else {
                return response()->json(['success' => true, 'message' => 'You can only delete your own made leads!']);
            }
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
}
