<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Interview;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RecruitmentController extends Controller
{
    public function index()
    {
        $stats = [
            'active_jobs' => Job::where('status', 'published')->count(),
            'total_candidates' => Candidate::count(),
            'in_process' => Candidate::whereIn('status', ['screening', 'shortlisted', 'interview_scheduled', 'interviewed'])->count(),
            'hired' => Candidate::where('status', 'hired')->count(),
            'interviews_today' => Interview::whereDate('scheduled_at', today())->count(),
            'pending_review' => Candidate::where('status', 'applied')->count()
        ];

        $recent_jobs = Job::with(['department', 'designation'])
            ->latest()
            ->limit(5)
            ->get();

        $recent_candidates = Candidate::with(['job'])
            ->latest()
            ->limit(10)
            ->get();

        $upcoming_interviews = Interview::with(['candidate', 'job', 'interviewer'])
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get();

        return view('admin.recruitment.index', compact(
            'stats',
            'recent_jobs',
            'recent_candidates',
            'upcoming_interviews'
        ));
    }

    // Job Management
    public function jobs()
    {
        $jobs = Job::with(['department', 'designation', 'createdBy'])
            ->withCount('candidates')
            ->latest()
            ->paginate(15);

        return view('admin.recruitment.jobs.index', compact('jobs'));
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
            'title' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
            'vacancies' => 'required|integer|min:1',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'responsibilities' => 'nullable|string',
            'qualifications' => 'nullable|string',
            'experience_level' => 'required|in:entry,mid,senior,lead,manager',
            'job_type' => 'required|in:full_time,part_time,contract,internship,remote',
            'closing_date' => 'required|date|after:today',
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|gt:min_salary',
            'location' => 'nullable|string',
            'skills_required' => 'nullable|array',
            'benefits' => 'nullable|array',
            'status' => 'required|in:draft,published'
        ]);
        
        $validated['posted_date'] = now();
        $validated['created_by'] = auth()->id();

        if (isset($validated['skills_required'])) {
            $validated['skills_required'] = json_encode($validated['skills_required']);
        }
        if (isset($validated['benefits'])) {
            $validated['benefits'] = json_encode($validated['benefits']);
        }

        Job::create($validated);

        return redirect()->route('admin.recruitment.jobs')
            ->with('success', 'Job posted successfully');
    }

    public function editJob($id)
    {
        $job = Job::findOrFail($id);
        $departments = Department::where('is_active', true)->get();
        $designations = Designation::where('is_active', true)->get();
        $candidates = Candidate::all();

        return view('admin.recruitment.jobs.edit', compact('job', 'departments', 'designations', 'candidates'));
    }

    public function updateJob(Request $request, $id)
    {
        $job = Job::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
            'vacancies' => 'required|integer|min:1',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'responsibilities' => 'nullable|string',
            'qualifications' => 'nullable|string',
            'experience_level' => 'required|in:entry,mid,senior,lead,manager',
            'job_type' => 'required|in:full_time,part_time,contract,internship,remote',
            'closing_date' => 'required|date',
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|gt:min_salary',
            'location' => 'nullable|string',
            'skills_required' => 'nullable|array',
            'benefits' => 'nullable|array',
            'status' => 'required|in:draft,published,closed,on_hold'
        ]);

        if (isset($validated['skills_required'])) {
            $validated['skills_required'] = json_encode($validated['skills_required']);
        }
        if (isset($validated['benefits'])) {
            $validated['benefits'] = json_encode($validated['benefits']);
        }

        $job->update($validated);

        return redirect()->route('admin.recruitment.jobs')
            ->with('success', 'Job updated successfully');
    }

    public function showJob($id)
    {
        $job = Job::with(['department', 'designation', 'createdBy', 'candidates'])
            ->withCount('candidates')
            ->findOrFail($id);

        $candidates = Candidate::where('job_id', $id)
            ->latest()
            ->paginate(15);

        return view('admin.recruitment.jobs.show', compact('job', 'candidates'));
    }

    public function updateJobStatus(Request $request, $id)
    {
        $job = Job::findOrFail($id);
        $job->update(['status' => $request->status]);

        return response()->json(['success' => true]);
    }

    // Candidate Management
    public function candidates(Request $request)
    {
        $query = Candidate::with(['job']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('job_id')) {
            $query->where('job_id', $request->job_id);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->search}%")
                    ->orWhere('last_name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%")
                    ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        $candidates = $query->latest()->paginate(15);
        $jobs = Job::where('status', 'published')->get();
        $statuses = [
            'applied',
            'screening',
            'shortlisted',
            'interview_scheduled',
            'interviewed',
            'technical_round',
            'hr_round',
            'selected',
            'offered',
            'hired',
            'rejected',
            'withdrawn'
        ];

        return view('admin.recruitment.candidates.index', compact('candidates', 'jobs', 'statuses'));
    }

    public function createCandidate()
    {
        $jobs = Job::where('status', 'published')->get();
        return view('admin.recruitment.candidates.create', compact('jobs'));
    }

    public function storeCandidate(Request $request)
    {
        $validated = $request->validate([
            'job_id' => 'required|exists:jobs,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:candidates,email',
            'phone' => 'required|string|max:20',
            'current_company' => 'nullable|string',
            'current_designation' => 'nullable|string',
            'experience_years' => 'nullable|integer|min:0|max:50',
            'current_ctc' => 'nullable|numeric|min:0',
            'expected_ctc' => 'nullable|numeric|min:0',
            'skills' => 'nullable|string',
            'qualifications' => 'nullable|string',
            'source' => 'required|in:website,linkedin,referral,job_portal,walk_in,other',
            'available_from_date' => 'nullable|date',
            'notice_period' => 'nullable|string',
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'cover_letter' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'photo' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('resume')) {
            $validated['resume_path'] = $request->file('resume')
                ->store('candidates/resumes', 'public');
        }
        if ($request->hasFile('cover_letter')) {
            $validated['cover_letter_path'] = $request->file('cover_letter')
                ->store('candidates/cover_letters', 'public');
        }
        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request->file('photo')
                ->store('candidates/photos', 'public');
        }

        $validated['applied_date'] = now();
        $validated['status'] = 'applied';

        Candidate::create($validated);

        // Update job applications count
        Job::where('id', $request->job_id)->increment('applications_count');

        return redirect()->route('admin.recruitment.candidates')
            ->with('success', 'Candidate added successfully');
    }

   public function showCandidate($id)
{
    $candidate = Candidate::with([
        'job',
        'interviews.interviewer'
    ])->findOrFail($id);

    return view('admin.recruitment.candidates.show', compact('candidate'));
}

    public function updateCandidateStatus(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);
        $candidate->update(['status' => $request->status]);

        return response()->json(['success' => true]);
    }

    // Interview Management
    public function interviews()
    {
        $interviews = Interview::with(['candidate', 'job', 'interviewer'])
            ->latest()
            ->paginate(15);

        return view('admin.recruitment.interviews.index', compact('interviews'));
    }

    public function scheduleInterview(Request $request)
    {
        $validated = $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'job_id' => 'required|exists:jobs,id',
            'interviewer_id' => 'required|exists:users,id',
            'round' => 'required|in:screening,technical_1,technical_2,hr,manager,final',
            'scheduled_at' => 'required|date|after:now',
            'duration_minutes' => 'required|integer|min:15|max:240',
            'mode' => 'required|in:online,offline,phone',
            'location_or_link' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        Interview::create($validated);

        // Update candidate status
        Candidate::where('id', $request->candidate_id)
            ->update(['status' => 'interview_scheduled']);

        return response()->json(['success' => true]);
    }


    public function updateInterviewFeedback(Request $request, $id)
    {
        $interview = Interview::findOrFail($id);

        $validated = $request->validate([
            'rating' => 'required|numeric|min:1|max:5',
            'feedback' => 'required|string',
            'result' => 'required|in:selected,rejected,on_hold'
        ]);

        $interview->update([
            'rating' => $validated['rating'],
            'feedback' => $validated['feedback'],
            'result' => $validated['result'],
            'status' => 'completed'
        ]);

        // Update candidate status based on result
        $candidate = Candidate::find($interview->candidate_id);
        if ($validated['result'] == 'selected') {
            $candidate->update(['status' => 'selected']);
        } elseif ($validated['result'] == 'rejected') {
            $candidate->update(['status' => 'rejected']);
        }

        return response()->json(['success' => true]);
    }

    // Delete Job - GET version
public function destroyJob($id)
{
    try {
        DB::beginTransaction();
        
        $job = Job::withCount('candidates')->findOrFail($id);
        
        // Check if job has candidates
        if ($job->candidates_count > 0) {
            return redirect()->back()->with('error', 'Cannot delete job with existing candidates. Please delete candidates first.');
        }
        
        // Delete related interviews first
        Interview::where('job_id', $id)->delete();
        
        // Delete the job
        $job->delete();
        
        DB::commit();
        
        return redirect()->route('admin.recruitment.jobs')
            ->with('success', 'Job deleted successfully');
        
    } catch (\Exception $e) {
        DB::rollBack();
        
        return redirect()->back()
            ->with('error', 'Error deleting job: ' . $e->getMessage());
    }
}

// Delete Candidate - GET version
public function destroyCandidate($id)
{
    try {
        DB::beginTransaction();
        
        $candidate = Candidate::withCount('interviews')->findOrFail($id);
        
        // Delete related interviews
        Interview::where('candidate_id', $id)->delete();
        
        // Delete files from storage
        if ($candidate->resume_path) {
            Storage::disk('public')->delete($candidate->resume_path);
        }
        if ($candidate->cover_letter_path) {
            Storage::disk('public')->delete($candidate->cover_letter_path);
        }
        if ($candidate->photo_path) {
            Storage::disk('public')->delete($candidate->photo_path);
        }
        
        // Delete the candidate
        $candidate->delete();
        
        DB::commit();
        
        return redirect()->route('admin.recruitment.candidates')
            ->with('success', 'Candidate deleted successfully');
        
    } catch (\Exception $e) {
        DB::rollBack();
        
        return redirect()->back()
            ->with('error', 'Error deleting candidate: ' . $e->getMessage());
    }
}

// Delete Interview - GET version
public function destroyInterview($id)
{
    try {
        DB::beginTransaction();
        
        $interview = Interview::findOrFail($id);
        $candidateId = $interview->candidate_id;
        $candidate = Candidate::find($candidateId);
        $candidateName = $candidate->full_name;
        
        // Delete the interview
        $interview->delete();
        
        // Check if candidate has any other interviews
        $otherInterviews = Interview::where('candidate_id', $candidateId)->count();
        
        // If no other interviews, revert candidate status to previous state
        if ($otherInterviews == 0) {
            $candidate->update(['status' => 'applied']);
        }
        
        DB::commit();
        
        return redirect()->back()
            ->with('success', 'Interview deleted successfully for ' . $candidateName);
        
    } catch (\Exception $e) {
        DB::rollBack();
        
        return redirect()->back()
            ->with('error', 'Error deleting interview: ' . $e->getMessage());
    }
}
}
