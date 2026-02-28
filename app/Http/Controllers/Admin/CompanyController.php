<?php
// app/Http/Controllers/Admin/CompanyController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    
    public function index()
    {
        $company = Company::first();
        
      
        
        return view('admin.companies.index', compact('company'));
    }


    public function update(Request $request)
    {
        $company = Company::first();
        
        if (!$company) {
            $company = new Company();
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email,' . ($company->id ?? 'NULL'),
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'tax_id' => 'nullable|string|max:100',
            'registration_number' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive'
        ]);

        if ($request->hasFile('logo')) {
            $request->validate([
                'logo' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);
            

            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            
            $path = $request->file('logo')->store('company/logo', 'public');
            $validated['logo'] = $path;
        }

        if ($company->id) {
            $company->update($validated);
            $message = 'Company profile updated successfully';
        } else {
            $company = Company::create($validated);
            $message = 'Company profile created successfully';
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'company' => $company
            ]);
        }

        return redirect()->route('admin.companies.index')
            ->with('success', $message);
    }


    public function edit()
    {
        $company = Company::first();
        
        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'No company found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'company' => $company
        ]);
    }

    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $company = Company::first();
        
        if (!$company) {
            $company = new Company();
        }

        if ($company->logo) {
            Storage::disk('public')->delete($company->logo);
        }

        $path = $request->file('logo')->store('uploads', 'public');
        
        if ($company->id) {
            $company->update(['logo' => $path]);
        } else {
            $company->logo = $path;
            $company->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logo uploaded successfully',
            'logo_url' => Storage::url($path)
        ]);
    }
}