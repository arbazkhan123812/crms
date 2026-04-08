<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Contact;
use App\Models\Email;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class EmailController extends Controller
{
    public function __construct()
    {
    }

    public function sendEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'entity_type' => 'required|in:account,contact,lead',
            'entity_id' => 'required|integer',
            'to_email' => 'required|email',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'cc' => 'nullable|string',
            'bcc' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            // Get entity name for logging
            $entityName = '';
            if ($request->entity_type == 'account') {
                $entity = Account::find($request->entity_id);
                $entityName = $entity ? $entity->name : 'Unknown';
            } elseif ($request->entity_type == 'contact') {
                $entity = Contact::find($request->entity_id);
                $entityName = $entity ? $entity->full_name : 'Unknown';
            } elseif ($request->entity_type == 'lead') {
                $entity = Lead::find($request->entity_id);
                $entityName = $entity ? $entity->full_name : 'Unknown';
            }

            // Save email record
            $email = Email::create([
                'from_email' => auth()->user()->email,
                'to_email' => $request->to_email,
                'cc' => $request->cc,
                'bcc' => $request->bcc,
                'subject' => $request->subject,
                'body' => $request->body,
                'entity_type' => $request->entity_type,
                'entity_id' => $request->entity_id,
                'status' => 'sent',
                'sent_by' => auth()->id()
            ]);

            // Here you can integrate actual email sending via Mailgun, SMTP, etc.
            // For now, we'll just log it

            return response()->json([
                'success' => true,
                'message' => 'Email sent successfully!',
                'data' => $email
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function getEmails($entityType, $entityId)
    {
        try {
            $emails = Email::where('entity_type', $entityType)
                ->where('entity_id', $entityId)
                ->with('sentBy')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $emails
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching emails!'
            ]);
        }
    }
}