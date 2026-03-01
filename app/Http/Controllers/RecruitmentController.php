<?php

namespace App\Http\Controllers\Admin;

use App\Models\Job;
use App\Models\Candidate;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RecruitmentController extends Controller
{
    public function index()
    {
        $jobs = Job::with(['department', 'designation'])->latest()->paginate(10);
        $candidates = Candidate::with('job')->latest()->take(5)->get();
        $stats = [
            'active_jobs' => Job::where('status', 'published')->count(),
            'total_candidates' => Candidate::count(),
            'hired' => Candidate::where('status', 'hired')->count(),
            'in_process' => Candidate::whereIn('status', ['shortlisted', 'interviewed'])->count()
        ];
        return view('admin.recruitment.index', compact('jobs', 'candidates', 'stats'));
    }

    public function createJob()
    {
        $departments = Department::where('is_active', true)->get();
        $designations = Designation::where('is_active', true)->get();
        return view('admin.recruitment.jobs.create', compact('departments', 'designations'));
    }

    public function storeJob(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
            'vacancies' => 'required|integer|min:1',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'type' => 'required|in:full_time,part_time,contract,internship',
            'closing_date' => 'required|date|after:today',
            'min_salary' => 'nullable|numeric',
            'max_salary' => 'nullable|numeric|gt:min_salary',
        ]);

        $validated['posted_date'] = now();
        $validated['status'] = 'published';

        Job::create($validated);

        return redirect()->route('admin.recruitment.index')->with('success', 'Job posted successfully');
    }

    public function candidates($jobId)
    {
        $job = Job::with('candidates')->findOrFail($jobId);
        return view('admin.recruitment.candidates', compact('job'));
    }

    public function updateCandidateStatus(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);
        $candidate->update(['status' => $request->status]);
        return response()->json(['success' => true]);
    }
}