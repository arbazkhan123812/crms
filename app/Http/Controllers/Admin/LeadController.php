<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\LeadNotification;
use App\Models\EmailTemplate;
use App\Models\Lead;
use App\Models\LeadEmail;
use App\Models\LeadTask;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class LeadController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:leads_view', ['only' => ['index']]);
        $this->middleware('permission:leads_save', ['only' => ['save']]);
        $this->middleware('permission:leads_delete', ['only' => ['delete']]);
        $this->middleware('permission:leads_sendemail', ['only' => ['sendEmail']]);
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
            $tasks = $lead->tasks()->with('assignedTo')->get();

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
                'subject'   => $request->subject, // User input se subject
                'body'      => $request->body,    // User input se body (matter)
                'lead_name' => $lead->first_name . ' ' . $lead->last_name,
            ];
            Mail::to($request->to_email)->send(new LeadNotification($details));

        
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
                'call_date' => 'required|date'
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
                $request->call_date
            );

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

    public function getCalls($id)
    {
        try {
            $lead = Lead::findOrFail($id);
            $calls = $lead->calls()->with('calledBy')->get();

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
}
