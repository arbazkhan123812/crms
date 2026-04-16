<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\LeadNotification;
use App\Models\EmailTemplate;
use App\Models\Lead;
use App\Models\LeadCall;
use App\Models\LeadMeeting;
use App\Models\LeadNote;
use App\Models\LeadTask;
use App\Models\User;
use App\Notifications\RecordAssignedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class LeadController extends Controller
{
    public function __construct()
    {
        // $this->middleware('permission:leads_view', ['only' => ['index']]);
        // $this->middleware('permission:leads_view|leads_details', ['only' => ['show']]);
        // $this->middleware('permission:leads_save', ['only' => ['save']]);
        // $this->middleware('permission:leads_delete', ['only' => ['delete']]);
        // $this->middleware('permission:leads_sendemail', ['only' => ['sendEmail']]);
        // $this->middleware('permission:leads_notes|leads_addnote|leads_addnotes', ['only' => ['getNotes']]);
        // $this->middleware('permission:leads_addnote|leads_addnotes', ['only' => ['addNote', 'updateNote', 'deleteNote']]);
        // $this->middleware('permission:leads_task|leads_addtasks', ['only' => ['getTasks']]);
        // $this->middleware('permission:leads_addtasks', ['only' => ['addTask', 'updateTask', 'deleteTask', 'updateTaskStatus']]);
        // $this->middleware('permission:leads_call|leads_viewlogcall|leads_schedulecalls|leads_createcall|leads_logcall|leads_addlogcall', ['only' => ['getLeadCalls', 'getCalls', 'getCall']]);
        // $this->middleware('permission:leads_logcall|leads_addlogcall', ['only' => ['logCall', 'addCall']]);
        // $this->middleware('permission:leads_schedulecalls|leads_createcall', ['only' => ['createScheduledCall']]);
        // $this->middleware('permission:leads_markcompleteupcommingcall|leads_addlogcall', ['only' => ['updateCallToLogged']]);
        // $this->middleware('permission:leads_editscheduledcalls|leads_addlogcall|leads_createcall', ['only' => ['updateCall']]);
        // $this->middleware('permission:leads_deletecall', ['only' => ['deleteLeadCall', 'deleteCall']]);
        // $this->middleware('permission:leads_viewschedulemeeting|leads_viewupcomingmeeting|leads_viewpastmeeting|leads_schedulemeeting', ['only' => ['getLeadMeetings']]);
        // $this->middleware('permission:leads_schedulemeeting', ['only' => ['scheduleMeeting', 'updateMeeting']]);
        // $this->middleware('permission:leads_markcompleteupcommingmeeting', ['only' => ['completeMeeting']]);
        // $this->middleware('permission:leads_deleteupcomingmeeting|leads_deletepastmeeting', ['only' => ['deleteMeeting']]);
    }

    public function index()
    {
        $query = Lead::with('owner');

        if (!Auth::user()->hasRole('Admin')) {
            $query->where(function ($q) {
                $q->where('created_by', Auth::id())
                    ->orWhere('lead_owner', Auth::id());
            });
        }

        $leads = $query->latest()->get();
        $users = User::all();

        $leadSources = ['Website', 'Referral', 'Email Campaign', 'Social Media', 'Advertisement', 'Trade Show', 'Cold Call', 'Other'];
        $leadStatuses = ['New', 'Contacted', 'Qualified', 'Lost', 'Cancelled', 'Junk'];

        return view('Admin.Leads.index', compact('leads', 'users', 'leadSources', 'leadStatuses'));
    }

    public function show($id)
    {
        $query = Lead::with([
            'owner',
            'user',
            'notes.createdBy',
            'tasks.assignedTo',
            'activities.assignedTo',
            'calls.calledBy',
            'calls.callOwner',
            'emails.sentBy',
            'meetings.assignedTo',
        ]);

        if (!Auth::user()->hasRole('Admin')) {
            $query->where(function ($q) {
                $q->where('created_by', Auth::id())
                    ->orWhere('lead_owner', Auth::id());
            });
        }

        $lead = $query->findOrFail($id);

        $scheduledCalls = $lead->calls->where('status', 'scheduled')->sortBy('call_date');
        $loggedCalls = $lead->calls->where('status', '!=', 'scheduled')->sortByDesc('call_date');
        $upcomingMeetings = $lead->meetings->where('status', 'scheduled')->sortBy('meeting_date');
        $completedMeetings = $lead->meetings->where('status', '!=', 'scheduled')->sortByDesc('meeting_date');
        $pendingActivities = $lead->activities->where('status', 'pending')->sortBy('due_date');
        $completedActivities = $lead->activities->where('status', '!=', 'pending')->sortByDesc('completed_at');
        $users = User::orderBy('username')->get();
        $emailTemplates = EmailTemplate::orderBy('name')->get();

        return view('Admin.Leads.show', compact(
            'lead',
            'scheduledCalls',
            'loggedCalls',
            'upcomingMeetings',
            'completedMeetings',
            'pendingActivities',
            'completedActivities',
            'users',
            'emailTemplates'
        ));
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
                $previousOwnerId = $lead->lead_owner;
                if ($request->hasFile('lead_image') && $lead->lead_image && file_exists(public_path($lead->lead_image))) {
                    unlink(public_path($lead->lead_image));
                }
                $lead->update($data);
                $this->notifyAssignedLeadOwner($lead->fresh('owner'), $previousOwnerId);
                return response()->json(['success' => true, 'message' => 'Lead updated successfully!']);
            } else {
                $data['created_by'] = Auth::user()->id;
                $lead = Lead::create($data);
                
                            $this->notifyAssignedActivitiesForLead($lead, 'lead');

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

    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lead_ids' => 'required|array|min:1',
            'lead_ids.*' => 'integer|exists:leads,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $leadIds = collect($request->input('lead_ids', []))
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            $query = Lead::whereIn('id', $leadIds);

            if (!Auth::user()->hasRole('Admin')) {
                $query->where(function ($q) {
                    $q->where('created_by', Auth::id())
                        ->orWhere('lead_owner', Auth::id());
                });
            }

            $leads = $query->get();

            if ($leads->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid leads were found for deletion.',
                ], 404);
            }

            foreach ($leads as $lead) {
                if ($lead->lead_image && file_exists(public_path($lead->lead_image))) {
                    unlink(public_path($lead->lead_image));
                }

                $lead->delete();
            }

            return response()->json([
                'success' => true,
                'message' => $leads->count() . ' lead(s) deleted successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting selected leads!',
            ], 500);
        }
    }

    public function get($id)
    {
        try {
            $lead = Lead::with(['owner', 'user'])->findOrFail($id);
            return response()->json(['success' => true, 'data' => $lead]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lead not found!']);
        }
    }

    public function getNotes($id)
    {
        try {
            $lead = Lead::findOrFail($id);
            $notes = $lead->notes()->with('createdBy')->get()->map(function ($note) {
                return [
                    'id' => $note->id,
                    'note' => $note->note,
                    'type' => $note->type,
                    'created_at' => $note->created_at,
                    'created_by_name' => optional($note->createdBy)->username ?? optional($note->createdBy)->name ?? 'User',
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $notes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching notes!'
            ]);
        }
    }

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
            $lead->addActivity(
                'note',
                'Note added',
                $request->note,
                null,
                auth()->id()
            );
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

    public function updateNote(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'note' => 'required|string|min:2',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ]);
            }

            $note = LeadNote::findOrFail($id);
            $note->note = $request->note;
            $note->save();

            return response()->json([
                'success' => true,
                'message' => 'Note updated successfully!',
                'data' => $note
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteNote($id)
    {
        try {
            $note = LeadNote::findOrFail($id);
            $note->delete();

            return response()->json([
                'success' => true,
                'message' => 'Note deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting note!'
            ]);
        }
    }

    public function addTask(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'lead_id' => 'required|exists:leads,id',
                'subject' => 'required|string|min:3|max:255',
                'description' => 'nullable|string',
                'assigned_to' => 'required|exists:users,id',
                'due_date' => 'nullable|date|after_or_equal:today'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ]);
            }

            $lead = Lead::findOrFail($request->lead_id);

            $task = $lead->addTask(
                $request->subject,
                $request->description,
                $request->assigned_to,
                $request->due_date
            );
            

            $lead->addActivity(
                'task',
                'Task created: ' . $request->subject,
                $request->description,
                $request->due_date,
                $request->assigned_to
            );
            $this->notifyAssignedActivitiesForLead($task , 'task');

            return response()->json([
                'success' => true,
                'message' => 'Task created successfully!',
                'data' => $task
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function getTasks($id)
    {
        try {
            $lead = Lead::findOrFail($id);
            $tasks = $lead->tasks()->with('assignedTo')->get()->map(function ($task) {
                return [
                    'id' => $task->id,
                    'subject' => $task->subject,
                    'description' => $task->description,
                    'due_date' => $task->due_date,
                    'status' => $task->status,
                    'assigned_to_name' => optional($task->assignedTo)->username ?? optional($task->assignedTo)->name ?? '-',
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $tasks
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching tasks!'
            ]);
        }
    }

    public function updateTaskStatus(Request $request, $id)
    {
        try {
            $task = LeadTask::findOrFail($id);

            if ($request->status == 'completed') {
                $task->complete();
            } else {
                $task->status = $request->status;
                $task->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Task status updated!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating task!'
            ]);
        }
    }

    public function updateTask(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'subject' => 'required|string|min:3|max:255',
                'description' => 'nullable|string',
                'assigned_to' => 'required|exists:users,id',
                'due_date' => 'nullable|date',
                'status' => 'required|string|max:50',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ]);
            }

            $task = LeadTask::findOrFail($id);
            $task->update([
                'subject' => $request->subject,
                'description' => $request->description,
                'assigned_to' => $request->assigned_to,
                'due_date' => $request->due_date,
                'status' => $request->status,
                'completed_at' => $request->status === 'completed' ? now() : null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Task updated successfully!',
                'data' => $task
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteTask($id)
    {
        try {
            $task = LeadTask::findOrFail($id);
            $task->delete();

            return response()->json([
                'success' => true,
                'message' => 'Task deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting task!'
            ]);
        }
    }

    public function sendEmail(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'lead_id' => 'required|exists:leads,id',
                'to_email' => 'required|email',
                'subject' => 'required|string|max:255',
                'body' => 'required|string',
                'cc' => 'nullable|string',
                'bcc' => 'nullable|string',
                'template_id' => 'nullable|exists:email_templates,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ]);
            }

            $lead = Lead::findOrFail($request->lead_id);

            // Prepare email content
            $body = $request->body;
            $subject = $request->subject;

            // Replace placeholders if template is used
            if ($request->template_id) {
                $template = EmailTemplate::find($request->template_id);
                if ($template) {
                    $body = str_replace(
                        ['{name}', '{company}', '{email}', '{phone}'],
                        [$lead->first_name . ' ' . $lead->last_name, $lead->company, $lead->email, $lead->phone],
                        $template->body
                    );
                    $subject = str_replace(
                        ['{name}', '{company}'],
                        [$lead->first_name . ' ' . $lead->last_name, $lead->company],
                        $template->subject
                    );
                }
            }

            // Replace placeholders in custom email
            $body = str_replace(
                ['{name}', '{company}', '{email}', '{phone}'],
                [$lead->first_name . ' ' . $lead->last_name, $lead->company, $lead->email, $lead->phone],
                $body
            );
            $subject = str_replace(
                ['{name}', '{company}'],
                [$lead->first_name . ' ' . $lead->last_name, $lead->company],
                $subject
            );

            $lead->addActivity(
                'email',
                'Email sent: ' . $subject,
                'To: ' . $request->to_email . "\nSubject: " . $subject,
                null,
                auth()->id()
            );
            // Log email activity
            $emailLog = $lead->logEmail(
                Auth::user()->email,
                $request->to_email,
                $subject,
                $body,
                $request->cc,
                $request->bcc
            );

            $details = [
                'subject'   => $request->subject, 
                'body'      => $request->body,    
                'lead_name' => $lead->first_name . ' ' . $lead->last_name,
            ];
            Mail::to($request->to_email)->queue(new LeadNotification($details));


            return response()->json([
                'success' => true,
                'message' => 'Email sent successfully!',
                'data' => $emailLog
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function getEmailTemplates()
    {
        try {
            $templates = EmailTemplate::all();
            return response()->json([
                'success' => true,
                'data' => $templates
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching templates!'
            ]);
        }
    }

    public function getEmailTemplate($id)
    {
        try {
            $template = EmailTemplate::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $template
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Template not found!'
            ]);
        }
    }

    public function saveEmailTemplate(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'subject' => 'required|string|max:255',
                'body' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ]);
            }

            $template = EmailTemplate::create([
                'name' => $request->name,
                'subject' => $request->subject,
                'body' => $request->body,
                'category' => $request->category,
                'created_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Template saved successfully!',
                'data' => $template
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    public function addCall(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'lead_id' => 'required|exists:leads,id',
                'call_type' => 'required|in:inbound,outbound',
                'call_purpose' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
                'duration' => 'nullable|string|max:20',
                'status' => 'required|in:completed,missed,voicemail,no_answer',
                'call_date' => 'required|date',
                'call_owner' => 'required|exists:users,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ]);
            }

            $lead = Lead::findOrFail($request->lead_id);

            $call = $lead->addCall(
                $request->call_type,
                $request->call_purpose,
                $request->notes,
                $request->duration,
                $request->status,
                $request->call_date,
                $request->call_owner
            );

            
            
            
            $lead->addActivity(
                'call',
                'Call logged: ' . ($request->call_purpose ?? 'Call'),
                'Type: ' . $request->call_type . "\nStatus: " . $request->status . "\nDuration: " . ($request->duration ?? 'N/A') . "\nNotes: " . ($request->notes ?? ''),
                null,
                auth()->id()
                );

            $this->notifyAssignedActivitiesForLead($call, 'call');
                
            // Update lead's last_contacted_at
            $lead->last_contacted_at = now();
            $lead->save();

            return response()->json([
                'success' => true,
                'message' => 'Call logged successfully!',
                'data' => $call
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function getLeadCalls($id)
    {
        try {
            $lead = Lead::findOrFail($id);
            $loggedCalls = $lead->calls()->with(['calledBy', 'callOwner'])->where('status', 'completed')->orderBy('call_date', 'desc')->get()->map(function ($call) {
                $call->call_owner_name = optional($call->callOwner)->username ?? optional($call->callOwner)->name ?? optional($call->calledBy)->username ?? optional($call->calledBy)->name ?? '-';
                return $call;
            });
            $scheduledCalls = $lead->calls()->with(['calledBy', 'callOwner'])->where('status', 'scheduled')->orderBy('call_date', 'asc')->get()->map(function ($call) {
                $call->call_owner_name = optional($call->callOwner)->username ?? optional($call->callOwner)->name ?? optional($call->calledBy)->username ?? optional($call->calledBy)->name ?? '-';
                return $call;
            });

            return response()->json([
                'success' => true,
                'logged_calls' => $loggedCalls,
                'scheduled_calls' => $scheduledCalls
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching calls!'
            ]);
        }
    }

    public function getCalls($id)
    {
        try {
            $lead = Lead::findOrFail($id);
            $calls = $lead->calls()->with(['calledBy', 'callOwner'])->get();

            return response()->json([
                'success' => true,
                'data' => $calls
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching calls!'
            ]);
        }
    }

    // Get all calls (for calls index page)
    public function callsIndex()
    {
        $calls = LeadCall::with(['lead', 'calledBy'])
            ->orderBy('call_date', 'desc')
            ->paginate(20);

        $leads = Lead::all();
        $users = User::all();

        return view('Admin.Calls.index', compact('calls', 'leads', 'users'));
    }

    // Create a new scheduled call (future call)
    public function createCall(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'lead_id' => 'nullable|exists:leads,id',
                'call_type' => 'required|in:inbound,outbound',
                'call_purpose' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
                'duration' => 'nullable|string|max:20',
                'status' => 'required|in:scheduled,completed,missed,voicemail,no_answer',
                'call_date' => 'required|date',
                'called_by' => 'required|exists:users,id',
                'call_owner' => 'required|exists:users,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ]);
            }

            $call = LeadCall::create([
                'lead_id' => $request->lead_id,
                'call_type' => $request->call_type,
                'call_purpose' => $request->call_purpose,
                'notes' => $request->notes,
                'duration' => $request->duration,
                'status' => $request->status,
                'call_date' => $request->call_date,
                'called_by' => $request->called_by,
                'call_owner' => $request->call_owner
            ]);

            // If call is completed and has lead, update lead's last_contacted_at
            if ($request->status == 'completed' && $request->lead_id) {
                $lead = Lead::find($request->lead_id);
                if ($lead) {
                    $lead->last_contacted_at = now();
                    $lead->save();
                }
            }

            return response()->json([
                'success' => true,
                'message' => $request->status == 'scheduled' ? 'Call scheduled successfully!' : 'Call logged successfully!',
                'data' => $call
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function logCall(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'lead_id' => 'required|exists:leads,id',
                'call_type' => 'required|in:inbound,outbound',
                'call_purpose' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
                'duration' => 'nullable|string|max:20',
                'status' => 'required|in:completed,missed,voicemail,no_answer',
                'call_date' => 'required|date',
                'call_owner' => 'required|exists:users,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ]);
            }

            $lead = Lead::findOrFail($request->lead_id);

            $call = $lead->calls()->create([
                'call_type' => $request->call_type,
                'call_purpose' => $request->call_purpose,
                'notes' => $request->notes,
                'duration' => $request->duration,
                'status' => $request->status,
                'call_date' => $request->call_date,
                'called_by' => auth()->id(),
                'call_owner' => $request->call_owner
            ]);

            // Update lead's last_contacted_at
            $lead->last_contacted_at = now();
            $lead->save();
                        $this->notifyAssignedActivitiesForLead($call, 'call');


            return response()->json([
                'success' => true,
                'message' => 'Call logged successfully!',
                'data' => $call
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    // Create a scheduled call (future call)
    public function createScheduledCall(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'lead_id' => 'required|exists:leads,id',
                'call_type' => 'required|in:inbound,outbound',
                'call_purpose' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
                'call_date' => 'required|date|after:now',
                'call_owner' => 'required|exists:users,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ]);
            }

            $lead = Lead::findOrFail($request->lead_id);

            $call = $lead->calls()->create([
                'call_type' => $request->call_type,
                'call_purpose' => $request->call_purpose,
                'notes' => $request->notes,
                'duration' => null,
                'status' => 'scheduled',
                'call_date' => $request->call_date,
                'called_by' => auth()->id(),
                'call_owner' => $request->call_owner
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Call scheduled successfully!',
                'data' => $call
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    // Update scheduled call to logged call
    public function updateCallToLogged(Request $request, $id)
    {
        try {
            $call = LeadCall::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'duration' => 'nullable|string|max:20',
                'notes' => 'nullable|string',
                'status' => 'required|in:completed,missed,voicemail,no_answer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ]);
            }

            $call->duration = $request->duration;
            $call->notes = $request->notes;
            $call->status = $request->status;
            $call->save();

            // Update lead's last_contacted_at if completed
            if ($request->status == 'completed' && $call->lead_id) {
                $lead = Lead::find($call->lead_id);
                if ($lead) {
                    $lead->last_contacted_at = now();
                    $lead->save();
                }
            }


            return response()->json([
                'success' => true,
                'message' => 'Call updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    // Delete call
    public function deleteLeadCall($id)
    {
        try {
            $call = LeadCall::findOrFail($id);
            $call->delete();

            return response()->json([
                'success' => true,
                'message' => 'Call deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting call!'
            ]);
        }
    }

    // Update call
    public function updateCall(Request $request, $id)
    {
        try {
            $call = LeadCall::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'lead_id' => 'nullable|exists:leads,id',
                'call_type' => 'required|in:inbound,outbound',
                'call_purpose' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
                'duration' => 'nullable|string|max:20',
                'status' => 'required|in:scheduled,completed,missed,voicemail,no_answer',
                'call_date' => 'required|date',
                'called_by' => 'nullable|exists:users,id',
                'call_owner' => 'required|exists:users,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ]);
            }

            $oldStatus = $call->status;
            $call->update([
                'lead_id' => $request->input('lead_id', $call->lead_id),
                'call_type' => $request->call_type,
                'call_purpose' => $request->call_purpose,
                'notes' => $request->notes,
                'duration' => $request->duration,
                'status' => $request->status,
                'call_date' => $request->call_date,
                'called_by' => $request->input('called_by', $call->called_by),
                'call_owner' => $request->call_owner,
            ]);

            // If status changed to completed and has lead, update lead's last_contacted_at
            if ($oldStatus != 'completed' && $request->status == 'completed' && $call->lead_id) {
                $lead = Lead::find($call->lead_id);
                if ($lead) {
                    $lead->last_contacted_at = now();
                    $lead->save();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Call updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    // Delete call
    public function deleteCall($id)
    {
        try {
            $call = LeadCall::findOrFail($id);
            $call->delete();

            return response()->json([
                'success' => true,
                'message' => 'Call deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting call!'
            ]);
        }
    }

    // Get call details
    public function getCall($id)
    {
        try {
            $call = LeadCall::with(['lead', 'calledBy', 'callOwner'])->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $call
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Call not found!'
            ]);
        }
    }

    // Get upcoming calls for dashboard
    public function getUpcomingCalls()
    {
        try {
            $calls = LeadCall::upcoming()->with('lead')->get();
            return response()->json([
                'success' => true,
                'data' => $calls
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching calls!'
            ]);
        }
    }

    // Convert scheduled call to logged call
    public function convertToLoggedCall(Request $request, $id)
    {
        try {
            $call = LeadCall::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'duration' => 'nullable|string|max:20',
                'notes' => 'nullable|string',
                'call_purpose' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ]);
            }

            $call->status = 'completed';
            if ($request->duration) {
                $call->duration = $request->duration;
            }
            if ($request->notes) {
                $call->notes = $request->notes;
            }
            if ($request->call_purpose) {
                $call->call_purpose = $request->call_purpose;
            }
            $call->save();

            // Update lead's last_contacted_at
            if ($call->lead_id) {
                $lead = Lead::find($call->lead_id);
                if ($lead) {
                    $lead->last_contacted_at = now();
                    $lead->save();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Call logged successfully!',
                'data' => $call
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function scheduleMeeting(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'lead_id' => 'required|exists:leads,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'meeting_link' => 'nullable|url|max:500',
                'assigned_to' => 'required|exists:users,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ]);
            }

            $lead = Lead::findOrFail($request->lead_id);

            $meeting = $lead->scheduleMeeting(
                $request->title,
                $request->description,
                $request->meeting_type,
                $request->location,
                $request->meeting_link,
                $request->meeting_date,
                $request->duration,
                $request->assigned_to
            );

            // Add activity to timeline
            $lead->addActivity(
                'meeting',
                'Meeting scheduled: ' . $request->title,
                'Date: ' . date('d M Y, h:i A', strtotime($request->meeting_date)) . "\nType: " . $request->meeting_type . "\n" . ($request->description ?? ''),
                null,
                auth()->id()
            );

            $this->notifyAssignedActivitiesForLead($meeting, 'meeting');

            return response()->json([
                'success' => true,
                'message' => 'Meeting scheduled successfully!',
                'data' => $meeting
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    // Get lead meetings
    public function getLeadMeetings($id)
    {
        try {
            $lead = Lead::findOrFail($id);
            $upcomingMeetings = $lead->upcomingMeetings()->with('assignedTo')->get()->map(function ($meeting) {
                return [
                    'id' => $meeting->id,
                    'title' => $meeting->title,
                    'description' => $meeting->description,
                    'meeting_type' => $meeting->meeting_type,
                    'location' => $meeting->location,
                    'meeting_link' => $meeting->meeting_link,
                    'meeting_date' => $meeting->meeting_date,
                    'duration' => $meeting->duration,
                    'status' => $meeting->status,
                    'notes' => $meeting->notes,
                    'assigned_to_name' => optional($meeting->assignedTo)->username ?? optional($meeting->assignedTo)->name ?? '-',
                ];
            });
            $completedMeetings = $lead->completedMeetings()->with('assignedTo')->get()->map(function ($meeting) {
                return [
                    'id' => $meeting->id,
                    'title' => $meeting->title,
                    'description' => $meeting->description,
                    'meeting_type' => $meeting->meeting_type,
                    'location' => $meeting->location,
                    'meeting_link' => $meeting->meeting_link,
                    'meeting_date' => $meeting->meeting_date,
                    'duration' => $meeting->duration,
                    'status' => $meeting->status,
                    'notes' => $meeting->notes,
                    'assigned_to_name' => optional($meeting->assignedTo)->username ?? optional($meeting->assignedTo)->name ?? '-',
                ];
            });

            return response()->json([
                'success' => true,
                'upcoming_meetings' => $upcomingMeetings,
                'completed_meetings' => $completedMeetings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching meetings!'
            ]);
        }
    }

    // Update meeting
    public function updateMeeting(Request $request, $id)
    {
        try {
            $meeting = LeadMeeting::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'meeting_link' => 'nullable|url|max:500',
                'meeting_date' => 'required|date',
                'status' => 'required|in:scheduled,completed,cancelled,rescheduled',
                'assigned_to' => 'required|exists:users,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ]);
            }

            $meeting->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Meeting updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    // Delete meeting
    public function deleteMeeting($id)
    {
        try {
            $meeting = LeadMeeting::findOrFail($id);
            $meeting->delete();

            return response()->json([
                'success' => true,
                'message' => 'Meeting deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting meeting!'
            ]);
        }
    }

    // Complete meeting
    public function completeMeeting(Request $request, $id)
    {
        try {
            $meeting = LeadMeeting::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'notes' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ]);
            }

            $meeting->complete($request->notes);

            // Update lead's last_contacted_at
            if ($meeting->lead_id) {
                $lead = Lead::find($meeting->lead_id);
                if ($lead) {
                    $lead->last_contacted_at = now();
                    $lead->save();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Meeting marked as completed!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function convertToCustomer($id)
{
    try {
        $lead = Lead::findOrFail($id);
        
        // Check if already converted
        if ($lead->isConverted()) {
            return redirect()->route('admin.leads.index')->with('error', 'Lead already converted!');
        }
        
        return view('Admin.Leads.convert', compact('lead'));
    } catch (\Exception $e) {
        return redirect()->route('admin.leads.index')->with('error', 'Error: ' . $e->getMessage());
    }
}

public function processConversion(Request $request, $id)
{
    try {
        $lead = Lead::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'account_name' => 'required|string|max:255',
            'account_email' => 'nullable|email',
            'account_phone' => 'nullable|string',
            'account_website' => 'nullable|url',
            'account_industry' => 'nullable|string',
            'account_annual_revenue' => 'nullable|numeric',
            'account_no_of_employees' => 'nullable|integer',
            'account_street' => 'nullable|string',
            'account_city' => 'nullable|string',
            'account_state' => 'nullable|string',
            'account_country' => 'nullable|string',
            'account_zip_code' => 'nullable|string',
            'account_description' => 'nullable|string',
            'contact_first_name' => 'required|string|max:255',
            'contact_last_name' => 'nullable|string|max:255',
            'contact_title' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string',
            'contact_mobile' => 'nullable|string',
            'contact_description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $accountData = [
            'name' => $request->account_name,
            'email' => $request->account_email,
            'phone' => $request->account_phone,
            'website' => $request->account_website,
            'industry' => $request->account_industry,
            'annual_revenue' => $request->account_annual_revenue,
            'no_of_employees' => $request->account_no_of_employees,
            'street' => $request->account_street,
            'city' => $request->account_city,
            'state' => $request->account_state,
            'country' => $request->account_country,
            'zip_code' => $request->account_zip_code,
            'description' => $request->account_description,
            'type' => 'Customer'
        ];

        $contactData = [
            'first_name' => $request->contact_first_name,
            'last_name' => $request->contact_last_name,
            'title' => $request->contact_title,
            'email' => $request->contact_email,
            'phone' => $request->contact_phone,
            'mobile' => $request->contact_mobile,
            'description' => $request->contact_description
        ];

        $result = $lead->convertToAccountAndContact($accountData, $contactData);

        if ($result['success']) {
            return redirect()->route('admin.leads.index')->with('success', 'Lead converted to customer successfully!');
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
    }
}

    

    public function notifyAssignedActivitiesForLead($hh, $type): void
    {
        switch ($type) {
            case "task" :

            $assignedUser = User::find($hh->assigned_to);
    
        if (!$assignedUser) {
            return;
        }

        $assignedUser->notify(new RecordAssignedNotification(
            'Task',
            $hh->id,
            'Task Assigned',
            'A new Task has been assigned to you for lead ' . (''),
            route('admin.lead.show', $hh->lead_id),
            Auth::id()
        ));
        break ;
        
        case 'call':
            
            $assignedUser = User::find($hh->assigned_to);
    
        if (!$assignedUser) {
            return;
        }

        $assignedUser->notify(new RecordAssignedNotification(
            'Call',
            $hh->id,
            'Call Assigned',
            'A new Call has been assigned to you for lead ' . (''),
            route('admin.lead.show', $hh->lead_id),
            Auth::id()
        ));
        break;

        case 'meeting':

             $assignedUser = User::find($hh->assigned_to);
    
        if (!$assignedUser) {
            return;
        }
            $assignedUser->notify(new RecordAssignedNotification(
            'Meeting',
            $hh->id,
            'Meeting Assigned',
            'A new Meeting has been assigned to you ' . (''),
            route('admin.lead.show', $hh->lead_id),
            Auth::id()
        ));
        case 'lead':

             $assignedUser = User::find($hh->lead_owner);
    
        if (!$assignedUser) {
            return;
        }
            $assignedUser->notify(new RecordAssignedNotification(
            'Lead',
            $hh->id,
            'Lead Assigned',
            'A new Lead has been assigned to you ' . (''),
            route('admin.lead.show', $hh->id),
            Auth::id()
        ));
        break;
        }        
    }
}
